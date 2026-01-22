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

//echo "SELECT id_menu, nombre, url, icono FROM menu $where AND tipo_menu='2' ORDER BY orden ;";

if($where!=""){
$sql2 	= $mysql->query("SELECT id_menu, nombre, url, icono FROM menu $where AND tipo_menu='2' ORDER BY nombre ;");

while($result2 = $mysql->f_obj($sql2)){
 $lista_menu = $lista_menu . "
 				<a class='dropdown-item' href='$result2->url'>
				 <i class='$result2->icono'></i>$result2->nombre
				 </a>
				";
}
}

if($lista_menu!="")
$lista_menu = "<div class='dropdown-header text-center'>
<strong>Configuraciones</strong>
</div>
$lista_menu";


  

?>