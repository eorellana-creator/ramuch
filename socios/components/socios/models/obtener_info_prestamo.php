<?php
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

session_start();

// Limpiar buffer de salida
ob_clean();

header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'mensaje' => 'Error inicial'
];

try {
    // Verificar sesión
    if (!isset($_SESSION['usuario_token'])) {
        throw new Exception('Sesión no válida');
    }

    // Validar token
    $token = trim($_POST['token'] ?? '');
    
    if (empty($token)) {
        throw new Exception('Token no válido');
    }

    // Conexión a DB
    $mysql = new mysql;
    $mysql->connect();
    $tokenUsuario = str_replace("'", "''", $_SESSION['usuario_token']);

    // Obtener información del préstamo
    $sql = "SELECT ep.*, e.nombre as nombre_equipo
            FROM equipo_prestamo ep
            LEFT JOIN equipo e ON ep.id_equipo = e.id_equipo
            INNER JOIN usuario u ON u.id_usuario = ep.id_usuario_prestamo
            WHERE ep.token = '$token'
            AND u.token = '$tokenUsuario'
            AND ep.estado = 'prestado'
            LIMIT 1";
    
    $result = $mysql->query($sql);
    
    if (!$result) {
        throw new Exception('Error al consultar la base de datos');
    }

    $prestamo = $mysql->f_obj($result);
    
    if (!$prestamo) {
        throw new Exception('Préstamo no encontrado');
    }

    $data = [
        'fecha_primera_extension' => $prestamo->fecha_propuesta_extension ?? null,
        'nombre_equipo' => $prestamo->nombre_equipo ?? 'Equipo no encontrado',
        'fecha_debe_devolver' => $prestamo->fecha_debe_devolver ?? null,
        'estado_extension' => $prestamo->estado_extension ?? null,
        'estado_extension2' => $prestamo->estado_extension2 ?? null,
        'fecha_propuesta_extension' => $prestamo->fecha_propuesta_extension ?? null
    ];

    $response = [
        'success' => true,
        'data' => $data
    ];

} catch (Exception $e) {
    $response['mensaje'] = $e->getMessage();
    error_log("Error en obtener_info_prestamo: " . $e->getMessage());
}

// Limpiar y enviar respuesta
ob_end_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>
