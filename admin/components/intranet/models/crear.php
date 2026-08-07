<?php
include('_bootstrap.php');
intranetValidarCsrf();
$texto = intranetTextoSql($_POST['texto'] ?? '');
if ($texto === '') intranetJson(['error' => 'Debes escribir la solicitud.'], 400);
$nombre = intranetTextoSql($nombreUsuarioIntranet, 255);
$token = md5(uniqid((string)$idUsuarioIntranet, true));
$ahora = date('Y-m-d H:i:s');

$transaccion = $mysql->query('START TRANSACTION');
if (!$transaccion) intranetJson(['error' => 'No se pudo iniciar el guardado de la solicitud.'], 500);
$ok = $mysql->query("INSERT INTO intranet_solicitud (token,id_solicitante,solicitante_nombre,texto,estado,fecha_solicitud,fecha_actualizacion) VALUES ('$token','$idUsuarioIntranet','$nombre','$texto','solicitada','$ahora','$ahora');");
if (!$ok) {
    $mysql->query('ROLLBACK');
    error_log('Intranet: falló la inserción de una solicitud.');
    intranetJson(['error' => 'No se pudo guardar la solicitud.'], 500);
}
$idSolicitud = $mysql->ultimo_id();
$okHistorial = $mysql->query("INSERT INTO intranet_solicitud_historial (id_solicitud,id_usuario,usuario_nombre,accion,estado_anterior,estado_nuevo,comentario,fecha) VALUES ('$idSolicitud','$idUsuarioIntranet','$nombre','crear',NULL,'solicitada','$texto','$ahora');");
if (!$okHistorial) {
    $mysql->query('ROLLBACK');
    error_log('Intranet: falló el historial inicial; se revirtió la solicitud.');
    intranetJson(['error' => 'No se pudo registrar el historial. La solicitud no fue guardada.'], 500);
}
if (!$mysql->query('COMMIT')) {
    $mysql->query('ROLLBACK');
    error_log('Intranet: falló la confirmación de una solicitud.');
    intranetJson(['error' => 'No se pudo confirmar la solicitud.'], 500);
}
intranetJson(['ok' => true, 'token' => $token]);
?>
