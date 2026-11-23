<?php
session_start();
include("../../includes/sql_inyection_salto_textarea.php");
include("../../configuration.php");
include("../../includes/conexionMysql.php");
include("../../includes/funciones.php");
require_once __DIR__ . "/../../includes/procesos_automaticos.php";



$config 	= new Config;
 

date_default_timezone_set($config->zona_horaria);

$mysql 		= new mysql;
$mysql->connect();
$hoy 	= date("Y-m-d");

$registroProceso = ramuchProcesoIniciar($mysql, 'avisa_devolver_equipo', 'Recordatorio de devolución de equipos');
$prestamosVencidos = 0;
$recordatoriosEnviados = 0;
$erroresProceso = [];

	$sql 	= $mysql->query("SELECT * FROM equipo_prestamo WHERE fecha_debe_devolver>='2022-01-01' AND fecha_debe_devolver<='$hoy' AND estado='prestado' ;");
	if ($sql === false) {
		ramuchProcesoFinalizar($registroProceso, false, 'No fue posible consultar los préstamos vencidos. La base de datos respondió: ' . mysqli_error($mysql->conexion));
		exit(1);
	}
	while($result = $mysql->f_obj($sql)){
		$prestamosVencidos++;


                          $id_equipo 				    = $result->id_equipo;
                          $id_usuario_prestamo 	= $result->id_usuario_prestamo;
	                          $fecha1 				      = $result->fecha_prestamo;
	                          $fecha2 				      = $result->fecha_debe_devolver;
	                          $fecha_extension_pendiente = "";
	                          if (@$result->estado_extension2 === 'pendiente' && !empty($result->fecha_propuesta_extension2)) {
	                            $fecha_extension_pendiente = $result->fecha_propuesta_extension2;
	                          } elseif (@$result->estado_extension === 'pendiente' && !empty($result->fecha_propuesta_extension)) {
	                            $fecha_extension_pendiente = $result->fecha_propuesta_extension;
	                          }


                          $sql55 	= $mysql->query("SELECT nombre FROM equipo WHERE id_equipo='$id_equipo' ;");
                          $result55 = $mysql->f_obj($sql55);
                          $nombre_equipo = $result55->nombre;

                          $sql77 	= $mysql->query("SELECT email, nombre_usuario FROM usuario WHERE id_usuario='$id_usuario_prestamo' ;");
                          $result77 = $mysql->f_obj($sql77);
                          $nombre_usuario = $result77->nombre_usuario;
                          $email_usuario = $result77->email;

	                          $fecha1 				= fecha_mysql_a_normal($fecha1);
	                          $fecha2 				= fecha_mysql_a_normal($fecha2);
	                          $aviso_extension = "";
	                          if ($fecha_extension_pendiente !== "") {
	                            $fecha_extension_formateada = fecha_mysql_a_normal($fecha_extension_pendiente);
	                            $aviso_extension = "<br><br><strong>Importante:</strong> Existe una solicitud de extensión hasta el <strong>$fecha_extension_formateada</strong>, pero todavía se encuentra <strong>pendiente de aprobación</strong> por la Comisión de Equipo. Mientras no sea aprobada, la fecha vigente de devolución continúa siendo el <strong>$fecha2</strong>.";
	                          }

                        //Configuración de envío ***************************************************************
                        $host 			= "mail.ramuch.cl";
                        $email_user 	= "no-responder@ramuch.cl";
                        $email_from 	= "no-responder@ramuch.cl";
                        $email_to 		= "";
                        $email_name 	= "Ramuch";
                        $email_reply 	= "no-responder@montanauchile.cl";
                        $email_subject 	= "Devolución de préstamo de equipo";
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
                          <strong>Devolución de equipo,</strong>
                          <br><br>
                          
	                          <strong>$nombre_usuario</strong>, te recordamos que en nuestro sistema tienes pendiente la devolución del equipo <strong>$nombre_equipo</strong>, cuyo préstamo fue registrado desde el <strong>$fecha1</strong> y mantiene como fecha vigente de devolución el <strong>$fecha2</strong>.$aviso_extension<br><br>Si ya devolviste el equipo o necesitas aclarar esta situación, comunícate con la Comisión de Equipo o la Directiva.<br><br><br>
                          <strong>Sistema Ramuch</strong>
                          </p>
                          <br> 
                          <br> 

                        <p><a href='$ruta'><img src='$ruta/images/email_footer.png' border='0'  alt=''/><a/></p>

                        </body>
                        ";



                        //*************************************************************************


                          
                        require_once __DIR__ . "/../../includes/RamuchMailer.php";

                        //Create a new PHPMailer instance
                        $mail = crearMailerRamuch(false);

                        $mail->setFrom($email_from, "Sistema Ramuch");
                        //Set an alternative reply-to address
                        $mail->addReplyTo($email_reply, 'Devolución de préstamo de equipo');
                        //Set who the message is to be sent to
                        //$mail->addAddress($email_to, "$nombre ");
                        //$mail->addAddress("montana.uchile@gmail.com", "Ramuch");

                        //$mail->addAddress("$email_usuario", "$nombre_usuario");


                          //los correos de la comisión

                        $sql88 	= $mysql->query("SELECT c.*, c.token as ctoken, u.* FROM comision_prestamo c INNER JOIN usuario u ON c.id_usuario=u.id_usuario ORDER BY u.nombre_usuario ;");
                        
                        $comision = "";
                        while($result88 = $mysql->f_obj($sql88)){

                        $mail->addAddress("$result88->email", "$result88->nombre_usuario");

                       
                        
                        }//while($result2 = $mysql->f_obj($sql2))

                        // Kop eliminado que se envie a la cirectiva $mail->addAddress("montana.uchile@gmail.com", "Ramuch");

                        $mail->addAddress("$email_usuario", "$nombre_usuario");

 



                        


                        //Set the subject line
	                        $mail->Subject = 'Recordatorio de devolución de equipo';
                        //Read an HTML message body from an external file, convert referenced images to embedded,
                        //convert HTML into a basic plain-text alternative body
                        $mail->msgHTML($cuerpo);
                        //Replace the plain text body with one created manually
                        //$mail->AltBody = 'Este es un mensaje';
                        //Attach an image file
                        //$mail->addAttachment('images/phpmailer_mini.png');

                        //send the message, check for errors
                        if (!$mail->send()) {
                          echo "Mailer Error: " . $mail->ErrorInfo;
                          $erroresProceso[] = "No se pudo enviar el recordatorio de \"$nombre_equipo\" a $nombre_usuario: " . $mail->ErrorInfo;
                        } else {
                          echo "Message sent!";
                          $recordatoriosEnviados++;
                        }

}	//while($result = $mysql->f_obj($sql))

$descripcionProceso = $prestamosVencidos === 0
	? "No hay préstamos de equipo vencidos. No se enviaron recordatorios."
	: "Préstamos vencidos: $prestamosVencidos. Recordatorios enviados al socio y a la Comisión de Equipos: $recordatoriosEnviados.";
if ($erroresProceso) {
	$descripcionProceso .= "\n\nErrores (" . count($erroresProceso) . "):\n- " . implode("\n- ", $erroresProceso);
}
ramuchProcesoFinalizar($registroProceso, !$erroresProceso, $descripcionProceso);
 

 


?>
