<?php
// Incluir el archivo de conexión
require_once '../../../includes/conexionMysql.php';

// Configuración de la cabecera para devolver una respuesta JSON
header('Content-Type: application/json');

// Mostrar errores para depuración (elimina esto en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Crear una instancia de la clase mysql y conectar a la base de datos
$db = new mysql();
$mysqli = $db->connect();

if (!$mysqli) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al conectar con la base de datos: ' . mysqli_connect_error()
    ]);
    exit;
}

// Validar que todos los campos requeridos están presentes
$camposObligatorios = ['nombre', 'precio', 'stock', 'categoria_id', 'estado'];
foreach ($camposObligatorios as $campo) {
    if (empty($_POST[$campo])) {
        echo json_encode([
            'success' => false,
            'message' => "El campo $campo es obligatorio"
        ]);
        exit;
    }
}

// Recibir y sanitizar los datos del formulario
$nombre = $mysqli->real_escape_string($_POST['nombre']);
$precio = floatval($_POST['precio']);
$stock = intval($_POST['stock']);
$categoria_id = intval($_POST['categoria_id']);
$estado = $mysqli->real_escape_string($_POST['estado']);
$marca = isset($_POST['marca']) ? $mysqli->real_escape_string($_POST['marca']) : null;
$modelo = isset($_POST['modelo']) ? $mysqli->real_escape_string($_POST['modelo']) : null;
$talla = isset($_POST['talla']) ? $mysqli->real_escape_string($_POST['talla']) : null;
$color = isset($_POST['color']) ? $mysqli->real_escape_string($_POST['color']) : null;
$descripcion = isset($_POST['descripcion']) ? $mysqli->real_escape_string($_POST['descripcion']) : null;
$descuento = isset($_POST['descuento']) ? floatval($_POST['descuento']) : 0.0;

// Generar un token único de 15 caracteres
do {
    $token = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 15);
    $queryToken = "SELECT id FROM productos WHERE token = '$token'";
    $result = $mysqli->query($queryToken);
} while ($result && $result->num_rows > 0);

// Manejo del archivo de imagen
$imagenRuta = null;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    // Verificar y crear el directorio de destino si no existe
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/admin/components/mercado/images/productos/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Crear el directorio con permisos
    }

    // Ruta completa del archivo destino
    $targetFile = $targetDir . basename($_FILES['imagen']['name']);

    // Validar tipo de archivo permitido
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($_FILES['imagen']['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'El formato de la imagen no es válido. Se permiten JPEG, PNG o GIF.'
        ]);
        exit;
    }

    // Mover el archivo subido al directorio de destino
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar la imagen en el servidor. Verifica permisos y rutas.'
        ]);
        exit;
    }

    // Guardar la ruta relativa de la imagen
    $imagenRuta = '' . basename($_FILES['imagen']['name']);
}

// Insertar el producto en la base de datos
$query = "INSERT INTO productos (nombre, precio, stock, categoria_id, estado, marca, modelo, talla, color, descripcion, descuento, token, imagen_nombre)
          VALUES (
              '$nombre', 
              $precio, 
              $stock, 
              $categoria_id, 
              '$estado', 
              " . ($marca ? "'$marca'" : "NULL") . ", 
              " . ($modelo ? "'$modelo'" : "NULL") . ", 
              " . ($talla ? "'$talla'" : "NULL") . ", 
              " . ($color ? "'$color'" : "NULL") . ", 
              " . ($descripcion ? "'$descripcion'" : "NULL") . ", 
              $descuento, 
              '$token', 
              " . ($imagenRuta ? "'$imagenRuta'" : "NULL") . "
          )";

if ($mysqli->query($query)) {
    echo json_encode([
        'success' => true,
        'message' => 'Producto guardado exitosamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el producto: ' . $mysqli->error
    ]);
}

// Cerrar la conexión a la base de datos
$db->close();
?>
