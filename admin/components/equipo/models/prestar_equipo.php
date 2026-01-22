<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];


$fecha			= $_GET['fecha'];
$user 			= $_GET['user'];
$token			= $_GET['token'];
$tokenEquipo 	= $token;


$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();


//Comprobamos si tiene deudas de más de 3 meses: *************************************************************

$fechaActual = date('Y-m-d');

$fecha3mesesAtras = strtotime ('-3 month', strtotime($fechaActual));

$fecha3mesesAtras = date('Y-m-d', $fecha3mesesAtras);
 
$cantidad_deuda_atrasada = 0;

$sql0 	= $mysql->query("SELECT id_deuda FROM deudas  WHERE id_usuario_deuda='$id_usuario' AND estado='activa' AND fecha<'$fecha3mesesAtras' ;");
$cantidad_deuda_atrasada = $mysql->f_num($sql0);



//********************************************************************************************************** */







if($cantidad_deuda_atrasada<=0){



				$hoy 	= date("Y-m-d");

				if($token!=""){

				$sql 	= $mysql->query("SELECT id_usuario, nombre_usuario FROM usuario WHERE id_usuario='$id_usuario' ;");
				$result = $mysql->f_obj($sql);
				$nombre_usuario = $result->nombre_usuario;


				$sql 	= $mysql->query("SELECT id_usuario, nombre_usuario, email FROM usuario WHERE id_usuario='$user' ;");
				$result = $mysql->f_obj($sql);
				$nombre_prestado_a = $result->nombre_usuario;
				$email_prestado_a = $result->email;


				$sql 	= $mysql->query("SELECT id_equipo FROM equipo WHERE token='$token' ;");
				$result = $mysql->f_obj($sql);
				$id_equipo = $result->id_equipo;



				$token_nuevo = md5(rand(99999, 99999999).$user.date("Y m d H s"));



				$sql 	= $mysql->query("INSERT INTO equipo_prestamo (id_equipo, id_usuario_prestamo, id_usuario_responsable, fecha_prestamo, fecha_debe_devolver, estado, token ) 
															VALUES('$id_equipo', '$user',          '$id_usuario',           '$hoy',          '$fecha', 'solicitado', '$token_nuevo' ) ;");


				$sql 	= $mysql->query("UPDATE equipo SET  prestado_a_id_usuario='$user', prestado_a_nombre='$nombre_prestado_a', id_responsable_prestamo='$id_usuario' , nombre_responsable_prestamo='$nombre_usuario', fecha_devolucion='$fecha' WHERE token ='$token';");


				$ultimo_id = $mysql->ultimo_id(); 

				$token = $token_nuevo;


				$_SESSION["equipo_prestado"] = "<div class='alert alert-success' role='alert'>El préstamo se ha realizado.</div>";

















$sql2 	= $mysql->query("SELECT * FROM equipo_prestamo WHERE token ='$tokenEquipo' ;");
$result2 = $mysql->f_obj($sql2);

$fecha1    = fecha_mysql_a_normal($result2->fecha_prestamo);
$fecha2    = fecha_mysql_a_normal($result2->fecha_debe_devolver);



$sql12 	= $mysql->query("SELECT * FROM equipo WHERE id_equipo ='$result2->id_equipo' ;");
$result12 = $mysql->f_obj($sql12);
$id_equipo = @$result12->id_equipo;
$nombre_equipo = @$result12->nombre;



 

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
   
  <strong>$nombre_prestado_a</strong> ($email_prestado_a) autorizado el préstamo del equipo N°$result2->id_equipo <strong>$nombre_equipo</strong> entre las fechas <strong>$fecha1</strong> y <strong>$fecha2</strong><br><br><br>
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
 $mail->addReplyTo($email_reply, 'Préstamo de equipo');
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

//Kop eliminado que se envie a la directiva $mail->addAddress("montana.uchile@gmail.com", "Ramuch");


$mail->addAddress("$email_prestado_a", "$nombre_prestado_a");

 



 



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
   $_SESSION["equipo_prestado"]="<div class='alert alert-danger' role='alert'>Ha ocurrido un error, por favor internte nuevamente o escríbanos a equipo@ramuch.cl indicando el equipo y fechas de solicitud de préstamo</div>";
 } else {
   echo "Message sent!";
   $_SESSION["equipo_prestado"] = "<div class='alert alert-success' role='alert'>El préstamo se ha solicitado exitosamente. Nos coordinaremos para ver disponibilidad y préstamo. Si deseas cancelar la solicitud, debes ir a Mi Perfil, en la pestaña Historial de Préstamos</div>";
 }































				}


				echo "|$token|";


}else{

	echo "|0|";


}//if($cantidad_deuda_atrasada<=0)



?>