<?php
// Habilitar la visualización de errores de PHP (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Verificar si se recibió el token del plan a eliminar
if (isset($_POST['token'])) {
    $token = $_POST['token'];

    // Conectar a la base de datos
    $mysql = new mysql;
    $mysql->connect();

    // Registrar el token recibido para depuración
    error_log("Token recibido para eliminar: $token");

    // Eliminar el plan de la tabla `plan`
    $sql = $mysql->query("DELETE FROM plan WHERE token = '$token';");

    // Verificar si la eliminación fue exitosa
    if ($sql) {
        error_log("Plan eliminado correctamente: $token");
        $_SESSION["plan_eliminado"] = "<div class='alert alert-success' role='alert'>El plan se ha eliminado correctamente.</div>";
    } else {
        error_log("Error al eliminar el plan: " . $mysql->error());
        $_SESSION["plan_eliminado"] = "<div class='alert alert-danger' role='alert'>Error al eliminar el plan.</div>";
    }

    // Redirigir de vuelta a la lista de planes
    header("Location: index.php?component=plan&view=plan_list");
    exit();
} else {
    // Si no se recibió el token, redirigir con un mensaje de error
    error_log("Token no proporcionado para eliminar plan.");
    $_SESSION["plan_eliminado"] = "<div class='alert alert-danger' role='alert'>Token no proporcionado.</div>";
    header("Location: index.php?component=plan&view=plan_list");
    exit();
}
?>