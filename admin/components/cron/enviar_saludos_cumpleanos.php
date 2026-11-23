<?php
/**
 * Envía el saludo de cumpleaños a los socios vigentes.
 *
 * Este archivo está bloqueado para ejecutarse únicamente desde CLI y dentro
 * del árbol staging.ramuch.cl. La programación de las 09:00 se configura en
 * el cron del servidor; el script también rechaza ejecuciones fuera de hora.
 *
 * Uso seguro para revisión, sin enviar correos:
 * php enviar_saludos_cumpleanos.php --dry-run
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Ejecución no permitida.\n");
}

$directorioActual = str_replace('\\', '/', __DIR__);
if (strpos($directorioActual, '/staging.ramuch.cl/') === false) {
    fwrite(STDERR, "Proceso habilitado solamente para staging.\n");
    exit(1);
}

// RamuchMailer usa HTTP_HOST para redirigir todos los destinatarios al buzón
// de pruebas. En una ejecución CLI esa variable no existe, por lo que se fija
// explícitamente para impedir envíos a socios reales desde staging.
$_SERVER['HTTP_HOST'] = 'staging.ramuch.cl';

set_time_limit(300);

include __DIR__ . '/../../configuration.php';
include __DIR__ . '/../../includes/conexionMysql.php';
require_once __DIR__ . '/../../includes/procesos_automaticos.php';

$config = new Config;
date_default_timezone_set($config->zona_horaria);

$dryRun = in_array('--dry-run', $argv, true);

$mysql = new mysql;
$conectado = $mysql->connect();
$registroProceso = ramuchProcesoIniciar(
    $mysql,
    'enviar_saludos_cumpleanos',
    $dryRun ? 'Saludos de cumpleaños (prueba sin envío)' : 'Saludos de cumpleaños'
);

function terminarCumpleanos($registroProceso, $ok, $mensaje, $codigo)
{
    ramuchProcesoFinalizar($registroProceso, $ok, $mensaje);
    fwrite($ok ? STDOUT : STDERR, $mensaje . "\n");
    exit($codigo);
}

if (!$conectado) {
    terminarCumpleanos($registroProceso, false, 'No fue posible conectar con la base de datos.', 1);
}

if (!$dryRun && date('H') !== '09') {
    terminarCumpleanos($registroProceso, false, 'El proceso solo puede enviar correos durante las 09:00 y se ejecutó a las ' . date('H:i') . '. Revisa la hora programada del cron en cPanel.', 1);
}

$directorioEstado = __DIR__ . '/runtime';
if (!is_dir($directorioEstado) && !mkdir($directorioEstado, 0750, true)) {
    terminarCumpleanos($registroProceso, false, 'No fue posible crear el directorio de control (admin/components/cron/runtime).', 1);
}

$fechaProceso = date('Y-m-d');
$archivoCompletado = $directorioEstado . '/cumpleanos-' . $fechaProceso . '.done';
$archivoBloqueo = $directorioEstado . '/cumpleanos.lock';
$bloqueo = fopen($archivoBloqueo, 'c');
if (!$bloqueo || !flock($bloqueo, LOCK_EX | LOCK_NB)) {
    terminarCumpleanos($registroProceso, false, 'Ya existe otra ejecución del proceso en curso; esta se canceló para no enviar saludos repetidos.', 1);
}

if (!$dryRun && file_exists($archivoCompletado)) {
    flock($bloqueo, LOCK_UN);
    fclose($bloqueo);
    terminarCumpleanos($registroProceso, true, "Los saludos de hoy ($fechaProceso) ya se habían enviado. No se envió nada nuevo.", 0);
}

$mes = (int)date('n');
$dia = (int)date('j');
$sql = $mysql->query(
    "SELECT u.id_usuario,
            COALESCE(NULLIF(TRIM(p.nombre), ''), u.nombre_usuario) AS nombre,
            COALESCE(NULLIF(TRIM(p.mail), ''), u.email) AS email
       FROM usuario AS u
       INNER JOIN perfil AS p ON p.id_usuario = u.id_usuario
      WHERE u.estado = 'Vigente'
        AND p.fecha_nacimiento IS NOT NULL
        AND p.fecha_nacimiento <> '0000-00-00'
        AND MONTH(p.fecha_nacimiento) = '$mes'
        AND DAY(p.fecha_nacimiento) = '$dia'
        AND COALESCE(NULLIF(TRIM(p.mail), ''), u.email) <> ''
      ORDER BY u.id_usuario"
);

if (!$sql) {
    terminarCumpleanos($registroProceso, false, 'No fue posible consultar los cumpleaños del día: ' . mysqli_error($mysql->conexion), 1);
}

$socios = array();
while ($socio = $mysql->f_obj($sql)) {
    if (filter_var($socio->email, FILTER_VALIDATE_EMAIL)) {
        $socios[] = $socio;
    }
}

if ($dryRun) {
    echo 'Cumpleaños encontrados para ' . $fechaProceso . ': ' . count($socios) . "\n";
    foreach ($socios as $socio) {
        echo '- ID ' . $socio->id_usuario . ': ' . $socio->nombre . "\n";
    }
    flock($bloqueo, LOCK_UN);
    fclose($bloqueo);
    ramuchProcesoFinalizar($registroProceso, true, 'Prueba sin envío: hoy (' . $fechaProceso . ') ' . (count($socios) === 1 ? 'hay 1 socio' : 'hay ' . count($socios) . ' socios') . ' de cumpleaños. No se envió ningún correo.');
    exit(0);
}

$enviados = 0;
$errores = 0;
$detalleErrores = array();
foreach ($socios as $socio) {
    $idUsuario = (int)$socio->id_usuario;
    $marcaSocio = $directorioEstado . '/cumpleanos-' . $fechaProceso . '-' . $idUsuario . '.sent';
    if (file_exists($marcaSocio)) {
        continue;
    }

    $nombre = trim($socio->nombre);
    $nombreHtml = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    $asunto = '¡Feliz cumpleaños de parte de RAMUCH!';
    $mensajeTexto = "Estimado(a) $nombre:\n\n"
        . "En este día especial, quienes formamos parte de la RAMUCH queremos enviarte un afectuoso saludo y nuestros mejores deseos en tu cumpleaños.\n\n"
        . "Esperamos que este nuevo año de vida venga acompañado de salud, felicidad, nuevos desafíos y muchas experiencias en la montaña.\n\n"
        . "Agradecemos que seas parte de nuestra comunidad y esperamos seguir compartiendo rutas, aprendizajes y cumbres contigo.\n\n"
        . "¡Felicidades en tu día!\n\n"
        . "Rama de Montaña U. de Chile";
    $mensajeHtml = '<html><body style="font-family:Arial,sans-serif;font-size:14px;color:#1a1a1a">'
        . '<p>Estimado(a) ' . $nombreHtml . ':</p>'
        . '<p>En este día especial, quienes formamos parte de la RAMUCH queremos enviarte un afectuoso saludo y nuestros mejores deseos en tu cumpleaños.</p>'
        . '<p>Esperamos que este nuevo año de vida venga acompañado de salud, felicidad, nuevos desafíos y muchas experiencias en la montaña.</p>'
        . '<p>Agradecemos que seas parte de nuestra comunidad y esperamos seguir compartiendo rutas, aprendizajes y cumbres contigo.</p>'
        . '<p><strong>¡Felicidades en tu día!</strong></p>'
        . '<p>Rama de Montaña U. de Chile</p>'
        . '</body></html>';

    try {
        $mail = crearMailerRamuch(true);
        $mail->setFrom('no-responder@ramuch.cl', 'RAMUCH');
        $mail->addAddress($socio->email, $nombre);
        $mail->addBCC('montana.uchile@gmail.com', 'Directiva RAMUCH');
        $mail->addReplyTo('directiva@ramuch.cl', 'RAMUCH');
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensajeHtml;
        $mail->AltBody = $mensajeTexto;
        $mail->send();

        if (file_put_contents($marcaSocio, date('c') . PHP_EOL, LOCK_EX) === false) {
            throw new RuntimeException('No fue posible registrar el control del envío.');
        }
        $enviados++;
    } catch (Throwable $e) {
        $errores++;
        $detalleErrores[] = 'Socio ID ' . $idUsuario . ': ' . $e->getMessage();
        error_log('Cumpleaños staging, usuario ' . $idUsuario . ': ' . $e->getMessage());
    }
}

if ($errores === 0) {
    $resumen = "fecha=$fechaProceso enviados=$enviados candidatos=" . count($socios) . PHP_EOL;
    if (file_put_contents($archivoCompletado, $resumen, LOCK_EX) === false) {
        terminarCumpleanos($registroProceso, false, "Se enviaron $enviados saludos, pero no fue posible cerrar el control diario; una nueva ejecución hoy podría repetir envíos.", 1);
    }
}

flock($bloqueo, LOCK_UN);
fclose($bloqueo);

$descripcionProceso = count($socios) === 0
    ? "Hoy ($fechaProceso) no hay socios de cumpleaños."
    : "Cumpleaños de hoy ($fechaProceso): " . count($socios) . ". Saludos enviados: $enviados.";
if ($errores > 0) {
    $descripcionProceso .= "\n\nErrores ($errores):\n- " . implode("\n- ", $detalleErrores);
}
ramuchProcesoFinalizar($registroProceso, $errores === 0, $descripcionProceso);

echo "Proceso finalizado. Enviados: $enviados. Errores: $errores.\n";
exit($errores === 0 ? 0 : 1);
