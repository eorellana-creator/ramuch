<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Aquí iría la lógica para actualizar la base de datos
    // Por ejemplo: $query = "UPDATE articulos SET prestado = 1 WHERE id = ?";
    // Ejecutar la consulta en la base de datos.

    // Simulamos una respuesta exitosa
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
