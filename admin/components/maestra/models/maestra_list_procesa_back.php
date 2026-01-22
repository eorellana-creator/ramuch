<?php
//include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");



@$draw			= $_POST["draw"];
@$inicio		= $_POST["start"];
@$fin			  = $_POST["length"];
@$busqueda 	= $_POST["search"]["value"];
@$orden 		= $_POST["order"][0]["column"];
@$direccion = $_POST["order"][0]["dir"];

$subcuenta       = @$_GET["subcuenta"];



if($busqueda!=""){
$busqueda = " WHERE ( id_cuenta_maestra LIKE '%$busqueda%' OR nombre_usuario_sistema LIKE '%$busqueda%'   )  ";
}else{
  $busqueda = " ";
}


if($inicio=="")
$inicio = 0;
 
$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect(); 		

$usuarios	= "";
$datos		= "";

 
$orderby = " ORDER BY id_cuenta_maestra ASC";
if($orden==0)
$orderby = " ORDER BY id_cuenta_maestra $direccion ";
 

$sql 	= $mysql->query("SELECT * FROM cuenta_maestra  $busqueda $orderby LIMIT $inicio,$fin ;");

$sql2 	= $mysql->query("SELECT id_cuenta_maestra FROM cuenta_maestra  $busqueda ;");
$cantidad_filtrados = $mysql->f_num($sql2);

$sql3 	= $mysql->query("SELECT id_cuenta_maestra FROM cuenta_maestra $busqueda;");

$cantidad_registros = $mysql->f_num($sql3);


$coma = 0;
$signo_coma = "";

$saldo = 0;

while($result = $mysql->f_obj($sql)){

if($coma==1)
$signo_coma = ",";

$coma = 1;

$result->fecha = fecha_mysql_a_normal($result->fecha);

$sql3 	= $mysql->query("SELECT id_usuario, nombre_usuario, token FROM usuario WHERE id_usuario ='$result->id_usuario_movimiento';");
$result3 = $mysql->f_obj($sql3);
$nombre_usuario = @$result3->nombre_usuario;

$submonto = $result->monto;
$result->monto = number_format($result->monto, 0, '', '.');

$monto_ingreso = "";
$monto_egreso = "";

if($result->tipo=="ingreso"){
  $monto_ingreso =$result->monto;
  $saldo = $saldo + $submonto;
}

if($result->tipo=="egreso"){
  $monto_egreso =$result->monto;
  $saldo = $saldo - $submonto;
}


$saldo_imprime = number_format($saldo, 0, '', '.');


$span1 = "";
$span2 = "";
if(@$result->estado=="eliminado"){
  $span1 = "<span style='color:#ff0000;' >";
  $span2 = "</span>";

}


if(@$result->documento_respaldo!=""){
  $documento = "<a href='images/ingresos/$result->documento_respaldo' target='_blank'> <i class='fas fa-file-alt'></i> Ver documento</a>";
  }else{
  $documento = " ";
  }


$datos = "
     
	 [
	    \"$span1 $result->id_cuenta_maestra $span2\",
      \"$span1 $result->fecha $span2\",
      \"$span1 $nombre_usuario $span2\",
      \"$span1 $result->glosa $span2\",
      \"$span1 $result->medio $span2\",
	    \"$span1 $documento $span2\",
      \"$span1 $result->estado $span2\",
      \"$span1 $monto_ingreso $span2\",
      \"$span1 $monto_egreso $span2\"
    ]
    $signo_coma
    ".$datos;
	
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