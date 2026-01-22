<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];
$nombre_usuario_sistema = $_SESSION["usuario_nombre"];

$nombre		        = $_POST['nombre'];
$glosa		        = $_POST['glosa'];
$fecha 				= $_POST['fecha'];
$medio				= $_POST['medio'];
$monto				= $_POST['monto'];
$observacion		= $_POST['observacion'];

$token				= @$_POST['token'];


$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();


$hoy 	= date("Y-m-d");

if($fecha=="")
$fecha = $hoy;




if($token==""){



$token_nuevo = md5(rand(99999, 99999999).$nombre.date("Y m d H s").$glosa);

$fecha_ingreso = $hoy;

$aleatorio = rand(11111,99999999);


$sql 	= $mysql->query("INSERT INTO cuenta_maestra (id_usuario_sistema, nombre_usuario_sistema, id_usuario_movimiento, nombre,  fecha, tipo, sub_cuenta, glosa, observacion, medio, id_transaccion, documento_respaldo, monto, estado, token)
											VALUES ( '$id_usuario', '$nombre_usuario_sistema',     '0',             '$nombre', '$fecha', 'egreso', 'egresos','$glosa','$observacion','$medio','$aleatorio $hoy $id_usuario $monto','','$monto','activo', '$token_nuevo'  ) ;");


$ultimo_id = $mysql->ultimo_id(); 

$token = $token_nuevo;


}else{


if($token!=""){
$sql 	= $mysql->query("UPDATE cuenta_maestra SET  nombre='$nombre', glosa='$glosa', fecha='$fecha' , monto='$monto', medio='$medio', observacion='$observacion' WHERE token ='$token';");


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

				move_uploaded_file($_FILES["archivo"]["tmp_name"], "../../../images/egresos/" . $doc_ima_fisico);


			}
}

if($doc_ima_fisico!="")
$sql 	= $mysql->query("UPDATE cuenta_maestra SET  documento_respaldo='$doc_ima_fisico' WHERE token ='$token';");



$_SESSION["egreso_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos del egreso se han guardado.</div>";


echo "|$token|";



?>