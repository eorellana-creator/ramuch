<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$rut			= $_GET['rut'];
$token			= $_GET['token'];

//Formateo del rut, Ej: 11.111.111-1,  15.223.443-K
$rut = formatea_rut($rut);

$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect();

$existe	= 0;


$sql3 	= $mysql->query("SELECT id_usuario FROM usuario WHERE token ='$token';");
$result = $mysql->f_obj($sql3);
$id_usuario = $result->id_usuario;


$sql 	= $mysql->query("SELECT rut FROM perfil WHERE rut='$rut' AND id_usuario!='$id_usuario'  ; ");
$existe	= $mysql->f_num($sql);

if($existe>0)
$existe = 1;



echo "|$existe|";
 
?>