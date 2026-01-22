<?php
//include("../../../includes/sql_inyection.php");
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

$sql 	= $mysql->query("SELECT id_rol, nombre, token from rol $busqueda $orderby LIMIT $inicio, $fin ;");

$sql2 	= $mysql->query("SELECT id_rol, nombre, token from rol $busqueda ;");

$cantidad_registros = $mysql->f_num($sql2);




$coma = 0;
$signo_coma = "";
while($result = $mysql->f_obj($sql)){

if($coma==1)
$signo_coma = ",";

$coma = 1;

$editar = "";
$editar = "<a href='index.php?component=rol&view=rol&token=$result->token'><i class='far fa-edit'></i></a>"; 
 
$eliminar = "";
if($result->id_rol!="1" &&  $result->id_rol!="5" &&  $result->id_rol!="6" &&  $result->id_rol!="7" &&  $result->id_rol!="8" )
$eliminar = "<a href='javascript: deleteRol(\\\"$result->token\\\");'><i class='fas fa-trash'></i></a>";


$datos = $datos ."
     $signo_coma
	 [
      \"$result->nombre\",
      \"$editar\",
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