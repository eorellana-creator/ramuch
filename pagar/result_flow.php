<?php
/**
 * Pagina del comercio para redireccion del pagador
 * A esta página Flow redirecciona al pagador pasando vía POST
 * el token de la transacción. En esta página el comercio puede
 * mostrar su propio comprobante de pago
 */
require(__DIR__ . "/flow/lib/FlowApi.class.php");


$mensaje 	= "";
$titulo 	= "";

include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

try {
	//Recibe el token enviado por Flow
	/*
	if(isset($_POST["token"])) {
		throw new Exception("tiene token", 1);
		$token = filter_input(INPUT_POST, 'token');
		throw new Exception($token, 1);
	}
	*/
	if(!isset($_POST["token"])) {
		throw new Exception("No se recibio el token", 1);
	}
	$token = filter_input(INPUT_POST, 'token');
	$params = array(
		"token" => $token
	);
	//Indica el servicio a utilizar
	$serviceName = "payment/getStatus";
	$flowApi = new FlowApi();
	$response = $flowApi->send($serviceName, $params, "GET");
	
	//print_r($response);

	$orden = @$response["commerceOrder"];
	$monto = @$response["amount"];
    $orden = @$response["flowOrder"];

	$monto = number_format($monto, 0, '', '.');

	
 	$fecha = date("m-d-Y");
	//print_r($response["status"]);
	
	if($response["status"]=="2"){ // 2 = pagada
		$titulo = "Pago exitoso";
		$mensaje = "<strong>El pago se ha realizado exitosamente.</strong><br><br>
		<strong>Fecha:</strong> $fecha<br> 
		<strong>N° de Orden:</strong> $orden<br>
		<strong>Monto:</strong> $monto<br><br>
		<strong>Muchas gracias!</strong>
		";
	}//if($response["status"]=="2")


	if($response["status"]=="3"){ // 3 = rechazada
		$titulo = "Pago rechazado";
		$mensaje = "<strong>El pago ha sido rechazado por su banco.</strong><br><br>
		<strong>Fecha:</strong> $fecha<br> 
		<strong>N° de Orden:</strong> $orden<br>
		<strong>Monto:</strong> $monto<br><br>
		<strong>Muchas gracias.</strong>
		";
	}//if($response["status"]=="3")


	if($response["status"]=="4"){ // 4 = anulado
		$titulo = "Pago anulado";
		$mensaje = "<strong>El pago ha sido anulado.</strong><br><br>
		<strong>Fecha:</strong> $fecha<br> 
		<strong>N° de Orden:</strong> $orden<br>
		<strong>Monto:</strong> $monto<br><br>
		<strong>Muchas gracias.</strong>
		";
	}//if($response["status"]=="4")




/*

	$monto = @$response["amount"];
	$estado = @$response["status"];


	$mysql 		= new mysql;
	$mysql->connect();

	$sql = $mysql->query("UPDATE flow SET flow_status='$estado' WHERE flow_order='$orden'  ;");

 


	if($estado==2){

		$sql 					= $mysql->query("SELECT * FROM flow WHERE flow_order='$flow_order' ;");
		$result					= @$mysql->f_obj($sql);

		$id_usuario = $result->id_usuario;
		$monto 		= $result->monto;
		$ids_deudas	= $result->ids_deudas;

		$agno_actual = date("Y");

		$valor_cuota = 0;
		$valor_cuota_final = 0;

		if($ids_deudas=="semestre1" || $ids_deudas=="semestre2" ){

			$valor_cuota 		= (int)($monto / 6);
			$valor_cuota_final 	= $monto - $valor_cuota;

		}//if($ids_deudas="semestre1" || $ids_deudas="semestre2" )

		if($ids_deudas=="semestre1semestre2"){

			$valor_cuota 		= (int)($monto / 12);
			$valor_cuota_final 	= $monto - $valor_cuota;

		}//if($ids_deudas="semestre1semestre2")
		 


		if($ids_deudas=="semestre1"){
			$sqlS1	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-01-01' OR fecha='$agno_actual-02-01' OR fecha='$agno_actual-03-01' OR fecha='$agno_actual-04-01' OR fecha='$agno_actual-05-01'   ) ;");
			$sqlS1	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota_final' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND (  fecha='$agno_actual-06-01' ) ;");
		}//if($ids_deudas="semestre1")



		if($ids_deudas=="semestre2"){
			$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-07-01' OR fecha='$agno_actual-08-01' OR fecha='$agno_actual-09-01' OR fecha='$agno_actual-10-01' OR fecha='$agno_actual-11-01'   ) ;");
			$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota_final' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND (   fecha='$agno_actual-12-01' ) ;");
		}//if($ids_deudas="semestre2")



		if($ids_deudas=="semestre1semestre2"){
			$sqlS1	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-01-01' OR fecha='$agno_actual-02-01' OR fecha='$agno_actual-03-01' OR fecha='$agno_actual-04-01' OR fecha='$agno_actual-05-01' OR fecha='$agno_actual-06-01'  ) ;");
			$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-07-01' OR fecha='$agno_actual-08-01' OR fecha='$agno_actual-09-01' OR fecha='$agno_actual-10-01' OR fecha='$agno_actual-11-01'   ) ;");
			$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota_final' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND (   fecha='$agno_actual-12-01' ) ;");
		}//if($ids_deudas="semestre1semestre2")




		//Para cuotas o pagos normales****************************************************************
		if( $ids_deudas!="semestre1" && $ids_deudas!="semestre2" && $ids_deudas!="semestre1semestre2"){

			$ids_deudas = explode("|",$ids_deudas);


			foreach($ids_deudas as $id_deuda){
				$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND id_deuda='$id_deuda' ;");			  
			}// foreach



		}//if( $ids_deudas!="semestre1" && $ids_deudas!="semestre2" && $ids_deudas=="semestre1semestre2")





	}//if($estado==2)

*/

















	
} catch (Exception $e) {
	echo "Error: " . $e->getCode() . " - " . $e->getMessage();
}

