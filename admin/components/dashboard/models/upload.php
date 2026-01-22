<?php
header('Content-Type: application/json');
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];
$nombre_usuario_sistema = $_SESSION["usuario_nombre"];

date_default_timezone_set("$config->zona_horaria");

$mysql 	= new mysql;
$mysql->connect();

// Recibir los datos del formulario
$id_comision = $_POST['id_comision'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$tipo_documento = $_POST['tipo_documento'] ?? null;
$archivo = $_FILES['archivo'] ?? null;

// Configuración
$directorioDestino = '../../../documentos/';
$urlBase = 'https://www.ramuch.cl/admin/documentos/';
$extensionesPermitidas = ['pdf', 'ppt', 'jpg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
$tamanoMaximo = 5 * 1024 * 1024; // 5MB

try {
    // Validar datos requeridos
    if (!$id_comision || !$descripcion || !$archivo) {
        throw new Exception('Todos los campos son requeridos');
    }

    // Verificar archivo
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se recibió el archivo o hubo un error en la subida');
    }

    $archivo = $_FILES['archivo'];

    // Validaciones
    if ($archivo['size'] > $tamanoMaximo) {
        throw new Exception('El archivo excede el tamaño máximo de 5MB');
    }

    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $extensionesPermitidas)) {
        throw new Exception('Tipo de archivo no permitido');
    }

    // Generar nombres y token
    $nombreArchivo = uniqid() . '.' . $extension;
    $token = md5(uniqid(rand(), true));
    $nombreOriginal = basename($archivo['name']);
    $rutaCompleta = $directorioDestino . $nombreArchivo;

    // Mover archivo
    if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        throw new Exception('Error al guardar el archivo');
    }

    if ($id_usuario == 0) {
        throw new Exception('Usuario no identificado');
    }

    // En la parte donde procesas el formulario, antes de la inserción:
    $id_tipo = (int)$_POST['tipo_documento'];
    $sql_tipo = $mysql->query("SELECT id_tipo, nombre FROM tipo_documento WHERE id_tipo = $id_tipo");
    $tipo_data = $mysql->f_obj($sql_tipo);

    if (!$tipo_data) {
        throw new Exception('Tipo de documento no válido');
    }

    $nombre_tipo = $tipo_data->nombre;

    // Insertar directamente con query (sin prepared statements)
    $sql = "INSERT INTO documentos_compartidos (
                id_usuario, 
                nombre_archivo, 
                nombre_guardado, 
                id_comision, 
                token, 
                descripcion, 
                tamaño, 
                extension,
                tipo_documento,
                id_tipo_documento
              ) VALUES (
                '$id_usuario', 
                '$nombreOriginal', 
                '$nombreArchivo', 
                '$id_comision', 
                '$token', 
                '$descripcion', 
                '{$archivo['size']}', 
                '$extension',
                '$nombre_tipo',
                '$id_tipo'
              )";

    $result = $mysql->query($sql);
    
    if (!$result) {
        unlink($rutaCompleta);
        throw new Exception('Error al guardar en BD: ' . $mysql->error);
    }

    // Respuesta
    echo json_encode([
        'success' => true,
        'message' => 'Archivo subido correctamente',
        'file_url' => $urlBase . $nombreArchivo,
        'file_name' => $nombreOriginal,
        'documento_id' => $stmt->insert_id,
        'token' => $token
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}