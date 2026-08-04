<?php
include('_bootstrap.php');
intranetValidarCsrf();
$token = preg_match('/^[a-f0-9]{32}$/', $_POST['token'] ?? '') ? $_POST['token'] : '';
$texto = intranetTextoSql($_POST['texto'] ?? '');
if ($token === '' || $texto === '') intranetJson(['error' => 'Los datos de la solicitud no son válidos.'], 400);

$mysql->query('START TRANSACTION');
$sql = $mysql->query("SELECT id_solicitud,texto,estado FROM intranet_solicitud WHERE token='$token' FOR UPDATE;");
$item = $mysql->f_obj($sql);
if (!$item || $item->estado !== 'solicitada') {
    $mysql->query('ROLLBACK');
    intranetJson(['error' => 'La solicitud ya no puede editarse.'], 409);
}
$ahora = date('Y-m-d H:i:s');
$mysql->query("UPDATE intranet_solicitud SET texto='$texto',fecha_actualizacion='$ahora' WHERE id_solicitud='$item->id_solicitud';");
$nombre = intranetTextoSql($nombreUsuarioIntranet, 255);
$comentarioHistorial = intranetTextoSql('Texto anterior: ' . $item->texto . "\nNuevo texto: " . stripslashes($texto));
$mysql->query("INSERT INTO intranet_solicitud_historial (id_solicitud,id_usuario,usuario_nombre,accion,estado_anterior,estado_nuevo,comentario,fecha) VALUES ('$item->id_solicitud','$idUsuarioIntranet','$nombre','editar','solicitada','solicitada','$comentarioHistorial','$ahora');");
$mysql->query('COMMIT');
intranetJson(['ok' => true]);
?>
