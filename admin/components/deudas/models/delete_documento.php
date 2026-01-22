<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token				= $_POST['token'];

$config 	= new Config;


$mysql 		= new mysql;
$mysql->connect();

$sql 			= $mysql->query("SELECT documento_respaldo FROM deudas WHERE token='$token';");
$result 		= $mysql->f_obj($sql);
$archivo		= @$result->documento_respaldo;


$sql2  = $mysql->query("UPDATE deudas SET documento_respaldo='' WHERE token='$token' ;");
@unlink("../../../images/deudas/".$archivo);


    




echo "|1|";




?>