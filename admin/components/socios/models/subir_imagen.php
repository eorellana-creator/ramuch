<?php
session_start();
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token				= $_POST['token'];
 

$nombre_usuario	= $_SESSION["usuario_nombre"];
//$id_usuario		= $_SESSION["usuario_id"];	


$mysql 		= new mysql;
$mysql->connect();





$patronIMG 	= "%\.(png|PNGjpg|JPG|jpeg|JPEG|gif|GIF)$%i";

$fis_arch = $_FILES["foto"]["name"];
$aleatorio = rand(9999,99999999);

if ($fis_arch!="") {
	preg_match($patronIMG, $fis_arch) == 1 ? $archivoValido ="S": $archivoValido ="N";
			if($archivoValido == "S"){
				$doc_ima = $fis_arch;
				$doc_ima_fisico =  date('Ymd_his') . "_$aleatorio." . pathinfo($fis_arch, PATHINFO_EXTENSION);

				move_uploaded_file($_FILES["foto"]["tmp_name"], "../../../images/img_perfil/" . $doc_ima_fisico);


			}
}

 
$sql3 	= $mysql->query("SELECT id_usuario FROM usuario WHERE token ='$token';");

$result = $mysql->f_obj($sql3);

$id_usuario = $result->id_usuario;

$sql 	= $mysql->query("UPDATE perfil SET  img_perfil='$doc_ima_fisico' WHERE id_usuario ='$id_usuario';");
 
echo "|$doc_ima_fisico|";

?>