<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Habilitar logs de errores en un archivo
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

$mysql = new mysql;
$mysql->connect();

$sql = $mysql->query("SELECT id_venta, fecha_venta, nombre_usuario, total, orden_flow FROM ventas v, usuario u where v.id_usuario = u.id_usuario ORDER BY fecha_venta DESC");
$ventas = array();

error_log("sql de ventas: " . print_r($sql, true));
error_log("las ventas: " . print_r($ventas, true));

while($result = $mysql->f_obj($sql)) {
    $ventas[] = array(
        'id' => $result->id_venta,
        'fecha' => $result->fecha_venta,
        'comprador' => $result->nombre_usuario,
        'total' => $result->total,
        'orden_flow' => $result->orden_flow
    );
}

header('Content-Type: application/json');
echo json_encode($ventas);

$mysql->close();
?>