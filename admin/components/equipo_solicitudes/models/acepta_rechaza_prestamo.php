<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];
$usuario_nombre	= $_SESSION["usuario_nombre"];
$email_usuario  = $_SESSION["usuario_email"];


$observacion	= $_GET['observacion'];
$tipo 			= $_GET['tipo']; //0=rechaza  1=acepta
$token			= $_GET['token'];


$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();


$hoy 	= date("Y-m-d");

if($token!=""){


	$sql 	= $mysql->query("SELECT * FROM equipo_prestamo WHERE token='$token' ;");
	$result = $mysql->f_obj($sql);
	$id_equipo 				= $result->id_equipo;
	$id_usuario_prestamo 	= $result->id_usuario_prestamo;
	$fecha1 				= $result->fecha_prestamo;
	$fecha2 				= $result->fecha_debe_devolver;


	$sql 	= $mysql->query("SELECT nombre FROM equipo WHERE id_equipo='$id_equipo' ;");
	$result = $mysql->f_obj($sql);
	$nombre_equipo = $result->nombre;



	$sql 	= $mysql->query("SELECT email, nombre_usuario FROM usuario WHERE id_usuario='$id_usuario_prestamo' ;");
	$result = $mysql->f_obj($sql);
	$nombre_usuario = $result->nombre_usuario;
	$email_usuario = $result->email;


$texto_estado = "";

if($tipo==0){

$sql 	= $mysql->query("UPDATE equipo_prestamo SET estado='rechazado', id_usuario_responsable='$id_usuario', comentario='$observacion' WHERE token ='$token';");

$texto_estado = "<strong>rechazado</strong> el préstamo de equipo. El motivo es: $observacion . ";
$_SESSION["equipo_prestado"] = "<div class='alert alert-success' role='alert'>El préstamo se ha rechazado.</div>";

}//if($tipo==0)


//********************************************************************************************************************************** */

if($tipo==1){


$sql 	= $mysql->query("UPDATE equipo_prestamo SET estado='prestado', id_usuario_responsable='$id_usuario'  WHERE token ='$token';");


$sql 	= $mysql->query("UPDATE equipo SET  prestado_a_id_usuario='$id_usuario_prestamo', prestado_a_nombre='$nombre_usuario', id_responsable_prestamo='$id_usuario' , nombre_responsable_prestamo='$usuario_nombre', fecha_devolucion='$fecha2' WHERE id_equipo ='$id_equipo';");


//echo "UPDATE equipo SET  prestado_a_id_usuario='$id_usuario_prestamo', prestado_a_nombre='$nombre_usuario', id_responsable_prestamo='$id_usuario' , nombre_responsable_prestamo='$usuario_nombre', fecha_devolucion='$fecha2' WHERE id_equipo ='$id_equipo';";



$texto_estado = "<strong>autorizado</strong> el préstamo del equipo. ";
$_SESSION["equipo_prestado"] = "<div class='alert alert-success' role='alert'>El préstamo se ha realizado.</div>";

}//if($tipo==1)

$fecha1 				= fecha_mysql_a_normal($fecha1);
$fecha2 				= fecha_mysql_a_normal($fecha2);


 //Configuración de envío ***************************************************************
 $host 			= "mail.ramuch.cl";
 $email_user 	= "no-responder@ramuch.cl";
 $email_pass 	= "1941ramuch2024";
 $email_from 	= "no-responder@ramuch.cl";
 $email_to 		= "";
 $email_name 	= "Ramuch";
 $email_reply 	= "no-responder@ramuch.cl";
 $email_subject 	= "Respuesta solicitud de préstamo de equipo";
 $ruta			= "https://ramuch.cl/registro";
 //**************************************************************************************


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

 //*************************************************************************
 require_once("../../../includes/PHPMailer2/PHPMailerAutoload.php");

 //Create a new PHPMailer instance
 $mail = new PHPMailer;
 //Tell PHPMailer to use SMTP
 $mail->isSMTP();
 //Enable SMTP debugging
 // 0 = off (for production use)
 // 1 = client messages
 // 2 = client and server messages
 $mail->SMTPDebug = 0;
 //Ask for HTML-friendly debug output
 $mail->Debugoutput = 'html';
 //Set the hostname of the mail server
 $mail->Host = $host;
 //Set the SMTP port number - likely to be 25, 465 or 587
 $mail->Port = 25;
 //Whether to use SMTP authentication
 $mail->SMTPAuth = true;
 //Username to use for SMTP authentication
 $mail->Username = $email_user;
 //Password to use for SMTP authentication
 $mail->Password = $email_pass;
 //Set who the message is to be sent from
 $mail->CharSet = 'UTF-8';

 $mail->setFrom($email_from, "Sistema Ramuch");
 //Set an alternative reply-to address
 $mail->addReplyTo($email_reply, 'Re: Solicitud de Préstamo');
 //Set who the message is to be sent to
 //$mail->addAddress($email_to, "$nombre ");
  //los correos de la comisión

  $sql 	= $mysql->query("SELECT c.*, c.token as ctoken, u.* FROM comision_prestamo c INNER JOIN usuario u ON c.id_usuario=u.id_usuario ORDER BY u.nombre_usuario ;");
 
  $comision = "";
  while($result = $mysql->f_obj($sql)){

    $mail->addAddress("$result->email", "$result->nombre_usuario");
 
  }//while($result2 = $mysql->f_obj($sql2))

// Kop eliminado que se envie copia a la directiva $mail->addAddress("montana.uchile@gmail.com", "Ramuch");

$mail->addAddress("$email_usuario", "$nombre_usuario");

 //Set the subject line
 $mail->Subject = 'Respuesta solicitud de Préstamo';
 //Read an HTML message body from an external file, convert referenced images to embedded,
 //convert HTML into a basic plain-text alternative body
 $mail->msgHTML($cuerpo);
 //Replace the plain text body with one created manually
 //$mail->AltBody = 'Este es un mensaje';
 //Attach an image file
 //$mail->addAttachment('images/phpmailer_mini.png');

 $mail->SMTPOptions = array(
	 'ssl' => array(
		 'verify_peer' => false,
		 'verify_peer_name' => false,
		 'allow_self_signed' => true
	 )
 );

 //send the message, check for errors
 if (!$mail->send()) {
   echo "Mailer Error: " . $mail->ErrorInfo;
  
 } else {
   echo "Message sent!";
   
 }


}//if($token!="")


echo "|$token|";



?>