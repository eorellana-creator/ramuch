<?php
session_start();
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");


$token	    = $_POST['token'];
$id_company	= $_SESSION["company_id"];

$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect();


//$sql 	= $mysql->query("DELETE FROM usuario WHERE token = '$token'  AND id_company = '$id_company';");
$sql 	= $mysql->query("UPDATE usuario set estado='Eliminado' WHERE token = '$token'  AND id_company = '$id_company';");

?>