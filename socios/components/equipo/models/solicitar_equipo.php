<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company = (int)($_SESSION["company_id"] ?? 0);
$id_usuario = (int)($_SESSION["usuario_id"] ?? 0);
$nombre_usuario = $_SESSION["usuario_nombre"] ?? '';
$email_usuario = $_SESSION["usuario_email"] ?? '';
$sesionAutorizada = (
    ($_SESSION["usuario_valido_socio_ramuch"] ?? '') === 'true'
    && ($_SESSION["usuario_origen"] ?? '') === 'socios'
) || (
    ($_SESSION["usuario_valido_bastro_ruta"] ?? '') === 'true'
    && ($_SESSION["usuario_origen"] ?? '') === 'admin'
);

$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];
$tokens = isset($_GET['tokens']) ? (is_array($_GET['tokens']) ? $_GET['tokens'] : [$_GET['tokens']]) : [];

$config = new Config;
date_default_timezone_set($config->zona_horaria);
$mysql = new mysql;
$mysql->connect();

// No confiar solamente en la sesión: el usuario debe seguir existiendo y vigente.
$sqlUsuario = $mysql->query("SELECT nombre_usuario, email FROM usuario WHERE id_usuario='$id_usuario' AND estado='Vigente' LIMIT 1;");
$usuarioValido = $mysql->f_obj($sqlUsuario);
if (!$sesionAutorizada || $id_usuario <= 0 || !$usuarioValido) {
    http_response_code(401);
    exit("invalid user");
}
$nombre_usuario = $usuarioValido->nombre_usuario;
$email_usuario = $usuarioValido->email;

$hoy = date("Y-m-d");
$equipos_solicitados = [];
$equipos_no_disponibles = [];

function fechaValida($fecha) {
    $objetoFecha = DateTime::createFromFormat('!Y-m-d', $fecha);
    return $objetoFecha && $objetoFecha->format('Y-m-d') === $fecha;
}

if (empty($id_usuario) || empty($tokens) || !fechaValida($fecha1) || !fechaValida($fecha2)
    || $fecha1 < $hoy || $fecha2 < $fecha1) {
    http_response_code(400);
    $_SESSION["equipo_prestado"] = "<div class='alert alert-danger' role='alert'>Las fechas o los equipos seleccionados no son válidos.</div>";
    exit("invalid");
}

