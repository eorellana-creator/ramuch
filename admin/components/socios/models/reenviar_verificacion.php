<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

function responder_reenvio($codigo, $mensaje)
{
    http_response_code($codigo);
    echo json_encode(array('ok' => $codigo >= 200 && $codigo < 300, 'mensaje' => $mensaje));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_reenvio(405, 'Método no permitido.');
}

if (empty($_SESSION['usuario_valido_bastro_ruta']) || $_SESSION['usuario_valido_bastro_ruta'] !== 'true') {
    responder_reenvio(401, 'La sesión ha expirado. Ingrese nuevamente.');
}

$csrf_sesion = isset($_SESSION['csrf_reenvio_verificacion']) ? $_SESSION['csrf_reenvio_verificacion'] : '';
$csrf_recibido = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
if ($csrf_sesion === '' || !hash_equals($csrf_sesion, $csrf_recibido)) {
    responder_reenvio(403, 'La solicitud no es válida. Recargue la página e intente nuevamente.');
}

$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
if (!$id_usuario || $id_usuario < 1) {
    responder_reenvio(400, 'El socio indicado no es válido.');
}

require_once __DIR__ . '/../../../configuration.php';
require_once __DIR__ . '/../../../includes/conexionMysql.php';
require_once __DIR__ . '/../../../includes/RamuchMailer.php';

$mysql = new mysql;
if (!$mysql->connect()) {
    responder_reenvio(500, 'No fue posible conectar con la base de datos.');
}

$rol = isset($_SESSION['usuario_rol']) ? (int) $_SESSION['usuario_rol'] : 0;
$puede_reenviar = ($rol === 1);
if (!$puede_reenviar && $rol > 0) {
    $consulta_rol = $mysql->query("SELECT nombre FROM rol WHERE id_rol='$rol' LIMIT 1;");
    $rol_sesion = $consulta_rol ? $mysql->f_obj($consulta_rol) : null;
    $nombre_rol = $rol_sesion ? strtolower(trim($rol_sesion->nombre)) : '';
    $puede_reenviar = ($nombre_rol === 'administrador de socios');
}
if (!$puede_reenviar) {
    responder_reenvio(403, 'No tiene permisos para reenviar correos de verificación.');
}

$id_usuario = (int) $id_usuario;
$consulta = $mysql->query(
    "SELECT u.nombre_usuario, u.email, u.token
     FROM usuario AS u
     WHERE u.id_usuario = '$id_usuario'
       AND u.estado = 'Por confirmar email'
       AND u.token != ''
       AND (u.web_matricula_pagada IS NULL OR u.web_matricula_pagada = '' OR u.web_matricula_pagada = 'No')
       AND EXISTS (
           SELECT 1 FROM deudas AS d
           WHERE d.id_usuario_deuda = u.id_usuario
             AND d.sub_cuenta = 'matricula'
             AND LOWER(d.estado) = 'por confirmar email'
       )
     LIMIT 1"
);

if (!$consulta || $mysql->f_num($consulta) !== 1) {
    responder_reenvio(409, 'El socio ya verificó su correo, pagó la matrícula o no tiene una matrícula pendiente.');
}

$socio = $mysql->f_obj($consulta);
if (!filter_var($socio->email, FILTER_VALIDATE_EMAIL)) {
    responder_reenvio(422, 'El correo registrado para este socio no es válido.');
}

$host_web = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
$host_web = preg_replace('/:\\d+$/', '', $host_web);
$base_registro = ($host_web === 'staging.ramuch.cl')
    ? 'https://staging.ramuch.cl/registro'
    : 'https://ramuch.cl/registro';
$enlace = $base_registro . '/verificacion.php?token=' . rawurlencode($socio->token);
$nombre_html = htmlspecialchars($socio->nombre_usuario, ENT_QUOTES, 'UTF-8');
$enlace_html = htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8');

$mensaje_html = "<html><body style='font-family:Arial,sans-serif;font-size:14px;color:#1a1a1a'>
<p><a href='$base_registro'><img src='$base_registro/images/email_cabecera.png' border='0' alt='Ramuch'></a></p>
<p><strong>Completa tu registro</strong></p>
<p>Estimado(a) <strong>$nombre_html</strong>:</p>
<p>Te enviamos nuevamente el enlace solicitado para verificar que este correo te pertenece:</p>
<p><a href='$enlace_html'>Verificar mi cuenta</a></p>
<p>También puedes copiar y pegar esta dirección en tu navegador:<br>$enlace_html</p>
<p>Una vez completado tu registro, podrás acceder al sistema de socios de Ramuch.</p>
<p><strong>Muchas gracias</strong></p>
<p><a href='$base_registro'><img src='$base_registro/images/email_footer.png' border='0' alt='Ramuch'></a></p>
</body></html>";

$mensaje_texto = "Completa tu registro\n\nEstimado(a) {$socio->nombre_usuario}:\n\n"
    . "Te enviamos nuevamente el enlace solicitado para verificar que este correo te pertenece:\n"
    . "$enlace\n\nUna vez completado tu registro, podrás acceder al sistema de socios de Ramuch.\n\nMuchas gracias";

try {
    $mail = crearMailerRamuch(true);
    $mail->isSMTP();
    $mail->Host = 'mail.ramuch.cl';
    $mail->SMTPAuth = true;
    $mail->Username = 'no-responder@ramuch.cl';
    $mail->Password = '1941ramuch2024';
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('no-responder@ramuch.cl', 'Ramuch');
    if ($host_web === 'staging.ramuch.cl') {
        $mail->addAddress('eorellana@gmail.com', 'Pruebas staging');
        $mail->addReplyTo('eorellana@gmail.com', 'Pruebas staging');
    } else {
        $mail->addAddress($socio->email, $socio->nombre_usuario);
        $mail->addReplyTo('directiva@ramuch.cl', 'Ramuch');
    }
    $mail->isHTML(true);
    $mail->Subject = ($host_web === 'staging.ramuch.cl' ? '[STAGING] ' : '')
        . 'Reenvío de verificación de correo - Ramuch';
    $mail->Body = $mensaje_html;
    $mail->AltBody = $mensaje_texto;
    $mail->send();
} catch (\Throwable $e) {
    error_log('Error al reenviar verificación al usuario ' . $id_usuario . ': ' . $e->getMessage());
    responder_reenvio(502, 'El servidor de correo no pudo enviar el mensaje. Intente nuevamente.');
}

responder_reenvio(200, 'Correo de verificación reenviado a ' . $socio->email . '.');
