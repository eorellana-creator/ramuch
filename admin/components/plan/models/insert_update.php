<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];


$nombre		        = $_POST['nombre'];
$tipo 				= $_POST['tipo'];
$valor				= $_POST['valor'];
$dia				= $_POST['dia'];
$pp			    	= $_POST['pp'];

$token				= $_POST['token'];

$config 	= new Config;


$mysql 		= new mysql;
$mysql->connect();

$token_nuevo = md5(rand(99999, 99999999).$rut.date("Y m d H s"));



if($token==""){

$hoy 	= date("Y-m-d i s");

$token_nuevo = md5( $hoy . $nombre . rand(999,999999) . $valor );

$clave = md5($token_nuevo);

$sql 	= $mysql->query("INSERT INTO plan_matricula (nombre, dia_pago_1, tipo, valor, publica_privada, activa, token) 
		                 	          VALUES('$nombre', '$dia', '$tipo', '$valor', '$pp', '1', '$token_nuevo' ) ;");

$ultimo_id = $mysql->ultimo_id(); 
echo "|$token_nuevo|";

}//if($token=="")




if($token!=""){
$sql 	= $mysql->query("UPDATE plan_matricula SET nombre='$nombre', dia_pago_1='$dia', tipo='$tipo', valor='$valor', publica_privada='$pp' WHERE token ='$token';");

echo "|$token|";
}
 


$_SESSION["plan_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos se han actualizado.</div>";

?>