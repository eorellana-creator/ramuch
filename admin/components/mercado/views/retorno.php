<?php
session_start();
include("../../../includes/conexionMysql.php");

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Habilitar logs de errores en un archivo
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

try {

    // Recibe el token enviado por Flow
    if (!isset($_POST["token"])) {
        throw new Exception("No se recibió el token", 1);
    }

    $token = filter_input(INPUT_POST, 'token');

    // Conectar a la base de datos
    $mysql = new mysql;
    $mysql->connect();

    // Verificar el estado del pago en Flow
    require_once __DIR__ . '/../flow/lib/FlowApi.class.php';
    $flowApi = new FlowApi();
    $params = array("token" => $token);
    $response = $flowApi->send("payment/getStatus", $params, "GET");

    // Mostrar un mensaje al usuario según el estado del pago
    echo "<div style='text-align: center; padding: 20px; font-family: Arial, sans-serif;'>"; // Contenedor centrado

    // Formatear el monto pagado
    $monto = number_format($response['amount'], 0, '', '.'); // Formatear el monto con separadores de miles
    $fecha = date("d-m-Y H:i:s"); // Fecha actual
    $orden = @$response['commerceOrder']; // Número de orden

    // Determinar el mensaje según el estado del pago
    switch ($response['status']) {
        case "2": // Pagada
            echo "<h1 style='color: #4CAF50;'>¡Pago exitoso!</h1>";
            echo "<p>Gracias por tu compra. Tu orden ha sido procesada correctamente.</p>";
            echo "<p><strong>N° de Orden:</strong> $orden</p>";
            echo "<p><strong>Monto pagado:</strong> $$monto CLP</p>";
            echo "<p><strong>Fecha:</strong> $fecha</p>";

            // Actualizar el campo orden_flow en la tabla ventas
            if (!empty($orden)) {
                $sql_update = $mysql->query("
                    UPDATE ventas
                    SET orden_flow = '$orden'
                    WHERE token_flow = '$token'
                ");

                if (!$sql_update) {
                    error_log("Error al actualizar el campo orden_flow en la tabla ventas.");
                    throw new Exception("Error al actualizar el campo orden_flow.");
                }
            } else {
                error_log("El valor de la orden (commerceOrder) está vacío o no es válido.");
                throw new Exception("El valor de la orden no es válido.");
            }
        
            // Obtener los detalles de la venta desde la base de datos
            $sql = $mysql->query("SELECT * FROM ventas WHERE token_flow = '$token'");
            $venta = $mysql->f_obj($sql);

            if ($venta) {
                // Obtener los detalles de los productos vendidos
                $sql_detalle = $mysql->query("
                    SELECT dv.*, p.nombre 
                    FROM detalle_venta dv
                    INNER JOIN productos p ON dv.id_producto = p.id
                    WHERE dv.id_venta = '$venta->id_venta'
                ");

                // Mostrar los detalles de los productos comprados
                echo "<h2>Detalles de la compra:</h2>";
                echo "<table border='1' style='margin: 0 auto; border-collapse: collapse; width: 80%;'>"; // Tabla centrada
                echo "<tr>
                        <th style='padding: 10px; background-color: #f2f2f2;'>Producto</th>
                        <th style='padding: 10px; background-color: #f2f2f2;'>Cantidad</th>
                        <th style='padding: 10px; background-color: #f2f2f2;'>Precio Unitario</th>
                        <th style='padding: 10px; background-color: #f2f2f2;'>Subtotal</th>
                      </tr>";
                while ($detalle = $mysql->f_obj($sql_detalle)) {
                    $subtotal = number_format($detalle->cantidad * $detalle->precio_unitario, 0, '', '.');
                    echo "<tr>
                            <td style='padding: 10px;'>$detalle->nombre</td>
                            <td style='padding: 10px; text-align: center;'>$detalle->cantidad</td>
                            <td style='padding: 10px; text-align: right;'>$$detalle->precio_unitario</td>
                            <td style='padding: 10px; text-align: right;'>$$subtotal</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: #ff0000;'>No se encontraron detalles de la venta.</p>";
            }
            break;

        case "3": // Rechazada
            echo "<h1 style='color: #ff0000;'>Pago rechazado</h1>";
            echo "<p>Lo sentimos, tu pago ha sido rechazado. Por favor, inténtalo nuevamente.</p>";
            echo "<p><strong>N° de Orden:</strong> $orden</p>";
            echo "<p><strong>Monto:</strong> $$monto CLP</p>";
            echo "<p><strong>Fecha:</strong> $fecha</p>";
            break;

        case "4": // Anulada
            echo "<h1 style='color: #ff0000;'>Pago anulado</h1>";
            echo "<p>Tu pago ha sido anulado. Por favor, contacta con soporte si necesitas ayuda.</p>";
            echo "<p><strong>N° de Orden:</strong> $orden</p>";
            echo "<p><strong>Monto:</strong> $$monto CLP</p>";
            echo "<p><strong>Fecha:</strong> $fecha</p>";
            break;

        default:
            echo "<h1 style='color: #ff0000;'>Estado de pago desconocido</h1>";
            echo "<p>El estado de tu pago no pudo ser determinado. Por favor, contacta con soporte.</p>";
            break;
    }

    // Agregar botones de redirección
    echo "<div style='margin-top: 30px;'>";
    echo "<a href='https://ramuch.cl' style='text-decoration: none;'>";
    echo "<button style='padding: 10px 20px; margin: 5px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;'>Home de Ramuch</button>";
    echo "</a>";
    echo "<a href='https://ramuch.cl/admin/index.php?component=mercado&view=mercado' style='text-decoration: none;'>";
    echo "<button style='padding: 10px 20px; margin: 5px; background-color: #008CBA; color: white; border: none; border-radius: 5px; cursor: pointer;'>Volver al Mercado</button>";
    echo "</a>";
    echo "</div>";

    echo "</div>"; // Cierre del contenedor centrado
} catch (Exception $e) {
    error_log("Error en try retorno.php: " . $e->getMessage());
    echo "<div style='text-align: center; padding: 20px; font-family: Arial, sans-serif;'>"; // Contenedor centrado
    echo "<h1 style='color: #ff0000;'>Error</h1>";
    echo "<p>Ha ocurrido un error al procesar tu pago. Por favor, inténtalo nuevamente.</p>";
    echo "<p><strong>Detalles del error:</strong> " . $e->getMessage() . "</p>";

    // Agregar botones de redirección en caso de error
    echo "<div style='margin-top: 30px;'>";
    echo "<a href='https://ramuch.cl' style='text-decoration: none;'>";
    echo "<button style='padding: 10px 20px; margin: 5px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;'>Home de Ramuch</button>";
    echo "</a>";
    echo "<a href='https://ramuch.cl/admin/index.php?component=mercado&view=mercado' style='text-decoration: none;'>";
    echo "<button style='padding: 10px 20px; margin: 5px; background-color: #008CBA; color: white; border: none; border-radius: 5px; cursor: pointer;'>Volver al Mercado</button>";
    echo "</a>";
    echo "</div>";

    echo "</div>"; // Cierre del contenedor centrado
}
?>