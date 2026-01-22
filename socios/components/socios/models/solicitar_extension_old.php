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

    // Validar datos de entrada
    $token = trim($_POST['token'] ?? '');
    $nuevaFecha = trim($_POST['nueva_fecha'] ?? '');
    $motivo = trim($_POST['motivo'] ?? '');

    if (empty($token) || empty($nuevaFecha) || empty($motivo)) {
        throw new Exception('Todos los campos son requeridos');
    }

    // Validar fecha
    $hoy = date('Y-m-d');
    if ($nuevaFecha <= $hoy) {
        throw new Exception('La fecha debe ser futura');
    }

    // Conexión a DB
    $mysql = new mysql;
    $mysql->connect();

    // 1. Verificar préstamo existente
    $sql = "SELECT id_equipo_prestamo, extensiones_solicitadas 
            FROM equipo_prestamo 
            WHERE token = '$token' AND estado = 'prestado'";
    $result = $mysql->query($sql);
    
    if (!$result) {
        throw new Exception('Error al verificar préstamo');
    }

    $prestamo = $mysql->f_obj($result);
    
    if (!$prestamo) {
        throw new Exception('Préstamo no encontrado o no elegible para extensión');
    }

    // 2. Verificar límite de extensiones
    if ($prestamo->extensiones_solicitadas >= 2) {
        throw new Exception('Ya alcanzó el límite de 2 extensiones');
    }

    // Escapar el motivo manualmente
    $motivoSeguro = str_replace("'", "''", $motivo);
    
    // 3. Actualizar registro
    $nuevoContador = $prestamo->extensiones_solicitadas + 1;
    $sqlUpdate = "UPDATE equipo_prestamo SET
                  fecha_solicitud_extension = NOW(),
                  fecha_propuesta_extension = '$nuevaFecha',
                  motivo_extension = '$motivoSeguro',
                  estado_extension = 'pendiente',
                  extensiones_solicitadas = $nuevoContador
                  WHERE token = '$token'";

    // Ejecutar la consulta
    $updateResult = $mysql->query($sqlUpdate);
    
    if (!$updateResult) {
        throw new Exception('Error al actualizar el préstamo');
    }

    // Asumir que la actualización fue exitosa si no hubo error
    // (ya que tu clase mysql no tiene affected_rows)
    $response = [
        'success' => true,
        'mensaje' => 'Extensión solicitada correctamente',
        'extensiones_restantes' => 2 - $nuevoContador
    ];

} catch (Exception $e) {
    $response['mensaje'] = $e->getMessage();
    error_log("Error en solicitar_extension: " . $e->getMessage());
}

// Limpiar y enviar respuesta
ob_end_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>