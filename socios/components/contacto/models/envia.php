<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];


$nombre			    = $_POST['nombre'];
$asunto			    = $_POST['asunto'];
$mensaje			  = $_POST['mensaje'];



$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

 
$fecha = date("d-m-Y");


$nombre_solicita 	= @$_SESSION["usuario_nombre"];
$email_solicita 	= @$_SESSION["usuario_email"];




$hoy 	= date("Y-m-d");

if($nombre_solicita!=""){






 //Configuración de envío ***************************************************************
 $host 			= "mail.montanauchile.cl";
 $email_user 	= "no-responder@montanauchile.cl";
 $email_pass 	= "123ramuchchile2022";
 $email_from 	= "no-responder@montanauchile.cl";
 $email_to 		= "";
 $email_name 	= "Ramuch";
 $email_reply 	= "no-responder@montanauchile.cl";
 $email_subject 	= "Contacto desde Intranet Socios";
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
   <strong>Directiva de Ramuch,</strong>
   <br><br>
   
  <strong>$nombre_solicita</strong> ($email_solicita) ha enviado el siguiente mensaje:<br>
  <strong>Asunto :</strong> $asunto<br>
  <strong>Mensaje :</strong> $mensaje<br>
  
  <br><br><br>
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
 $mail->addReplyTo($email_reply, 'Re: Contacto desde Intranet Socios');
 //Set who the message is to be sent to
 //$mail->addAddress($email_to, "$nombre ");
 $mail->addAddress("montana.uchile@gmail.com", "Ramuch");
 //$mail->addAddress("maudichili@gmail.com", "Ramuch");


 //Set the subject line
 $mail->Subject = 'Contacto desde Intranet Socios';
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
   $_SESSION["mensaje_contactanos"]="<div class='alert alert-danger' role='alert'>Ha ocurrido un error, por favor intenta nuevamente o escríbenos a montana.uchile@gmail.com tu mensaje</div>";
 } else {
   echo "Message sent!";
   $_SESSION["mensaje_contactanos"] = "<div class='alert alert-success' role='alert'>Gracias por contactarnos. Responderemos a la brevedad. </div>";
 }








 


}//if($token!="")


 



?>