foreach ($tokens as $token) {
    if (!preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
        $equipos_no_disponibles[] = 'Equipo no válido';
        continue;
    }

    $mysql->query("START TRANSACTION");
    $sql = $mysql->query("SELECT * FROM equipo WHERE token='$token' FOR UPDATE;");
    $result = $mysql->f_obj($sql);
    
    if ($result) {
        $id_equipo = $result->id_equipo;
        
        // Check if equipment is already requested or borrowed
        $sqlp = $mysql->query("SELECT * FROM equipo_prestamo WHERE id_equipo = $id_equipo AND (estado = 'solicitado' OR estado = 'prestado');");
        $se_presto = $mysql->f_num($sqlp);
        
        if ($se_presto == 0) {
            $token_nuevo = md5(date("Y-m-d h i s") . $id_usuario . rand(9999,9999999));
            
            $sql = $mysql->query("INSERT INTO equipo_prestamo (id_equipo, id_usuario_prestamo, id_usuario_responsable, fecha_prestamo, fecha_debe_devolver, estado, token) 
                                VALUES('$id_equipo', '$id_usuario', '0', '$fecha1', '$fecha2', 'solicitado', '$token_nuevo');");

            if ($sql) {
                $mysql->query("COMMIT");
                $equipos_solicitados[] = [
                    'id_equipo' => $id_equipo,
                    'nombre' => $result->nombre,
                    'id_unico' => $result->id_unico
                ];
            } else {
                $mysql->query("ROLLBACK");
                $equipos_no_disponibles[] = $result->nombre;
            }
        } else {
            $mysql->query("ROLLBACK");
            $equipos_no_disponibles[] = $result->nombre;
        }
    } else {
        $mysql->query("ROLLBACK");
    }
}

if (!empty($equipos_solicitados)) {
    // Email configuration
    require_once("../../../includes/PHPMailer2/PHPMailerAutoload.php");
    $mail = new PHPMailer;
    
    // Configure SMTP settings
    $mail->isSMTP();
    $mail->Host = "mail.montanauchile.cl";
    $mail->SMTPAuth = true;
    $mail->Username = "no-responder@montanauchile.cl";
    $mail->Password = "123ramuchchile2022";
    $mail->Port = 25;
    $mail->CharSet = 'UTF-8';
    
    $mail->setFrom("no-responder@montanauchile.cl", "Sistema Ramuch");
    $mail->addReplyTo("no-responder@montanauchile.cl", 'Re: Solicitud de Préstamo');
    
    // Add commission members as recipients
    $sql = $mysql->query("SELECT c.*, u.* FROM comision_prestamo c INNER JOIN usuario u ON c.id_usuario=u.id_usuario ORDER BY u.nombre_usuario;");
    while($result = $mysql->f_obj($sql)) {
        $mail->addAddress($result->email, $result->nombre_usuario);
    }
    
    $mail->addAddress("montana.uchile@gmail.com", "Ramuch");
    $mail->addAddress($email_usuario, $nombre_usuario);
    
    // Format dates
    $fecha1_formato = date("d-m-Y", strtotime($fecha1));
    $fecha2_formato = date("d-m-Y", strtotime($fecha2));
    
    // Create email content
    $equipos_lista = "";
    foreach ($equipos_solicitados as $equipo) {
        $equipos_lista .= "- Equipo N°{$equipo['id_equipo']} con ID Único {$equipo['id_unico']}: {$equipo['nombre']}<br>";
    }
    
    $cuerpo = "
    <html>
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
        <style>
            body { font-family: Arial, serif; font-size: 14px; color: #1a1a1a; }
            .textos { font-family: Arial, serif; font-size: 12px; color: #1a1a1a; }
        </style>
    </head>
    <body>
        <p>
            <a href='https://ramuch.cl/registro'><img src='https://ramuch.cl/registro/images/email_cabecera.png' border='0' alt=''/></a>
            <br><br>
            <strong>Solicitud de préstamo de equipos,</strong>
            <br><br>
            <strong>$nombre_usuario</strong> ($email_usuario) ha solicitado el préstamo de los siguientes equipos entre las fechas <strong>$fecha1_formato</strong> y <strong>$fecha2_formato</strong>:<br><br>
            $equipos_lista
            <br>
            <strong>Sistema Ramuch</strong>
        </p>
        <br><br>
        <p><a href='https://ramuch.cl/registro'><img src='https://ramuch.cl/registro/images/email_footer.png' border='0' alt=''/></a></p>
    </body>
    </html>";
    
    $mail->Subject = 'Solicitud de Préstamo Multiple';
    $mail->msgHTML($cuerpo);
    
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    if (!$mail->send()) {
        $_SESSION["equipo_prestado"] = "<div class='alert alert-danger' role='alert'>Ha ocurrido un error, por favor intente nuevamente o escríbanos a directiva@ramuch.cl indicando los equipos y fechas de solicitud de préstamo</div>";
    } else {
        $mensaje = "Los siguientes equipos han sido solicitados exitosamente:<br>" . $equipos_lista;
        if (!empty($equipos_no_disponibles)) {
            $mensaje .= "<br>Los siguientes equipos no estaban disponibles:<br>" . implode("<br>", $equipos_no_disponibles);
        }
        $_SESSION["equipo_prestado"] = "<div class='alert alert-success' role='alert'>$mensaje</div>";
    }
} else {
    $_SESSION["equipo_prestado"] = "<div class='alert alert-danger' role='alert'>No se pudo procesar ninguna solicitud. Todos los equipos seleccionados no están disponibles.</div>";
}

echo "success";
?>
