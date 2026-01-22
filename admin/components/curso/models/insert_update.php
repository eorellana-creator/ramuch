<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];


$nombre		        = $_POST['nombre'];
$fechaInicio 		= $_POST['fechaInicio'];
$fechaFin			= $_POST['fechaFin'];
$tipo				= $_POST['tipo'];
$precio			    = $_POST['precio'];
$capacidad			= $_POST['capacidad'];


$token				= @$_POST['token'];



$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();




if($fechaInicio=="")
$fechaMantencion = "0000-00-00";

if($fechaFin=="")
$fechaCompra = "0000-00-00";


$hoy 	= date("Y-m-d");

if($token==""){



$token_nuevo = md5(rand(99999, 99999999).$nombre.date("Y m d H s"));

$fecha_ingreso = $hoy;



$sql 	= $mysql->query("INSERT INTO curso (nombre, tipo, fecha_inicio, fecha_fin, precio, capacidad,  token) 
		                 	     VALUES('$nombre', '$tipo', '$fechaInicio', '$fechaFin', '$precio', '$capacidad',  '$token_nuevo' ) ;");

$ultimo_id = $mysql->ultimo_id(); 

$token = $token_nuevo;


}else{


if($token!=""){
$sql 	= $mysql->query("UPDATE curso SET  nombre='$nombre', tipo='$tipo', nombre='$nombre' , fecha_inicio='$fechaInicio', fecha_fin='$fechaFin', precio='$precio', capacidad='$capacidad'  WHERE token ='$token';");


}
}







$_SESSION["curso_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos del curso se han guardado.</div>";


echo "|$token|";



?>