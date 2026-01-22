<?php
session_start();
include("../includes/sql_inyection.php");
include("../configuration.php");
include("../includes/conexionMysql.php");
include("../includes/funciones.php");


$id_company 	= @$_SESSION["company_id"];
$token			= $_POST["token"];

$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect();

$sql 	= $mysql->query("DELETE FROM mensaje WHERE token = '$token' AND id_company='$id_company' ;");


?>