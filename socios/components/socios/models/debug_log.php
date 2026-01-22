<?php
// Obtener datos del POST
$archivo = $_POST['archivo'] ?? 'debug';
$mensaje = $_POST['mensaje'] ?? '';

// Sanitizar nombre de archivo
$archivo = preg_replace('/[^a-zA-Z0-9_-]/', '', $archivo);
$archivoPath = __DIR__ . '/' . $archivo . '.log';

// Escribir en el archivo
file_put_contents($archivoPath, $mensaje, FILE_APPEND | LOCK_EX);

// Responder éxito
echo json_encode(['success' => true]);
?>