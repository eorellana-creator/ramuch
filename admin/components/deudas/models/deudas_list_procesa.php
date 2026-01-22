<?php

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

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
 
// Primero construyes el string SQL sin ejecutarlo
$sql_string = "SELECT id_deuda,
                      fecha, 
                      monto, 
                      estado,
                      fecha_modificacion, 
                      nombre_deudor, 
                      fecha_insercion, 
                      token, 
                      sub_cuenta, 
                      documento_respaldo, 
                      glosa,
                      observacion,
                      id_usuario
               FROM deudas
               $busqueda $orderby 
               LIMIT $inicio,$fin";

// Registras el SQL en el error_log antes de ejecutarlo
//error_log("Consulta SQL a ejecutar: " . $sql_string);

// Luego ejecutas la consulta
$sql = $mysql->query($sql_string);

$sql2 	= $mysql->query("SELECT id_deuda FROM deudas  $busqueda ;");
$cantidad_filtrados = $mysql->f_num($sql2);

$sql3 	= $mysql->query("SELECT id_deuda FROM deudas $busqueda;");

$cantidad_registros = $mysql->f_num($sql3);


$coma = 0;
$signo_coma = "";
$info_adicional = "";
while($result = $mysql->f_obj($sql)){

  $info_adicional = "";
  if($coma==1)
  $signo_coma = ",";

  $coma = 1;
  $result->fecha = fecha_mysql_a_normal($result->fecha);
  $result->monto = number_format($result->monto, 0, '', '.');

  $span1 = "";
  $span2 = "";
  $desactivar = "-";
  $eliminar = "-";

  // Definir colores según estado
  
  if($result->estado == "eliminada"){
      $span1 = "<span style='color:red; display:block; padding:5px;' >";
      $span2 = "</span>";
  } elseif($result->estado == "pagada"){
      $span1 = "<span style='background-color:#20a8d8; color:white; display:block; padding:5px;' >";
      $span2 = "</span>";
  } elseif($result->estado == "condonada"){
      $span1 = "<span style='background-color:#4dbd74; color:white; display:block; padding:5px;' >";
      $span2 = "</span>";
  } elseif($result->estado == "desactivada"){
      $span1 = "<span style='color:#6f42c1; display:block; padding:5px;' >";
      $span2 = "</span>";
  }
  
  $IDU = $result->id_usuario;

  if (!empty($IDU)) {
      $sqlNU = "SELECT * FROM usuario WHERE id_usuario = $IDU";
      //error_log("IDU: " . $IDU);
      //error_log("Consulta SQL a ejecutar: " . $sqlNU);
      
      // Primero ejecutar la consulta
      $query_result = $mysql->query($sqlNU);
      
      if ($query_result) {
          // Luego obtener el objeto
          $resultNU = $mysql->f_obj($query_result);
          
          // Verificar si se obtuvo un resultado
          if ($resultNU) {
              $nombre_de_usuario = $resultNU->nombre_usuario;
          } else {
              $nombre_de_usuario = "generación automática (no encontrado)";
              //error_log("Usuario con ID $IDU no encontrado");
          }
      } else {
          $nombre_de_usuario = "generación automática (error en consulta)";
          //error_log("Error en consulta SQL: " . $mysql->error);
      }
  } else {
      $nombre_de_usuario = "generación automática";
  }

  if($result->estado == "eliminada" || $result->estado == "condonada" || $result->estado == "pagada") {
    // Formatear fecha de modificación si existe
    $fecha_mod = !empty($result->fecha_modificacion) ? fecha_mysql_a_normal($result->fecha_modificacion) : '';
    //$usuario = !empty($nombre_de_usuario) ? $nombre_de_usuario : 'generacion automática';
    $usuario = $nombre_de_usuario;
    
    $info_adicional = "Modificado: $fecha_mod<br>Por: $usuario";
    
    //$span1 = "<span style='color:red; display:block; padding:5px;' data-toggle='tooltip' data-html='true' title='$info_adicional'>";
    //$span2 = "</span>";

  } elseif($result->estado == "pagada") {
    // Similar para estado pagada
    $fecha_mod = !empty($result->fecha_modificacion) ? fecha_mysql_a_normal($result->fecha_modificacion) : '';
    $usuario = !empty($nombre_de_usuario) ? $nombre_de_usuario : 'generacion automática';
    $usuario = $nombre_de_usuario;
    
    $info_adicional = "Pagado: $fecha_mod<br>Registrado por: $usuario";
    
    //$span1 = "<span style='background-color:#20a8d8; color:white; display:block; padding:5px;' data-toggle='tooltip' data-html='true' title='$info_adicional'>";
    //$span2 = "</span>";
  } elseif($result->estado == "condonada") {
    // Similar para estado condonada
    $fecha_mod = !empty($result->fecha_modificacion) ? fecha_mysql_a_normal($result->fecha_modificacion) : '';
    $usuario = !empty($nombre_de_usuario) ? $nombre_de_usuario : 'generacion automática';
    $usuario = $nombre_de_usuario;
    
    $info_adicional = "Condonado: $fecha_mod<br>Por: $usuario";
    
    //$span1 = "<span style='background-color:#4dbd74; color:white; display:block; padding:5px;' data-toggle='tooltip' data-html='true' title='$info_adicional'>";
    //$span2 = "</span>";
  }

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

  // Botón Eliminar - Mostrar solo si está activa
  if ($result->estado == "activa" || $result->estado == "pagada" || $result->estado == "condonada") {
      //$eliminar = "<a href='javascript:deleteItem(\\\"$result->token\\\")'><i class='fas fa-trash'></i> Eliminar</a>";
      //$eliminar = "<a href='index.php?component=deudas&view=deuda_eliminar&token=$result->token'><i class='fas fa-trash'></i> Eliminar </a>";
      $eliminar = "<a href='index.php?component=deudas&view=deuda_condonar&token=$result->token&accion=eliminar'><i class='fas fa-trash'></i> Eliminar</a>";
  }
 
  // Botón Desactivar - Mostrar solo si está activa
  if($result->estado == "activa"){
      $desactivar = "<a href='javascript:desactiva_deuda(\\\"$result->token\\\")'><i class='fas fa-toggle-off'></i> Desactivar</a>";
  } elseif($result->estado == "desactivada") {
      $desactivar = "<a href='javascript:activa_deuda(\\\"$result->token\\\")'><i class='fas fa-toggle-on'></i> Activar</a>";
  }

  $agregar_ingreso = "";
  if($result->sub_cuenta!="otros"){
    //$eliminar = "-";
    $editar   = "-";
  }

  if(@$result->documento_respaldo!=""){
    $documento = "<a href='images/deudas/$result->documento_respaldo' target='_blank'> <i class='fas fa-file-alt'></i> Ver documento</a>";
    }else{
    $documento = " ";
    }
  $editar = "<a href='index.php?component=deudas&view=deuda&token=$result->token'><i class='fas fa-edit'></i> Editar</a>";

  //$condonar = "<a href='index.php?component=deudas&view=deuda_condonar&token=$result->token'><i class='fas fa-clipboard-check'></i> Condonar </a>";
  $condonar = "<a href='index.php?component=deudas&view=deuda_condonar&token=$result->token&accion=condonar'><i class='fas fa-clipboard-check'></i> Condonar</a>";


  $pagar = "<a href='index.php?component=deudas&view=deuda_pagar&token=$result->token'><i class='fas fa-hand-holding-usd'></i> Pagar </a>";

  if($result->estado=="condonada"){
    $editar   = "-";
    $condonar = "-";
    $pagar    = "-";
    //$eliminar = "-";
    //$desactivar = "-";

    $span1 = "<span style='color:#4dbd74;' >";
    $span2 = "</span>";
  
  }


  if($result->estado=="eliminada"){
    $editar   = "-";
    $condonar = "-";
    $pagar    = "-";
    //$eliminar = "-";
    //$desactivar = "-";
  }

  if($result->estado=="desactivada"){
    $editar   = "-";
    $condonar = "-";
    $pagar    = "-";
    //$eliminar = "-";
    //$desactivar = "-";
  }


  if($result->estado=="pagada"){
    $editar   = "-";
    $condonar = "-";
    $pagar    = "-";
    //$eliminar = "-";
    //$desactivar = "-";

    $span1 = "<span style='color:#20a8d8;' >";
    $span2 = "</span>";
    
  }

  $estado = $result->estado . "<br>" . $info_adicional;

  
  $datos = $datos ."
      $signo_coma
      [
          \"$span1 $result->id_deuda $span2\",
          \"$span1 $result->fecha $span2\",
          \"$span1 $result->nombre_deudor $span2\",
          \"$span1 $result->glosa $span2\",
          \"$span1 $result->observacion $span2\",
          \"$span1 $documento $span2\",
          \"$span1 $estado $span2\",
          \"$span1 $result->monto $span2\",
          \"$span1 $editar $span2\",
          \"$span1 $condonar $span2\",
          \"$span1 $pagar $span2\",
          \"$span1 $eliminar $span2\",
          \"$span1 $desactivar $span2\"
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
