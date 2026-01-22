<?php
// Incluir el archivo de conexión
include("../../../includes/conexionMysql.php");

// Crear una instancia de la clase mysql y conectar a la base de datos
$db = new mysql();
$mysqli = $db->connect(); // Establecer conexión

// Configuración de la cabecera para manejar errores y contenido HTML
header('Content-Type: text/html; charset=utf-8');

// Verificar que se envió una solicitud POST con datos del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
    // Decodificar el carrito enviado como JSON
    $cart = json_decode($_POST['cart'], true);

    // Validar que el carrito es un arreglo
    if (!is_array($cart)) {
        echo '<p class="text-danger text-center">El formato del carrito es inválido.</p>';
        exit;
    }

    // Crear el contenido HTML para los productos del carrito
    $output = '';
    $totalCarrito = 0;

    foreach ($cart as $item) {
        $productId = intval($item['id']);
        $quantity = intval($item['quantity']);

        // Consultar información del producto
        $query = $db->query("SELECT nombre, precio FROM productos WHERE id = $productId");
        if ($db->f_num($query) > 0) {
            $product = $db->f_array($query);
            $subtotal = $product['precio'] * $quantity;
            $totalCarrito += $subtotal;

            // Agregar producto al contenido HTML
            $output .= '
                <tr>
                    <td class="text-center">' . htmlspecialchars($productId) . '</td>
                    <td>' . htmlspecialchars($product['nombre']) . '</td>
                    <td class="text-center">' . htmlspecialchars($quantity) . '</td>
                    <td class="text-right">$' . formatoPrecio($product['precio']) . '</td>
                    <td class="text-right">$' . formatoPrecio($subtotal) . '</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-danger remove-item" data-product-id="' . $productId . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            ';
        }
        $db->free_sql($query); // Liberar resultados de la consulta
    }

    // Si no hay productos en el carrito
    if ($output === '') {
        echo '<p class="text-center">El carrito está vacío.</p>';
    } else {
        // Renderizar la tabla con los productos
        echo '
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-right">Precio</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>' . $output . '</tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right font-weight-bold">Total</td>
                        <td class="text-right font-weight-bold">$' . number_format($totalCarrito, 0) . '</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        ';
    }
} else {
    // Manejo de solicitud no válida
    echo '<p class="text-danger text-center">Solicitud no válida.</p>';
}

// Cerrar la conexión a la base de datos
$db->close();


function formatoPrecio($numero) {
    // Formatear el número con puntos como separadores de miles y sin decimales
    $numero = str_replace('$', '', $numero);
    return number_format($numero, 0, '', '.');
}
?>