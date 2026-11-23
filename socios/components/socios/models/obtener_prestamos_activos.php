<?php
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

session_start();

header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'mensaje' => 'Error inicial',
    'data' => []
];
try {
    // Verificar sesión
    if (!isset($_SESSION['usuario_token'])) {
        throw new Exception('Sesión no válida');
    }

    $token = trim($_SESSION['usuario_token']);
    
    // Conexión a DB
    $mysql = new mysql;
    $mysql->connect();

    // Obtener ID del usuario
    $sqlUsuario = $mysql->query("SELECT id_usuario FROM usuario WHERE token = '$token'");
    $usuario = $mysql->f_obj($sqlUsuario);
    
    if (!$usuario) {
        throw new Exception('Usuario no encontrado');
    }

    $id_usuario = $usuario->id_usuario;
    
    // Obtener todos los préstamos activos del usuario
    $sql = "SELECT 
                ep.token,
                ep.fecha_prestamo,
                ep.fecha_debe_devolver,
                ep.extensiones_solicitadas,
                ep.estado_extension,
                ep.estado_extension2,
                ep.fecha_propuesta_extension,
                ep.fecha_propuesta_extension2,
                e.nombre as nombre_equipo,
                e.imagen
            FROM equipo_prestamo ep
            INNER JOIN equipo e ON ep.id_equipo = e.id_equipo
            WHERE ep.id_usuario_prestamo = '$id_usuario' 
            AND ep.estado = 'prestado'
            ORDER BY ep.fecha_prestamo DESC";

    $result = $mysql->query($sql);
    
    if (!$result) {
        throw new Exception('Error al obtener préstamos activos: ' . $mysql->error);
    }

    $prestamos = [];
    $contador = 0;
    
    while ($prestamo = $mysql->f_obj($result)) {
        $extensionesUtilizadas = 0;
        if (in_array((string)$prestamo->estado_extension, ['pendiente', 'aprobada'], true)) {
            $extensionesUtilizadas++;
        }
        if (in_array((string)$prestamo->estado_extension2, ['pendiente', 'aprobada'], true)) {
            $extensionesUtilizadas++;
        }

        // Reconciliar contadores históricos que hayan incluido solicitudes
        // rechazadas. El token proviene de la propia consulta a la base.
        if ((int)$prestamo->extensiones_solicitadas !== $extensionesUtilizadas) {
            $tokenPrestamo = str_replace("'", "''", (string)$prestamo->token);
            $mysql->query("UPDATE equipo_prestamo
                           SET extensiones_solicitadas = '$extensionesUtilizadas'
                           WHERE token = '$tokenPrestamo'");
        }

        $contador++;
        
        $prestamos[] = [
            'token' => $prestamo->token,
            'nombre_equipo' => $prestamo->nombre_equipo,
            'fecha_prestamo' => fecha_mysql_a_normal($prestamo->fecha_prestamo),
            'fecha_debe_devolver' => fecha_mysql_a_normal($prestamo->fecha_debe_devolver),
            'extensiones_solicitadas' => $extensionesUtilizadas,
            'estado_extension' => $prestamo->estado_extension ?: 'no solicitada',
            'estado_extension2' => $prestamo->estado_extension2 ?: 'no solicitada',
            'fecha_propuesta_extension' => $prestamo->fecha_propuesta_extension,
            'fecha_propuesta_extension2' => $prestamo->fecha_propuesta_extension2,
            'imagen' => $prestamo->imagen
        ];
    }

    $response = [
        'success' => true,
        'mensaje' => 'Préstamos activos obtenidos correctamente',
        'data' => $prestamos
    ];

} catch (Exception $e) {
    $response['mensaje'] = $e->getMessage();
    error_log("Error en obtener_prestamos_activos: " . $e->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