?>




<!DOCTYPE html>

<html lang="es">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>Pagos Ramuch</title>

    <!-- Icons-->
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">

    <link href="node_modules/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
    <link href="node_modules/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
    <link href="node_modules/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="node_modules/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="css/style.css" rel="stylesheet">
    <link href="vendors/pace-progress/css/pace.min.css" rel="stylesheet">

    <link rel="stylesheet" href="js/validate-password/css/jquery.passwordRequirements.css" />


  </head>

  <style>
      .app, app-dashboard, app-root{
        min-height: 0px !important;
      }
  </style>


 


  <body class="app flex-row align-items-center" >


  


      <div class="container" style="margin-top:40px;">
            <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data">
            <input id="total" name="total" type="hidden" value="0">
            <input id="rutpagador" name="rutpagador" type="hidden" value="0">
            <input id="emailpagador" name="emailpagador" type="hidden" value="0">

                  <div class="row justify-content-center">
                    <div class="col-md-6">
                      <div class="card mx-4">
                        <div class="card-body p-4">
                          <h1><img src="images/tf.png" alt="Logo Ramuch" > &nbsp; Ramuch</h1>

                          
                          <p class="text-muted"> <?php echo $titulo;?></p>




<div  id="contenido">


     
<br> 


                     <div style="border:1px solid #cccccc; padding:20px;">
					 <?php echo $mensaje;?>

					</div>



 



                          
<br> <br>
             
                        </div>


</div>



                        <div class="card-footer p-4">
                          <div class="row">
                            <div class="col-6">
                              <a href="index.php">
                              <button class="btn btn-block btn-secondary color-bl" type="button">
                                <span>volver a Pagos</span>
                              </button>
                              </a>
                            </div>
                            <div class="col-6">
                            <a href="http://www.ramuch.cl/">
                              <button class="btn btn-block btn-info color-bl" type="button">
                                <span>volver a Ramuch</span>
                              </button>
                            </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
            </form>
      </div>









    <!-- CoreUI and necessary plugins-->
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="node_modules/pace-progress/pace.min.js"></script>
    <script src="node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
    <script src="node_modules/@coreui/coreui/dist/js/coreui.min.js"></script>

    <script language="JavaScript" src="js/jquery.blockUI.js"></script>
    <script src="js/validadores.js"></script>
    <script src="js/rut/jquery.rut.js"></script>
    <script src="js/validate-password/js/jquery.passwordRequirements.js"></script>


    <script>

 

    </script>



  </body>
</html>
