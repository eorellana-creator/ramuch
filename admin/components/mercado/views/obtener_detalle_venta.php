<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");

$mysql = new mysql;
$mysql->connect();

// Obtener el ID de la venta desde la solicitud GET
$id_venta = isset($_GET['id_venta']) ? intval($_GET['id_venta']) : 0;

// Depuración: Verificar el valor de id_venta
error_log("ID de Venta recibido en obtener_detalle_venta.php: " . $id_venta);

if ($id_venta > 0) {
    // Consulta para obtener los detalles de la venta
    $sql = $mysql->query("
        SELECT dv.id_producto, p.nombre as nombre_producto, dv.cantidad, dv.precio_unitario, (dv.cantidad * dv.precio_unitario) as total
        FROM detalle_venta dv
        JOIN productos p ON dv.id_producto = p.id
        WHERE dv.id_venta = $id_venta and dv.estado = 'activo'
    ");

    if ($sql) {
        $detalles = array();

        while ($result = $mysql->f_obj($sql)) {
            $detalles[] = array(
                'id_producto' => $result->id_producto,
                'nombre_producto' => $result->nombre_producto,
                'cantidad' => $result->cantidad,
                'precio_unitario' => $result->precio_unitario,
                'total' => $result->total
            );
        }

        // Establecer el encabezado JSON
        header('Content-Type: application/json');

        // Devolver la respuesta JSON
        echo json_encode($detalles);
    } else {
        // Si hay un error en la consulta
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error en la consulta SQL']);
    }
} else {
    // Si el ID de la venta es inválido
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID de venta inválido']);
}

$mysql->close();
?>