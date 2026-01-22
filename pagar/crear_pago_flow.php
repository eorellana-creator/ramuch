<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

//*************************************************************************

$rut 	        = @$_POST["rutpagador"];
$emailpagador = @$_POST["emailpagador"];
$ultimo_check = @$_POST["ultimocheck"];

// Validar datos requeridos
if (empty($rut) || empty($emailpagador)) {
  echo "error|El RUT y el correo son requeridos.";
  error_log("error|El RUT y el correo son requeridos.");
  //exit;
}

if (!is_numeric($ultimo_check) || $ultimo_check <= 0) {
  echo "error|El valor de 'ultimo_check' es inválido.";
  error_log("error|El valor de 'ultimo_check' es inválido.");
  //exit;
}

if($ultimo_check=="")
$$ultimo_check = 0;

$rut = formatea_rut($rut);

$mysql 		= new mysql;
$mysql->connect();

//Recorrer los input para obtener los id a pagar

$ids        = "";
$monto      = 0;
$id_usuario = 0;
$token_usuario="";

$hoy = date("Y-m-d");
//$token_deuda = $_POST["pago$i"];

for($i=1;$i<=$ultimo_check;$i++){
  if( isset( $_POST["pago".$i] ) ){
    $token_deuda = $_POST["pago$i"];
    $sqlD 					= $mysql->query("SELECT * FROM deudas WHERE token='$token_deuda' ;");

    error_log("for para los token de tabla deudas:");
    error_log($token_deuda);

    $resultD				= @$mysql->f_obj($sqlD);
    if(@$resultD->id_deuda>0){
       $ids = $ids . "|$resultD->id_deuda";
       $monto = $monto + $resultD->monto;
    }
  }
}


//Variables de los combos*****************************************
if( isset( $_POST["semestre1"] ) ||  isset( $_POST["semestre2"] ) ||  isset( $_POST["semestre1semestre2"] ) ){

  //Selecciono al usuario
  $sql 					= $mysql->query("SELECT id_usuario, nombre, token FROM perfil WHERE rut='$rut' ;");
  $result				= @$mysql->f_obj($sql);

  $id_usuario   = @$result->id_usuario;
  $nombre_usuario   = @$result->nombre;
  $token_usuario= @$result->token;

  var_export($token_usuario);
  echo $token_usuario;

  //Selección tipo de inscripción
  $sql 					= $mysql->query("SELECT tipo_inscripcion, id_plan_matricula FROM perfil WHERE id_usuario='$id_usuario' ;");
  $result				= @$mysql->f_obj($sql);
  $id_plan_matricula   = @$result->id_plan_matricula;

  //Selecciono valor del pago semestral para los combos
  $sql 					= $mysql->query("SELECT valor FROM plan WHERE id_plan_matricula='$id_plan_matricula' and  periodo = 'semestral';");
  $result				= @$mysql->f_obj($sql);
  $valor_semestral   = @$result->valor;

  //kop
  //Selecciono valor del pago anual para los combos
  $sql 					= $mysql->query("SELECT valor FROM plan WHERE id_plan_matricula='$id_plan_matricula' and  periodo = 'anual' ;");
  $result				= @$mysql->f_obj($sql);
  $valor_anual   = @$result->valor;
  //Kop

  $agno_actual= date("Y");

  if( isset( $_POST["semestre1"] ) ){
    $monto = $monto + $valor_semestral ;
    $ids = "semestre1";
  }

  if( isset( $_POST["semestre2"] ) ){
    $monto = $monto + $valor_semestral ;
    $ids = "semestre2";
  }

  //Kop
  if( isset( $_POST["semestre1semestre2"] ) ){
    $monto = $monto + $valor_anual;
    $ids = "semestre1semestre2";
  }
  //kop

}//if( isset( $_POST["semestre1"] ) ||  isset( $_POST["semestre2"] ) )


//**************************************************************** 

if($ids!="")
$ids= ltrim ($ids, "|");

//Selecciono al usuario
$sql 					= $mysql->query("SELECT id_usuario, token FROM perfil WHERE rut='$rut' ;");
$result				= @$mysql->f_obj($sql);

if(@$result->id_usuario!=""){
 $id_usuario   = @$result->id_usuario;
 $token_usuario= @$result->token;
}

$token = md5( rand(999,9999999) . $rut . $hoy . $emailpagador . $i );

$sql 					= $mysql->query("INSERT INTO flow (ids_deudas, id_usuario, fecha, rut, email, monto, flow_order, flow_status, token) VALUES ( '$ids','$id_usuario','$hoy','$rut','$emailpagador','$monto','0','0','$token' ) ;");
 
$ultimo_id    = $mysql->ultimo_id(); 

//error_log("token creado en crear_pago_flow.php: " . print_r($token, true));

echo "|$token|";



?>