<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token				= $_POST['token'];

$config 	= new Config;


$mysql 		= new mysql;
$mysql->connect();

$sql3   = $mysql->query("DELETE FROM curso_participantes WHERE token='$token';");

    echo "|1|";


?>