<?php
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

error_log("Solicitud recibida con estos parámetros: " . print_r($_GET, true));

$token = $_GET['token'] ?? '';
if(empty($token)) {
    error_log("Error: Token no recibido");
    echo json_encode(['success' => false, 'error' => 'Token no proporcionado']);
    exit;
}

// Validar token recibido
$token = $_GET['token'] ?? '';
$debug = $_GET['debug'] ?? false;

if (empty($token)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Token no proporcionado',
        'debug' => $debug ? ['received_data' => $_GET] : null
    ]);
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
    
    error_log("Consulta SQL ejecutada: ".$sql); // Log para depuración
    
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
        
        error_log("Mostrando datos de SEGUNDA extensión para token: $token");
        
    } 
    // Si no hay segunda extensión pendiente, verificar la primera
    else if (!empty($data->estado_extension) && $data->estado_extension == 'pendiente') {
        $esSegundaExtension = false;
        $fecha_propuesta = $data->fecha_propuesta_extension;
        $fecha_propuesta_formateada = fecha_mysql_a_normal($data->fecha_propuesta_extension);
        $estado = $data->estado_extension;
        
        error_log("Mostrando datos de PRIMERA extensión para token: $token");
    }
    // Si ninguna está pendiente, usar la primera por defecto (para compatibilidad)
    else {
        $esSegundaExtension = false;
        $fecha_propuesta = $data->fecha_propuesta_extension;
        $fecha_propuesta_formateada = fecha_mysql_a_normal($data->fecha_propuesta_extension);
        $estado = $data->estado_extension;
        
        error_log("Mostrando datos de PRIMERA extensión (por defecto) para token: $token");
    }
    
    $response = [
        'success' => true,
        'fecha_propuesta' => $fecha_propuesta,
        'fecha_propuesta_formateada' => $fecha_propuesta_formateada,
        'estado' => $estado,
        'es_segunda_extension' => $esSegundaExtension,
        'motivo_extension' => $motivo_extension
    ];
    
    // Agregar datos extra si está en modo depuración
    if ($debug) {
        $response['debug'] = [
            'query' => $sql,
            'token_received' => $token,
            'full_record' => $data,
            'extension_detected' => $esSegundaExtension ? 'segunda' : 'primera'
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error en obtener_datos_extension.php: ".$e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => $debug ? ['exception' => $e->getTraceAsString()] : null
    ]);
}