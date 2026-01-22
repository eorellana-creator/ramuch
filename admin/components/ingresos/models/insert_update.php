<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];
$nombre_usuario_sistema = $_SESSION["usuario_nombre"];

$nombre 			= $_POST['nombre'];
$glosa		        = $_POST['glosa'];
$fecha 				= $_POST['fecha'];
$medio				= $_POST['medio'];
$monto				= $_POST['monto'];
$observacion		= $_POST['observacion'];
$token				= @$_POST['token'];
$tipo_ingreso       = $_POST['tipo_ingreso'];

$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");
$mysql 		= new mysql;
$mysql->connect();
$hoy 	= date("Y-m-d");

$id_participante = 0;
$nombre_sistema = explode("|", $nombre);

if(is_numeric($nombre_sistema[0])) {
    $id_participante = $nombre_sistema[0];
    $nombre = $nombre_sistema[1];
}

if($fecha=="")
$fecha = $hoy;

if($token==""){
	$token_nuevo = md5(rand(99999, 99999999).$nombre.date("Y m d H s").$glosa);
	$fecha_ingreso = $hoy;
	$aleatorio = rand(11111,99999999);

	$sql 	= $mysql->query("INSERT INTO cuenta_maestra (id_usuario_sistema, nombre_usuario_sistema, id_usuario_movimiento, nombre,  fecha, tipo, sub_cuenta, glosa, observacion, medio, id_transaccion, documento_respaldo, monto, estado, token)
												VALUES ( '$id_usuario', '$nombre_usuario_sistema',     '$id_participante',       '$nombre', '$fecha', 'ingreso', '$tipo_ingreso','$glosa','$observacion','$medio','$aleatorio $hoy $id_usuario $monto','','$monto','activo', '$token_nuevo'  ) ;");


	$ultimo_id = $mysql->ultimo_id(); 

	$token = $token_nuevo;

}else{
	if($token!=""){
	$sql 	= $mysql->query("UPDATE cuenta_maestra SET  glosa='$glosa', fecha='$fecha' , monto='$monto', medio='$medio', observacion='$observacion', sub_cuenta='$tipo_ingreso' WHERE token ='$token';");
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

				move_uploaded_file($_FILES["archivo"]["tmp_name"], "../../../images/ingresos/" . $doc_ima_fisico);


			}
}

if($doc_ima_fisico!="")
$sql 	= $mysql->query("UPDATE cuenta_maestra SET  documento_respaldo='$doc_ima_fisico' WHERE token ='$token';");

$_SESSION["ingreso_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos del ingreso se han guardado.</div>";

echo "|$token|";
?>