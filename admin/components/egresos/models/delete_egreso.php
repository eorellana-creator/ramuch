<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token				= $_POST['token'];

$config 	= new Config;


$mysql 		= new mysql;
$mysql->connect();

$sql 			= $mysql->query("SELECT id_cuenta_maestra, fecha_insercion FROM cuenta_maestra WHERE token='$token';");
$result 		= $mysql->f_obj($sql);
$id_cuenta_maestra		= @$result->id_cuenta_maestra;


//solo se pueden eliminar egresos de 
$hoy = date("Y-m-d");
$date1 = new DateTime($result->fecha_insercion);
$date2 = new DateTime($hoy);
$diff = $date1->diff($date2);
// will output 2 days
$diferencia = $diff->days;



if($diferencia<=2){
    $sql2  = $mysql->query("DELETE FROM cuenta_maestra WHERE token='$token' ;");
}else{
    $sql2  = $mysql->query("UPDATE cuenta_maestra SET estado='eliminado' WHERE token='$token' ;");
}//if($diferencia<=2)



echo "|1|";




?>