<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];
$nombre_usuario_sistema = $_SESSION["usuario_nombre"];


$observacion				= $_POST['observacion'];
$medio				= $_POST['medio'];
 

$token				= @$_POST['token'];


$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();


$hoy 	= date("Y-m-d");

 
$fecha = $hoy;



$sql 	= $mysql->query("UPDATE deudas SET  observacion='$observacion', estado='pagada' WHERE token ='$token';");


$sql 	= $mysql->query("SELECT * FROM deudas WHERE token ='$token' AND token!='' ;");
$result = $mysql->f_obj($sql);
$subcuenta_deuda = $result->sub_cuenta;
$id_usuario_deuda = $result->id_usuario_deuda;

$token_nuevo = md5( $token . date("Y-m-d h i s") .$result->id_deuda  );



//id_transaccion usaremos el token de curso_participantes
$sql 	= $mysql->query("INSERT INTO cuenta_maestra (id_usuario_sistema, nombre_usuario_sistema, id_usuario_movimiento, nombre,  fecha, tipo,   sub_cuenta,   glosa,         observacion,     medio, id_transaccion, documento_respaldo, monto, estado, token)
											VALUES ( '$id_usuario',   '$nombre_usuario_sistema',     '0',                 '', '$fecha', 'ingreso', 'otros','$result->glosa','$observacion', '$medio','$result->token',          '',        '$result->monto','activo', '$token_nuevo'  ) ;");


$ultimo_id = $mysql->ultimo_id(); 


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
$sql 	= $mysql->query("UPDATE cuenta_maestra SET  documento_respaldo='$doc_ima_fisico' WHERE id_cuenta_maestra ='$ultimo_id';");



$_SESSION["deuda_mensaje"] = "<div class='alert alert-success' role='alert'>La deuda se ha pagado.</div>";

if($subcuenta_deuda=="matricula")
$sql 	= $mysql->query("UPDATE usuario SET  web_matricula_pagada='Si'  WHERE $id_usuario_deuda  ='$id_usuario_deuda ';");

echo "|$token|";



?>