<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];
$sql 	= $mysql->query("SELECT * FROM rol WHERE token ='$token' ;");
$result = $mysql->f_obj($sql);

$id_rol_usuario = @$result->id_rol;


$sql2 	= $mysql->query("SELECT id_menu, nombre, token FROM menu WHERE activo ='1' ORDER BY nombre ASC;");

$permisos = "";
while($result2 = $mysql->f_obj($sql2)){
	
	
	$autorizado = "";
	$sql3	= $mysql->query("SELECT id_menu_rol FROM menu_rol WHERE id_menu='$result2->id_menu' AND id_rol='$id_rol_usuario' AND activo ='1';");
	$autorizado = $mysql->f_num($sql3);
	
	
	if($autorizado>0){
	$autorizado = " checked ";
	}else{
	$autorizado = " ";
	}
	
	$token_permiso = $result2->token;
	$permisos = $permisos . "<input type=\"checkbox\" name=\"permiso_$token_permiso\" value=\"1\" $autorizado > $result2->nombre<br>";
	
}//while($result2 = $mysql->f_obj($sql2))



?>