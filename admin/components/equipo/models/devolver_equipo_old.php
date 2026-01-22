<?php
session_start();
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

    foreach($equipos as $id_prestamo) { // Ahora es id_equipo_prestamo
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
                id_usuario_recepciono = '{$_SESSION["usuario_id"]}',
                comentario = '$observacion',
                estado_devolucion = '$estado',
                estado = '$estado_prestamo'
            WHERE id_equipo_prestamo = '$id_prestamo' 
        ");
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

    $_SESSION["equipo_prestado"] = "<div class='alert alert-success'>
        Devolución exitosa de " . count($equipos) . " equipos
    </div>";

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Registrar error en archivo
    error_log("Error en devolución: " . $e->getMessage());
    
    // Respuesta al frontend
    echo json_encode([
        'success' => false,
        'message' => 'Error (respuesta al frontend - devolucion equipo): ' . $e->getMessage()
    ]);
}
?>