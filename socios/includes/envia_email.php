<?php


function envia_mail($nombre_from, $nombre, $email, $asunto, $mensaje, $nivel, $logo ){
	
$config 	= new Config;

//Configuración de envío ***************************************************************
$web			= $config->sitename;
$ruta			= $config->url_web;
$email_subject 	= $asunto;
$email_to 		= $email;
$email_name_to 	= $nombre;
//**************************************************************************************


$cuerpo = "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
<head>

<style>


body {
  font-family: Arial, serif;
  font-size: 14px;
  color: #000000;
}

.textos {
  font-family: Arial, serif;
  font-size: 12px;
  color: #1a1a1a;
}

h2{
	color:#1f278e;
	font-size: 18px;
	font-weight:bold;
	text-align:center;
	}
	
	
.boton {
    background-color: #eeab26;
    border-radius: 4px;
    color: #ffffff;
    display: inline-block;
    font-family: Verdana,sans-serif;
    font-size: 14px;
    line-height: 44px;
    text-align: center;
    text-decoration: none;
    width: 200px;
}


</style>

</head><body>
<div style='width:100%;'>
<p>
<div style='width:100%; border-bottom: 1px solid #cccccc;'>
<img src='$ruta/images/$logo' border='0' alt='' style='height:60px; width:auto;'/> 
</div>
  <br> 
   <br>
   
   <body><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tbody>
    <tr>
         <td>
   
  $mensaje

         </td>
    </tr>
  </tbody>
</table>

</div>
   <br> 
   <br>

</div>
</body>
";



//*************************************************************************




if($asunto!="" && $mensaje!=""  ){

if($nivel==0)
require_once("includes/PHPMailer2/PHPMailerAutoload.php");

if($nivel==1)
require_once("../includes/PHPMailer2/PHPMailerAutoload.php");

if($nivel==2)
require_once("../../includes/PHPMailer2/PHPMailerAutoload.php");

if($nivel==3)
require_once("../../../includes/PHPMailer2/PHPMailerAutoload.php");

if($nivel==4)
require_once("../../../includes/PHPMailer2/PHPMailerAutoload.php");

	
//**********************************************************************
	
$mail = new PHPMailer(); // create a new object
//$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
$mail->Host = "mail.fibravel.cl";
$mail->Port = 465; // or 587
	
$mail->Timeout = 3600; 
	
$mail->IsHTML(true);
$mail->Username = $config->email_user;
$mail->Password = $config->email_pass;
$mail->SetFrom($config->email_user, $nombre_from);
$mail->CharSet = 'UTF-8';
	

 
//Set an alternative reply-to address
$mail->addReplyTo($_SESSION["company_email"], "Re: $asunto");

//Set who the message is to be sent to
//$mail->addAddress("maudichili@gmail.com", "Mauricio");
$mail->addAddress("$email_to", "$nombre");

 
//$mail->addAddress("emasmas@emasmas.cl", "Emasmas");

//Set the subject line
$mail->Subject = "$asunto";
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
	
 


}//if($asunto!="" && $mensaje!=""  )

}//function

?>