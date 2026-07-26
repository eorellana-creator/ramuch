<?php
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

header('Content-Type: application/json');

// DEBUG: Log de todos los datos recibidos
error_log("=== DEBUG PROCESAR_EXTENSION ===");
error_log("POST data: " . print_r($_POST, true));
error_log("Token: " . ($_POST['token'] ?? 'NO_RECIBIDO'));
error_log("Acción: " . ($_POST['accion'] ?? 'NO_RECIBIDO'));
error_log("Motivo: " . ($_POST['motivo'] ?? 'NO_RECIBIDO'));
error_log("Fecha extension: " . ($_POST['fecha_extension'] ?? 'NO_RECIBIDO'));
error_log("Aplicar a todos: " . ($_POST['aplicar_a_todos'] ?? 'NO_RECIBIDO'));

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

// Obtener fecha de extensión si se aprobó
$fecha_extension = $_POST['fecha_extension'] ?? '';

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

// Validación para aprobación: requiere fecha
if ($accion === 'aprobar' && empty($fecha_extension)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Debe seleccionar una fecha para la aprobación']);
    exit;
}

if ($accion === 'aprobar') {
    $fechaObjeto = DateTime::createFromFormat('!Y-m-d', $fecha_extension);
    if (!$fechaObjeto || $fechaObjeto->format('Y-m-d') !== $fecha_extension) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'La fecha de extensión no es válida']);
        exit;
    }
}

