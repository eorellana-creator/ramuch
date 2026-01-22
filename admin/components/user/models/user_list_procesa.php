<?php
session_start();

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

$id_company 	= $_SESSION["company_id"];
$usuario_activo_rol	= $_SESSION["usuario_rol"];


if($busqueda!=""){
$busqueda = " WHERE estado='Vigente' AND (nombre_usuario LIKE '%$busqueda%' OR email LIKE '%$busqueda%' ) AND id_company = '$id_company' ";
}else{
$busqueda = " WHERE estado='Vigente' AND id_company = '$id_company'  ";
}

if($inicio=="")
$inicio = 0;
 
$config 	= new Config;
$mysql 		= new mysql;

$mysql->connect(); 		

$usuarios	= "";
$datos		= "";

$orderby = " ORDER BY orden asc";
if($orden==0)
$orderby = " ORDER BY nombre_usuario $direccion ";

if($orden==2)
$orderby = " ORDER BY email $direccion ";

$sql0 	= $mysql->query("SELECT * from usuario $busqueda ;");
$cantidad_registros = $mysql->f_num($sql0);

$sql 	= $mysql->query("SELECT * from usuario $busqueda $orderby LIMIT $inicio,$fin ;");


$coma = 0;
$signo_coma = "";
 
while($result = $mysql->f_obj($sql)){

    if($coma==1)
    $signo_coma = ",";

    $coma = 1;


    $ver = "";
    $ver = "<a href='index.php?component=user&view=user&token=$result->token'><i class='far fa-edit'></i></a>";

    $eliminar = "";
    $eliminar = "<a href='javascript: deleteUser(\\\"$result->token\\\");'><i class='fas fa-trash'></i></a>";



    $usuario_rol    = $result->id_rol;
    $sqlRol 	= $mysql->query("SELECT nombre from rol WHERE id_rol ='$usuario_rol'  ;");
    $resultRol = $mysql->f_obj($sqlRol);
    $nombre_rol = @$resultRol->nombre;

    //***********************************************************

    if(  $usuario_activo_rol!="1" ) 
    $eliminar = "<a href='javascript: alert(\\\"No tiene permisos para eliminar usuarios. Contacte a un Administrador del sistema.\\\");'><i class='far fa-edit' style='color:#cccccc;'></i></a>";

    

    //***********************************************************
    $sql5 	= $mysql->query("SELECT id_menu FROM menu_rol WHERE id_rol ='$usuario_rol' AND activo='1' ;");

    $permisos = "";
    while($result5 = $mysql->f_obj($sql5)){
      
      $sql6 	= $mysql->query("SELECT nombre FROM menu WHERE id_menu='$result5->id_menu' ;");
      $result6 = $mysql->f_obj($sql6);
      $permisos = $permisos .@$result6->nombre. "<br>";
      
    }//while($result2 = $mysql->f_obj($sql2))

    $permisos = "<div style='font-size:12px;'>$permisos</div>";
    

    $datos = $datos ."
        $signo_coma
      [
          \"$result->id_usuario\",
          \"<a href='index.php?component=user&view=user&token=$result->token' class='link-negro'>$result->nombre_usuario</a>\",
          \"$result->email\",
        \"$nombre_rol\",
        \"$permisos\",
        \"$result->estado\",
        \"$ver\"
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