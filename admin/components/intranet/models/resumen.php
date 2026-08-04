<?php
include('_bootstrap.php');
$resumen = ['solicitada' => 0, 'valorizada' => 0, 'desarrollo' => 0, 'finalizada' => 0];
$sql = $mysql->query("SELECT estado,COUNT(*) cantidad FROM intranet_solicitud GROUP BY estado;");
while ($fila = $mysql->f_obj($sql)) {
    if ($fila->estado === 'solicitada') $resumen['solicitada'] += (int)$fila->cantidad;
    if ($fila->estado === 'valorizada') $resumen['valorizada'] += (int)$fila->cantidad;
    if (in_array($fila->estado, ['aprobada','en_desarrollo','realizada'], true)) $resumen['desarrollo'] += (int)$fila->cantidad;
    if ($fila->estado === 'finalizada') $resumen['finalizada'] += (int)$fila->cantidad;
}
intranetJson($resumen);
?>
