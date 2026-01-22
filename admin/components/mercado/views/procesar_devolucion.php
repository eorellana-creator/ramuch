<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");

$mysql = new mysql;
$mysql->connect();

// Leer el cuerpo de la solicitud (JSON)
$input = file_get_contents('php://input');

// Decodificar la cadena JSON
$data = json_decode($input, true);

// Obtener el ID de la venta desde la solicitud GET
//$id_venta = isset($_GET['id_venta']) ? intval($_GET['id_venta']) : 0;
//error_log("Ítems a devolver idVenta en procesa devolucion: " . print_r($id_venta, true));

error_log("Datos recibidos en procesar devolucion1: " . print_r($data, true));

// Verificar si la decodificación fue exitosa
if (json_last_error() === JSON_ERROR_NONE && isset($data['items'])) {
    $items = $data['items'];

    error_log("Datos recibidos en procesar devolucion2: ");

    // Iniciar una transacción para asegurar la atomicidad
    $mysql->query("START TRANSACTION");
    error_log("Datos recibidos en procesar devolucion3: ");
    try {
        foreach ($items as $item) {
            // Verificar que el ítem tenga las claves necesarias
            error_log("Datos recibidos en procesar devolucion4: ");
            if (!isset($item['id_producto']) || !isset($item['cantidad']) || !isset($item['id_venta'])) {
                throw new Exception("Ítem incompleto: " . print_r($item, true));
            }

            $id_producto = intval($item['id_producto']);
            $cantidad = intval($item['cantidad']);
            $id_venta = intval($item['id_venta']);

            error_log("Datos recibidos en procesar devolucion5: " . $id_venta ." " . $cantidad . " ". $id_producto );

            // 1. Actualizar el stock del producto
            $mysql->query("UPDATE productos SET stock = stock + $cantidad WHERE id = $id_producto");

            error_log("Datos recibidos en procesar devolucion6: " );

            // 2. Actualizar el estado del ítem en detalle_venta a "devuelto"
            $mysql->query("UPDATE detalle_venta SET estado = 'devuelto' WHERE id_venta = $id_venta AND id_producto = $id_producto");
            error_log("Datos recibidos en procesar devolucion7: " );

            // 3. Obtener el precio unitario del producto devuelto
            $sql = $mysql->query("SELECT precio_unitario FROM detalle_venta WHERE id_venta = $id_venta AND id_producto = $id_producto");
            $result = $mysql->f_obj($sql);
            $precio_unitario = $result->precio_unitario;
            error_log("Datos recibidos en procesar devolucion8: " );

            // 4. Calcular el monto a restar
            $monto_a_restar = $precio_unitario * $cantidad;
            error_log("Datos recibidos en procesar devolucion8.5: " . $monto_a_restar );

            // 5. Restar el monto del total en la tabla ventas
            $mysql->query("UPDATE ventas SET total = total - $monto_a_restar WHERE id_venta = $id_venta");
            error_log("Datos recibidos en procesar devolucion9: " );

            // 6. Obtener el token_flow de la venta
            $sql = $mysql->query("SELECT token_flow FROM ventas WHERE id_venta = $id_venta");
            error_log("Datos recibidos en procesar devolucion10: " .  print_r($sql, true) );

            $result = $mysql->f_obj($sql);
            $token_flow = $result->token_flow;
            error_log("Datos recibidos en procesar devolucion11: " . $token_flow );

            // 7. Restar el monto en la tabla cuenta_maestra
            $elsql = "UPDATE cuenta_maestra SET monto = monto - $monto_a_restar WHERE token = '$token_flow'";
            error_log("Datos recibidos en procesar devolucion11.5: " . $elsql );
            $mysql->query($elsql);

            error_log("Datos recibidos en procesar devolucion12: " );
        }

        error_log("Datos recibidos en procesar devolucion13: " . print_r($data, true));
        error_log("Monto a restar: $monto_a_restar");
        error_log("Token Flow: $token_flow");
        error_log("Datos recibidos en procesar devolucion14: " );

        // Confirmar la transacción
        $mysql->query("COMMIT");
        error_log("Datos recibidos en procesar devolucion15: " );

        // Devolver una respuesta de éxito
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $mysql->query("ROLLBACK");

        // Devolver una respuesta de error
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    // Si hay un error en el JSON o no se encontraron ítems
    echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
}

$mysql->close();
?>