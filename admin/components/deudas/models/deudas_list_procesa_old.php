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



if($busqueda!=""){
$busqueda = " WHERE nombre_deudor LIKE '%$busqueda%'  OR id_deuda LIKE '%$busqueda%' OR glosa LIKE '%$busqueda%'  OR estado LIKE '%$busqueda%'  ";
}else{
  $busqueda = "  ";
}


if($inicio=="")
$inicio = 0;
 
$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect(); 		

$usuarios	= "";
$datos		= "";

 
$orderby = " ORDER BY id_deuda DESC";
if($orden==0)
$orderby = " ORDER BY id_deuda $direccion ";

if($orden==1)
$orderby = " ORDER BY fecha $direccion ";

if($orden==6)
$orderby = " ORDER BY estado  $direccion ";
 
 
$sql 	= $mysql->query("SELECT * FROM deudas  $busqueda $orderby LIMIT $inicio,$fin ;");

$sql2 	= $mysql->query("SELECT id_deuda FROM deudas  $busqueda ;");
$cantidad_filtrados = $mysql->f_num($sql2);

$sql3 	= $mysql->query("SELECT id_deuda FROM deudas $busqueda;");

$cantidad_registros = $mysql->f_num($sql3);


$coma = 0;
$signo_coma = "";
while($result = $mysql->f_obj($sql)){

if($coma==1)
$signo_coma = ",";

$coma = 1;

$result->fecha = fecha_mysql_a_normal($result->fecha);


$result->monto = number_format($result->monto, 0, '', '.');

$span1 = "";
$span2 = "";

if(@$result->estado=="eliminada"){
  $span1 = "<span style='color:#ff0000;' >";
  $span2 = "</span>";
}

//$aleatorio = md5(rand(999,99999999).$result->id_deuda.$result->monto  );
//$sql8 	= $mysql->query("UPDATE deudas SET token ='$aleatorio' WHERE id_deuda='$result->id_deuda' ;");


//solo se pueden eliminar deudas de 
$hoy = date("Y-m-d");
$date1 = new DateTime($result->fecha_insercion);
$date2 = new DateTime($hoy);
$diff = $date1->diff($date2);
// will output 2 days
$diferencia = $diff->days;


if($diferencia<=2){
$editar = "<a href='index.php?component=deudas&view=deuda&token=$result->token'><i class='fas fa-edit'></i> Editar</a>";
}else{
$editar = "<span data-toggle=\\\"tooltip\\\" data-placement=\\\"top\\\" title=\\\"Han transcurrido más de 2 días desde que se creó la deuda. No se puede editar.\\\"   ><i class='fas fa-question-circle' style='color:#707070;'  ></i></span>";
}



$eliminar = "";

if($diferencia<=2){
$eliminar = "<a href='javascript:deleteItem(\\\"$result->token\\\")'><i class='fas fa-trash'></i> Eliminar</a>";
}else{
$eliminar = "<a href='javascript:deleteItem(\\\"$result->token\\\")'><i class='fas fa-trash'></i> Eliminar</a><span data-toggle=\\\"tooltip\\\" data-placement=\\\"top\\\" title=\\\"Han transcurrido más de 2 días, por lo tanto este ingreso se marcará como eliminado, no se borrará del listado.\\\"   ><i class='fas fa-question-circle' style='color:#707070;'  ></i></span>";
}



$agregar_ingreso = "";
if($result->sub_cuenta!="otros"){
  $eliminar = "-";
  $editar   = "-";
}


if(@$result->documento_respaldo!=""){
  $documento = "<a href='images/deudas/$result->documento_respaldo' target='_blank'> <i class='fas fa-file-alt'></i> Ver documento</a>";
  }else{
  $documento = " ";
  }
$editar = "<a href='index.php?component=deudas&view=deuda&token=$result->token'><i class='fas fa-edit'></i> Editar</a>";

$condonar = "<a href='index.php?component=deudas&view=deuda_condonar&token=$result->token'><i class='fas fa-clipboard-check'></i> Condonar </a>";

$pagar = "<a href='index.php?component=deudas&view=deuda_pagar&token=$result->token'><i class='fas fa-hand-holding-usd'></i> Pagar </a>";

if($result->estado=="condonada"){
  $editar   = "-";
  $condonar = "-";
  $pagar    = "-";
  $eliminar = "-";

  $span1 = "<span style='color:#4dbd74;' >";
  $span2 = "</span>";

 
}


if($result->estado=="eliminada"){
  $editar   = "-";
  $condonar = "-";
  $pagar    = "-";
  $eliminar = "-";
}


if($result->estado=="pagada"){
  $editar   = "-";
  $condonar = "-";
  $pagar    = "-";
  $eliminar = "-";

  $span1 = "<span style='color:#20a8d8;' >";
  $span2 = "</span>";
}

 


$datos = $datos ."
     $signo_coma
	 [
	    \"$span1 $result->id_deuda $span2\",
      \"$span1 $result->fecha $span2\",
      \"$span1 $result->nombre_deudor $span2\",
      \"$span1 $result->glosa $span2\",
      \"$span1 $result->observacion $span2\",
	    \"$span1 $documento $span2\",
      \"$span1 $result->estado $span2\",
      \"$span1 $result->monto $span2\",
      \"$span1 $editar $span2\",
      \"$span1 $condonar $span2\",
      \"$span1 $pagar $span2\",
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