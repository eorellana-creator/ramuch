<?php
include("../../../includes/conexionMysql.php");

$mysql = new mysql;
$mysql->connect();

$sql = $mysql->query("SELECT id, nombre FROM categorias ORDER BY nombre");
$categorias = [];

while ($row = $mysql->f_array($sql)) {
    $categorias[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre']
    ];
}

echo json_encode($categorias);

$mysql->close();
?>