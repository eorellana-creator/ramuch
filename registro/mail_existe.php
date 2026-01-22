<?php
include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

$email			= $_GET['email'];

//Formateo del rut, Ej: 11.111.111-1,  15.223.443-K
//$rut = formatea_rut($rut);



$mysql 		= new mysql;
$mysql->connect();

$existe	= 0;



$sql 	= $mysql->query("SELECT email FROM usuario WHERE email='$email' AND email!=''  ; ");
$existe	= $mysql->f_num($sql);

if($existe>0)
$existe = 1;



echo "|$existe|";
 
?>