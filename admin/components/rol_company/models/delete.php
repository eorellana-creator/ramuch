<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");


$token			= $_POST['token'];

$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect();


$sql 	= $mysql->query("SELECT id_rol FROM rol_empresa WHERE token='$token' AND token!='';");
$result = $mysql->f_obj($sql);

$id_rol = $result->id_rol;

$sql 	= $mysql->query("DELETE FROM rol_empresa WHERE id_rol = '$id_rol';");

?>