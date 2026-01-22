<?php
ini_set("session.cookie_lifetime","28800");
ini_set("session.gc_maxlifetime","28800");
session_start();
include("includes/sql_inyection.php");
include("configuration.php");
include("includes/conexionMysql.php");
include("includes/listar_directorio.php");
include("includes/funciones.php");



if(@$_SESSION["usuario_valido_socio_ramuch"]!="true"){
include("login/login.php");
}else{
	
	
$config		= new Config;
$mysql 		= new mysql;

$archivos	=  new archivos;


$component 	= @$_GET["component"];
$view		= @$_GET["view"];

//Aquí leemos los archivos js del componente para incluirlos
$incluir_js		= "";
$incluir_css	= "";

$document_js	= @$archivos->listar("components/$component/js/");
if($document_js!="")
foreach($document_js as $archivo_js)
$incluir_js	 	= $incluir_js."<script src=\"components/$component/js/$archivo_js?v=8\"></script>

" ;
$archivos->limpiar();

//Aquí leemos los archivos css del componente para incluirlos
$document_css	= @$archivos->listar("components/$component/css/");
if($document_css!="")
foreach($document_css as $archivo_css)
$incluir_css 	= @$incluir_css."<link href=\"components/$component/css/$archivo_css\" rel=\"stylesheet\">
" ;


include("template/index.php");

	

}//if($_SESSION["usuario_valido_socio_ramuch"]!="true")
?>