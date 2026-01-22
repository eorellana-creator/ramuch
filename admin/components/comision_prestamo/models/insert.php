<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_usuario	= $_POST['usuario'];
 

$config 	= new Config;
 

$mysql 		= new mysql;
$mysql->connect();

$token_nuevo = md5(rand(99999, 99999999).$id_usuario.date("Y m d H s"));

 

$sql 	= $mysql->query("INSERT INTO comision_prestamo (id_usuario, token) VALUES ('$id_usuario', '$token_nuevo');");

 
 

echo " ||";



?>