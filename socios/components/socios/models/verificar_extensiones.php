<?php
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

session_start();

header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'extensiones_restantes' => 0,
    'mensaje' => 'Error inicial'
];

try {
    // Verificar sesión y token
    $token = trim($_POST['token'] ?? '');
    
    if (empty($token)) {
        throw new Exception('Token no proporcionado');
    }

    if (!isset($_SESSION['usuario_token'])) {
        throw new Exception('Sesión no válida');
    }

    // Conexión a DB
    $mysql = new mysql;
    $mysql->connect();

    $tokenUsuario = str_replace("'", "''", $_SESSION['usuario_token']);

    // Consultar únicamente préstamos pertenecientes al usuario autenticado.
    $sql = "SELECT ep.extensiones_solicitadas, ep.estado
            FROM equipo_prestamo ep
            INNER JOIN usuario u ON u.id_usuario = ep.id_usuario_prestamo
            WHERE ep.token = '$token' AND u.token = '$tokenUsuario'
            LIMIT 1";
    $result = $mysql->query($sql);
    
    if (!$result) {
        throw new Exception('Error en consulta SQL');
    }

    $prestamo = $mysql->f_obj($result);
    
    if (!$prestamo) {
        throw new Exception('Préstamo no encontrado');
    }

    if ($prestamo->estado !== 'prestado') {
        throw new Exception('El préstamo no está en estado válido');
    }

    // Preparar respuesta exitosa
    $response = [
        'success' => true,
        'extensiones_restantes' => max(0, 2 - (int)$prestamo->extensiones_solicitadas),
        'mensaje' => 'OK'
    ];

} catch (Exception $e) {
    $response['mensaje'] = $e->getMessage();
    error_log("Error en verificar_extensiones: " . $e->getMessage());
}

// Asegurar que la salida sea solo el JSON
ob_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>
