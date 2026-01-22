<?php
header('Content-Type: application/json');
session_start();
include("../../../includes/conexionMysql.php");

$mysql = new mysql;
$mysql->connect();

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$token = isset($_POST['token']) ? $_POST['token'] : '';

try {
    if ($id <= 0 || empty($token)) {
        throw new Exception('Datos incompletos para la eliminación');
    }

    // Primero obtener información del documento
    $sql = $mysql->query("SELECT nombre_guardado FROM documentos_compartidos 
                         WHERE id_documento = $id AND token = '$token'");
    
    $documento = $mysql->f_obj($sql);
    
    // Verificar si se encontró el documento
    if (!$documento) {
        throw new Exception('Documento no encontrado o token inválido');
    }
    
    $rutaArchivo = '../../../documentos/' . $documento->nombre_guardado;
    
    // Actualizar estado en BD (borrado lógico)
    $updateResult = $mysql->query("UPDATE documentos_compartidos SET estado = 0 
                                 WHERE id_documento = $id AND token = '$token'");
    
    // Verificar si la actualización fue exitosa
    if (!$updateResult) {
        throw new Exception('Error al actualizar el estado del documento');
    }
    
    // Borrar el archivo físico
    if (file_exists($rutaArchivo)) {
        if (!unlink($rutaArchivo)) {
            throw new Exception('Error al eliminar el archivo físico');
        }
    }
    
    echo json_encode(array(
        'success' => true,
        'message' => 'Documento eliminado correctamente'
    ));
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'success' => false,
        'error' => $e->getMessage()
    ));
}