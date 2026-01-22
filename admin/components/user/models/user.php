<?php

//error_reporting(E_ALL);
ini_set('display_errors', '1');

//@include("../../includes/sql_inyection.php");

$mysql->connect();

$id_company = $_SESSION["company_id"];

$token	= @$_GET["token"];
$sql 	= $mysql->query("SELECT * FROM usuario WHERE token ='$token' AND id_company = '$id_company' ;");
$result = $mysql->f_obj($sql);

//***********************************************************************************************************
/* Kop
$local = "";
$usuario_rol    = $_SESSION["usuario_rol"];

$sql2 	= $mysql->query("SELECT id_local, nombre, token from local WHERE id_company = '$id_company' ORDER BY nombre ASC;");
$select_local = "";
$todos		  = " selected ";

while($result2 = $mysql->f_obj($sql2)){

	$select_local = "";
	if(@$result->id_local == @$result2->id_local ){
		$select_local = " selected ";
		$todos		  = "";
	}

	$local = $local . "<option value='$result2->token' $select_local >$result2->nombre</option>";
}//while($result = $mysql->f_obj($sql))

$local = "<select id='local' name='local' class='form-control' required >
$local
<option value='0' $todos >Todos los Locales</option>
</select>";
*/ //Kop

//************************************************************************************************************

$rol = "";
$sql2 	= $mysql->query("SELECT id_rol, nombre, token from rol ORDER BY nombre ASC;");
$select_rol = "";

while($result2 = $mysql->f_obj($sql2)){

$select_rol = "";
if(@$result->id_rol == @$result2->id_rol )
$select_rol = " selected ";


$rol = $rol . "<option value='$result2->token' $select_rol >$result2->nombre</option>";
}//while($result = $mysql->f_obj($sql))

$rol = "<select id='rol' name='rol' class='form-control' required >
$rol
</select>";

//***************************************************************************************************************

$pass_required = " required ";
$pass_placeholder = "Ingrese la contraseña para acceso al sistema (obligatorio)";
$ingrese_nuevo = " (debe tener al menos 6 caracteres)";

if($token!=""){
	$pass_required = "";
	$pass_placeholder = "*************";
	$ingrese_nuevo = " (ingréselo solo si quiere cambiar la contraseña actual)";
}//if($token!="")
	
//*****************************************************************************************************************


?>