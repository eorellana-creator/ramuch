<?php
//ini_set('display_errors', '1');
session_start();
//include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= (int)($_SESSION["company_id"] ?? 0);
$id_usuario 	= (int)($_SESSION["usuario_id"] ?? 0);
$sesionAutorizada = (
  ($_SESSION["usuario_valido_socio_ramuch"] ?? '') === 'true'
  && ($_SESSION["usuario_origen"] ?? '') === 'socios'
) || (
  ($_SESSION["usuario_valido_bastro_ruta"] ?? '') === 'true'
  && ($_SESSION["usuario_origen"] ?? '') === 'admin'
);

$nombre_usuario	= $_SESSION["usuario_nombre"];
$email_usuario  = $_SESSION["usuario_email"];


$fecha1			= $_GET['fecha1'];
$fecha2			= $_GET['fecha2'];
$token			= $_GET['token'];

$nombre_solicita 	= @$_SESSION["usuario_nombre"];
$email_solicita 	= @$_SESSION["usuario_email"];


$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

// Ruta heredada: impedir solicitudes anónimas o con una sesión obsoleta.
$sqlUsuario = $mysql->query("SELECT id_usuario FROM usuario WHERE id_usuario='$id_usuario' AND estado='Vigente' LIMIT 1;");
if (!$sesionAutorizada || $id_usuario <= 0 || !$mysql->f_obj($sqlUsuario)) {
  http_response_code(401);
  exit("invalid user");
}


$hoy 	= date("Y-m-d");

if($token!=""){

    $sql 	= $mysql->query("SELECT * FROM equipo WHERE token='$token' ;");
    $result = $mysql->f_obj($sql);
    $id_equipo = $result->id_equipo;
    $nombre_equipo = $result->nombre;
    $id_unico = $result->id_unico;

    $token_nuevo = md5( date("Y-m-d h i s") . $id_usuario . rand(9999,9999999)  );
    
    // Kop validacion que el equipo ya no tenga una solicitud previa
    $se_presto = 0;
    $sqlp 	= $mysql->query("SELECT * FROM equipo_prestamo WHERE id_equipo = $id_equipo AND (estado = 'solicitado' OR estado = 'prestado') ;");
    $se_presto = $mysql->f_num($sqlp);
    //echo($se_presto);

    if ($se_presto == 0) { //si no existe prestado puede hacerlo, sino debe avisar que ya se solicito o presto.

      $sql 	= $mysql->query("INSERT INTO equipo_prestamo (id_equipo, id_usuario_prestamo, id_usuario_responsable, fecha_prestamo, fecha_debe_devolver, estado, token ) 
                                            VALUES('$id_equipo', '$id_usuario',          '0',           '$fecha1',          '$fecha2', 'solicitado', '$token_nuevo' ) ;");

      $fecha1 = date("d-m-Y", strtotime($fecha1));
      $fecha2 = date("d-m-Y", strtotime($fecha2));


      //Configuración de envío ***************************************************************
      $host 			= "mail.montanauchile.cl";
      $email_user 	= "no-responder@montanauchile.cl";
      $email_pass 	= "123ramuchchile2022";
      $email_from 	= "no-responder@montanauchile.cl";
      $email_to 		= "";
      $email_name 	= "Ramuch";
      $email_reply 	= "no-responder@montanauchile.cl";
      $email_subject 	= "Solicitud de préstamo de equipo";
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
        
        <strong>$nombre_solicita</strong> ($email_solicita) ha solicitado el préstamo del equipo N°$id_equipo con ID Unico $id_unico <strong>$nombre_equipo</strong> entre las fechas <strong>$fecha1</strong> y <strong>$fecha2</strong><br><br><br>
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
      //$mail->addAddress("montana.uchile@gmail.com", "Ramuch");

      //$mail->addAddress("djvaldiv@gmail.com", "Diego Valdivia");
      //$mail->addAddress("maudichili@gmail.com", "Mauricio Díaz");


      //los correos de la comisión

      $sql 	= $mysql->query("SELECT c.*, c.token as ctoken, u.* FROM comision_prestamo c INNER JOIN usuario u ON c.id_usuario=u.id_usuario ORDER BY u.nombre_usuario ;");
      
      $comision = "";
      while($result = $mysql->f_obj($sql)){

        $mail->addAddress("$result->email", "$result->nombre_usuario");
      
      }//while($result2 = $mysql->f_obj($sql2))

      $mail->addAddress("montana.uchile@gmail.com", "Ramuch");

      $mail->addAddress("$email_usuario", "$nombre_usuario");

      //Set the subject line
      $mail->Subject = 'Solicitud de Préstamo';
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
        $_SESSION["equipo_prestado"]="<div class='alert alert-danger' role='alert'>Ha ocurrido un error, por favor internte nuevamente o escríbanos a directiva@ramuch.cl indicando el equipo y fechas de solicitud de préstamo</div>";
      } else {
        echo "Message sent!";
        $_SESSION["equipo_prestado"] = "<div class='alert alert-success' role='alert'>El préstamo se ha solicitado exitosamente. Nos coordinaremos para ver disponibilidad y préstamo. Si deseas cancelar la solicitud, debes ir a Mi Perfil, en la pestaña Historial de Préstamos $se_presto</div>";
      }
    } else { // kop mensaje para avisar que ya se solicito o presto anteriormente el equipo.
      // kop
      echo "Message sent!";
      $_SESSION["equipo_prestado"] = "<div class='alert alert-danger' role='alert'>El equipo solicitado ya se encuentra prestado o solicitado, por favor refresca la pagina.</div>";
    }

}//if($token!="")


 



?>
