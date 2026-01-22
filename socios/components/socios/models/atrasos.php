<?php
// Depuración - habilita para ver errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// atrasos.php - Backend para obtener préstamos con atraso

// Incluir la clase mysql
include("../../../includes/conexionMysql.php");

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Crear instancia y conectar
    $mysql = new mysql;
    $mysql->connect();
    
    // Obtener el token de la sesión
    $token = @$_SESSION["usuario_token"];
    
    // Verificar que hay un token
    if (!$token) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Usuario no autenticado."
        ]);
        $mysql->close();
        exit();
    }
    
    // Obtener el ID del usuario desde la base de datos usando el token de la sesión
    $sql7 = $mysql->query("SELECT * FROM usuario WHERE token ='$token' AND token!='' ;");
    $result7 = $mysql->f_obj($sql7);
    $id_usuario = @$result7->id_usuario;
    
    // Verificar que se obtuvo un ID de usuario válido
    if (!$id_usuario) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Usuario no autenticado o token inválido."
        ]);
        $mysql->close();
        exit();
    }
    
    // Consulta para obtener préstamos con atraso
    $sql = "SELECT 
                e.nombre as equipo,
                ep.fecha_prestamo,
                ep.fecha_debe_devolver as fecha_compromiso_devolucion,
                ep.fecha_devolucion_efectiva,
                ep.estado,
                ep.estado_devolucion
            FROM equipo_prestamo ep
            INNER JOIN equipo e ON ep.id_equipo = e.id_equipo
            WHERE ep.id_usuario_prestamo = '$id_usuario' 
            AND  ep.estado <> 'rechazado'
            AND (
                (ep.fecha_devolucion_efectiva IS NULL AND ep.fecha_debe_devolver < CURDATE())
                OR 
                (ep.fecha_devolucion_efectiva > ep.fecha_debe_devolver)
            )
            ORDER BY ep.fecha_debe_devolver DESC";
    
    // Ejecutar consulta
    $result = $mysql->query($sql);
    
    $prestamos = [];
    
    while ($row = $mysql->f_obj($result)) {
        // Calcular días de atraso
        if ($row->fecha_devolucion_efectiva) {
            $dias_atraso = max(0, strtotime($row->fecha_devolucion_efectiva) - strtotime($row->fecha_compromiso_devolucion));
            $dias_atraso = floor($dias_atraso / (60 * 60 * 24));
        } else {
            $dias_atraso = max(0, time() - strtotime($row->fecha_compromiso_devolucion));
            $dias_atraso = floor($dias_atraso / (60 * 60 * 24));
        }
        
        // Formatear fechas para mostrar
        $fecha_prestamo = $row->fecha_prestamo > "0000-00-00" 
            ? date("d-m-Y", strtotime($row->fecha_prestamo)) 
            : '';
            
        $fecha_compromiso = $row->fecha_compromiso_devolucion > "0000-00-00" 
            ? date("d-m-Y", strtotime($row->fecha_compromiso_devolucion)) 
            : '';
            
        $fecha_devolucion = $row->fecha_devolucion_efectiva > "0000-00-00" 
            ? date("d-m-Y", strtotime($row->fecha_devolucion_efectiva)) 
            : 'Pendiente';
        
        // Determinar clase CSS según el estado
        $clase_estado = '';
        if ($row->estado_devolucion === 'devuelto') {
            $clase_estado = 'text-success';
        } elseif ($row->estado_devolucion === 'rechazado' || $row->estado === 'rechazado') {
            $clase_estado = 'text-danger';
        } elseif ($row->estado === 'prestado') {
            $clase_estado = 'text-warning';
        }

        $prestamos[] = [
            'equipo' => $row->equipo,
            'fecha_prestamo' => $fecha_prestamo,
            'fecha_compromiso_devolucion' => $fecha_compromiso,
            'fecha_efectiva_devolucion' => $fecha_devolucion,
            'dias_atraso' => $dias_atraso . ' días',
            'estado_devolucion' => $row->estado_devolucion ? ucfirst($row->estado_devolucion) : 'Pendiente',
            'estado_solicitud' => $row->estado ? ucfirst($row->estado) : 'N/A',
            'clase_estado' => $clase_estado
        ];
    }
    
    $mysql->close();
    
    // Devolver respuesta
    echo json_encode([
        "success" => true,
        "data" => $prestamos,
        "message" => count($prestamos) . " préstamos con atraso encontrados",
        "user_id" => $id_usuario // Para depuración
    ]);
    
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido. Use POST."
    ]);
}
?>