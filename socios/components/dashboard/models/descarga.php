<?php
session_start();
include("../../../includes/conexionMysql.php");

$mysql = new mysql;
$mysql->connect();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Obtener información del documento
    $sql = $mysql->query("SELECT nombre_archivo, nombre_guardado FROM documentos_compartidos WHERE id_documento = $id");
    $doc = $mysql->f_obj($sql);
    
    if (!$doc) {
        throw new Exception("Documento no encontrado");
    }

    $rutaArchivo = '../../../documentos/' . $doc->nombre_guardado;
    
    if (!file_exists($rutaArchivo)) {
        throw new Exception("Archivo no encontrado en el servidor");
    }

    // Configurar headers para forzar descarga
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $doc->nombre_archivo . '"');
    header('Content-Length: ' . filesize($rutaArchivo));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Limpiar buffer de salida y enviar archivo
    ob_clean();
    flush();
    readfile($rutaArchivo);
    exit;
    
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Error al descargar el archivo: " . $e->getMessage();
}