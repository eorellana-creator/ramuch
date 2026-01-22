<?php
$mysql 		= new mysql;
$mysql->connect();

$usuario_token  = $_SESSION["usuario_token"]; 
$usuario_rol    = $_SESSION["usuario_rol"];
$company_token	= $_SESSION["company_token"]; 

$sql 	= $mysql->query("SELECT id_menu FROM menu_rol WHERE id_rol = '$usuario_rol' AND activo='1';");



$where = "";
while($result = $mysql->f_obj($sql)){
 $where = $where . " OR id_menu = '$result->id_menu' ";
}

if($where!=""){
	$where = " WHERE (id_menu = '9999999999' $where ) AND activo ='1' ";

}

$lista_menu = "";

if($where!=""){
$sql2 	= $mysql->query("SELECT id_menu, nombre, url, icono FROM menu $where AND tipo_menu='1' ORDER BY orden ;");

while($result2 = $mysql->f_obj($sql2)){
 $lista_menu = $lista_menu . "
 <li class='nav-item'>
 <a class='nav-link' href='$result2->url'>
   <i class='$result2->icono'></i> $result2->nombre</a>
 </li>
 ";
}
}

 
?>