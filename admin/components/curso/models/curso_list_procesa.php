<?php
include("../../../includes/sql_inyection.php");
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
$busqueda = " WHERE ( id_curso LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%'  )  ";
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
$imagen = "";





 
$orderby = " ORDER BY id_curso ASC";
if($orden==0)
$orderby = " ORDER BY id_curso $direccion ";


$orderby = " ORDER BY id_curso DESC";
//if($orden==0)
//$orderby = " ORDER BY id_curso $direccion ";
 
 
$sql 	= $mysql->query("SELECT * FROM curso  $busqueda $orderby LIMIT $inicio,$fin ;");

$sql2 	= $mysql->query("SELECT id_curso FROM curso  $busqueda ;");
$cantidad_filtrados = $mysql->f_num($sql2);

$sql3 	= $mysql->query("SELECT id_curso FROM curso $busqueda;");

$cantidad_registros = $mysql->f_num($sql3);


$coma = 0;
$signo_coma = "";

$saldo = 0;

while($result = $mysql->f_obj($sql)){

if($coma==1)
$signo_coma = ",";

$coma = 1;



$detalle = "<a href='index.php?component=curso&view=curso&token=$result->token'><i class='fas fa-search-plus'></i> ver curso</a>";

$result->fecha_inicio = fecha_mysql_a_normal($result->fecha_inicio);
$result->fecha_fin = fecha_mysql_a_normal($result->fecha_fin);

$result->precio = number_format($result->precio, 0, '', '.');

$cantidad_participantes = 0;
$sql8 	= $mysql->query("SELECT * FROM curso_participantes WHERE id_curso='$result->id_curso' ;");
$result8 = $mysql->f_obj($sql8);
$cantidad_participantes = $mysql->f_num($sql8);


if($cantidad_participantes>0){
$eliminar = "<span data-toggle=\\\"tooltip\\\" data-placement=\\\"top\\\" title=\\\"No se puede eliminar un curso con participantes inscritos. No se puede eliminar.\\\"   ><i class='fas fa-question-circle' style='color:#707070;'  ></i></span>";  
}else{
$eliminar = "<a href='javascript:eliminarCurso(\\\"$result->token\\\")'><i class='fas fa-trash'></i> Eliminar</a>";
}



$recaudado  = 0;
$adeudado   = 0;

$sql9 	= $mysql->query("SELECT * FROM curso_participantes WHERE id_curso='$result->id_curso' ;");
while($result9 = $mysql->f_obj($sql9)){

  if($result9->estado_pago=="Pendiente")
  $adeudado  = $adeudado + $result9->precio_a_pagar;

  if($result9->estado_pago=="Pagado")
  $recaudado  = $recaudado + $result9->precio_a_pagar;

}//while($result9 = $mysql->f_obj($sql9))




$inscritos  = $cantidad_participantes;


$recaudado  = number_format($recaudado, 0, '', '.');
$adeudado   = number_format($adeudado, 0, '', '.');

 

$datos = $datos."
     $signo_coma
	 [
	    \"$result->id_curso\",
      \"<strong>$result->tipo:</strong> $result->nombre\",
      \"$result->fecha_inicio<br>$result->fecha_fin\",
      \"$result->capacidad\",
	    \"$inscritos\",
      \"$result->precio\",
      \"$recaudado\",
      \"$adeudado\",
      \"$detalle\",
      \"$eliminar\"
    ]
    
    ";
	
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