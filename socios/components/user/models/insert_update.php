<?php
session_start();
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");
 


$nombre			= $_POST['nombre'];
$email			= $_POST['email'];
$token_rol		= $_POST['rol'];
$clave			= $_POST['clave'];
$token			= @$_POST['token'];

 
$id_empleado 	= "";
 

if($clave!="")
$clave	= md5($clave);

$id_company 	= $_SESSION["company_id"];

$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

$token_nuevo	= md5(rand(99999, 99999999).$nombre.date("Y m d H s"));

if($_SESSION["usuario_token"] == $token)
$_SESSION["usuario_token"] = $token_nuevo;



$sql 	= $mysql->query("SELECT id_rol FROM rol WHERE  token = '$token_rol';");
$result = $mysql->f_obj($sql);
$id_rol = $result->id_rol;

	$fecha 	= date("Y-m-d");
	$hora	= date("H:i:s");
	$ip = getRealIP();


if($token=="" && $nombre!="" && $email!="" ){
	

$sql 	= $mysql->query("INSERT INTO usuario (id_company, id_empleado, id_rol, nombre_usuario, email, password, fecha_registro, hora_registro, ip_registro, estado, token) VALUES('$id_company', '$id_empleado', '$id_rol', '$nombre', '$email', '$clave', '$fecha', '$hora', '$ip', 'Vigente', '$token_nuevo' ) ;");
$ultimo_id = $mysql->ultimo_id(); 


echo ",xxx,$token_nuevo,xxx,";

}//if($token=="" && $nombre!="")

//***********************************************************************************************************

if($token!="" && $nombre!="" && $email!="" ){
	
	
		if($clave!="")
		$clave = ", password='$clave'";
	
	
	$sql = $mysql->query("UPDATE usuario SET id_rol='$id_rol', id_empleado='$id_empleado', nombre_usuario='$nombre', email='$email' $clave, fecha_actualizacion='$fecha', hora_actualizacion='$hora', ip_actualizacion='$ip', estado='Vigente' WHERE token='$token' ;");
	

echo ",xxx,$token,xxx,";
	
	
}//if($token!="" && $nombre!=""){


?>