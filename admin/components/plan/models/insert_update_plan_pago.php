<?php
// Configuración inicial de errores y sesión
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

// Inclusión de archivos necesarios
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Variables de sesión
$id_company = $_SESSION["company_id"];
$id_usuario = $_SESSION["usuario_id"];

// Recuperación de datos POST
$nombre         = $_POST['nombre'];
$periodo        = $_POST['periodo'];
$valor          = $_POST['valor'];
$dia            = $_POST['diap'];
$pp             = $_POST['ppp'];
$token          = $_POST['tokenMatricula'];
$tokenPlanPago  = $_POST['tokenPlanPago'];

// Recuperar fechas si existen (solo para semestral o anual)
$fecha_cierre1   = isset($_POST['fecha_cierre1']) ? $_POST['fecha_cierre1'] : null;
$fecha_cierre2  = isset($_POST['fecha_cierre2']) ? $_POST['fecha_cierre2'] : null;

// Instancias de clases
$config = new Config;
$mysql  = new mysql;
$mysql->connect();

// Generación de un token nuevo
$token_nuevo = md5(rand(99999, 99999999) . $nombre . date("Y m d H s"));

// Verificación si el tokenPlanPago está vacío (INSERT)
if ($tokenPlanPago == "") {
    $hoy = date("Y-m-d i s");
    $token_nuevo = md5($hoy . $nombre . rand(999, 999999) . $valor);
    $clave = md5($token_nuevo);

    // Consulta para obtener el id_plan_matricula
    $sql = $mysql->query("SELECT * FROM plan_matricula WHERE token ='$token' AND token!='';");
    $result = $mysql->f_obj($sql);
    $id_plan_matricula = $result->id_plan_matricula;

    // Agregar las fechas condicionalmente
    if ($periodo === "semestral" || $periodo === "anual") {
        $campos_adicionales = ", fecha_cierre1, fecha_cierre2";
        $valores_adicionales = ", '$fecha_cierre1', '$fecha_cierre2'";
    } else {
        $campos_adicionales = "";
        $valores_adicionales = "";
    }

    // Inserción en la tabla "plan"
    $sql = $mysql->query("
        INSERT INTO plan (
            id_plan_matricula, nombre, periodo, dia_pago, valor, publico_privado, activo, token $campos_adicionales
        ) VALUES (
            '$id_plan_matricula', '$nombre', '$periodo', '$dia', '$valor', '$pp', '1', '$token_nuevo' $valores_adicionales
        );
    ");

    // Debugging: Mostrar la consulta ejecutada
    echo "INSERT INTO plan ('id_plan_matricula', nombre, periodo, dia_pago, valor, publico_privado, activo, token $campos_adicionales) 
          VALUES ('$id_plan_matricula', '$nombre', '$periodo', '$dia', '$valor', '$pp', '1', '$token_nuevo' $valores_adicionales);";

    // Obtener el último ID insertado
    $ultimo_id = $mysql->ultimo_id();
    echo "|$token_nuevo|";
}

// Verificación si el tokenPlanPago no está vacío (UPDATE)
if ($tokenPlanPago != "") {
    // Agregar las fechas condicionalmente
    if ($periodo === "semestral" || $periodo === "anual") {
        $actualizacion_fechas = ", fecha_cierre1='$fecha_cierre1', fecha_cierre2='$fecha_cierre2'";
    } else {
        $actualizacion_fechas = "";
    }

    $sql = $mysql->query("
        UPDATE plan 
        SET nombre='$nombre', dia_pago='$dia', periodo='$periodo', valor='$valor', publico_privado='$pp' $actualizacion_fechas
        WHERE token ='$tokenPlanPago';
    ");

    echo "|$tokenPlanPago|";
}

// Mensaje de éxito en la sesión
$_SESSION["plan_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos se han actualizado.</div>";
?>