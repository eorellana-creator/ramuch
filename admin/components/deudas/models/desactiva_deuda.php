<?php
// Configurar registro de errores en un archivo
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // Ruta donde se guardará el log

include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token = $_POST['token'];
error_log("Token recibido: " . $token); // Guarda el token en el log

$config = new Config;
$mysql = new mysql;
$mysql->connect();

// Obtener estado actual
$sql = $mysql->query("SELECT estado FROM deudas WHERE token='$token'");
$result = $mysql->f_obj($sql);
$nuevo_estado = ($result->estado == 'desactivada') ? 'activa' : 'desactivada';

// Mostrar y registrar el SQL del UPDATE
$update_sql = "UPDATE deudas SET estado='$nuevo_estado' WHERE token='$token'";
error_log("SQL ejecutado: " . $update_sql); // Guarda el SQL en el log

// Actualizar
$mysql->query($update_sql);

// Después de la actualización, verifica el estado actual
$sql = $mysql->query("SELECT estado FROM deudas WHERE token='$token'");
$new_result = $mysql->f_obj($sql);

if($new_result->estado == $nuevo_estado) {
    echo "|1|Estado cambiado a $nuevo_estado|";
    error_log("Éxito: Estado cambiado a $nuevo_estado (Token: $token)");
} else {
    echo "|0|No se pudo cambiar el estado|";
    error_log("Error: No se pudo cambiar el estado (Token: $token)");
}
?>