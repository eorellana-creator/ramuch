<?php
include('_bootstrap.php');
intranetValidarCsrf();

$token = preg_match('/^[a-f0-9]{32}$/', $_POST['token'] ?? '') ? $_POST['token'] : '';
$accion = $_POST['accion'] ?? '';
$comentario = intranetTextoSql($_POST['comentario'] ?? '');
$valor = max(0, (int)($_POST['valor'] ?? 0));
if ($token === '') intranetJson(['error' => 'Solicitud inválida.'], 400);

if ($accion === 'descartar') {
    if ($comentario === '') intranetJson(['error' => 'Debes indicar el motivo para descartar.'], 400);
    $mysql->query('START TRANSACTION');
    $sqlItem = $mysql->query("SELECT * FROM intranet_solicitud WHERE token='$token' FOR UPDATE;");
    $item = $mysql->f_obj($sqlItem);
    if (!$item || in_array($item->estado, ['finalizada', 'descartada'], true)) {
        $mysql->query('ROLLBACK');
        intranetJson(['error' => 'La solicitud ya no puede descartarse.'], 409);
    }
    $ahora = date('Y-m-d H:i:s');
    $mysql->query("UPDATE intranet_solicitud SET estado='descartada',fecha_actualizacion='$ahora' WHERE id_solicitud='$item->id_solicitud';");
    $nombre = intranetTextoSql($nombreUsuarioIntranet, 255);
    $mysql->query("INSERT INTO intranet_solicitud_historial (id_solicitud,id_usuario,usuario_nombre,accion,estado_anterior,estado_nuevo,comentario,fecha) VALUES ('$item->id_solicitud','$idUsuarioIntranet','$nombre','descartar','$item->estado','descartada','$comentario','$ahora');");
    $mysql->query('COMMIT');
    intranetJson(['ok' => true]);
}

$reglas = [
    'valorizar' => ['desarrollador', 'solicitada', 'valorizada'],
    'aprobar' => ['directiva', 'valorizada', 'aprobada'],
    'rechazar' => ['directiva', 'valorizada', 'rechazada'],
    'iniciar' => ['desarrollador', 'aprobada', 'en_desarrollo'],
    'realizar' => ['desarrollador', 'en_desarrollo', 'realizada'],
    'finalizar' => ['directiva', 'realizada', 'finalizada'],
    'observar' => ['directiva', 'realizada', 'en_desarrollo']
];

if ($accion === 'pagar' || $accion === 'pago_pendiente') {
    if ($rolIntranet !== 'directiva') intranetJson(['error' => 'Sólo Directiva puede registrar pagos.'], 403);
    $pagado = $accion === 'pagar' ? 1 : 0;
    $ahora = date('Y-m-d H:i:s');
    $sqlItem = $mysql->query("SELECT id_solicitud,estado FROM intranet_solicitud WHERE token='$token' LIMIT 1;");
    $item = $mysql->f_obj($sqlItem);
    if (!$item) intranetJson(['error' => 'Solicitud no encontrada.'], 404);
    $mysql->query("UPDATE intranet_solicitud SET pagado='$pagado',fecha_actualizacion='$ahora' WHERE token='$token';");
    $nombre = intranetTextoSql($nombreUsuarioIntranet, 255);
    $accionTexto = $pagado ? 'pago_confirmado' : 'pago_pendiente';
    $mysql->query("INSERT INTO intranet_solicitud_historial (id_solicitud,id_usuario,usuario_nombre,accion,estado_anterior,estado_nuevo,comentario,fecha) VALUES ('$item->id_solicitud','$idUsuarioIntranet','$nombre','$accionTexto','$item->estado','$item->estado','$comentario','$ahora');");
    intranetJson(['ok' => true]);
}

if (!isset($reglas[$accion])) intranetJson(['error' => 'Acción no permitida.'], 400);
[$rolRequerido, $estadoActual, $estadoNuevo] = $reglas[$accion];
if ($rolIntranet !== $rolRequerido) intranetJson(['error' => 'No tienes permiso para esta etapa.'], 403);
if (in_array($accion, ['rechazar', 'realizar', 'observar'], true) && $comentario === '') intranetJson(['error' => 'Debes ingresar un comentario.'], 400);
if ($accion === 'valorizar' && $valor <= 0) intranetJson(['error' => 'Debes ingresar un valor mayor que cero.'], 400);

$mysql->query('START TRANSACTION');
$sqlItem = $mysql->query("SELECT * FROM intranet_solicitud WHERE token='$token' FOR UPDATE;");
$item = $mysql->f_obj($sqlItem);
if (!$item || $item->estado !== $estadoActual) { $mysql->query('ROLLBACK'); intranetJson(['error' => 'La solicitud cambió de estado. Recarga el listado.'], 409); }

$campos = "estado='$estadoNuevo',fecha_actualizacion='" . date('Y-m-d H:i:s') . "'";
if ($accion === 'valorizar') $campos .= ",valor='$valor',detalle_valorizacion='$comentario'";
if (in_array($accion, ['aprobar', 'rechazar'], true)) $campos .= ",observacion_directiva='$comentario'";
if (in_array($accion, ['iniciar', 'realizar'], true)) $campos .= ",observacion_desarrollo='$comentario'";
if (in_array($accion, ['finalizar', 'observar'], true)) $campos .= ",observacion_final='$comentario'";
$mysql->query("UPDATE intranet_solicitud SET $campos WHERE id_solicitud='$item->id_solicitud';");

$nombre = intranetTextoSql($nombreUsuarioIntranet, 255);
$ahora = date('Y-m-d H:i:s');
$mysql->query("INSERT INTO intranet_solicitud_historial (id_solicitud,id_usuario,usuario_nombre,accion,estado_anterior,estado_nuevo,comentario,fecha) VALUES ('$item->id_solicitud','$idUsuarioIntranet','$nombre','$accion','$estadoActual','$estadoNuevo','$comentario','$ahora');");
$mysql->query('COMMIT');
intranetJson(['ok' => true]);
?>
