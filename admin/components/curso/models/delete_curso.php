<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token				= $_POST['token'];

$config 	= new Config;


$mysql 		= new mysql;
$mysql->connect();

$sql 			= $mysql->query("SELECT id_curso FROM curso WHERE token='$token';");
$result 		= $mysql->f_obj($sql);
$id_curso		= @$result->id_curso;

$sql2 			    = $mysql->query("SELECT id_curso_participantes FROM curso_participantes WHERE id_curso='$id_curso';");
$tiene_participantes    = $mysql->f_num($sql2);
$result2 		    = $mysql->f_obj($sql2);
$id_curso_participantes = @$result2->id_curso_participantes;

if($tiene_participantes==0){
    $sql3   = $mysql->query("DELETE FROM curso WHERE id_curso='$id_curso';");
    echo "|1|";
}else{
    echo "|0|";
}//if($tiene_participantes==0)


?>