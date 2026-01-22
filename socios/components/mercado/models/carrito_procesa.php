<?php
session_start();
include("../../../includes/conexionMysql.php");

$mysql = new mysql;
$mysql->connect();

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Procesar las diferentes acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $product_id = intval($_POST['product_id']);
            
            // Verificar stock
            $sql = $mysql->query("SELECT stock FROM productos WHERE id = $product_id");
            if ($result = $mysql->f_obj($sql)) {
                if ($result->stock > 0) {
                    // Agregar al carrito
                    if (isset($_SESSION['carrito'][$product_id])) {
                        $_SESSION['carrito'][$product_id]++;
                    } else {
                        $_SESSION['carrito'][$product_id] = 1;
                    }
                    echo json_encode([
                        'success' => true,
                        'count' => array_sum($_SESSION['carrito']) // Incluir el contador actualizado
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Producto sin stock'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ]);
            }
            break;

        case 'remove':
            $product_id = intval($_POST['product_id']);
            if (isset($_SESSION['carrito'][$product_id])) {
                unset($_SESSION['carrito'][$product_id]);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado en el carrito']);
            }
            break;

        case 'update':
            $product_id = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);
            
            // Verificar stock
            $sql = $mysql->query("SELECT stock FROM productos WHERE id = $product_id");
            if ($result = $mysql->f_obj($sql)) {
                if ($quantity <= $result->stock) {
                    $_SESSION['carrito'][$product_id] = $quantity;
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cantidad excede el stock disponible']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            }
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'count':
            $count = array_sum($_SESSION['carrito']);
            echo json_encode(['count' => $count]);
            break;

        case 'details':
            $items = [];
            $total = 0;

            foreach ($_SESSION['carrito'] as $product_id => $quantity) {
                $sql = $mysql->query("SELECT id, nombre, precio, stock FROM productos WHERE id = $product_id");
                if ($product = $mysql->f_obj($sql)) {
                    $subtotal = $product->precio * $quantity;
                    $items[] = [
                        'id' => $product->id,
                        'nombre' => $product->nombre,
                        'precio' => number_format($product->precio, 0, ',', '.'),
                        'cantidad' => $quantity,
                        'stock' => $product->stock,
                        'subtotal' => number_format($subtotal, 0, ',', '.')
                    ];
                    $total += $subtotal;
                }
            }

            echo json_encode([
                'items' => $items,
                'total' => number_format($total, 0, ',', '.')
            ]);
            break;
    }
}

$mysql->close();
?>