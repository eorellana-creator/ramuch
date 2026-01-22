<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];
$nombre_usuario_sistema = $_SESSION["usuario_nombre"];

$observacion		= $_POST['observacion'];
$token				= @$_POST['token'];

$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();
$hoy 	= date("Y-m-d");

$_SESSION["deuda_mensaje"] = "<div class='alert alert-success' role='alert'>La deuda se ha condonado.</div>";
 
$sql 	= $mysql->query("UPDATE deudas SET  observacion='$observacion', estado='condonada', id_usuario = '$id_usuario', fecha_modificacion = '$hoy'  WHERE token ='$token';");

echo "|$token|";

?>