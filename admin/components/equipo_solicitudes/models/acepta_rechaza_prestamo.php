<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

function responderErrorSolicitud($mensaje, $estadoHttp)
{
	http_response_code($estadoHttp);
	echo json_encode([
		'success' => false,
		'error' => $mensaje
	]);
	exit;
}

include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];
$usuario_nombre	= $_SESSION["usuario_nombre"];
$email_usuario  = $_SESSION["usuario_email"];


$observacion	= trim($_POST['observacion'] ?? '');
$tipo 			= $_POST['tipo'] ?? ''; //0=rechaza  1=acepta
$token			= trim($_POST['token'] ?? '');


$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	responderErrorSolicitud('Método no permitido', 405);
}

if (empty($id_usuario) || !in_array((string)$tipo, ['0', '1'], true)
	|| !preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
	responderErrorSolicitud('Datos inválidos', 400);
}

if ((string)$tipo === '0' && $observacion === '') {
	responderErrorSolicitud('Debe ingresar el motivo del rechazo', 400);
}

$observacion = substr(strip_tags($observacion), 0, 255);
$observacion = str_replace("'", "''", $observacion);

$hoy 	= date("Y-m-d");

if($token!=""){

		$mysql->query("START TRANSACTION");
		$sql 	= $mysql->query("SELECT * FROM equipo_prestamo WHERE token='$token' AND estado='solicitado' FOR UPDATE;");
		$result = $mysql->f_obj($sql);
		if (!$result) {
			$mysql->query("ROLLBACK");
			responderErrorSolicitud('La solicitud ya fue procesada o cancelada', 409);
		}
		$id_equipo 				= $result->id_equipo;
	$id_usuario_prestamo 	= $result->id_usuario_prestamo;
	$fecha1 				= $result->fecha_prestamo;
	$fecha2 				= $result->fecha_debe_devolver;


	$sql 	= $mysql->query("SELECT nombre FROM equipo WHERE id_equipo='$id_equipo' ;");
	$result = $mysql->f_obj($sql);
	if (!$result) {
		$mysql->query("ROLLBACK");
		responderErrorSolicitud('No se encontró el equipo solicitado', 404);
	}
	$nombre_equipo = $result->nombre;



	$sql 	= $mysql->query("SELECT email, nombre_usuario FROM usuario WHERE id_usuario='$id_usuario_prestamo' ;");
	$result = $mysql->f_obj($sql);
	if (!$result) {
		$mysql->query("ROLLBACK");
		responderErrorSolicitud('No se encontró el usuario solicitante', 404);
	}
	$nombre_usuario = $result->nombre_usuario;
	$email_usuario = $result->email;


$texto_estado = "";

if($tipo==0){

$sql 	= $mysql->query("UPDATE equipo_prestamo SET estado='rechazado', id_usuario_responsable='$id_usuario', comentario='$observacion' WHERE token ='$token' AND estado='solicitado';");
if (!$sql) {
	$mysql->query("ROLLBACK");
	responderErrorSolicitud('No se pudo rechazar la solicitud', 500);
}

$texto_estado = "<strong>rechazado</strong> el préstamo de equipo. El motivo es: $observacion . ";
$_SESSION["equipo_prestado"] = "<div class='alert alert-success' role='alert'>El préstamo se ha rechazado.</div>";

}//if($tipo==0)


//********************************************************************************************************************************** */

if($tipo==1){

$sqlActivo = $mysql->query("SELECT id_equipo_prestamo FROM equipo_prestamo
	WHERE id_equipo='$id_equipo'
	AND token!='$token'
	AND estado IN ('solicitado', 'prestado')
	LIMIT 1 FOR UPDATE;");
if ($mysql->f_num($sqlActivo) > 0) {
	$mysql->query("ROLLBACK");
	responderErrorSolicitud('El equipo ya posee otra solicitud o préstamo activo', 409);
}

$sql 	= $mysql->query("UPDATE equipo_prestamo SET estado='prestado', id_usuario_responsable='$id_usuario' WHERE token ='$token' AND estado='solicitado';");
if (!$sql) {
	$mysql->query("ROLLBACK");
	responderErrorSolicitud('No se pudo aceptar la solicitud', 500);
}

$sql 	= $mysql->query("UPDATE equipo SET  prestado_a_id_usuario='$id_usuario_prestamo', prestado_a_nombre='$nombre_usuario', id_responsable_prestamo='$id_usuario' , nombre_responsable_prestamo='$usuario_nombre', fecha_devolucion='$fecha2' WHERE id_equipo ='$id_equipo';");
if (!$sql) {
	$mysql->query("ROLLBACK");
	responderErrorSolicitud('No se pudo actualizar el equipo', 500);
}

//echo "UPDATE equipo SET  prestado_a_id_usuario='$id_usuario_prestamo', prestado_a_nombre='$nombre_usuario', id_responsable_prestamo='$id_usuario' , nombre_responsable_prestamo='$usuario_nombre', fecha_devolucion='$fecha2' WHERE id_equipo ='$id_equipo';";



$texto_estado = "<strong>autorizado</strong> el préstamo del equipo. ";
$_SESSION["equipo_prestado"] = "<div class='alert alert-success' role='alert'>El préstamo se ha realizado.</div>";

}//if($tipo==1)

$mysql->query("COMMIT");

$fecha1 				= fecha_mysql_a_normal($fecha1);
$fecha2 				= fecha_mysql_a_normal($fecha2);


	// El préstamo ya quedó confirmado. El correo es una operación posterior:
	// si falla, no debe convertir una entrega correcta en un error para el usuario.
	$ruta = "https://ramuch.cl/registro";
	$cuerpo = "
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

 </head><body>
 <p><a href='$ruta'><img src='$ruta/images/email_cabecera.png' border='0' alt=''/></a> 
   <br> 
   <br>
   <strong>Solicitud de préstamo de equipo,</strong>
   <br><br>
   
  <strong>$nombre_usuario</strong> Se ha $texto_estado  Nombre del equipo: $nombre_equipo  entre las fechas <strong>$fecha1</strong> y <strong>$fecha2</strong><br><br><br>
   <strong>Sistema Ramuch</strong>
   </p>
   <br> 
   <br> 

 <p><a href='$ruta'><img src='$ruta/images/email_footer.png' border='0'  alt=''/><a/></p>

 </body>
 ";

	$correoEnviado = false;
	$errorCorreo = '';

	try {
		require_once(__DIR__ . "/../../../../vendor/autoload.php");

		$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Host = "mail.ramuch.cl";
		$mail->Port = 25;
		$mail->SMTPAuth = true;
		$mail->Username = "no-responder@ramuch.cl";
		$mail->Password = "1941ramuch2024";
		$mail->CharSet = 'UTF-8';
		$mail->setFrom("no-responder@ramuch.cl", "Sistema Ramuch");
		$mail->addReplyTo("no-responder@ramuch.cl", 'Re: Solicitud de Préstamo');

		$hostActual = strtolower($_SERVER['HTTP_HOST'] ?? '');
		$esStaging = strpos($hostActual, 'staging.ramuch.cl') !== false;

		if ($esStaging) {
			$mail->addAddress('eorellana@gmail.com', 'Pruebas staging');
		} else {
			$sqlComision = $mysql->query("SELECT c.*, c.token as ctoken, u.* FROM comision_prestamo c INNER JOIN usuario u ON c.id_usuario=u.id_usuario ORDER BY u.nombre_usuario;");
			while ($integrante = $mysql->f_obj($sqlComision)) {
				$mail->addAddress($integrante->email, $integrante->nombre_usuario);
			}
			$mail->addAddress($email_usuario, $nombre_usuario);
		}

		$mail->Subject = 'Respuesta solicitud de Préstamo';
		$mail->msgHTML($cuerpo);
		$mail->SMTPOptions = [
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			]
		];

		$correoEnviado = $mail->send();
	} catch (\Throwable $error) {
		$errorCorreo = $error->getMessage();
		error_log("Préstamo procesado, pero falló el correo: " . $errorCorreo);
	}

	$accionRealizada = ((string)$tipo === '1') ? 'Préstamo aceptado correctamente.' : 'Solicitud rechazada correctamente.';
	$mensajeRespuesta = $accionRealizada;

	if (!$correoEnviado) {
		$mensajeRespuesta .= ' No se pudo enviar el correo de notificación, pero la operación quedó registrada.';
	}

	echo json_encode([
		'success' => true,
		'message' => $mensajeRespuesta,
		'email_sent' => $correoEnviado,
		'token' => $token
	]);
	exit;
}

responderErrorSolicitud('No se recibió una solicitud válida', 400);
