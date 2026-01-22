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
    
    // Consulta ampliada para depuración
    $sql = "SELECT 
                fecha_propuesta_extension,
                estado_extension,
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
    
    $response = [
        'success' => true,
        'fecha_propuesta' => $data->fecha_propuesta_extension,
        'fecha_propuesta_formateada' => fecha_mysql_a_normal($data->fecha_propuesta_extension),
        'estado' => $data->estado_extension
    ];
    
    // Agregar datos extra si está en modo depuración
    if ($debug) {
        $response['debug'] = [
            'query' => $sql,
            'token_received' => $token,
            'full_record' => $data
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