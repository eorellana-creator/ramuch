<?php
// Configurar registro de errores en un archivo
ini_set('log_errors', 1);

include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token = $_POST['token'];

$config = new Config;
$mysql = new mysql;
$mysql->connect();

// Obtener estado actual
$sql = $mysql->query("SELECT estado FROM deudas WHERE token='$token'");
$result = $mysql->f_obj($sql);
$nuevo_estado = ($result->estado == 'desactivada') ? 'activa' : 'desactivada';

// Mostrar y registrar el SQL del UPDATE
$update_sql = "UPDATE deudas SET estado='$nuevo_estado' WHERE token='$token'";

// Actualizar
$mysql->query($update_sql);

// Después de la actualización, verifica el estado actual
$sql = $mysql->query("SELECT estado FROM deudas WHERE token='$token'");
$new_result = $mysql->f_obj($sql);

if($new_result->estado == $nuevo_estado) {
    echo "|1|Estado cambiado a $nuevo_estado|";
} else {
    echo "|0|No se pudo cambiar el estado|";
    error_log("Error: no se pudo cambiar el estado de la deuda.");
}
?>
