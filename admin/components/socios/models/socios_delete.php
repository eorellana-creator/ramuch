<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token				= $_POST['token'];

$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$hoy = date ("Y-m-d");

$mysql 		= new mysql;
$mysql->connect();

$sql 			= $mysql->query("SELECT id_cliente FROM cliente WHERE token='$token';");
$result 		= $mysql->f_obj($sql);
$id_cliente		= @$result->id_cliente;


/*
$sql 			= $mysql->query("SELECT id_presupuesto FROM presupuesto WHERE id_empresa='$id_empresa';");
$result 		= $mysql->f_obj($sql);
$id_presupuesto	 	= @$result->id_presupuesto;

if($id_presupuesto==""){
	
}


*/

if(1==1){
	
$sql 	= $mysql->query("DELETE FROM clientes WHERE token='$token';");

echo "xxx,okxxx,xxx,  ";
}


else{

echo "xxx,noxxx,xxx,  ";

}

?>