<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token				= $_POST['token'];

$config 	= new Config;


$mysql 		= new mysql;
$mysql->connect();

$sql 			= $mysql->query("SELECT id_equipo FROM equipo WHERE token='$token';");
$result 		= $mysql->f_obj($sql);
$id_equipo		= @$result->id_equipo;

$sql2 			    = $mysql->query("SELECT id_equipo_prestamo FROM equipo_prestamo WHERE id_equipo='$id_equipo';");
$tiene_historial    = $mysql->f_num($sql2);
$result2 		    = $mysql->f_obj($sql2);
$id_equipo_prestamo = @$result2->id_equipo_prestamo;

if($tiene_historial==0){
    $sql3   = $mysql->query("DELETE FROM equipo WHERE id_equipo='$id_equipo';");
    echo "|1|";
}else{
    echo "|0|";
}//if($tiene_historial==0)


?>