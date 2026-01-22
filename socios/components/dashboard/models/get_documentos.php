<?php
header('Content-Type: application/json');
session_start();
include("../../../includes/conexionMysql.php");

$mysql = new mysql;
$mysql->connect();

// Obtener parámetros de filtrado
$filtroComision = isset($_GET['filtro_comision']) ? intval($_GET['filtro_comision']) : 0;
$filtrotipodoc  = isset($_GET['filtro_tipodoc']) ? intval($_GET['filtro_tipodoc']) : 0;
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha_subida DESC';


try {
 $query = "SELECT d.*, c.nombre as nombre_comision 
              FROM documentos_compartidos d
              JOIN comisiones c ON d.id_comision = c.id
              WHERE d.estado = 1";
    
    // Aplicar filtro por comisión si es necesario
    if ($filtroComision > 0) {
        $query .= " AND d.id_comision = $filtroComision";
    }
    
    if ($filtrotipodoc > 0) {
        $query .= " AND d.id_comision = $filtrotipodoc";
    }

    // Aplicar ordenamiento
    $query .= " ORDER BY $orden";
    
    $sql = $mysql->query($query);
    
    $documentos = array();
    while ($row = $mysql->f_obj($sql)) {
        $documentos[] = array(
            'id_documento' => $row->id_documento,
            'nombre_archivo' => $row->nombre_archivo,
            'nombre_guardado' => $row->nombre_guardado,
            'nombre_comision' => $row->nombre_comision,
            'descripcion' => $row->descripcion,
            'tipo_documento' => $row->tipo_documento,
            'fecha_subida' => $row->fecha_subida,
            'token' => $row->token
        );
    }
    
    echo json_encode(array(
        'success' => true,
        'documentos' => $documentos
    ));
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'error' => $e->getMessage()
    ));
}