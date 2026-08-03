<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

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
$busqueda = " WHERE ( id_equipo LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%'  ) AND estado='solicitado' ";
}else{
  $busqueda = " WHERE  estado='solicitado' ";
}


if($inicio=="")
$inicio = 0;
 
$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect(); 		

$usuarios	= "";
$datos		= "";
$imagen = "";

$orderby = " ORDER BY id_equipo_prestamo DESC ";
 
 
if($orden==0) $orderby = " ORDER BY id_equipo_prestamo DESC ";

if($orden==2) $orderby = " ORDER BY nombre $direccion ";

if($orden==3) $orderby = " ORDER BY id_unico $direccion ";

if($orden==4) $orderby = " ORDER BY estado $direccion ";

if($orden==6) $orderby = " ORDER BY nombre_responsable_prestamo $direccion ";

//if($orden==0)
//$orderby = " ORDER BY id_equipo $direccion ";
// echo "SELECT * FROM equipo  $busqueda $orderby LIMIT $inicio,$fin ;";
 
$sql 	= $mysql->query("SELECT * FROM equipo_prestamo  $busqueda $orderby LIMIT $inicio,$fin ;");

$sql2 	= $mysql->query("SELECT id_equipo FROM equipo_prestamo  $busqueda ;");
$cantidad_filtrados = $mysql->f_num($sql2);

$sql3 	= $mysql->query("SELECT id_equipo FROM equipo_prestamo $busqueda;");

$cantidad_registros = $mysql->f_num($sql3);


$coma = 0;
$signo_coma = "";

$saldo = 0;

while($result = $mysql->f_obj($sql)){

  if($coma==1)
  $signo_coma = ",";

  $coma = 1;
  $img_url = "";
  //$hoy = date("Y-m-d");

  $sqle 	= $mysql->query("SELECT * FROM equipo WHERE id_equipo = '$result->id_equipo' ;");
  $resulte = $mysql->f_obj($sqle);

  //$descrip = str_replace(',', '', $resulte->descripcion);
  //onblur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this); 


  //$nombre_equipo = $resulte->nombre . " <br> " . $resulte->estado . " <br> " . $resulte->estado . " <br> " . $resulte->observacion . " " . $resulte->talla ;
  
  $nombre = str_replace([',', "\r", "\n"], '', $resulte->nombre);
  $estado = str_replace([',', "\r", "\n"], '', $resulte->estado);
  $descripcion = str_replace([',', "\r", "\n"], '', $resulte->descripcion);
  $observacion = str_replace([',', "\r", "\n"], '', $resulte->observacion);
  $talla = str_replace([',', "\r", "\n"], '', $resulte->talla);

  $nombre_equipo = $nombre . " <br> " . $descripcion . " <br> " . $estado . " <br> " . $talla . " <br> " . $observacion;

  $id_unico = $resulte->id_unico;
  $id_equipo = $resulte->id_equipo;

  $img_url = "<img src='images/equipo_sin_imagen.jpg' width='90' height='120'>" ; 
  if($resulte->imagen!=""){
    $img_url =  "<img src='images/equipo/$resulte->imagen' onclick='' width='90' height='120'>" ; 
    //$img_url = "<img src='images/equipo/$resulte->imagen' onclick=\"mostrarImagen('images/equipo/$resulte->imagen')\" width='90' height='120'>";
  }
  
  $sqlu 	= $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario = '$result->id_usuario_prestamo' ;");
  $resultu = $mysql->f_obj($sqlu);
  $usuario_valido = $resultu && (int)$result->id_usuario_prestamo > 0;
  $usuario_solicitud = $usuario_valido
    ? $resultu->nombre_usuario
    : "<span class='text-danger'><strong>Solicitud inválida</strong><br>Usuario ID: $result->id_usuario_prestamo</span>";

  $desde = fecha_mysql_a_normal($result->fecha_prestamo);
  $hasta = fecha_mysql_a_normal($result->fecha_debe_devolver);

  $periodo = "Desde el $desde <br>al $hasta";

  if ($usuario_valido) {
    $aceptar_rechazar = "<button type='button' class='btn btn-success' onClick='aceptaRechaza(1,\\\"$result->token\\\");' >Aceptar</button> <button type='button' class='btn btn-danger' onClick='seteaTokenPrestamo(\\\"$result->token\\\")' data-toggle='modal' data-target='#primaryModal' >Rechazar</button>";
  } else {
    $aceptar_rechazar = "<button type='button' class='btn btn-default' disabled>Solicitud inválida</button>";
  }
  //$aceptar_rechazar = "<button id='btn-aceptar-{$result->token}' type='button' class='btn btn-success' onClick='aceptaRechaza(1,\"{$result->token}\");' >Aceptar</button> <button id='btn-rechazar-{$result->token}' type='button' class='btn btn-danger' onClick='seteaTokenPrestamo(\"{$result->token}\")' data-toggle='modal' data-target='#primaryModal' >Rechazar</button>";
  $datos = $datos."
      $signo_coma
    [
        \"$id_equipo\",
        \"$img_url\",
        \"$id_unico\",      
        \"$nombre_equipo\",
        \"$usuario_solicitud\",
        \"$periodo\",
        \"$aceptar_rechazar\"
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
