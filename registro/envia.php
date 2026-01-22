<?php
include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

// Configuración del sistema de logs
define('LOG_FILE', __DIR__.'/email_errors.log');

function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
}

// Cargar PHPMailer
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Obtener el token reCAPTCHA v3 enviado desde el formulario
$recaptchaToken = $_POST["recaptchaToken"];

// Verificar el token reCAPTCHA v3 con la API de Google
//$secretKey = "6Ldsrc0pAAAAALAI9AhbXFrnFT3xHwlMu5KAWqIO";
//$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaToken");
//$responseKeys = json_decode($response, true);

// Verificar la respuesta de la API de Google
//if ($responseKeys["success"]) {
    // Token reCAPTCHA v3 válido, continuar con el procesamiento del formulario

    // Obtener los datos del formulario
    $nombre     = @$_POST["nombre"];
    $rut        = @$_POST["rut"];
    $email      = @$_POST["email"];
    $telefono   = @$_POST["telefono"];
    $password   = @$_POST["password"];
    $referencia = isset($_POST["referencia"]) ? $_POST["referencia"] : "desconocido";
    
    // Si seleccionó "Otro" y proporcionó texto adicional
    if ($referencia == "Otro" && isset($_POST["otro_referencia"])) {
        $referencia = $_POST["otro_referencia"];
    }

    // Formatear el RUT
    $rut = formatea_rut($rut);

    // Generar token
    $fecha      = time();
    $hora       = date("H:i", $fecha);
    $dia        = date("Y-m-d", $fecha);
    $password   = md5($password);
    $token      = md5($password . date("Y-m-d-h-i-s") . $email . rand(9999, 999999));
    $hoy        = date("Y-m-d");
    $hora       = date("H:i:s");
    $ip         = getRealIP();

    // Verificar si se enviaron todos los campos obligatorios
    if ($nombre != "" && $rut != "" && $email != "" && $telefono != "" && $password != "") {
        $mysql = new mysql;
        $mysql->connect();

        // Insertar usuario en la base de datos
        $sql = $mysql->query("INSERT INTO usuario (id_company, id_rol, nombre_usuario, email, password, fecha_registro, hora_registro, ip_registro, fecha_actualizacion, hora_actualizacion, ip_actualizacion, estado, token, referencia) VALUES 
                                                              ('1', '8', '$nombre', '$email', '$password', '$hoy', '$hora', '$ip', '$hoy', '$hora', '$ip', 'Por confirmar email', '$token', '$referencia' ) ");
        $ultimo_id = $mysql->ultimo_id();

        $nacimiento = "0000-00-00";
        $hoy        = date("Y-m-d H:i:s");

        $certificado_estudios="";

        $patronIMG  = "%\.(jpg|PNG|png|JPG|JPEG|jpeg|pdf|PDF)$%i";

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
            $sql8                   = $mysql->query("SELECT valor FROM plan_matricula WHERE id_plan_matricula='3';");
            $result8                = @$mysql->f_obj($sql8);

            $valor_matricula = $result8->valor;

            $fecha_deuda = date("Y-m-d");

            $token_deuda = md5( rand(9999,999999).$nombre.$fecha_deuda.$hoy.$ultimo_id  );

            $sql4             = $mysql->query("INSERT INTO deudas (id_usuario, id_usuario_deuda, nombre_deudor, sub_cuenta,    fecha,           monto,  glosa, estado, fecha_insercion, token) 
                                                 VALUES (      0,        '$ultimo_id',  '$nombre',     'matricula',   '$fecha_deuda','$valor_matricula','Matrícula Estudiante $nombre','por confirmar email','$fecha_deuda','$token')   ;");
        }else{

            $sql8                   = $mysql->query("SELECT valor FROM plan_matricula WHERE id_plan_matricula='1';");
            $result8                = @$mysql->f_obj($sql8);
            $valor_matricula = $result8->valor;
            $fecha_deuda = date("Y-m-d");
            $token_deuda = md5( rand(9999,999999).$nombre.$fecha_deuda.$hoy.$ultimo_id  );
            $sql4             = $mysql->query("INSERT INTO deudas (id_usuario, id_usuario_deuda, nombre_deudor, sub_cuenta,    fecha,           monto,  glosa, estado, fecha_insercion, token) 
                                                 VALUES (      0,        '$ultimo_id',  '$nombre',     'matricula',   '$fecha_deuda','$valor_matricula','Matrícula Profesional $nombre','por confirmar email','$fecha_deuda','$token')   ;");

        }

        $token_perfil = md5(  rand(999,999999) . date("Y-m-d-h-i-s") . $ultimo_id );

        $sql    = $mysql->query("INSERT INTO perfil (id_usuario, nombre,  tipo_inscripcion, id_plan_matricula,   fono, mail, rut, certificado_estudios, token ) 
                                        VALUES ('$ultimo_id','$nombre', '$tipo_inscripcion','$id_plan_matricula', '$telefono', '$email', '$rut','$certificado_estudios', '$token_perfil'  ) ");

        // Configuración PHPMailer ***************************************************************
        $host           = "mail.ramuch.cl";
        $email_user     = "no-responder@ramuch.cl";
        $email_pass     = "1941ramuch2024";
        $email_from     = "no-responder@ramuch.cl";
        $email_to       = $email;
        $email_name     = "Ramuch";
        $email_reply    = "directiva@ramuch.cl";
        $email_subject  = "Registro de Usuario Ramuch";
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
            <strong>Completa tu registro,</strong>
            <br><br>
            <strong>Estimado(a) $nombre, </strong><br><br>
            Para completar tu registro en <strong>Ramuch</strong> debes verificar tu email haciendo click en el siguiente enlace:<br>
            <a href='$ruta/verificacion.php?token=$token'>Verificar mi cuenta</a><br><br>
            También puedes copiar y pegar la siguiente dirección en tu navegador web:
            $ruta/verificacion.php?token=$token<br><br>
            Una vez completado tu registro, podrás acceder al sistema de socios de Ramuch.<br><br><br>
            <strong>Muchas Gracias</strong>
        </p>
        <br> 
        <br> 
        <p><a href='$ruta'><img src='$ruta/images/email_footer.png' border='0' alt=''/></a></p>
        </body>
        </html>";

        // Versión texto plano
        $message_plain = "Completa tu registro\n\nEstimado(a) $nombre,\n\nPara completar tu registro en Ramuch debes verificar tu email visitando el siguiente enlace:\n$ruta/verificacion.php?token=$token\n\nTambién puedes copiar y pegar esta dirección en tu navegador web.\n\nUna vez completado tu registro, podrás acceder al sistema de socios de Ramuch.\n\nMuchas Gracias";

       

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
    } else {
        echo "Error: Todos los campos son obligatorios.";
    }
?>