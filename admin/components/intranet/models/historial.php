<?php
include('_bootstrap.php');
$token = preg_match('/^[a-f0-9]{32}$/', $_GET['token'] ?? '') ? $_GET['token'] : '';
$sqlItem = $mysql->query("SELECT id_solicitud,texto FROM intranet_solicitud WHERE token='$token' LIMIT 1;");
$item = $mysql->f_obj($sqlItem);
if (!$item) { http_response_code(404); exit('<div class="alert alert-danger">Solicitud no encontrada.</div>'); }

echo '<div class="alert alert-light"><strong>Solicitud:</strong><br>' . nl2br(htmlspecialchars($item->texto, ENT_QUOTES, 'UTF-8')) . '</div>';
echo '<div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Cambio</th><th>Comentario</th></tr></thead><tbody>';
$sql = $mysql->query("SELECT * FROM intranet_solicitud_historial WHERE id_solicitud='$item->id_solicitud' ORDER BY id_historial DESC;");
while ($fila = $mysql->f_obj($sql)) {
    echo '<tr><td>' . date('d-m-Y H:i', strtotime($fila->fecha)) . '</td><td>' . htmlspecialchars($fila->usuario_nombre, ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars($fila->accion, ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars(($fila->estado_anterior ?: '-') . ' → ' . $fila->estado_nuevo, ENT_QUOTES, 'UTF-8') . '</td><td>' . nl2br(htmlspecialchars($fila->comentario ?? '', ENT_QUOTES, 'UTF-8')) . '</td></tr>';
}
echo '</tbody></table></div>';
?>
