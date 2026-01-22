<?php
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

header('Content-Type: application/json');

// Validar sesión y permisos
session_start();
if (!isset($_SESSION['usuario_token'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

// Validar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Recibir y sanitizar datos
//$token = $mysql->real_escape_string($_POST['token'] ?? '');
//$accion = in_array($_POST['accion'] ?? '', ['aprobar', 'rechazar']) ? $_POST['accion'] : '';
//$motivo = $mysql->real_escape_string($_POST['motivo'] ?? '');
//$aplicarATodos = isset($_POST['aplicar_a_todos']) && $_POST['aplicar_a_todos'] == '1';

// Validar token (ejemplo: alfanumérico y longitud fija)
$token = $_POST['token'] ?? '';
if (!preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
    die("Token inválido");
}

// Validar acción (solo permite 'aprobar' o 'rechazar')
$accion = in_array($_POST['accion'] ?? '', ['aprobar', 'rechazar']) ? $_POST['accion'] : '';

// Sanitizar motivo (elimina tags HTML y limita longitud)
$motivo = substr(strip_tags($_POST['motivo'] ?? ''), 0, 255);

// Validar checkbox booleano
$aplicarATodos = isset($_POST['aplicar_a_todos']) && $_POST['aplicar_a_todos'] == '1';

// Validaciones básicas
if (empty($token) || empty($accion)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

// Validación adicional para rechazo
if ($accion === 'rechazar' && empty($motivo)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Debe ingresar un motivo para el rechazo']);
    exit;
}

try {
    $mysql = new mysql;
    $mysql->connect();
    
    // Iniciar transacción para múltiples operaciones
    $mysql->query("START TRANSACTION");
    
    // 1. Verificar que existe la solicitud
    $sql = "SELECT ep.*, e.token as equipo_token 
            FROM equipo_prestamo ep
            JOIN equipo e ON ep.id_equipo = e.id_equipo
            WHERE ep.token = '$token' AND ep.estado_extension = 'pendiente'";
    $result = $mysql->query($sql);
    
    if ($mysql->f_num($result) === 0) {
        throw new Exception("Solicitud no encontrada o ya procesada");
    }
    
    $prestamo = $mysql->f_obj($result);
    $equipo_token = $prestamo->equipo_token;
    $id_usuario = $prestamo->id_usuario;
    
    // 2. Procesar según acción
    if ($accion === 'aprobar') {
        // Calcular nueva fecha (usar la propuesta o 1 semana por defecto)
        $nuevaFecha = !empty($prestamo->fecha_propuesta_extension) 
            ? $prestamo->fecha_propuesta_extension 
            : date('Y-m-d', strtotime('+1 week'));
        
        $update = "UPDATE equipo_prestamo SET
                  estado_extension = 'aprobada',
                  fecha_debe_devolver = '$nuevaFecha',
                  motivo_extension = '$motivo',
                  fecha_procesado_extension = NOW(),
                  procesado_por_extension = '{$_SESSION['usuario_id']}'
                  WHERE token = '$token'";
    } else {
        // Rechazar
        $update = "UPDATE equipo_prestamo SET
                  estado_extension = 'rechazada',
                  motivo_extension = '$motivo',
                  fecha_procesado_extension = NOW(),
                  procesado_por_extension = '{$_SESSION['usuario_id']}'
                  WHERE token = '$token'";
    }
    
    if (!$mysql->query($update)) {
        throw new Exception("Error al actualizar el préstamo");
    }
    
    // 3. Si es aplicar a todos, procesar otros equipos del usuario
    if ($aplicarATodos) {
        $sqlOtrosEquipos = "SELECT token FROM equipo_prestamo 
                           WHERE id_usuario = '$id_usuario' 
                           AND estado_extension = 'pendiente'
                           AND token != '$token'";
        $resultEquipos = $mysql->query($sqlOtrosEquipos);
        
        while ($equipo = $mysql->f_obj($resultEquipos)) {
            $updateTodos = "UPDATE equipo_prestamo SET
                           estado_extension = '{$accion}da',
                           motivo_extension = '$motivo',
                           fecha_procesado_extension = NOW(),
                           procesado_por_extension = '{$_SESSION['usuario_id']}'";
            
            if ($accion === 'aprobar') {
                $updateTodos .= ", fecha_debe_devolver = '$nuevaFecha'";
            }
            
            $updateTodos .= " WHERE token = '{$equipo->token}'";
            
            if (!$mysql->query($updateTodos)) {
                throw new Exception("Error al actualizar equipos adicionales");
            }
        }
    }
    
    // 4. Actualizar estado del equipo si es necesario
    //if ($accion === 'rechazar') {
    //    $updateEquipo = "UPDATE equipo SET 
    //                    estado = 'Prestado' 
    //                    WHERE token = '$equipo_token'";
    //    $mysql->query($updateEquipo);
    //}
    
    // Todo OK, confirmar transacción
    $mysql->query("COMMIT");
    
    echo json_encode([
        'success' => true,
        'message' => "Extensión {$accion}da correctamente",
        'nueva_fecha' => ($accion === 'aprobar') ? $nuevaFecha : null,
        'aplicado_a_todos' => $aplicarATodos
    ]);
    
} catch (Exception $e) {
    $mysql->query("ROLLBACK");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}