<?php
include('_bootstrap.php');
$token = preg_match('/^[a-f0-9]{32}$/', $_GET['token'] ?? '') ? $_GET['token'] : '';
$sql = $mysql->query("SELECT token,texto,estado FROM intranet_solicitud WHERE token='$token' LIMIT 1;");
$item = $mysql->f_obj($sql);
if (!$item) intranetJson(['error' => 'Solicitud no encontrada.'], 404);
if ($item->estado !== 'solicitada') intranetJson(['error' => 'Sólo se puede editar una solicitud que aún no ha sido valorizada.'], 409);
intranetJson(['token' => $item->token, 'texto' => $item->texto, 'estado' => $item->estado]);
?>
