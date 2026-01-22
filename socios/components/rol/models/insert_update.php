<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$nombre	= $_POST['nombre'];
$token	= $_POST['token'];

$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

$token_nuevo = md5(rand(99999, 99999999).$nombre.date("Y m d H s"));

if($token=="" ){

$sql 	= $mysql->query("INSERT INTO rol (nombre, token) VALUES ('$nombre', '$token_nuevo');");

echo "insert";

}else{  

if($token!="" ){
	
$sql 	= $mysql->query("UPDATE rol SET nombre = '$nombre', token='$token_nuevo'  WHERE token ='$token' ;");

echo "update";

}
}


//*******************************************************************************************************

$sql 	= $mysql->query("SELECT * FROM rol WHERE token ='$token_nuevo' ;");
$result = $mysql->f_obj($sql);

$id_rol_usuario = @$result->id_rol;

$sql2 	= $mysql->query("SELECT id_menu, nombre, token FROM menu WHERE activo ='1' ORDER BY nombre ASC;");

$sql4	= $mysql->query("DELETE FROM menu_rol WHERE id_rol='$id_rol_usuario' AND activo ='1';");



while($result2 = $mysql->f_obj($sql2)){
		
	
	
	$token_permiso = $result2->token;
	
	if( @$_POST["permiso_".$token_permiso]=="1"  )
	$sql4	= $mysql->query("INSERT INTO menu_rol (id_menu, id_rol, activo) VALUES ('$result2->id_menu','$id_rol_usuario','1') ;");
	
}//while($result2 = $mysql->f_obj($sql2))

echo " ,xxx$token_nuevo,xxx";



?>