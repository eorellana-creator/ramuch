<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token		= $_POST['token'];
$config 	= new Config;
$mysql 		= new mysql;
$mysql->connect();

$sql 			= $mysql->query("SELECT id_deuda, fecha_insercion FROM deudas WHERE token='$token';");
$result 		= $mysql->f_obj($sql);
$id_deuda		= @$result->id_deuda;

//solo se pueden eliminar egresos de 
$hoy = date("Y-m-d");
$date1 = new DateTime($result->fecha_insercion);
$date2 = new DateTime($hoy);
$diff = $date1->diff($date2);
// will output 2 days
$diferencia = $diff->days;

$sql2  = $mysql->query("UPDATE deudas SET estado='eliminada', fecha_modificacion = '$hoy' WHERE token='$token' ;");


echo "|1|";

?>