try {
    $mysql = new mysql;
    $mysql->connect();
    
    // Iniciar transacción para múltiples operaciones
    $mysql->query("START TRANSACTION");
    
    // 1. Verificar que existe la solicitud y determinar qué extensión está pendiente
    $sql = "SELECT ep.*, e.token as equipo_token, e.fecha_devolucion,
                   ep.estado_extension, ep.estado_extension2,
                   ep.fecha_propuesta_extension, ep.fecha_propuesta_extension2
            FROM equipo_prestamo ep
            JOIN equipo e ON ep.id_equipo = e.id_equipo
            WHERE ep.token = '$token' AND ep.estado = 'prestado'";
    
    $result = $mysql->query($sql);
    
    if ($mysql->f_num($result) === 0) {
        throw new Exception("Solicitud no encontrada");
    }
    
    $prestamo = $mysql->f_obj($result);
    $equipo_token = $prestamo->equipo_token;
    $id_usuario = $prestamo->id_usuario_prestamo;
    
    // DETERMINAR QUÉ EXTENSIÓN ESTÁ PENDIENTE
    $esSegundaExtension = false;
    $campo_estado = '';
    $campo_fecha_propuesta = '';
    $campo_motivo = '';
    $campo_fecha_procesado = '';
    $campo_procesado_por = '';
    
    if (!empty($prestamo->estado_extension2) && $prestamo->estado_extension2 == 'pendiente') {
        // Segunda extensión pendiente
        $esSegundaExtension = true;
        $campo_estado = 'estado_extension2';
        $campo_fecha_propuesta = 'fecha_propuesta_extension2';
        $campo_motivo = 'motivo_extension2';
        $campo_fecha_procesado = 'fecha_procesado_extension2';
        $campo_procesado_por = 'procesado_por_extension2';
    } else if (!empty($prestamo->estado_extension) && $prestamo->estado_extension == 'pendiente') {
        // Primera extensión pendiente
        $esSegundaExtension = false;
        $campo_estado = 'estado_extension';
        $campo_fecha_propuesta = 'fecha_propuesta_extension';
        $campo_motivo = 'motivo_extension';
        $campo_fecha_procesado = 'fecha_procesado_extension';
        $campo_procesado_por = 'procesado_por_extension';
    } else {
        throw new Exception("No hay extensiones pendientes para procesar");
    }
    
    // 2. Procesar según acción
    if ($accion === 'aprobar') {
        // Usar la fecha proporcionada en el modal
        $nuevaFecha = $fecha_extension;
        if ($nuevaFecha <= $prestamo->fecha_debe_devolver) {
            throw new Exception("La nueva fecha debe ser posterior a la fecha vigente de devolución");
        }
        
        $update = "UPDATE equipo_prestamo SET
                  $campo_estado = 'aprobada',
                  $campo_fecha_propuesta = '$nuevaFecha',
                  fecha_debe_devolver = '$nuevaFecha',
                  $campo_motivo = '$motivo',
                  $campo_fecha_procesado = NOW(),
                  $campo_procesado_por = '{$_SESSION['usuario_id']}'
                  WHERE token = '$token'";
        
        // Si es aprobación, actualizar también la fecha de devolución del equipo
        $update_equipo = "UPDATE equipo SET 
                         fecha_devolucion = '$nuevaFecha' 
                         WHERE token = '$equipo_token'";
        
        if (!$mysql->query($update_equipo)) {
            throw new Exception("Error al actualizar la fecha de devolución del equipo");
        }
        
    } else {
        // Rechazar
        $update = "UPDATE equipo_prestamo SET
                  $campo_estado = 'rechazada',
                  $campo_motivo = '$motivo',
                  $campo_fecha_procesado = NOW(),
                  $campo_procesado_por = '{$_SESSION['usuario_id']}'
                  WHERE token = '$token'";
    }
    
    // ELIMINAR ESTA LÍNEA DUPLICADA:
    // $update .= " WHERE token = '$token'";
    
    if (!$mysql->query($update)) {
        throw new Exception("Error al actualizar el préstamo");
    }
    
    // 3. Si es aplicar a todos, procesar otros equipos del usuario
    if ($aplicarATodos) {
        // Determinar qué campo de estado buscar en otros equipos
        $campo_buscar_estado = $esSegundaExtension ? 'estado_extension2' : 'estado_extension';
        $estado_pendiente = 'pendiente';
        
        $sqlOtrosEquipos = "SELECT token, fecha_debe_devolver FROM equipo_prestamo
                           WHERE id_usuario_prestamo = '$id_usuario' 
                           AND estado = 'prestado'
                           AND $campo_buscar_estado = '$estado_pendiente'
                           AND token != '$token'";
        $resultEquipos = $mysql->query($sqlOtrosEquipos);
        
        while ($equipo = $mysql->f_obj($resultEquipos)) {
            if ($accion === 'aprobar') {
                if ($nuevaFecha <= $equipo->fecha_debe_devolver) {
                    throw new Exception("La nueva fecha no es posterior a la fecha vigente de todos los préstamos seleccionados");
                }

                $updateTodos = "UPDATE equipo_prestamo SET
                               $campo_estado = 'aprobada',
                               $campo_fecha_propuesta = '$nuevaFecha',
                               fecha_debe_devolver = '$nuevaFecha',
                               $campo_motivo = '$motivo',
                               $campo_fecha_procesado = NOW(),
                               $campo_procesado_por = '{$_SESSION['usuario_id']}'
                               WHERE token = '{$equipo->token}'";
                
                // Actualizar también la fecha de devolución del equipo
                $mysql->query("UPDATE equipo e 
                              INNER JOIN equipo_prestamo ep ON e.id_equipo = ep.id_equipo 
                              SET e.fecha_devolucion = '$nuevaFecha' 
                              WHERE ep.token = '{$equipo->token}'");
            } else {
                $updateTodos = "UPDATE equipo_prestamo SET
                               $campo_estado = 'rechazada',
                               $campo_motivo = '$motivo',
                               $campo_fecha_procesado = NOW(),
                               $campo_procesado_por = '{$_SESSION['usuario_id']}'
                               WHERE token = '{$equipo->token}'";
            }
            
            if (!$mysql->query($updateTodos)) {
                throw new Exception("Error al actualizar equipos adicionales");
            }
        }
    }
    
    // Todo OK, confirmar transacción
    $mysql->query("COMMIT");
    
    echo json_encode([
        'success' => true,
        'message' => "Extensión " . ($accion === 'aprobar' ? 'aprobada' : 'rechazada') . " correctamente",
        'nueva_fecha' => ($accion === 'aprobar') ? $nuevaFecha : null,
        'aplicado_a_todos' => $aplicarATodos,
        'es_segunda_extension' => $esSegundaExtension
    ]);
    
} catch (Exception $e) {
    $mysql->query("ROLLBACK");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
