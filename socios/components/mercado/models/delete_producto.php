<?php
// Incluir el archivo de conexión
require_once '../../../includes/conexionMysql.php';

// Configuración de la cabecera para devolver una respuesta JSON
header('Content-Type: application/json');

// Validar que la solicitud sea POST y contenga el ID del producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $db = new mysql();
    $mysqli = $db->connect();

    if (!$mysqli) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al conectar con la base de datos: ' . mysqli_connect_error()
        ]);
        exit;
    }

    $productId2 = $_POST['id']; // Sanitizar el ID recibido
    $productId = intval($_POST['id']); // Sanitizar el ID recibido

    // Verificar si el producto existe
    $checkQuery = "SELECT id FROM productos WHERE id = $productId";
    $result = $mysqli->query($checkQuery);

    if ($result && $result->num_rows > 0) {
        // Si el producto existe, eliminarlo
        $deleteQuery = "DELETE FROM productos WHERE id = $productId";
        if ($mysqli->query($deleteQuery)) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto: ' . $mysqli->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado: '.$productId]);
    }

    $db->close(); // Cerrar la conexión a la base de datos
} else {
    // Manejar solicitudes inválidas
    echo json_encode([
        'success' => false,
        'message' => 'Solicitud inválida. No se proporcionó un ID de producto.'
    ]);
}
?>