<?php
include("../../../includes/conexionMysql.php");

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Habilitar logs de errores en un archivo
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

try {
    // Recibir el token de la transacción desde Flow
    if (!isset($_POST['token'])) {
        error_log("No se recibió el token de la transacción en confirmacion.php");
        throw new Exception("No se recibió el token de la transacción.");
    }

    $token = $_POST['token'];
    error_log("Token recibido en confirmacion.php: " . $token); // Log para verificar el token recibido

    // Conectar a la base de datos
    $mysql = new mysql;
    $mysql->connect();

    // Verificar el estado del pago en Flow
    require_once __DIR__ . '/../flow/lib/FlowApi.class.php';
    $flowApi = new FlowApi();
    $params = array("token" => $token);
    $response = $flowApi->send("payment/getStatus", $params, "GET");
    error_log("Respuesta de Flow en confirmacion.php: " . print_r($response, true)); // Log para verificar la respuesta de Flow

    // Recuperar el id_usuario y token_carrito desde los datos opcionales
    if (isset($response['optional'])) {
        // Verificar si $response['optional'] es una cadena JSON o un array
        if (is_string($response['optional'])) {
            $optional = json_decode($response['optional'], true);
        } else {
            $optional = $response['optional']; // Ya es un array, no es necesario decodificarlo
        }

        // Obtener el ID del usuario y el token_carrito
        if (isset($optional['id_user']) && isset($optional['token_carrito'])) {
            $id_usuario = $optional['id_user'];
            $token_carrito = $optional['token_carrito'];
            $nombre_usuario = $optional['usuario']; // Recuperar el nombre del usuario
            $id_transaccion = $response['flowOrder'];
        } else {
            error_log("No se encontró el ID de usuario o el token_carrito en los datos opcionales.");
            throw new Exception("No se encontró el ID de usuario o el token_carrito en los datos opcionales.");
        }
    } else {
        error_log("No se encontraron datos opcionales en la respuesta de Flow.");
        throw new Exception("No se encontraron datos opcionales en la respuesta de Flow.");
    }

    // Verificar que el id_usuario y token_carrito sean válidos
    if (empty($id_usuario) || empty($token_carrito)) {
        error_log("ID de usuario o token_carrito no válidos o nulos.");
        throw new Exception("ID de usuario o token_carrito no válidos o nulos.");
    }

    // Verificar si el pago fue exitoso
    if ($response['status'] == '2') {
        // Obtener el carrito desde la base de datos usando el token_carrito
        $sql_carrito = $mysql->query("
            SELECT carrito FROM carritos WHERE token_flow = '$token_carrito'
        ");

        // Verificar si se encontró el carrito
        $sql_count = $mysql->query("
            SELECT COUNT(*) as total FROM carritos WHERE token_flow = '$token_carrito'
        ");

        $count_data = $mysql->f_obj($sql_count);
        if ($count_data->total == 0) {
            error_log("No se encontró el carrito para el token_carrito: $token_carrito");
            throw new Exception("No se encontró el carrito para el token_carrito: $token_carrito");
        }

        $carrito_data = $mysql->f_obj($sql_carrito);
        $carrito = json_decode($carrito_data->carrito, true); // Convertir el JSON a array

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Error al decodificar el carrito: " . json_last_error_msg());
            throw new Exception("Error al decodificar el carrito.");
        }

        // Obtener el total y la fecha de la venta
        $total = $response['amount'];
        $fecha_venta = date("Y-m-d H:i:s");

        // Insertar la venta en la tabla `ventas`
        $sql_venta = $mysql->query("
            INSERT INTO ventas (id_usuario, fecha_venta, total, estado, token_flow)
            VALUES ('$id_usuario', '$fecha_venta', '$total', 'completada', '$token')
        ");

        if (!$sql_venta) {
            error_log("Error al insertar la venta en la tabla `ventas`.");
            throw new Exception("Error al insertar la venta en la tabla `ventas`.");
        }

        $id_venta = $mysql->ultimo_id(); // Obtener el ID de la venta recién insertada

        

        // Insertar en la tabla cuenta_maestra
        $fecha_insercion = date("Y-m-d H:i:s"); // Fecha actual
        $nombre_usuario_sistema = $nombre_usuario; // Nombre del usuario que realiza la transacción
        $id_usuario_movimiento = $id_usuario; // ID del usuario que realiza la transacción
        $nombre = "Venta de productos Mercado Ramuch"; // Nombre del movimiento
        $fecha = $fecha_venta; // Fecha de la venta
        $tipo = "ingreso"; // Tipo de movimiento (Ingreso o Egreso)
        $sub_cuenta = "mercado"; // Subcuenta asociada
        $glosa = "Pago vía Flow"; // Descripción breve
        $observacion = "Pago confirmado con token: $token"; // Observación adicional
        $medio = "Flow"; // Medio de pago
        $id_transaccion = $id_transaccion; // ID de la transacción (usamos el ID de la venta)
        $documento_respaldo = ""; // Documento de respaldo
        $monto = $total; // Monto de la transacción
        $estado = "activo"; // Estado de la transacción
        $token_cuenta = $token; // Token de la transacción

        $sql_cuenta_maestra = $mysql->query("
            INSERT INTO cuenta_maestra (
                id_usuario_sistema,
                fecha_insercion,
                nombre_usuario_sistema,
                id_usuario_movimiento,
                nombre,
                fecha,
                tipo,
                sub_cuenta,
                glosa,
                observacion,
                medio,
                id_transaccion,
                documento_respaldo,
                monto,
                estado,
                token
            ) VALUES (
                '$id_usuario',
                '$fecha_insercion',
                '$nombre_usuario_sistema',
                '$id_usuario_movimiento',
                '$nombre_usuario_sistema',
                '$fecha',
                '$tipo',
                '$sub_cuenta',
                '$glosa',
                '$observacion',
                '$medio',
                '$id_transaccion',
                '$documento_respaldo',
                '$monto',
                '$estado',
                '$token_cuenta'
            )
        ");

        if (!$sql_cuenta_maestra) {
            error_log("Error al insertar en la tabla cuenta_maestra.");
            throw new Exception("Error al insertar en la tabla cuenta_maestra.");
        }

        // Insertar los detalles de la venta en la tabla `detalle_venta`
        foreach ($carrito as $item) {
            $id_producto = $item['id'];
            $cantidad = $item['quantity'];
            $precio_unitario = $item['price'];

            $sql_detalle = $mysql->query("
                INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, estado)
                VALUES ('$id_venta', '$id_producto', '$cantidad', '$precio_unitario', 'activo')
            ");

            if (!$sql_detalle) {
                error_log("Error al insertar el detalle de la venta en la tabla `detalle_venta`.");
                throw new Exception("Error al insertar el detalle de la venta en la tabla `detalle_venta`.");
            }

            // Rebajar el stock del producto
            $sql_stock = $mysql->query("
                UPDATE productos SET stock = stock - $cantidad WHERE id = $id_producto
            ");

            if (!$sql_stock) {
                error_log("Error al actualizar el stock del producto con ID: $id_producto.");
                throw new Exception("Error al actualizar el stock del producto con ID: $id_producto.");
            }

        }

        // Después de registrar la confirmación en el log
        error_log("Venta confirmada: ID $id_venta, Token $token");

        // Obtener el email del usuario desde la tabla usuarios
        $sql_usuario = $mysql->query("
            SELECT email FROM usuario WHERE id_usuario = '$id_usuario'
        ");
        $usuario_data = $mysql->f_obj($sql_usuario);
        if (!$usuario_data) {
            error_log("No se encontró el usuario con ID: $id_usuario");
            throw new Exception("No se encontró el usuario con ID: $id_usuario");
        }
        $email_usuario = $usuario_data->email;

        // Enviar correo al usuario
        $asunto_usuario = "Confirmación de Compra - Mercado Ramuch";
        $mensaje_usuario = "
            <html>
            <body>
                <h1>¡Gracias por tu compra, $nombre_usuario!</h1>
                <p>Tu compra ha sido procesada exitosamente.</p>
                <p><strong>Detalles de la compra:</strong></p>
                <ul>
                    <li>ID de Venta: $id_venta</li>
                    <li>Fecha: $fecha_venta</li>
                    <li>Total Pagado: $$total</li>
                </ul>
                <p>Pronto recibirás más información sobre el envío de tus productos.</p>
                <p>Atentamente,<br>Mercado Ramuch</p>
            </body>
            </html>
        ";
        $headers_usuario = "From: no-reply@ramuch.cl\r\n";
        $headers_usuario .= "Content-Type: text/html; charset=UTF-8\r\n";

        if (!mail($email_usuario, $asunto_usuario, $mensaje_usuario, $headers_usuario)) {
            error_log("Error al enviar el correo al usuario con email: $email_usuario");
            throw new Exception("Error al enviar el correo al usuario.");
        }

        // Enviar correo a comision.merchandising@ramuch.cl
        $email_comision = "comision.merchandising@ramuch.cl";
        $asunto_comision = "Nueva Venta Confirmada - ID $id_venta";
        $mensaje_comision = "
            <html>
            <body>
                <h1>Nueva Venta Confirmada</h1>
                <p>Se ha registrado una nueva venta en Mercado Ramuch.</p>
                <p><strong>Detalles de la venta:</strong></p>
                <ul>
                    <li>ID de Venta: $id_venta</li>
                    <li>Usuario: $nombre_usuario</li>
                    <li>Fecha: $fecha_venta</li>
                    <li>Total Pagado: $$total</li>
                </ul>
                <p>Por favor, revisa los detalles en el sistema.</p>
                <p>Atentamente,<br>Sistema de Ventas - Mercado Ramuch</p>
            </body>
            </html>
        ";
        $headers_comision = "From: no-reply@ramuch.cl\r\n";
        $headers_comision .= "Content-Type: text/html; charset=UTF-8\r\n";

        if (!mail($email_comision, $asunto_comision, $mensaje_comision, $headers_comision)) {
            error_log("Error al enviar el correo a comision.merchandising@ramuch.cl");
            throw new Exception("Error al enviar el correo a comision.merchandising@ramuch.cl.");
        }
       
        // Limpiar el carrito de la base de datos (opcional)
        $mysql->query("DELETE FROM carritos WHERE id_usuario = '$id_usuario'");

    } else {
        print_r($response);
        error_log("Respuesta de Flow: " . print_r($response, true));
        throw new Exception("El pago no fue exitoso. Estado: " . $response['status']);
    }
} catch (Exception $e) {
    error_log("Error en confirmacion.php: " . $e->getMessage());
    error_log("Código de retorno: " . $response['status']);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>