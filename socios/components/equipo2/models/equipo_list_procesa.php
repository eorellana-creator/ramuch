<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

session_start();
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


$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];

$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect(); 		




//Comprobamos si tiene deudas de más de 3 meses: *************************************************************

$fechaActual = date('Y-m-d');

$fecha3mesesAtras = strtotime ('-3 month', strtotime($fechaActual));

$fecha3mesesAtras = date('Y-m-d', $fecha3mesesAtras);
 
$cantidad_deuda_atrasada = 0;

$sql0 	= $mysql->query("SELECT id_deuda FROM deudas  WHERE id_usuario_deuda='$id_usuario' AND estado='activa' AND fecha<'$fecha3mesesAtras' ;");
$cantidad_deuda_atrasada = $mysql->f_num($sql0);

//********************************************************************************************************** */

if($busqueda!=""){
$busqueda = " WHERE estado NOT IN ('Extraviado','Dado de baja','Inutilizable') AND ( id_equipo LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR id_unico LIKE '%$busqueda%' )   ";
}else{
  $busqueda = " ";
}

if($inicio=="")
$inicio = 0;

$usuarios	= "";
$datos		= "";
$imagen = "";


$orderby = " ORDER BY id_equipo ASC";
if($orden==0)
$orderby = " ORDER BY id_equipo $direccion ";


$orderby = " ORDER BY fecha_devolucion DESC, nombre ASC";


//if($orden==0)
//$orderby = " ORDER BY id_equipo $direccion ";
 
 
$sql 	= $mysql->query("SELECT * FROM equipo  $busqueda $orderby LIMIT $inicio,$fin ;");

$sql2 	= $mysql->query("SELECT id_equipo FROM equipo  $busqueda ;");
$cantidad_filtrados = $mysql->f_num($sql2);

$sql3 	= $mysql->query("SELECT id_equipo FROM equipo $busqueda;");

$cantidad_registros = $mysql->f_num($sql3);


$coma = 0;
$signo_coma = "";

$saldo = 0;

while($result = $mysql->f_obj($sql)){

  if (!in_array($result->estado, ['Extraviado', 'Dado de baja', 'Inutilizable',''])) {

    
    if($coma==1)
    $signo_coma = ",";

    $coma = 1;
    $img_url = "";
    $tipo_equipo = "Radios";
    $prestado_a = "";
    $responsable = "";
    $muestrafecha =  1;
    $se_encuentra_prestado = "";
    $solicitado_por = "";
    $solicitado_fecha = "";

    if($result->prestado_a_nombre!=""){
      $result->fecha_devolucion = date("d-m-Y", strtotime($result->fecha_devolucion));
      $se_encuentra_prestado = "<span style='color:#ff0000;'>Se encuentra prestado a $result->prestado_a_nombre hasta el $result->fecha_devolucion</span>";
      $muestrafecha =  0;
    }//if($result->prestado_a_nombre!="")

    // Kop para mostrar igual que arria que esta solicitado el equipo y no se puede prestar, mas abajo no mostraremos la fecha tambien
    // se debe consultar el id_equipo para saber si esta en la tabla de prestamos
    $solicitado = 0;
    $sqlp 	= $mysql->query("SELECT * FROM equipo_prestamo where id_equipo = $result->id_equipo and estado  = 'solicitado' ;");
    $solicitado = $mysql->f_num($sqlp);
    $resultp = $mysql->f_obj($sqlp);
    $solicitado_por = $resultp->id_usuario_prestamo;
    $solicitado_fecha = $resultp->fecha_debe_devolver;
    
    $solicitado_f = "";
    if($solicitado > 0){
      // buscar el nombre del ramuchin que solicito.
      $sqlu 	= $mysql->query("SELECT * FROM usuario where id_usuario = $solicitado_por ;");
      $resultu = $mysql->f_obj($sqlu);
      $solicitado_por = $resultu->nombre_usuario;
      $solicitado_f = "<span style='color:#ff0000;'>Se encuentra Solicitado por $solicitado_por hasta el $solicitado_fecha</span>";
    }// if($solicitado > 0)

    $img_url =  "<img src='https://ramuch.cl/admin/images/equipo_sin_imagen.jpg' width='90' height='120'>" ; 
    
    if($result->imagen!=""){
      $img_url ="<img src='https://ramuch.cl/admin/images/equipo/$result->imagen' width='90' height='120'>" ; 
      //$img_url ="<img src='/images/equipo/$result->imagen' width='90' height='120'>";
      $img_url2 ="<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#imageModal' data-img='https://ramuch.cl/admin/images/equipo/$result->imagen'> $img_url</button>";
  
    }  

    if($muestrafecha != 0 && $solicitado ==0){
    $prestado_a   = "<label>Fecha desde:</label><input class='form-control campofecha' id='fecha$result->id_equipo' type='date' name='fecha$result->id_equipo'  placeholder='date' value='' style='margin-bottom:6px;'> <label>Fecha hasta:</label><input class='form-control campofecha' id='fecha2$result->id_equipo' type='date' name='fecha2$result->id_equipo'  placeholder='date' value='' style='margin-bottom:6px;'> <button type='button' class='btn btn-primary' onClick='pedirEquipo(\\\"fecha$result->id_equipo\\\",\\\"fecha2$result->id_equipo\\\",\\\"$result->token\\\")' >Solicitar Equipo</button></div>";
    }

    $responsable      = "";
    $fecha_devolucion = "";
    $color_fecha      = "";

    if($cantidad_deuda_atrasada>0)
    $prestado_a   = "<span class='badge badge-danger'>No puedes pedir equipo.<br>Presentas deudas atrasadas <br>de 3 meses o más.</span>";
    
    $datos = $datos."
        $signo_coma
      [
          \"$result->id_equipo\",
          \"$img_url2\",
          \"$result->nombre<br>$se_encuentra_prestado<br>$solicitado_f\",
          \"$result->id_unico\",
          \"$result->estado\",
          \"$prestado_a\"
        ]
        
        ";
      
      $datos = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $datos);
  }  
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