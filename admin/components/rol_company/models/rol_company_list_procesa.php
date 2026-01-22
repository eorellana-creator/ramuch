<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

@$draw			= $_POST["draw"];
@$inicio		= $_POST["start"];
@$fin			= $_POST["length"];
@$busqueda 		= $_POST["search"]["value"];
@$orden 		= $_POST["order"][0]["column"];
@$direccion 	= $_POST["order"][0]["dir"];


if($busqueda!="")
$busqueda = " WHERE nombre LIKE '%$busqueda%' ";

if($inicio=="")
$inicio = 0;
 
$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect(); 		

$usuarios	= "";
$datos		= "";

$orderby = " ORDER BY nombre asc";
if($orden==0)
$orderby = " ORDER BY nombre $direccion ";

$sql 	= $mysql->query("SELECT id_rol, nombre, token from rol_empresa $busqueda $orderby LIMIT $inicio,$fin ;");

$sql2 	= $mysql->query("SELECT id_rol, nombre, token from rol_empresa $busqueda ;");

$cantidad_registros = $mysql->f_num($sql2);



$coma = 0;
$signo_coma = "";
while($result = $mysql->f_obj($sql)){

if($coma==1)
$signo_coma = ",";

$coma = 1;

/* $ver = "";
$ver = "<a href='index.php?component=profile&view=profile&token=$result->token'><span class='glyphicon glyphicon-search'></span></a>";
 */
 
$eliminar = "";
$eliminar = "<a href='javascript: deleteRolCompany(\\\"$result->token\\\");'><span class='glyphicon glyphicon-trash'></span></a>";

$datos = $datos ."
     $signo_coma
	 [
      \"$result->id_rol\",
      \"$result->nombre\",
      \"$eliminar\"
    ]";
	
}//while($result2 = $mysql->f_obj($sql))


echo "
{
  \"draw\": $draw,
  \"recordsTotal\": $cantidad_registros,
  \"recordsFiltered\": $cantidad_registros,
  \"data\": [
    $datos
  ]
}


";


?>