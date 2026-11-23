<?php
include('_bootstrap.php');
$resumen = [
    'solicitado' => 0,
    'valorado' => 0,
    'aprobado' => 0,
    'pagado' => 0,
    'finalizado' => 0,
    'descartado' => 0
];
$sql = $mysql->query("SELECT estado,COUNT(*) cantidad FROM intranet_solicitud GROUP BY estado;");
while ($fila = $mysql->f_obj($sql)) {
    if (array_key_exists($fila->estado, $resumen)) {
        $resumen[$fila->estado] = (int)$fila->cantidad;
    }
}
intranetJson($resumen);
?>
