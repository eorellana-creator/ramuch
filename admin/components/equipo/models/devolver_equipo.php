<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$config = new Config;
date_default_timezone_set($config->zona_horaria);
$mysql = new mysql;
$mysql->connect();

$hoy = date("Y-m-d");

// Obtener datos del formulario
$equipos = json_decode($_POST['equipos'] ?? '[]'); // Debe recibir id_equipo_prestamo
$observacion = $_POST['observacion'] ?? '';
$estado = $_POST['estado'] ?? '';
$idUsuario = $_POST['idUsuario'] ?? '';

try {
    if(empty($equipos)) {
        throw new Exception("No hay equipos seleccionados");
    }

    $estadosPermitidos = ['En el mismo estado', 'Con detalles', 'Extraviado', 'Inutilizable'];
    if (!in_array($estado, $estadosPermitidos, true)) {
        throw new Exception("El estado de devolución no es válido");
    }

    if (trim($observacion) === '') {
        throw new Exception("Debe ingresar una observación");
    }

    $observacion = mysqli_real_escape_string($mysql->conexion, substr($observacion, 0, 255));
    $usuarioRecepcion = (int)($_SESSION["usuario_id"] ?? 0);

    $mysql->query("START TRANSACTION");
    $equiposProcesados = [];

    foreach($equipos as $id_prestamo) { // Ahora es id_equipo_prestamo
        if (!ctype_digit((string)$id_prestamo) || (int)$id_prestamo <= 0) {
            throw new Exception("El identificador de préstamo no es válido");
        }
        $id_prestamo = (int)$id_prestamo;

        // 1. Obtener el id_equipo asociado al préstamo
        $sql_prestamo = $mysql->query("
            SELECT id_equipo 
            FROM equipo_prestamo 
            WHERE id_equipo_prestamo = '$id_prestamo' 
              AND estado = 'prestado' 
        ");
        
        if(!$prestamo = $mysql->f_obj($sql_prestamo)) {
            throw new Exception("Préstamo no encontrado: $id_prestamo");
        }

        // 2. Actualizar préstamo
        $estado_prestamo = ($estado == "Extraviado") ? 'extraviado' : 'devuelto';
        $update_prestamo = $mysql->query("
            UPDATE equipo_prestamo 
            SET 
                fecha_devolucion_efectiva = '$hoy',
                id_usuario_recepciono = '$usuarioRecepcion',
                comentario = '$observacion',
                estado_devolucion = '$estado',
                estado = '$estado_prestamo',
                estado_extension = 'finalizada',
                estado_extension2 = 'finalizada'
            WHERE id_equipo_prestamo = '$id_prestamo' 
        ");
        if (!$update_prestamo) {
            throw new Exception("No se pudo actualizar el préstamo: $id_prestamo");
        }
        /*
        error_log("update de prestamo: " ."
            UPDATE equipo_prestamo 
            SET 
                fecha_devolucion_efectiva = '$hoy',
                id_usuario_recepciono = '{$_SESSION["usuario_id"]}',
                comentario = '$observacion',
                estado_devolucion = '$estado',
                estado = '$estado_prestamo'
            WHERE id_equipo_prestamo = '$id_prestamo' 
        ");
        */
        // 3. Actualizar estado del equipo
        $update_equipo = $mysql->query("
            UPDATE equipo 
            SET 
                prestado_a_id_usuario = 0,
                prestado_a_nombre = '',
                id_responsable_prestamo = 0,
                nombre_responsable_prestamo = '',
                fecha_devolucion = '0000-00-00',
                estado = " . ($estado != 'En el mismo estado' ? "'$estado'" : 'estado') . "
            WHERE id_equipo = '{$prestamo->id_equipo}' 
        ");
        if (!$update_equipo) {
            throw new Exception("No se pudo actualizar el equipo: {$prestamo->id_equipo}");
        }

        $equiposProcesados[] = [
            'id_equipo_prestamo' => (int)$id_prestamo,
            'id_equipo' => (int)$prestamo->id_equipo
        ];
        /*
        error_log("update de equipo: " . "
            UPDATE equipo 
            SET 
                prestado_a_id_usuario = 0,
                prestado_a_nombre = '',
                id_responsable_prestamo = 0,
                nombre_responsable_prestamo = '',
                fecha_devolucion = '0000-00-00',
                estado = " . ($estado != 'En el mismo estado' ? "'$estado'" : 'estado') . "
            WHERE id_equipo = '{$prestamo->id_equipo}' 
        ");
        */
    }

    $mysql->query("COMMIT");

    echo json_encode([
        'success' => true,
        'message' => 'Devolución exitosa de ' . count($equiposProcesados) . ' equipos',
        'equipos' => $equiposProcesados
    ]);

} catch (Exception $e) {
    $mysql->query("ROLLBACK");
    // Registrar error en archivo
    error_log("Error en devolución: " . $e->getMessage());
    
    // Respuesta al frontend
    echo json_encode([
        'success' => false,
        'message' => 'Error (respuesta al frontend - devolucion equipo): ' . $e->getMessage()
    ]);
}
?>
