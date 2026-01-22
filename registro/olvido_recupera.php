<?php
include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

$dato			= @$_POST['dato'];


$mysql 		= new mysql;
$mysql->connect();

$existe	= 0;


$sql 	= $mysql->query("SELECT id_usuario,nombre_usuario, email, token FROM usuario WHERE  email='$dato' AND email!=''   ; ");
$existe	= $mysql->f_num($sql);
$result = $mysql->f_obj($sql);
$nombre      = @$result->nombre_usuario;
$id_usuario = @$result->id_usuario;
$email      = @$result->email;


//echo "SELECT id_usuario,nombre_usuario, email, token FROM usuario WHERE  email='$dato' AND mail!=''   ; ";


if($existe>0){
$existe = 1;

//echo "SELECT token FROM usuario WHERE id_usuario='$id_usuario'   ; ";

$sql2 	= $mysql->query("SELECT token FROM usuario WHERE id_usuario='$id_usuario'   ; ");
$result2 = $mysql->f_obj($sql2);
$token = @$result2->token;

      //Configuración de envío ***************************************************************
      $host 			= "mail.ramuch.cl";
      $email_user 	= "no-responder@ramuch.cl";
      $email_pass 	= "1941ramuch2024";
      $email_from 	= "no-responder@ramuch.cl";
      $email_to 		= "$email";
      $email_name 	= "Ramuch";
      $email_reply 	= "no-responder@ramuch.cl";
      $email_subject 	= "Recuperar contraseña Socio(a) Ramuch";
      $ruta			= "https://ramuch.cl/registro";
      //**************************************************************************************
/*
      require "includes/PHPMailer/src/Exception.php";
      require "includes/PHPMailer/src/PHPMailer.php";
      require "includes/PHPMailer/src/SMTP.php";
      
      $mail = new PHPMailer(true);
      try {
          // Configuración del servidor
          $mail->isSMTP();
          $mail->Host = "mail.ramuch.cl";
          $mail->SMTPAuth = true;
          $mail->Username = 'no-responder@ramuch.cl';
          $mail->Password = '1941ramuch2024';
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
          $mail->Port = 587;
      
          // Destinatarios
          $mail->setFrom('no-responder@ramuch.cl', 'Mailer');
          $mail->addAddress($email);
      
          // Contenido
          $mail->isHTML(true);
          $mail->Subject = 'Asunto del correo';
          $mail->Body    = 'Este es el cuerpo del mensaje en <b>HTML!</b>';
      
          $mail->send();
          echo 'El mensaje ha sido enviado';
      } catch (Exception $e) {
          echo "El mensaje no pudo ser enviado. Error: {$mail->ErrorInfo}";
      }

*/

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
        <strong>Recuperación de contraseña,</strong>
        <br><br>
        <strong>Estimado(a) usuario(a), </strong><br><br>
        Para recuperar tu contraseña en <strong>Ramuch</strong> debes ingresar haciendo click en el siguiente enlace:<br>
        <a href='$ruta/recupera.php?token=$token'></a><br><br>
        Tambien puedes copiar y pegar la siguiente dirección en tu navegador web:
        $ruta/recupera.php?token=$token<br><br>
        <br>
        <strong>Muchas Gracias</strong>
        </p>
        <br> 
        <br> 

      <p><a href='$ruta'><img src='$ruta/images/email_footer.png' border='0'  alt=''/><a/></p>

      </body>
      ";

       
      require_once("includes/PHPMailer2/PHPMailerAutoload.php");

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

      $mail->setFrom($email_from, "Ramuch");
      //Set an alternative reply-to address
      $mail->addReplyTo($email_reply, 'Re: recuperacion de contraseña Ramuch');
      //Set who the message is to be sent to
      $mail->addAddress($email_to, "Estimado(a)");
     

      //Set the subject line
      $mail->Subject = $email_subject;
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

}// if ($existe>0)

echo "|$existe|";
 
?>