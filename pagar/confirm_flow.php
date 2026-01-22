<?php
/**
 * Pagina del comercio para recibir la confirmación del pago
 * Flow notifica al comercio del pago efectuado
 */
require(__DIR__ . "/flow/lib/FlowApi.class.php");

// Configuración del sistema de logs
define('LOG_FILE', __DIR__.'/email_errors.log');

include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
}

// Cargar PHPMailer
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

	//print_r($estado);

	if($estado==2){

		$sql 					= $mysql->query("SELECT * FROM flow WHERE flow_order='$flow_order' ;");
		$result					= @$mysql->f_obj($sql);

		$id_usuario = $result->id_usuario;
		$monto 		= $result->monto;
		$ids_deudas	= $result->ids_deudas;

		$agno_actual = date("Y");

		$valor_cuota = 0;
		$valor_cuota_final = 0;

		// si esta pagando la matricula
		//if($ids_deudas=="matricula"){
		//	$sqlS5	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota', observacion='Pago vía Flow', documento_respaldo = '$flow_order' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='matricula' ;");
		//}

		if($ids_deudas=="semestre1" || $ids_deudas=="semestre2" ){

			$valor_cuota 		= (int)($monto / 6);
			$valor_cuota_final 	= $monto - ($valor_cuota*5);

		}//if($ids_deudas="semestre1" || $ids_deudas="semestre2" )

		if($ids_deudas=="semestre1semestre2"){

			$valor_cuota 		= (int)($monto / 12);
			$valor_cuota_final 	= $monto - ($valor_cuota*11);

		}//if($ids_deudas="semestre1semestre2")
		 
		if($ids_deudas=="semestre1"){
			$sqlS1	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota', observacion='Pago vía Flow', documento_respaldo = '$flow_order' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-01-01' OR fecha='$agno_actual-02-01' OR fecha='$agno_actual-03-01' OR fecha='$agno_actual-04-01' OR fecha='$agno_actual-05-01'   ) ;");
			$sqlS1	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota_final', observacion='Pago vía Flow', documento_respaldo = '$flow_order' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND (  fecha='$agno_actual-06-01' ) ;");
		}//if($ids_deudas="semestre1")



		if($ids_deudas=="semestre2"){
			$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota', observacion='Pago vía Flow', documento_respaldo = '$flow_order' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-07-01' OR fecha='$agno_actual-08-01' OR fecha='$agno_actual-09-01' OR fecha='$agno_actual-10-01' OR fecha='$agno_actual-11-01'   ) ;");
			$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota_final', observacion='Pago vía Flow', documento_respaldo = '$flow_order' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND (   fecha='$agno_actual-12-01' ) ;");
		}//if($ids_deudas="semestre2")



		if($ids_deudas=="semestre1semestre2"){
			$sqlS1	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-01-01' OR fecha='$agno_actual-02-01' OR fecha='$agno_actual-03-01' OR fecha='$agno_actual-04-01' OR fecha='$agno_actual-05-01' OR fecha='$agno_actual-06-01'  ) ;");
			$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-07-01' OR fecha='$agno_actual-08-01' OR fecha='$agno_actual-09-01' OR fecha='$agno_actual-10-01' OR fecha='$agno_actual-11-01'   ) ;");
			$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada', monto='$valor_cuota_final' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND (   fecha='$agno_actual-12-01' ) ;");
		}//if($ids_deudas="semestre1semestre2")



		//  aqui se incluiria el pago de la matricula ya que no esta filtrando que sea una subcuenta de cuota o otras,
		//  sin ese filtro pasaria la matricula por este update
		//Para cuotas o pagos normales****************************************************************
		if( $ids_deudas!="semestre1" && $ids_deudas!="semestre2" && $ids_deudas!="semestre1semestre2"){

			$ids_deudas = explode("|",$ids_deudas);


			foreach($ids_deudas as $id_deuda){
				$sqlS2	= $mysql->query("UPDATE deudas SET estado='pagada', observacion='Pago vía Flow', documento_respaldo = '$flow_order' WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND id_deuda='$id_deuda' ;");			  
			}// foreach

			// enviarmos correo si esta pagando una matricula

			//error_log("DEBUG: Pago confirmado (estado == 2) cero");
    		//error_log("DEBUG: Orden: $orden, Monto: $monto, Flow Order: $flow_order");

			$sqlDD = $mysql->query("SELECT * FROM deudas WHERE id_deuda='$id_deuda' ;");
			$resultDD	= @$mysql->f_obj($sqlDD);
			$matriculaX = $resultDD->sub_cuenta;

			if( $matriculaX == 'matricula'){
				//error_log("DEBUG: $matriculaX");

				$nombre= "";
				$email="";

				$sql = $mysql->query("SELECT * FROM usuario WHERE id_usuario='$id_usuario' ;");
				$result	= @$mysql->f_obj($sql);
				$nombre = $result->nombre_usuario;
				$email = $result->email;

				// Configuración PHPMailer ***************************************************************
				$host           = "mail.ramuch.cl";
				$email_user     = "no-responder@ramuch.cl";
				$email_pass     = "1941ramuch2024";
				$email_from     = "no-responder@ramuch.cl";
				$email_to       = "montana.uchile@gmail.com";
				$email_name     = "Ramuch";
				$email_reply    = "directiva@ramuch.cl";
				$email_subject  = "Nuevo Soci@ pago matricula";
				$ruta           = "https://ramuch.cl/registro";

				// Contenido del mensaje
				$message_html = "
				<html>
				<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
				<head>
				<style>
				body {
					font-family: Arial, serif;
					font-size: 14px;
					color: #1a1a1a;
				}
				.textos {
					font-family: Arial, serif;
					font-size: 12px;
					color: #1a1a1a;
				}
				</style>
				</head>
				<body>
				<p><a href='$ruta'><img src='$ruta/images/email_cabecera.png' border='0' alt=''/></a> 
					<br> 
					<br>
					<strong>Nuevo Socio(a) pago su matricula,</strong>
					<br><br>
					<strong>Estimada directiva, </strong><br><br>
					Soci(a) $nombre con email $email a pagado correctamente su matricula<br>
					<br><br>
					Ya es posible agregarlo al grupo de whatapps y enviar su saludos de bienvenida.
					<br><br>
					<strong>Servicios automaticos Ramuch</strong>
				</p>
				<br> 
				<br> 

				</body>
				</html>";

				// Versión texto plano
				$message_plain = "Nuevo Soci@ pago su matricula $nombre,\n\n Estimada directiva,\n\nSoci@ $nombre a pagado correctamente su matricula.\n\nYa es posible agregarlo al grupo de whatapps y enviar su saludos de bienvenida.\n\nServicios automaticos Ramuch";

				try {
					// Configuración del servidor SMTP
					$mail = new PHPMailer(true);
					$mail->isSMTP();
					$mail->Host       = $host;
					$mail->SMTPAuth   = true;
					$mail->Username   = $email_user;
					$mail->Password   = $email_pass;
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
					$mail->Port       = 587;
					$mail->SMTPDebug  = 2; // Habilita salida de depuración detallada
					$mail->Debugoutput = function($str, $level) {
						logError("PHPMailer [Nivel $level]: $str");
					};

					// Configuración de remitente y destinatario
					$mail->setFrom($email_from, $email_name);
					$mail->addAddress($email_to);
					$mail->addReplyTo($email_reply, $email_name);
					
					// Contenido del correo
					$mail->isHTML(true);
					$mail->Subject = $email_subject;
					$mail->Body    = $message_html;
					$mail->AltBody = $message_plain;
					
					// Enviar el correo
					if($mail->send()) {
						echo "El correo se ha enviado correctamente.";
						logError("Correo enviado con éxito a: $email_to");
					} else {
						throw new Exception("Error al enviar el correo");
					}



				} catch (Exception $e) {
					$errorMsg = "Error al enviar correo a $email_to: " . $e->getMessage() . " - PHPMailer Error: " . $mail->ErrorInfo;
					logError($errorMsg);
					echo "Error al enviar el correo. Se ha registrado el error para su revisión.";
				}

				// ENVÍO DE EMAIL DE BIENVENIDA AL NUEVO SOCIO
				try {
					$email_socio = $email; // Email del socio obtenido anteriormente
					$nombre_socio = $nombre; // Nombre del socio obtenido anteriormente
					
					// Configuración del email de bienvenida
					$email_subject_socio = "¡Bienvenido/a a Ramuch Club de Montaña!";
					$archivo_adjunto = "RAMUCH_Manual_de_Bienvenida.pdf"; // Nombre del archivo adjunto
					
					// Contenido HTML del mensaje de bienvenida
					$message_html_socio = "
					<html>
					<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
					<head>
					<style>
					body {
						font-family: Arial, sans-serif;
						font-size: 14px;
						color: #1a1a1a;
						line-height: 1.6;
					}
					.textos {
						font-family: Arial, sans-serif;
						font-size: 12px;
						color: #1a1a1a;
					}
					.header {
						background-color: #f8f9fa;
						padding: 20px;
						text-align: center;
					}
					.content {
						padding: 20px;
					}
					.footer {
						background-color: #f8f9fa;
						padding: 15px;
						text-align: center;
						font-size: 12px;
					}
					</style>
					</head>
					<body>
					<div class='header'>
						<img src='https://ramuch.cl/pagar/images/tf.png' alt='Logo Ramuch' width='150' border='0'>
						<h2>¡Bienvenido/a a Ramuch!</h2>
					</div>
					
					<div class='content'>
						<p><strong>Estimado/a $nombre_socio,</strong></p>
						
						<p>¡Te damos la más cordial bienvenida a nuestro Club de Montaña Ramuch!</p>
						
						<p>Nos alegra confirmar que tu matrícula ha sido procesada exitosamente. 
						A partir de ahora formas parte de nuestra comunidad de amantes de la montaña.</p>
						
						<p><strong>Información importante:</strong></p>
						<ul>
							<li>Número de orden: $flow_order</li>
							<li>Monto pagado: $$monto CLP</li>
							<li>Fecha de incorporación: " . date('d/m/Y') . "</li>
						</ul>
						
						<p>En el archivo adjunto encontrarás información importante sobre:</p>
						<ul>
							<li>Normativas del club</li>
							<li>Calendario de actividades</li>
							<li>Recomendaciones de seguridad</li>
							<li>Contactos importantes</li>
						</ul>
						
						<p>Próximamente nos estaremos comunicando contigo para integrarte a nuestros 
						grupos de WhatsApp y informarte sobre las próximas actividades programadas.</p>
						
						<p>¡Esperamos que disfrutes de muchas aventuras en la montaña con nosotros!</p>
						
						<p>Saludos cordiales,<br>
						<strong>Directiva Ramuch Club de Montaña</strong></p>
					</div>
					
					<div class='footer'>
						<p>RAMUCH - Club de Montaña Universidad de Chile<br>
						Email: directiva@ramuch.cl | Website: <a href='https://ramuch.cl'>ramuch.cl</a></p>
					</div>
					</body>
					</html>";

					// Versión texto plano
					$message_plain_socio = "¡Bienvenido/a a Ramuch Club de Montaña!

				Estimado/a $nombre_socio,

				Te damos la más cordial bienvenida a nuestro Club de Montaña Ramuch. 
				Tu matrícula ha sido procesada exitosamente.

				Información:
				- Orden: $flow_order
				- Monto: $$monto CLP
				- Fecha: " . date('d/m/Y') . "

				En el archivo adjunto encontrarás información importante sobre el club.

				Próximamente nos contactaremos para integrarte a nuestros grupos.

				Saludos cordiales,
				Directiva Ramuch Club de Montaña
				Email: directiva@ramuch.cl
				Web: ramuch.cl";

					// Configuración PHPMailer para el socio
					$mail_socio = new PHPMailer(true);
					$mail_socio->isSMTP();
					$mail_socio->Host       = $host;
					$mail_socio->SMTPAuth   = true;
					$mail_socio->Username   = $email_user;
					$mail_socio->Password   = $email_pass;
					$mail_socio->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
					$mail_socio->Port       = 587;
					$mail_socio->SMTPDebug  = 2;
					$mail_socio->Debugoutput = function($str, $level) {
						logError("PHPMailer Socio [Nivel $level]: $str");
					};

					// Configuración de remitente y destinatario
					$mail_socio->setFrom($email_from, $email_name);
					$mail_socio->addAddress($email_socio, $nombre_socio);
					$mail_socio->addReplyTo($email_reply, $email_name);
					
					// Adjuntar archivo (asegúrate de que la ruta sea correcta)
					//$ruta_archivo = $_SERVER['DOCUMENT_ROOT'] . 'components/socios/archivos/' . $archivo_adjunto;
					$ruta_archivo = __DIR__ . '/archivos/' . $archivo_adjunto;

					if(file_exists($ruta_archivo)) {
						$mail_socio->addAttachment($ruta_archivo, $archivo_adjunto);
					} else {
						logError("Archivo adjunto no encontrado: $ruta_archivo");
					}
					
					// Contenido del correo
					$mail_socio->isHTML(true);
					$mail_socio->Subject = $email_subject_socio;
					$mail_socio->Body    = $message_html_socio;
					$mail_socio->AltBody = $message_plain_socio;
					
					// Enviar el correo al socio
					if($mail_socio->send()) {
						logError("Email de bienvenida enviado con éxito a: $email_socio");
						echo "Email de bienvenida enviado al socio.";
					} else {
						throw new Exception("Error al enviar email de bienvenida al socio");
					}

				} catch (Exception $e) {
					$errorMsg = "Error al enviar email de bienvenida a $email_socio: " . $e->getMessage() . " - PHPMailer Error: " . $mail_socio->ErrorInfo;
					logError($errorMsg);
					echo "Error al enviar email de bienvenida. Se ha registrado el error.";
				}
			} else {
					//error_log("DEBUG ELSE: $matriculaX");
			}// if es matricula

		}//if( $ids_deudas!="semestre1" && $ids_deudas!="semestre2" && $ids_deudas=="semestre1semestre2")


		$fecha = date("Y-m-d");

		$token_nuevo = md5(  rand(999,999999) . $fecha );

		$sql 				= $mysql->query("SELECT id_usuario, nombre_usuario, token FROM usuario WHERE id_usuario='$id_usuario' ;");
		$result				= @$mysql->f_obj($sql);

		$id_usuario   = @$result->id_usuario;
		$nombre_usuario   = @$result->nombre_usuario;
		$token_usuario= @$result->token;

		$sql 	= $mysql->query("INSERT INTO cuenta_maestra (id_usuario_sistema, nombre_usuario_sistema, id_usuario_movimiento, nombre,             fecha,    tipo,   sub_cuenta,   glosa,         observacion,     medio, id_transaccion, documento_respaldo, monto, estado, token)
														VALUES ('$id_usuario',      '$nombre_usuario',      '$id_usuario',     '$nombre_usuario',  '$fecha', 'ingreso', 'cuota',    'Pago vía Flow',     '',          'Flow','$flow_order',          '',        '$monto','activo', '$token_nuevo'  ) ;");

		// agregar envio de correo si el pago de es metricula, avisando a la directiva que se pago la matricula.
		// subcuenta tiene el valor 


		}//if($estado==2)

	
} catch (Exception $e) {
	echo "Error: " . $e->getCode() . " - " . $e->getMessage();
}
?>