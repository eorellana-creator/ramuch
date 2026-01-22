<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");

header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit;
}

$config = new Config;
$mysql = new mysql;
$mysql->connect();

$productId = intval($_POST['id']);

$sql = $mysql->query("SELECT * FROM productos WHERE id = $productId");
$result = $mysql->f_obj($sql);

if ($result) {
    echo json_encode([
        'success' => true,
        'data' => $result
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Producto no encontrado'
    ]);
}

$mysql->close();
?>