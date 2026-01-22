<?php
session_start();
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");


$usuario	    = $_POST['usuario'];

$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect();

$id_empleado = explode("|",$usuario);
$id_empleado = $id_empleado[0];

$sql 	= $mysql->query("SELECT email FROM empleados WHERE id_empleado = '$id_empleado';");

$result = $mysql->f_obj($sql);

echo "xxx,".$result->email."xxx,";


?>