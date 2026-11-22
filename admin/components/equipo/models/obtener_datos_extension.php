<?php
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

header('Content-Type: application/json');

$token = $_GET['token'] ?? '';
if(empty($token)) {
    error_log("Error: Token no recibido");
    echo json_encode(['success' => false, 'error' => 'Token no proporcionado']);
    exit;
}

try {
    $mysql = new mysql;
    $mysql->connect();
    
    // CONSULTA MODIFICADA: Obtener todos los campos de extensión
    $sql = "SELECT 
                fecha_propuesta_extension,
                estado_extension,
                fecha_propuesta_extension2,
                estado_extension2,
                motivo_extension2,
                id_equipo,
                id_usuario_prestamo
            FROM equipo_prestamo 
            WHERE token = '$token' 
            LIMIT 1";
    
    $result = $mysql->query($sql);
    
    if ($mysql->f_num($result) === 0) {
        throw new Exception("Solicitud de extensión no encontrada para el token: ".$token);
    }
    
    $data = $mysql->f_obj($result);
    
    // LÓGICA PARA DETERMINAR QUÉ EXTENSIÓN MOSTRAR
    $esSegundaExtension = false;
    $fecha_propuesta = '';
    $fecha_propuesta_formateada = '';
    $estado = '';
    $motivo_extension = '';
    
    // Primero verificar si la segunda extensión está pendiente
    if (!empty($data->estado_extension2) && $data->estado_extension2 == 'pendiente') {
        $esSegundaExtension = true;
        $fecha_propuesta = $data->fecha_propuesta_extension2;
        $fecha_propuesta_formateada = fecha_mysql_a_normal($data->fecha_propuesta_extension2);
        $estado = $data->estado_extension2;
        $motivo_extension = $data->motivo_extension2 ?? '';
        
    } 
    // Si no hay segunda extensión pendiente, verificar la primera
    else if (!empty($data->estado_extension) && $data->estado_extension == 'pendiente') {
        $esSegundaExtension = false;
        $fecha_propuesta = $data->fecha_propuesta_extension;
        $fecha_propuesta_formateada = fecha_mysql_a_normal($data->fecha_propuesta_extension);
        $estado = $data->estado_extension;
        
    }
    // Si ninguna está pendiente, usar la primera por defecto (para compatibilidad)
    else {
        $esSegundaExtension = false;
        $fecha_propuesta = $data->fecha_propuesta_extension;
        $fecha_propuesta_formateada = fecha_mysql_a_normal($data->fecha_propuesta_extension);
        $estado = $data->estado_extension;
        
    }
    
    $response = [
        'success' => true,
        'fecha_propuesta' => $fecha_propuesta,
        'fecha_propuesta_formateada' => $fecha_propuesta_formateada,
        'estado' => $estado,
        'es_segunda_extension' => $esSegundaExtension,
        'motivo_extension' => $motivo_extension
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error en obtener_datos_extension.php: ".$e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
