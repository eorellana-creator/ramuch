<?php
include('_bootstrap.php');
intranetValidarCsrf();

function notificarDecisionDirectiva($mysql, $item, $accion, $responsable, $comentario)
{
    try {
        require_once __DIR__ . '/../../../includes/RamuchMailer.php';

        $hostActual = strtolower(preg_replace('/:\\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));
        $esStaging = $hostActual === 'staging.ramuch.cl';
        // Producción: solo la Directiva. Staging: solo el buzón de pruebas.
        $destinatarios = [];
        if (!$esStaging) {
            $sqlDirectiva = $mysql->query("SELECT DISTINCT u.email
                FROM usuario AS u
                INNER JOIN rol AS r ON r.id_rol = u.id_rol
                WHERE u.estado = 'Vigente'
                  AND LOWER(TRIM(r.nombre)) = 'administrador de socios'
                  AND u.email != '';");
            if ($sqlDirectiva) {
                while ($directivo = $mysql->f_obj($sqlDirectiva)) {
                    if (filter_var($directivo->email, FILTER_VALIDATE_EMAIL)) {
                        $destinatarios[] = strtolower(trim($directivo->email));
                    }
                }
            }
        } else {
            $destinatarios = ['eorellana@gmail.com'];
        }
        $destinatarios = array_values(array_unique($destinatarios));
        if (!$destinatarios) {
            error_log('Intranet: no hay correos de Directiva vigentes para notificar la decisión.');
            return false;
        }

        $decision = $accion === 'aprobar' ? 'aprobada' : 'rechazada';
        $titulo = $accion === 'aprobar' ? 'Valorización aprobada' : 'Solicitud rechazada';
        $asunto = ($esStaging ? '[STAGING] ' : '') . "$titulo - Solicitud #$item->id_solicitud";
        $textoSolicitud = htmlspecialchars($item->texto, ENT_QUOTES, 'UTF-8');
        $responsableHtml = htmlspecialchars($responsable, ENT_QUOTES, 'UTF-8');
        $comentarioLimpio = str_replace("''", "'", $comentario);
        $comentarioHtml = htmlspecialchars($comentarioLimpio, ENT_QUOTES, 'UTF-8');
        $valor = $item->valor === null ? 'Sin valor registrado' : '$ ' . number_format((int)$item->valor, 0, ',', '.');
        $urlIntranet = ($esStaging ? 'https://staging.ramuch.cl' : 'https://www.ramuch.cl')
            . '/admin/index.php?component=intranet&view=dashboard';

        $cuerpo = "<html><body style='font-family:Arial,sans-serif;color:#1a1a1a'>
            <h2>$titulo</h2>
            <p>La solicitud <strong>#$item->id_solicitud</strong> fue $decision por <strong>$responsableHtml</strong>.</p>
            <p><strong>Solicitud:</strong><br>" . nl2br($textoSolicitud) . "</p>
            <p><strong>Valor:</strong> $valor</p>
            <p><strong>Comentario:</strong><br>" . ($comentarioHtml !== '' ? nl2br($comentarioHtml) : 'Sin comentarios.') . "</p>
            <p><a href='$urlIntranet'>Abrir solicitudes de actualizaciones</a></p>
            </body></html>";

        $mail = crearMailerRamuch(true);
        $mail->setFrom('no-responder@ramuch.cl', 'Sistema Ramuch');
        $mail->addReplyTo('directiva@ramuch.cl', 'Directiva Ramuch');
        foreach ($destinatarios as $destinatario) {
            $mail->addAddress($destinatario);
        }
        $mail->Subject = $asunto;
        $mail->msgHTML($cuerpo);
        $mail->AltBody = "$titulo\n\nSolicitud #$item->id_solicitud, $decision por $responsable.\n"
            . "Solicitud: $item->texto\nValor: $valor\nComentario: "
            . ($comentarioLimpio !== '' ? $comentarioLimpio : 'Sin comentarios.')
            . "\n\n$urlIntranet";
        return $mail->send();
    } catch (\Throwable $error) {
        error_log('Intranet: decisión guardada, pero falló la notificación: ' . $error->getMessage());
        return false;
    }
}

$token = preg_match('/^[a-f0-9]{32}$/', $_POST['token'] ?? '') ? $_POST['token'] : '';
$accion = $_POST['accion'] ?? '';
$comentario = intranetTextoSql($_POST['comentario'] ?? '');
$valor = max(0, (int)($_POST['valor'] ?? 0));
if ($token === '') intranetJson(['error' => 'Solicitud inválida.'], 400);

// Flujo único: solicitado → valorado → aprobado → pagado → finalizado.
// Descartado es una salida terminal disponible mientras no haya finalizado.
$reglas = [
    'valorizar' => ['desarrollador', 'solicitado', 'valorado'],
    'aprobar' => ['directiva', 'valorado', 'aprobado'],
    'pagar' => ['directiva', 'aprobado', 'pagado'],
    'finalizar' => ['directiva', 'pagado', 'finalizado']
];

if ($accion === 'descartar') {
    if ($comentario === '') intranetJson(['error' => 'Debes indicar el motivo para descartar.'], 400);
    $estadoEsperado = null;
    $estadoNuevo = 'descartado';
} else {
    if (!isset($reglas[$accion])) intranetJson(['error' => 'Acción no permitida.'], 400);
    [$rolRequerido, $estadoEsperado, $estadoNuevo] = $reglas[$accion];
    if ($rolIntranet !== $rolRequerido) intranetJson(['error' => 'No tienes permiso para esta etapa.'], 403);
    if ($accion === 'valorizar' && $valor <= 0) intranetJson(['error' => 'Debes ingresar un valor mayor que cero.'], 400);
}

if (!$mysql->query('START TRANSACTION')) {
    intranetJson(['error' => 'No fue posible iniciar la actualización.'], 500);
}

$sqlItem = $mysql->query("SELECT * FROM intranet_solicitud WHERE token='$token' FOR UPDATE;");
$item = $sqlItem ? $mysql->f_obj($sqlItem) : null;
if (!$item) {
    $mysql->query('ROLLBACK');
    intranetJson(['error' => 'Solicitud no encontrada.'], 404);
}

if ($accion === 'descartar') {
    if (in_array($item->estado, ['finalizado', 'descartado'], true)) {
        $mysql->query('ROLLBACK');
        intranetJson(['error' => 'La solicitud ya no puede descartarse.'], 409);
    }
    $estadoEsperado = $item->estado;
} elseif ($item->estado !== $estadoEsperado) {
    $mysql->query('ROLLBACK');
    intranetJson(['error' => 'La solicitud cambió de estado. Recarga el listado.'], 409);
}

$ahora = date('Y-m-d H:i:s');
$campos = "estado='$estadoNuevo',fecha_actualizacion='$ahora'";
if ($accion === 'valorizar') $campos .= ",valor='$valor',detalle_valorizacion='$comentario'";
if ($accion === 'aprobar') $campos .= ",observacion_directiva='$comentario'";
if ($accion === 'pagar') $campos .= ",pagado=1,observacion_directiva='$comentario'";
if ($accion === 'finalizar') $campos .= ",observacion_final='$comentario'";

$okActualizar = $mysql->query("UPDATE intranet_solicitud SET $campos WHERE id_solicitud='$item->id_solicitud';");
$nombre = intranetTextoSql($nombreUsuarioIntranet, 255);
$okHistorial = $mysql->query("INSERT INTO intranet_solicitud_historial (id_solicitud,id_usuario,usuario_nombre,accion,estado_anterior,estado_nuevo,comentario,fecha) VALUES ('$item->id_solicitud','$idUsuarioIntranet','$nombre','$accion','$estadoEsperado','$estadoNuevo','$comentario','$ahora');");

if (!$okActualizar || !$okHistorial) {
    $mysql->query('ROLLBACK');
    error_log('Intranet: falló una transición o su registro de auditoría.');
    intranetJson(['error' => 'No fue posible guardar el cambio y su historial.'], 500);
}
if (!$mysql->query('COMMIT')) {
    $mysql->query('ROLLBACK');
    error_log('Intranet: falló la confirmación de una transición.');
    intranetJson(['error' => 'No fue posible confirmar el cambio.'], 500);
}

$debeNotificar = $accion === 'aprobar' || ($accion === 'descartar' && $rolIntranet === 'directiva');
$correoEnviado = null;
if ($debeNotificar) {
    $correoEnviado = notificarDecisionDirectiva($mysql, $item, $accion, $nombreUsuarioIntranet, $comentario);
}

$respuesta = ['ok' => true, 'estado' => $estadoNuevo];
if ($debeNotificar) {
    $respuesta['email_sent'] = $correoEnviado;
    if (!$correoEnviado) {
        $respuesta['warning'] = 'La decisión quedó registrada, pero no fue posible enviar la notificación por correo.';
    }
}
intranetJson($respuesta);
?>
