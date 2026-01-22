<?php
session_start();
include("../../../configuration.php");
require_once '../../../includes/conexionMysql.php';

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_POST['edit_product_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit;
}

$config = new Config;
// Crear una única instancia de la conexión
$db = new mysql();
$mysqli = $db->connect();

if (!$mysqli) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al conectar con la base de datos: ' . mysqli_connect_error()
    ]);
    exit;
}

$productId = intval($_POST['edit_product_id']);
$nombre = mysqli_real_escape_string($mysqli, $_POST['edit_nombre']);
$precio = floatval($_POST['edit_precio']);
$stock = intval($_POST['edit_stock']);
$stock_minimo = intval($_POST['edit_stock_minimo']);
$categoria_id = intval($_POST['edit_categoria_id']);
$marca = mysqli_real_escape_string($mysqli, $_POST['edit_marca']);
$modelo = mysqli_real_escape_string($mysqli, $_POST['edit_modelo']);
$estado = mysqli_real_escape_string($mysqli, $_POST['edit_estado']);
$talla = mysqli_real_escape_string($mysqli, $_POST['edit_talla']);
$color = mysqli_real_escape_string($mysqli, $_POST['edit_color']);
$descuento = floatval($_POST['edit_descuento']);
$descripcion = mysqli_real_escape_string($mysqli, $_POST['edit_descripcion']);

$imagen_sql = "";
if (isset($_FILES['edit_imagen']) && $_FILES['edit_imagen']['size'] > 0) {
    $imagen_nombre = time() . '_' . $_FILES['edit_imagen']['name'];
    $imagen_temporal = $_FILES['edit_imagen']['tmp_name'];
    $ruta_destino = "../images/productos/" . $imagen_nombre;
    
    if (move_uploaded_file($imagen_temporal, $ruta_destino)) {
        $imagen_sql = ", imagen_nombre = '$imagen_nombre'";
    }
}

$sql = "UPDATE productos SET 
        nombre = '$nombre',
        precio = $precio,
        stock = $stock,
        stock_minimo = $stock_minimo,
        categoria_id = $categoria_id,
        marca = '$marca',
        modelo = '$modelo',
        estado = '$estado',
        talla = '$talla',
        color = '$color',
        descuento = $descuento,
        descripcion = '$descripcion'
        $imagen_sql
        WHERE id = $productId";

if ($mysqli->query($sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Producto actualizado correctamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar el producto: ' . $mysqli->error
    ]);
}

$db->close();
?>