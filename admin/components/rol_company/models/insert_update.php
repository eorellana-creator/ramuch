<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_rol = $_POST['id_rol'];
$nombre	= $_POST['nombre'];
$token	= $_POST['token'];

$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

$token_nuevo = md5(rand(99999, 99999999).$nombre_usuario.date("Y m d H s"));

if($token=="" || $id_rol=="0" || $id_rol==""){

$sql 	= $mysql->query("INSERT INTO rol_empresa (nombre, token) VALUES ('$nombre', '$token_nuevo');");
echo "insert";

}else{  

if($token!="" && $id_rol!="0" && $id_rol!=""){
	
$sql 	= $mysql->query("UPDATE rol_empresa SET nombre = '$nombre', token='$token_nuevo' WHERE token ='$token' ;");
echo "update";

}
}
?>