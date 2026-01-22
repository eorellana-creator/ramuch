<?php
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

header('Content-Type: application/json');

// Consultar estado actual

$config = new Config;
$mysql = new mysql;
$mysql->connect();

$sql = $mysql->query("SELECT valor FROM valores WHERE id = 1");
if ($mysql->f_num($sql) > 0) {
    $row = $mysql->f_array($sql);
    $estado_actual = $row['valor'];
    $nuevo_estado = $estado_actual == 1 ? 0 : 1;
    
    // Actualizar estado
    $update = $mysql->query("UPDATE valores SET valor = $nuevo_estado WHERE id = 1");
    
    if ($update) {
        echo json_encode([
            'success' => true,
            'nuevo_estado' => $nuevo_estado,
            'message' => 'Estado actualizado correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar en la base de datos'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se encontró el registro de configuración'
    ]);
}
?>