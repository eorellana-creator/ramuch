<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];


$nombre		        = $_POST['nombre'];
$rut 				= $_POST['rut'];
$fechaNacimiento	= $_POST['fechaNacimiento'];
$mail				= $_POST['mail'];
$fono			    = $_POST['fono'];
$direccion			= $_POST['direccion'];

$nombre_contacto	= $_POST['nombreContacto'];
$fono_contacto		= $_POST['fonoContacto'];
$mail_contacto		= $_POST['mailContacto'];


$carrera	        = $_POST['carrera'];
$institucion		= $_POST['institucion'];
$estado_estudios	= $_POST['estado_estudios'];
$anos_cursados		= $_POST['anos_cursados'];
$anos_carrera	    = $_POST['anos_carrera'];
$fechaCertificado	= $_POST['fechaCertificado'];
 

$sangre			    = $_POST['sangre'];
$enfermedades	    = $_POST['enfermedades'];
$operaciones	    = $_POST['operaciones'];
$alergias	        = $_POST['alergias'];
$medicamentos		= $_POST['medicamentos'];
$diagnosticos	    = $_POST['diagnosticos'];
$prevision	        = $_POST['prevision'];
$preferencia	    = $_POST['preferencia'];
$donante			= $_POST['donante'];

$tipo				= @$_POST['tipoInscripcion'];


$token				= $_POST['token'];

//Formateo del rut, Ej: 11.111.111-1,  15.223.443-K
$rut = formatea_rut($rut);




$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

$token_nuevo = md5(rand(99999, 99999999).$rut.date("Y m d H s"));

$patronIMG 	= "%\.(pdf|PDF|jpg|JPG|jpeg|JPEG|png|PNG|doc|DOC|docx|DOCX|xls|XLS|xlsx|XLSX)$%i";

$fis_arch = $_FILES["archivo"]["name"];
$aleatorio = rand(9999,99999999);
$doc_ima_fisico = "";

if ($fis_arch!="") {
	preg_match($patronIMG, $fis_arch) == 1 ? $archivoValido ="S": $archivoValido ="N";
			if($archivoValido == "S"){
				$doc_ima = $fis_arch;
				$doc_ima_fisico =  date('Ymd_his') . "_$aleatorio." . pathinfo($fis_arch, PATHINFO_EXTENSION);

				move_uploaded_file($_FILES["archivo"]["tmp_name"], "../archivos/" . $doc_ima_fisico);


			}
}


$fecha_incorp = date("Y-m-d");
if($fechaCertificado!=""){
	//$fechaCertificado = fecha_normal_mysql($fechaCertificado);
}else{
    $fechaCertificado = "0000-00-00";
}


if($fechaNacimiento!=""){
	//$fechaNacimiento = fecha_normal_mysql($fechaNacimiento);
	}else{
		$fechaNacimiento = "0000-00-00";
	}




