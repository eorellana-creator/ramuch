<?php
session_start();
include("../includes/sql_inyection.php");
include("../configuration.php");
include("../includes/conexionMysql.php");
include("../includes/funciones.php");


$id_company 	= @$_SESSION["company_id"];

$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect();

$id_usuario = $_SESSION["usuario_id"];
$id_local 	= $_SESSION["usuario_local"];

$hoy = date("Y-m-d");
$tres_dias = date("Y-m-d",strtotime($hoy."- 3 days"));

$sql2 	= $mysql->query("DELETE FROM mensaje WHERE fecha < '$tres_dias' ;");

 
$sql 	= $mysql->query("SELECT * FROM mensaje WHERE id_usuario='$id_usuario' AND id_local='$id_local' AND id_company='$id_company' ORDER BY id_mensaje DESC ;");

 
$mensajes = "";
$i=100;
$aleatorio = rand(999, 9999999);

while($result = $mysql->f_obj($sql)){
	
	$i++;
	
	$result->fecha = fecha_mysql_a_normal($result->fecha);
	
	$mensajes = $mensajes . "
	<div class=\"mensjeria message$i$aleatorio\">
	<a href=\"$result->link\" class=\"mensajeria-link\"><img src=\"images/icon-mensaje/icon-campana.png\" class=\"mensaje-imagen\" ></a>
	<div class=\"mensaje-titulo\"><a href=\"$result->link\" class=\"mensajeria-link\">$result->asunto</a></div><div class=\"mensaje-close\"><a href=\"javascript: closemessage('$result->token','message$i$aleatorio')\"><i class=\"fa fa-window-close\" aria-hidden=\"true\"></i></a></div>
	<div class=\"mensaje-texto\"><a href=\"$result->link\" class=\"mensajeria-link\">$result->mensaje</a></div>
	<div class='mensaje-hora'>$result->hora ($result->fecha)</div>
	</div>
	";
	
$sql2 	= $mysql->query("DELETE FROM mensaje WHERE token = '$result->token' AND id_company='$id_company' ;");	

	
}//while

echo "xxx,".$mensajes."xxx,";


?>