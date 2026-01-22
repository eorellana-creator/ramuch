<?php
/**
 * Pagina del comercio para recibir la confirmación del pago
 * Flow notifica al comercio del pago efectuado
 */
require(__DIR__ . "/flow/lib/FlowApi.class.php");

include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");


try {
	if(!isset($_POST["token"])) {
			throw new Exception("No se recibio el token", 1);
	}
	
	$token = filter_input(INPUT_POST, 'token');
	$params = array(
		"token" => $token
	);
	$serviceName = "payment/getStatus";
	$flowApi = new FlowApi();
	$response = $flowApi->send($serviceName, $params, "GET");
	
	//Actualiza los datos en su sistema
	
	$orden = @$response["flowOrder"];
	$monto = @$response["amount"];
	$estado = @$response["status"];
	$flow_order = @$response["flowOrder"];

	$mysql 		= new mysql;
	$mysql->connect();

	$sql = $mysql->query("UPDATE flow SET flow_status='$estado' WHERE flow_order='$orden'  ;");

	// Convertir las variables en una cadena JSON para pasarlas a JavaScript

	$variables = json_encode([
		'Orden' => $orden,
		'Monto' => $monto,
		'Estado' => $estado,
		'Flow Order' => $flow_order,
	]);

	print_r($estado);

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
			$valor_cuota_final 	= $monto - ($valor_cuota*5);

		}//if($ids_deudas="semestre1" || $ids_deudas="semestre2" )

		if($ids_deudas=="semestre1semestre2"){

			$valor_cuota 		= (int)($monto / 12);
			$valor_cuota_final 	= $monto - ($valor_cuota*11);

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


$fecha = date("Y-m-d");

$token_nuevo = md5(  rand(999,999999) . $fecha );

$sql 				= $mysql->query("SELECT id_usuario, nombre_usuario, token FROM usuario WHERE id_usuario='$id_usuario' ;");
$result				= @$mysql->f_obj($sql);

$id_usuario   = @$result->id_usuario;
$nombre_usuario   = @$result->nombre_usuario;
$token_usuario= @$result->token;

$sql 	= $mysql->query("INSERT INTO cuenta_maestra (id_usuario_sistema, nombre_usuario_sistema, id_usuario_movimiento, nombre,  fecha, tipo,   sub_cuenta,   glosa,         observacion,     medio, id_transaccion, documento_respaldo, monto, estado, token)
										           	VALUES ( '$id_usuario',   '$nombre_usuario',   '$id_usuario',   '$nombre_usuario',  '$fecha', 'ingreso', 'cuota','Pago vía Flow',     '',          'Flow','$flow_order',          '',        '$monto','activo', '$token_nuevo'  ) ;");






	}//if($estado==2)



/*
	Array ( 
		[flowOrder] => 1091058 
		[commerceOrder] => 1543 
		[requestDate] => 2022-07-20 13:06:07 
		[status] => 2 
		[subject] => Pago de prueba 
		[currency] => CLP 
		[amount] => 5000 
		payer] => maudichili@gmail.com 
		[optional] => Array ( [rut] => 11111111-1 [otroDato] => otroDato ) [pending_info] => Array ( [media] => [date] => ) [paymentData] => Array ( [date] => 2022-07-20 13:06:39 [media] => Webpay [conversionDate] => [conversionRate] => [amount] => 5000.00 [fee] => 160.00 [balance] => 4810 [transferDate] => 2022-07-21 00:00:00 [currency] => CLP [taxes] => 30 ) [merchantId] => ) 
*/
	
} catch (Exception $e) {
	echo "Error: " . $e->getCode() . " - " . $e->getMessage();
}
?>