if($token==""){

$hoy 	= date("Y-m-d");
$hora	= date("H:i:s");
$ip 	= getRealIP();

$token_nuevo = md5( $hoy . $hora . rand(999,999999) . $nombre );

$clave = md5($token_nuevo);

$sql 	= $mysql->query("INSERT INTO usuario (id_company, id_rol, nombre_usuario, email, password, fecha_registro, hora_registro, fecha_actualizacion, ip_registro, estado, token) 
		                 	          VALUES('$id_company', '3', '$nombre', '$mail', '$clave', '$hoy', '$hora', '$hoy', '$ip', 'Vigente', '$token_nuevo' ) ;");


$ultimo_id = $mysql->ultimo_id(); 

 
$sql 	= $mysql->query("INSERT INTO perfil ( id_usuario, nombre, fono,    mail,    rut, fecha_nacimiento, certificado_estudios, certificado_vencimiento, carrera, institucion,       anos_cursados, anos_carrera, estado_estudios, direccion, nombre_contacto,  mail_contacto, fono_contacto, tipo_sangre,       enfermedades, operaciones,       alergias, medicamentos, otros_diagnosticos, prevision_salud, preferencia_atencion, donante )  VALUES (   '$ultimo_id', '$nombre', '$fono', '$mail', '$rut', '$fechaNacimiento', '$doc_ima_fisico', '$fechaCertificado', '$carrera', '$institucion', '$anos_cursados', '$anos_carrera', '$estado_estudios', '$direccion', '$nombre_contacto', '$mail_contacto', '$fono_contacto',  '$sangre', '$enfermedades', '$operaciones', '$alergias', '$medicamentos', '$diagnosticos', '$prevision', '$preferencia', '$donante');");

$ultimo_id_perfil = $mysql->ultimo_id(); 
echo "UPDATE perfil SET tipo_inscripcion='$tipo', id_plan_matricula='$tipo' WHERE id_perfil ='$ultimo_id_perfil' ;";
if($tipo!=""  && $ultimo_id_perfil!="" ){

	
	$sql 	= $mysql->query("UPDATE perfil SET tipo_inscripcion='$tipo', id_plan_matricula='$tipo' WHERE id_perfil ='$ultimo_id_perfil' ;");
}

 

echo "|$token_nuevo|";

}


else{

$hoy=date("Y-m-d");



if($token!=""){
$sql 	= $mysql->query("UPDATE usuario SET  nombre_usuario='$nombre', email='$mail', fecha_actualizacion='$hoy' WHERE token ='$token';");

echo "UPDATE usuario SET  nombre_usuario='$nombre', email='$mail', fecha_actualizacion='$hoy' WHERE token ='$token'; <br>";

$sql3 	= $mysql->query("SELECT id_usuario FROM usuario WHERE token ='$token';");
$result = $mysql->f_obj($sql3);
$id_usuario = $result->id_usuario;

$sql 	= $mysql->query("UPDATE perfil SET  nombre='$nombre', fono='$fono', mail='$mail', rut='$rut', fecha_nacimiento='$fechaNacimiento', certificado_vencimiento='$fechaCertificado', carrera='$carrera', institucion='$institucion', anos_cursados='$anos_cursados', anos_carrera='$anos_carrera', estado_estudios='$estado_estudios', direccion='$direccion', nombre_contacto='$nombre_contacto',  mail_contacto='$mail_contacto', fono_contacto='$fono_contacto', tipo_sangre='$sangre', enfermedades='$enfermedades', operaciones='$operaciones', alergias='$alergias', medicamentos='$medicamentos', otros_diagnosticos='$diagnosticos', prevision_salud='$prevision', preferencia_atencion='$preferencia', donante='$donante' WHERE id_usuario ='$id_usuario';");

if($doc_ima_fisico!="")
$sql 	= $mysql->query("UPDATE perfil SET  certificado_estudios='$doc_ima_fisico' WHERE id_usuario ='$id_usuario';");


//echo "UPDATE perfil SET  nombre='$nombre', fono='$fono', mail='$mail', rut='$rut', fecha_nacimiento='$fechaNacimiento', certificado_vencimiento='$fechaCertificado', carrera='$carrera', institucion='$institucion', anos_cursados='$anos_cursados', anos_carrera='$anos_carrera', estado_estudios='$estado_estudios', direccion='$direccion', nombre_contacto='$nombre_contacto',  mail_contacto='$mail_contacto', fono_contacto='$fono_contacto', tipo_sangre='$sangre', enfermedades='$enfermedades', operaciones='$operaciones', alergias='$alergias', medicamentos='$medicamentos', otros_diagnosticos='$diagnosticos', prevision_salud='$prevision', preferencia_atencion='$preferencia', donante='$donante' WHERE id_usuario ='$id_usuario';";

echo "|$token|";
}
}


$_SESSION["socio_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos se han actualizado.</div>";

?>