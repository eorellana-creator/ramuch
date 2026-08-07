<?php
include('_bootstrap.php');
intranetValidarCsrf();

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

intranetJson(['ok' => true, 'estado' => $estadoNuevo]);
?>
