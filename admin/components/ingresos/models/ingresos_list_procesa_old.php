<?php
ini_set('display_errors', "on");

//include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

/*
if (isset($_POST["order"]) && is_object($_POST["order"])) {
  // Accede al índice 0 de $_POST["order"] solo si es un objeto
  echo "La variable es una objeto.";
} else {
  echo "La variable no es un objeto.";
}

if (isset($_POST["order"]) && is_array($_POST["order"])) {
  // Accede al índice 0 de $_POST["order"] solo si es una matriz
  echo "es un arreglo";
  
  echo "fin";
 }else {
  echo "no es un arreglo";

  // Manejo del caso en el que $_POST["order"] no es una matriz
  // Puede imprimir un mensaje de error o tomar alguna otra acción adecuada
}
*/

//print_r($_POST["order"][0]);
//exit;

@$draw			= $_POST["draw"];
@$inicio		= $_POST["start"];
@$fin			  = $_POST["length"];
@$busqueda 	= $_POST["search"]["value"];
@$orden 		= $_POST["order"][0]["column"];
@$direccion = $_POST["order"][0]["dir"];

$subcuenta       = @$_GET["subcuenta"];

if($subcuenta != "")
$subcuenta = " sub_cuenta ='$subcuenta' ";

if($subcuenta == "")
$subcuenta = " id_cuenta_maestra>0 ";

if($busqueda!=""){
$busqueda = " WHERE tipo='ingreso' AND $subcuenta  AND ( id_cuenta_maestra LIKE '%$busqueda%' OR nombre_usuario_sistema LIKE '%$busqueda%'  )  ";
}else{
  $busqueda = " WHERE tipo='ingreso' AND $subcuenta ";
}


if($inicio=="")
$inicio = 0;
 
$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect(); 		

$usuarios	= "";
$datos		= "";
 
$orderby = " ORDER BY id_cuenta_maestra DESC";
if($orden==0)
$orderby = " ORDER BY id_cuenta_maestra $direccion ";
 
//echo "SELECT * FROM cuenta_maestra  $busqueda $orderby LIMIT $inicio,$fin ;";
$sql 	= $mysql->query("SELECT * FROM cuenta_maestra  $busqueda $orderby LIMIT $inicio,$fin ;");

$sql2 	= $mysql->query("SELECT id_cuenta_maestra FROM cuenta_maestra  $busqueda ;");
$cantidad_filtrados = $mysql->f_num($sql2);

$sql3 	= $mysql->query("SELECT id_cuenta_maestra FROM cuenta_maestra $busqueda;");

$cantidad_registros = $mysql->f_num($sql3);


$coma = 0;
$signo_coma = "";
while($result = $mysql->f_obj($sql)){

if($coma==1)
$signo_coma = ",";

$coma = 1;

$result->fecha = fecha_mysql_a_normal($result->fecha);

$sql3 	= $mysql->query("SELECT id_usuario, nombre_usuario, token FROM usuario WHERE id_usuario ='$result->id_usuario_movimiento';");
$result3 = $mysql->f_obj($sql3);
$nombre_usuario = @$result3->nombre_usuario;

$result->monto = number_format($result->monto, 0, '', '.');

$span1 = "";
$span2 = "";

if(@$result->estado=="eliminado"){
  $span1 = "<span style='color:#ff0000;' >";
  $span2 = "</span>";
}



//solo se pueden eliminar egresos de 
$hoy = date("Y-m-d");
$date1 = new DateTime($result->fecha_insercion);
$date2 = new DateTime($hoy);
$diff = $date1->diff($date2);
// will output 2 days
$diferencia = $diff->days;


if($diferencia<=2){
$editar = "<a href='index.php?component=ingresos&view=ingreso&token=$result->token'><i class='fas fa-edit'></i> Editar</a>";
}else{
$editar = "<span data-toggle=\\\"tooltip\\\" data-placement=\\\"top\\\" title=\\\"Han transcurrido más de 2 días desde que se creó el Ingreso. No se puede editar.\\\"   ><i class='fas fa-question-circle' style='color:#707070;'  ></i></span>";
}

$eliminar = "";

if($diferencia<=2){
$eliminar = "<a href='javascript:deleteItem(\\\"$result->token\\\")'><i class='fas fa-trash'></i> Eliminar</a>";
}else{
$eliminar = "<a href='javascript:deleteItem(\\\"$result->token\\\")'><i class='fas fa-trash'></i> Eliminar</a><span data-toggle=\\\"tooltip\\\" data-placement=\\\"top\\\" title=\\\"Han transcurrido más de 2 días, por lo tanto este ingreso se marcará como eliminado, no se borrará del listado.\\\"   ><i class='fas fa-question-circle' style='color:#707070;'  ></i></span>";
}

if($result->estado=="eliminado"){
  $eliminar = "-";
  $editar   = "-";
}

$agregar_ingreso = "";
if($result->sub_cuenta!="otros"){
  $eliminar = "-";
  $editar   = "-";
}


if(@$result->documento_respaldo!=""){
  $documento = "<a href='images/ingresos/$result->documento_respaldo' target='_blank'> <i class='fas fa-file-alt'></i> Ver documento</a>";
  }else{
  $documento = " ";
  }




$datos = $datos ."
     $signo_coma
	 [
	    \"$span1 $result->id_cuenta_maestra $span2\",
      \"$span1 $result->fecha $span2\",
      \"$span1 $nombre_usuario $span2\",
      \"$span1 $result->glosa $span2\",
      \"$span1 $result->observacion $span2\",
      \"$span1 $result->medio $span2\",
	    \"$span1 $documento $span2\",
      \"$span1 $result->estado $span2\",
      \"$span1 $result->monto $span2\",
      \"$span1 $editar $span2\",
      \"$span1 $eliminar $span2\"
    ]";
	
	$datos = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $datos);
	
}//while($result2 = $mysql->f_obj($sql))


echo "
{
  \"draw\": $draw,
  \"recordsTotal\": $cantidad_registros,
  \"recordsFiltered\": $cantidad_filtrados,
  \"data\": [
    $datos
  ]
}
 
";


?>