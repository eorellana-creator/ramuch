<?php

//ini_set('display_errors', "on");
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();
//include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$draw			 = @$_POST["draw"];
$inicio		 = @$_POST["start"];
$fin			 = @$_POST["length"];
$busqueda  = @$_POST["search"]["value"];
$orden 		 = @$_POST["order"][0]["column"];
$direccion = @$_POST["order"][0]["dir"];

$tipo       = @$_GET["tipo"];
$cuota      = @$_GET["cuota"];


if($tipo=="" || $tipo == "10" )
$tipo = " p.tipo_inscripcion ='1' OR p.tipo_inscripcion ='3' ";

if($tipo=="1" )
$tipo = " p.tipo_inscripcion ='1' ";

if($tipo=="2" )
$tipo = " p.tipo_inscripcion ='2' ";

if($tipo=="3" )
$tipo = " p.tipo_inscripcion ='3' ";

if($tipo=="6" )
$tipo = " p.tipo_inscripcion ='6' ";

if($tipo=="7" )
$tipo = " p.tipo_inscripcion ='7' ";

if($tipo=="8" )
$tipo = " p.tipo_inscripcion ='8' ";

if($tipo=="0123499" )
$tipo = " p.tipo_inscripcion ='1' OR p.tipo_inscripcion ='2' OR p.tipo_inscripcion ='3' OR p.tipo_inscripcion ='6' OR p.tipo_inscripcion ='7' OR p.tipo_inscripcion ='8' ";


$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect(); 	


//MARCAMOS LOS QUE SE REGISTRARON VÍA PÁGINA WEB Y NO HAN PAGADO LA MATRÍCULA*************************************************************************** */


$sqlD 	= $mysql->query("SELECT id_usuario_deuda FROM deudas WHERE sub_cuenta='matricula' AND estado='activa' ;");

while($resultD = $mysql->f_obj($sqlD)){
 
  $sqlUS 	= $mysql->query("UPDATE usuario SET web_matricula_pagada = 'No' WHERE id_usuario='$resultD->id_usuario_deuda' ;");
  

}//while($resultD = $mysql->f_obj($sqlD)){


// se actualiza si esta pagada la matricula en deudas, grabando en usuario el campo de matricula pagada
$sqlD 	= $mysql->query("SELECT id_usuario_deuda FROM deudas WHERE sub_cuenta='matricula' AND estado='pagada' ;");

while($resultD = $mysql->f_obj($sqlD)){
 
  $sqlUS 	= $mysql->query("UPDATE usuario SET web_matricula_pagada = 'Si' WHERE id_usuario='$resultD->id_usuario_deuda' ;");
  

}//while($resultD = $mysql->f_obj($sqlD)){


//****************************************************************************************************************************************************** */

if($busqueda!=""){
$busqueda = " WHERE ($tipo) AND ( u.nombre_usuario LIKE '%$busqueda%' OR u.email LIKE '%$busqueda%' OR p.rut LIKE '%$busqueda%' OR p.fono LIKE '%$busqueda%' ) "; // AND (u.web_matricula_pagada IS NULL OR u.web_matricula_pagada!='No')  ";
}else{
  $busqueda = " WHERE ($tipo) "; //AND (u.web_matricula_pagada IS NULL OR u.web_matricula_pagada!='No') ";
}


//print($busqueda);


if($inicio=="")
$inicio = 0;
	

$usuarios	= "";
$datos		= "";

$orderby = " ORDER BY u.estado asc, u.web_matricula_pagada asc, u.nombre_usuario asc";

if($orden==0)
$orderby = " ORDER BY u.estado asc, u.web_matricula_pagada asc, u.id_usuario $direccion ";

if($orden==1)
$orderby = " ORDER BY u.estado asc, u.web_matricula_pagada asc, u.nombre_usuario $direccion ";

if($orden==2)
$orderby = " ORDER BY p.rut $direccion ";

if($orden==3)
$orderby = " ORDER BY p.fono $direccion ";

if($orden==4)
$orderby = " ORDER BY p.mail $direccion ";

if($orden==5)
$orderby = " ORDER BY u.estado asc, u.web_matricula_pagada asc, p.tipo_inscripcion $direccion ";


//Para listado de correos
$correos = "";
$lista_excel = "";

$sqlC 	= $mysql->query("SELECT u.id_usuario, u.id_usuario, u.nombre_usuario, u.email, u.estado, p.tipo_inscripcion, p.fono, p.rut FROM usuario AS u LEFT JOIN perfil AS p ON u.id_usuario = p.id_usuario $busqueda ;");

//echo($busqueda);


while($resultC = $mysql->f_obj($sqlC)){

  if($resultC->email!=""){
    $resultC->email = strtolower($resultC->email);
    $correos = $correos . ", $resultC->email";

    //**************************************** */

    $hoy = date("Y-m-d");
  $sqlD 	= $mysql->query("SELECT SUM(monto) as deuda FROM deudas WHERE id_usuario_deuda='$resultC->id_usuario' AND fecha<'$hoy' AND estado='activa' ;");
  $resultD = $mysql->f_obj($sqlD);

$deuda = 0;
if($resultD->deuda>0)
$deuda = number_format($resultD->deuda, 0, '', '.');

    $lista_excel = $lista_excel."<tr>  <td>$resultC->id_usuario</td>  <td>$resultC->nombre_usuario</td> <td>$resultC->rut</td> <td>$resultC->fono</td> <td>$resultC->email</td> <td>$deuda</td>  </tr>";

    //*************************************************/
  }

}//while($resultC = $mysql->f_obj($sqlC))

