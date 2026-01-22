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

    $medios = array();
    $sql = $mysql->query("SELECT DISTINCT medio FROM cuenta_maestra WHERE medio != '' ORDER BY medio");

    if (!$sql) {
        throw new Exception("Error en consulta: " . $mysql->error);
    }

    while($result = $mysql->f_obj($sql)) {
        $medios[] = $result->medio;
    }

    // Registrar en log para debug
    //error_log("Medios encontrados: " . count($medios));
    
    echo json_encode($medios);
    
} catch (Exception $e) {
    //error_log("Error en obtener_medios.php: " . $e->getMessage());
    echo json_encode(array('error' => $e->getMessage()));
}
?>