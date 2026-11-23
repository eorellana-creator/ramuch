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

// Intranet privada: Directiva (rol Administrador de Socios) y desarrollador.
$id_usuario_actual = (int)($_SESSION["usuario_id"] ?? 0);
$acceso_intranet = ($id_usuario_actual === 1);
if (!$acceso_intranet && $id_usuario_actual > 0) {
	$sqlIntranet = $mysql->query("SELECT r.nombre FROM usuario u INNER JOIN rol r ON r.id_rol=u.id_rol WHERE u.id_usuario='$id_usuario_actual' AND u.estado='Vigente' LIMIT 1;");
	$rolIntranet = $mysql->f_obj($sqlIntranet);
	$acceso_intranet = $rolIntranet && trim(strtolower($rolIntranet->nombre)) === 'administrador de socios';
}

if ($acceso_intranet) {
	$lista_menu .= "
	<li class='nav-item'>
	<a class='nav-link' href='index.php?component=intranet&view=dashboard'>
	  <i class='fas fa-network-wired'></i> Intranet</a>
	</li>
	";
}

if($where!=""){
$sql2 	= $mysql->query("SELECT id_menu, nombre, url, icono FROM menu $where AND tipo_menu='1' ORDER BY orden ;");

while($result2 = $mysql->f_obj($sql2)){
 $lista_menu = $lista_menu . "
 <li class='nav-item'>
 <a class='nav-link' href='$result2->url'>
   <i class='$result2->icono'></i> $result2->nombre</a>
 </li>
 ";

 if(
	strpos($result2->url, "component=equipo") !== false
	&& strpos($result2->url, "view=equipo_list") !== false
 ){
	$lista_menu = $lista_menu . "
	<li class='nav-item'>
	<a class='nav-link' href='index.php?component=equipo_inventario&view=inventario'>
	  <i class='fas fa-boxes'></i> Inventario de Equipo</a>
	</li>
	";
 }
}
}

 
?>
