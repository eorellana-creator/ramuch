<?php
include('_bootstrap.php');
intranetValidarCsrf();
if ($rolIntranet !== 'directiva') intranetJson(['error' => 'Sólo Directiva puede crear solicitudes.'], 403);

$texto = intranetTextoSql($_POST['texto'] ?? '');
if ($texto === '') intranetJson(['error' => 'Debes escribir la solicitud.'], 400);
$nombre = intranetTextoSql($nombreUsuarioIntranet, 255);
$token = md5(uniqid((string)$idUsuarioIntranet, true));
$ahora = date('Y-m-d H:i:s');

$mysql->query('START TRANSACTION');
$ok = $mysql->query("INSERT INTO intranet_solicitud (token,id_solicitante,solicitante_nombre,texto,estado,fecha_solicitud,fecha_actualizacion) VALUES ('$token','$idUsuarioIntranet','$nombre','$texto','solicitada','$ahora','$ahora');");
if (!$ok) { $mysql->query('ROLLBACK'); intranetJson(['error' => 'No se pudo guardar la solicitud.'], 500); }
$idSolicitud = $mysql->ultimo_id();
$mysql->query("INSERT INTO intranet_solicitud_historial (id_solicitud,id_usuario,usuario_nombre,accion,estado_anterior,estado_nuevo,comentario,fecha) VALUES ('$idSolicitud','$idUsuarioIntranet','$nombre','crear',NULL,'solicitada','$texto','$ahora');");
$mysql->query('COMMIT');
intranetJson(['ok' => true, 'token' => $token]);
?>
