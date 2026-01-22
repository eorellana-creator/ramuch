<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para JSON
header('Content-Type: application/json');

include("../../../configuration.php");
include("../../../includes/conexionMysql.php");

try {
    $mysql = new mysql;
    $mysql->connect();

    $subcuentas = array();
    $sql = $mysql->query("SELECT DISTINCT sub_cuenta FROM cuenta_maestra WHERE sub_cuenta != '' ORDER BY sub_cuenta");

    if (!$sql) {
        throw new Exception("Error en consulta: " . $mysql->error);
    }

    while($result = $mysql->f_obj($sql)) {
        $subcuentas[] = $result->sub_cuenta;
    }

    // Registrar en log para debug
    //error_log("Subcuentas encontradas: " . count($subcuentas));
    
    echo json_encode($subcuentas);
    
} catch (Exception $e) {
    //error_log("Error en obtener_subcuentas.php: " . $e->getMessage());
    echo json_encode(array('error' => $e->getMessage()));
}
?>