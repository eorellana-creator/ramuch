<?php
include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

$rut			= $_GET['rut'];

//Formateo del rut, Ej: 11.111.111-1,  15.223.443-K
$rut = formatea_rut($rut);
$rut = str_replace(" ","", $rut);


$mysql 		= new mysql;
$mysql->connect();

$existe	= 0;


$sql 	= $mysql->query("SELECT rut FROM perfil WHERE rut='$rut' AND rut!=''  ; ");
$existe	= $mysql->f_num($sql);




if($existe>0)
$existe = 1;



echo "|$existe|";
 
?>