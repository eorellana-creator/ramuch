<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");


$token			= $_POST['token'];

$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect();

 
$sql 	= $mysql->query("DELETE FROM comision_prestamo WHERE token = '$token';");

?>