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
error_log("DEBUG - obtener prestamos activos");

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
    
    error_log("DEBUG - ID Usuario: $id_usuario");

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

    error_log("DEBUG - SQL ejecutado: $sql");
    
    $result = $mysql->query($sql);
    
    if (!$result) {
        throw new Exception('Error al obtener préstamos activos: ' . $mysql->error);
    }

    $prestamos = [];
    $contador = 0;
    
    while ($prestamo = $mysql->f_obj($result)) {
        $contador++;
        error_log("DEBUG - Préstamo $contador: " . 
                  "Token: {$prestamo->token}, " .
                  "Equipo: {$prestamo->nombre_equipo}, " .
                  "Ext1: " . ($prestamo->estado_extension ?: 'NULL') . ", " .
                  "Ext2: " . ($prestamo->estado_extension2 ?: 'NULL') . ", " .
                  "Solicitadas: {$prestamo->extensiones_solicitadas}");
        
        $prestamos[] = [
            'token' => $prestamo->token,
            'nombre_equipo' => $prestamo->nombre_equipo,
            'fecha_prestamo' => fecha_mysql_a_normal($prestamo->fecha_prestamo),
            'fecha_debe_devolver' => fecha_mysql_a_normal($prestamo->fecha_debe_devolver),
            'extensiones_solicitadas' => (int)$prestamo->extensiones_solicitadas,
            'estado_extension' => $prestamo->estado_extension ?: 'no solicitada',
            'estado_extension2' => $prestamo->estado_extension2 ?: 'no solicitada',
            'fecha_propuesta_extension' => $prestamo->fecha_propuesta_extension,
            'fecha_propuesta_extension2' => $prestamo->fecha_propuesta_extension2,
            'imagen' => $prestamo->imagen
        ];
    }

    error_log("DEBUG - Total de préstamos encontrados: $contador");

    $response = [
        'success' => true,
        'mensaje' => 'Préstamos activos obtenidos correctamente',
        'data' => $prestamos
    ];

} catch (Exception $e) {
    $response['mensaje'] = $e->getMessage();
    error_log("Error en obtener_prestamos_activos: " . $e->getMessage());
}

error_log("DEBUG - Respuesta final: " . json_encode($response));
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>