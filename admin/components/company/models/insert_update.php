<?php
session_start();
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");
include("../../../includes/resize.php");

//error_reporting(E_ALL | E_STRICT);


$nombre_fantasia	= $_POST['nombre_fantasia'];
$razon_social		= $_POST['razon_social'];
$rut				= $_POST['rut'];
$email				= $_POST['email'];
$telefono_1			= $_POST['telefono_1'];
$telefono_2			= $_POST['telefono_2'];
$celular			= $_POST['celular'];
$region				= $_POST['region'];
$ciudad				= $_POST['ciudad'];
$comuna				= $_POST['comuna'];
$direccion			= $_POST['direccion'];
$web				= $_POST['web'];
$facebook			= $_POST['facebook'];
$instagram			= $_POST['instagram'];
$twitter			= $_POST['twitter'];


$config 			= new Config;

$id_company = $_SESSION["company_id"];

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

$patronIMG 	= "%\.(jpg|PNG|png|JPG|JPEG|jpeg)$%i";

$sql 	= $mysql->query("UPDATE company SET nombre_fantasia = '$nombre_fantasia', razon_social='$razon_social', rut='$rut', email='$email', telefono_1='$telefono_1', telefono_2='$telefono_2', celular='$celular', region='$region', ciudad='$ciudad', comuna='$comuna', direccion='$direccion', web='$web', facebook='$facebook', instagram='$instagram', twitter='$twitter' WHERE id_company ='$id_company' ;");


$fis_arch = $_FILES["imagen"]["name"];
if ($fis_arch!="") {
	preg_match($patronIMG, $fis_arch) == 1 ? $archivoValido ="S": $archivoValido ="N";
	if($archivoValido == "S"){
		$doc_ima = $fis_arch;
		$doc_ima_fisico =  date('Ymd_his') . "_ima$id_company." . pathinfo($fis_arch, PATHINFO_EXTENSION);


move_uploaded_file($_FILES["imagen"]["tmp_name"], "../../../images/company/" . $doc_ima_fisico);

$sql 	= $mysql->query("SELECT logo FROM company  WHERE id_company = '$id_company'  ;");
$result = $mysql->f_obj($sql);
$imagen_anterior = $result->logo;
@unlink("../../../images/company/".$imagen_anterior);

$thumb=new thumbnail("../../../images/company/" . $doc_ima_fisico);
$thumb->size_height(70);
//$thumb->size_width(70);
$thumb->jpeg_quality(100);
$thumb->save("../../../images/company/" . $doc_ima_fisico);

		

		
$sql = $mysql->query("UPDATE company SET logo = '$doc_ima_fisico' WHERE id_company = '$id_company' ;");		
	}
}



echo "update";



?>