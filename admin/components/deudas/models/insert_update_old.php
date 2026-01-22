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
$observacion		= "";

$token				= @$_POST['token'];


$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();


$hoy 	= date("Y-m-d");

if($fecha=="")
$fecha = $hoy;




$id_participante = 0;

$nombre_sistema = explode("|",$nombre);

if(  is_numeric($nombre_sistema[0] )  ){

	$id_participante = $nombre_sistema[0];
	$nombre = $nombre_sistema[1];

}//if(  is_numeric($nombre)  )




if($token==""){



$token_nuevo = md5(rand(99999, 99999999).$nombre.date("Y m d H s").$glosa);

$fecha_ingreso = $hoy;

$aleatorio = rand(11111,99999999);


$sql 	= $mysql->query("INSERT INTO deudas (id_usuario, id_usuario_deuda, nombre_deudor, sub_cuenta,  fecha, monto,    glosa,  observacion, documento_respaldo, estado, token)
								VALUES ( '$id_usuario',   '$id_participante',   '$nombre',    'otros',  '$fecha', '$monto', '$glosa','$observacion',     '',     'activa', '$token_nuevo'  ) ;");


$ultimo_id = $mysql->ultimo_id(); 

$token = $token_nuevo;


}else{


if($token!=""){
$sql 	= $mysql->query("UPDATE deudas SET  id_usuario_deuda='$id_participante', nombre_deudor='$nombre', glosa='$glosa', fecha='$fecha' , monto='$monto', observacion='$observacion' WHERE token ='$token';");


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

				move_uploaded_file($_FILES["archivo"]["tmp_name"], "../../../images/deudas/" . $doc_ima_fisico);


			}
}

if($doc_ima_fisico!="")
$sql 	= $mysql->query("UPDATE deudas SET  documento_respaldo='$doc_ima_fisico' WHERE token ='$token';");



$_SESSION["deuda_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos de la deuda se han guardado.</div>";


echo "|$token|";



?>