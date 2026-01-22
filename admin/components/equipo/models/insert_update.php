<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];


$nombre		        = $_POST['nombre'];
$id_unico	       	= $_POST['idunico'];
$fechaCompra 		= $_POST['fechaCompra'];
$fechaMantencion	= $_POST['fechaMantencion'];
$estado				= $_POST['estado'];
$marca			    = $_POST['marca'];
$talla				= $_POST['talla'];
$observacion		= $_POST['observacion'];
$descripcion		= $_POST['descripcion'];


$token				= @$_POST['token'];



$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();




if($fechaMantencion=="")
$fechaMantencion = "0000-00-00";

if($fechaCompra=="")
$fechaCompra = "0000-00-00";


$hoy 	= date("Y-m-d");

if($token==""){



$token_nuevo = md5(rand(99999, 99999999).$nombre.date("Y m d H s"));

$fecha_ingreso = $hoy;


$sql 	= $mysql->query("INSERT INTO equipo (fecha_ingreso, fecha_compra, fecha_mantencion, nombre, id_unico, marca, descripcion, estado, observacion, talla, imagen, token) 
		                 	     VALUES('$fecha_ingreso', '$fechaCompra', '$fechaMantencion', '$nombre', '$id_unico', '$marca', '$descripcion', '$estado', '$observacion', '$talla', '', '$token_nuevo' ) ;");


$ultimo_id = $mysql->ultimo_id(); 

$token = $token_nuevo;


}else{


if($token!=""){
$sql 	= $mysql->query("UPDATE equipo SET  fecha_compra='$fechaCompra', fecha_mantencion='$fechaMantencion', nombre='$nombre', id_unico='$id_unico' , marca='$marca', descripcion='$descripcion', estado='$estado', observacion='$observacion', talla='$talla' WHERE token ='$token';");


}
}





$patronIMG 	= "%\.(jpg|JPG|jpeg|JPEG|png|PNG)$%i";

$fis_arch = $_FILES["archivo"]["name"];
$aleatorio = rand(9999,99999999);
$doc_ima_fisico = "";

if ($fis_arch!="") {
	preg_match($patronIMG, $fis_arch) == 1 ? $archivoValido ="S": $archivoValido ="N";
			if($archivoValido == "S"){
				$doc_ima = $fis_arch;
				$doc_ima_fisico =  date('Ymd_his') . "_$aleatorio." . pathinfo($fis_arch, PATHINFO_EXTENSION);

				move_uploaded_file($_FILES["archivo"]["tmp_name"], "../../../images/equipo/" . $doc_ima_fisico);


			}
}

if($doc_ima_fisico!="")
$sql 	= $mysql->query("UPDATE equipo SET  imagen='$doc_ima_fisico' WHERE token ='$token';");



$_SESSION["equipo_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos del equipo se han guardado.</div>";


echo "|$token|";



?>