$correos = trim($correos,",");
$correos = trim($correos);

$fp = fopen('lista_correos.txt', 'w');
fwrite($fp, $correos);
fclose($fp);

$id_usuario_sesion = @$_SESSION["usuario_id"]	;

$lista_excel = "<table><tr style='background-color:#313131; color:#ffffff;padding:4px;'>  <td>ID</td>  <td>Nombre</td> <td>Rut</td> <td>Teléfono</td> <td>Email</td> <td>Deuda</td>  </tr>$lista_excel</table>";

$fp = fopen("../excel/lista_usuarios_excel$id_usuario_sesion.xls", 'w');
fwrite($fp, $lista_excel);
fclose($fp);


// para sacar filtro y cantidades
$sql2 	= $mysql->query("SELECT u.id_usuario FROM usuario AS u LEFT JOIN perfil AS p ON u.id_usuario = p.id_usuario $busqueda ;");
$cantidad_filtrados = $mysql->f_num($sql2);

$sql3 	= $mysql->query("SELECT u.id_usuario FROM usuario AS u LEFT JOIN perfil AS p ON u.id_usuario = p.id_usuario $busqueda;");

$cantidad_registros = $mysql->f_num($sql3);

//kop
$sql 	= $mysql->query("SELECT u.id_rol, u.id_usuario, u.nombre_usuario, u.email, u.token, p.tipo_inscripcion, u.estado, u.fecha_registro, u.web_matricula_pagada, p.fono, p.rut FROM usuario AS u LEFT JOIN perfil AS p ON u.id_usuario = p.id_usuario $busqueda $orderby LIMIT $inicio,$fin ;");

$coma = 0;
$signo_coma = "";
while($result = $mysql->f_obj($sql)){

  if($coma==1)
  $signo_coma = ",";
  $usuario = $result->id_usuario;
  $coma = 1;
  $ver = "";
  $ver = "<a href='index.php?component=socios&view=socios&token=$result->token'><i class='fas fa-search-plus'></i></a>";

  $eliminar = "";
  
  // Kop cambiando el boton para eliminar la fila sin recargar todo
  //$eliminar = "<a href='javascript: deleteSocio(\\\"$result->token\\\");'><i class='fas fa-trash-alt'></i></a>";
  
  $eliminar = "<a href='javascript:eliminarFila($result->id_usuario);'><i class='fas fa-trash-alt'></i></a>";

  $estado = $result->estado;
  $fecha_registro = $result->fecha_registro;
  if($result->estado=='Por confirmar email')
  $estado = "<span style='color:#ff0000;'>$result->estado</span>";

  $web_matricula_pagada = $result->web_matricula_pagada;
  if($web_matricula_pagada == "" OR $web_matricula_pagada == 'No'){
    $web_matricula_pagada = "<span style='color:#ff0000;'>No</span>";
  }

  $correo= "";
  if($result->email!="")
  $correo= "<a href='mailto:$result->email'><i class='fas fa-envelope'></i> $result->email</a>";

  $telefono = "";
  if($result->fono!="")
  $telefono = "<a href='tel:$result->fono'><i class='fas fa-mobile'></i> $result->fono</a>";


  // Kop se debe eliminar y dejar la busqueda por la base de datos
  $tipo_inscripcion = "";
  switch($result->tipo_inscripcion){
  case 1:
    $tipo_inscripcion = "Profesional";
    break;
  case 3:
    $tipo_inscripcion = "Estudiante";
    break;
  case 6:
    $tipo_inscripcion = "Congelado";
    break;
  case 2:
    $tipo_inscripcion = "Honorario";
    break;
  case 7:
    $tipo_inscripcion = "Desvinculado";
    break;

//  case 8:
//    $tipo_inscripcion = "Eliminado";
//    break;


}//switch

  /*
  // tipo de inscripcion
  //$tipo_inscripcion = "";
  $sqliXX 	= $mysql->query("SELECT * FROM perfil WHERE id_usuario ='$usuario'; ");
  $resultXX = $mysql->f_obj($sqlXX);
  $tip_ins = $resultXX->tipo_inscripcion;

  $sqli 	= $mysql->query("SELECT * FROM plan_matricula WHERE id_plan_matricula ='$tip_ins'; ");
  $resulti = $mysql->f_obj($sqli);
  $tipo_inscripcion =$resulti->nombre;
*/

  // rol del socix
  $sqlR 	= $mysql->query("SELECT * FROM rol WHERE id_rol ='$result->id_rol'; ");
  $resultR = $mysql->f_obj($sqlR);
  $nombre_rol =$resultR->nombre;

  //Estado de deudas
  $hoy = date("Y-m-d");
  $sqlD 	= $mysql->query("SELECT SUM(monto) as deuda FROM deudas WHERE id_usuario_deuda='$result->id_usuario' AND fecha<'$hoy' AND estado='activa' ;");
  $resultD = $mysql->f_obj($sqlD);

  $deuda = 0;
  if($resultD->deuda>0)
  $deuda = "<span style='color:#ff0000;'>".number_format($resultD->deuda, 0, '', '.')."</span>";

  
  $datos = $datos ."
      $signo_coma
    [
      \"$result->id_usuario\",
      \"$nombre_rol\",
      \"<a href='index.php?component=socios&view=socios&token=$result->token' class='link-negro' >$result->nombre_usuario</a>\",
        \"$result->rut\",
        \"$telefono\",
        \"$correo\",
        \"$tipo_inscripcion\",
        \"$estado\",        
        \"$fecha_registro\", 
        \"$web_matricula_pagada \",      
        \"$deuda\",
        \"$ver\"
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

