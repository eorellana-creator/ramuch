<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

//*************************************************************************

$nombre 	  = @$_POST["nombre"];
$rut 	      = @$_POST["rut"];
$email 		  = @$_POST["email"];
$telefono 	= @$_POST["telefono"];
$password 	= @$_POST["password"];

$rut = formatea_rut($rut);

$fecha      = time (); 
$hora       =  date ( "H:i" , $fecha );
$dia        =  date ( "Y-m-d" , $fecha );

$password = md5($password);

$token = md5( $password . date("Y-m-d-h-i-s"). $email . rand(9999,999999) );

$hoy  = date("Y-m-d");
$hora = date("H:i:s");
$ip   = getRealIP();


if($nombre!="" && $rut!="" && $email!="" && $telefono!="" && $password!="" ){

      $mysql 		= new mysql;
      $mysql->connect();

      $sql 	= $mysql->query("INSERT INTO usuario (id_company, id_rol, nombre_usuario, email, password, fecha_registro, hora_registro, ip_registro, fecha_actualizacion, hora_actualizacion, ip_actualizacion, estado, token) VALUES 
                                                      ('1',     '8',  '$nombre',    '$email', '$password',  '$hoy',       '$hora',      '$ip',        '$hoy'           ,   '$hora',          '$ip',           'Por confirmar', '$token' ) ");
      $ultimo_id = $mysql->ultimo_id(); 

      $nacimiento = "0000-00-00";
      $hoy        = date("Y-m-d H:i:s");


$certificado_estudios="";

$patronIMG 	= "%\.(jpg|PNG|png|JPG|JPEG|jpeg|pdf|PDF)$%i";

$fis_arch = $_FILES["archivo"]["name"];
$aleatorio = rand(9999,99999999);

if ($fis_arch!="") {
	    preg_match($patronIMG, $fis_arch) == 1 ? $archivoValido ="S": $archivoValido ="N";
        if($archivoValido == "S"){
              $doc_ima = $fis_arch;
              $doc_ima_fisico =  date('Ymd_his') . "_cerfificado$aleatorio$ultimo_id." . pathinfo($fis_arch, PATHINFO_EXTENSION);

              move_uploaded_file($_FILES["archivo"]["tmp_name"], "../admin/components/socios/archivos/" . $doc_ima_fisico);

 

              $certificado_estudios = $doc_ima_fisico;
            }
}


$tipo_inscripcion   = 1; //Profesional
$id_plan_matricula  = 1;//Profesional


if( $certificado_estudios !=""){

$tipo_inscripcion   = 3; //Estudiante
$id_plan_matricula  = 3;//Estudiante


$sql8 					= $mysql->query("SELECT valor FROM plan_matricula WHERE id_plan_matricula='3';");
$result8 				= @$mysql->f_obj($sql8);

$valor_matricula = $result8->valor;

$fecha_deuda = date("Y-m-d");

$token_deuda = md5( rand(9999,999999).$nombre.$fecha_deuda.$hoy.$ultimo_id  );

$sql4 			= $mysql->query("INSERT INTO deudas (id_usuario, id_usuario_deuda, nombre_deudor, sub_cuenta,    fecha,           monto,  glosa, estado, fecha_insercion, token) 
                                         VALUES (      0,        '$ultimo_id',  '$nombre',     'matricula',   '$fecha_deuda','$valor_matricula','Matrícula Estudiante $nombre','por confirmar','$fecha_deuda','$token')	 ;");


}else{


$sql8 					= $mysql->query("SELECT valor FROM plan_matricula WHERE id_plan_matricula='1';");
$result8 				= @$mysql->f_obj($sql8);

$valor_matricula = $result8->valor;

$fecha_deuda = date("Y-m-d");

$token_deuda = md5( rand(9999,999999).$nombre.$fecha_deuda.$hoy.$ultimo_id  );

$sql4 			= $mysql->query("INSERT INTO deudas (id_usuario, id_usuario_deuda, nombre_deudor, sub_cuenta,    fecha,           monto,  glosa, estado, fecha_insercion, token) 
                                         VALUES (      0,        '$ultimo_id',  '$nombre',     'matricula',   '$fecha_deuda','$valor_matricula','Matrícula Profesional $nombre','por confirmar','$fecha_deuda','$token')	 ;");



}//if( $certificado_estudios !="")

      $token_perfil = md5(  rand(999,999999) . date("Y-m-d-h-i-s") . $ultimo_id );

      $sql 	= $mysql->query("INSERT INTO perfil (id_usuario, nombre,  tipo_inscripcion, id_plan_matricula,   fono, mail, rut, certificado_estudios, token ) 
                                          VALUES ('$ultimo_id','$nombre', '$tipo_inscripcion','$id_plan_matricula', '$telefono', '$email', '$rut','$certificado_estudios', '$token_perfil'  ) ");

 
 





      //Configuración de envío ***************************************************************
      $host 			= "mail.montanauchile.cl";
      $email_user 	= "no-responder@montanauchile.cl";
      $email_pass 	= "123ramuchchile2022";
      $email_from 	= "no-responder@montanauchile.cl";
      $email_to 		= "$email";
      $email_name 	= "Ramuch";
      $email_reply 	= $email;
      $email_subject 	= "Registro de Usuario Ramuch";
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
        <strong>Completa tu registro,</strong>
        <br><br>
        <strong>Estimado(a) $nombre, </strong><br><br>
        Para completar tu registro en <strong>Ramuch</strong> debes verificar tu email haciendo click en el siguiente enlace:<br>
        <a href='$ruta/verificacion.php?token=$token'></a><br><br>
        Tambien puedes copiar y pegar la siguiente dirección en su navegador web:
        $ruta/verificacion.php?token=$token<br><br>
        Una vez completado su registro, podrás acceder al sistema de socios de Ramuch.<br><br><br>
        <strong>Muchas Gracias</strong>
        </p>
        <br> 
        <br> 

      <p><a href='$ruta'><img src='$ruta/images/email_footer.png' border='0'  alt=''/><a/></p>

      </body>
      ";



      //*************************************************************************


        
      require_once("includes/PHPMailer2/PHPMailerAutoload.php");

      //Create a new PHPMailer instance
      $mail = new PHPMailer;
      //Tell PHPMailer to use SMTP
      $mail->isSMTP();
      //Enable SMTP debugging
      // 0 = off (for production use)
      // 1 = client messages
      // 2 = client and server messages
      $mail->SMTPDebug = 2;
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

      $mail->setFrom($email_from, "$nombre");
      //Set an alternative reply-to address
      $mail->addReplyTo($email_reply, 'Re: Registro de Usuario');
      //Set who the message is to be sent to
      $mail->addAddress($email_to, "$nombre ");
     

      //Set the subject line
      $mail->Subject = 'Registro de Usuario';
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

	


}//if($nombre!="" && $apellido!="" && $rut!="" && $email!="" && $telefono!="" && $password!="" )


?>