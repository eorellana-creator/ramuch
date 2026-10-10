<?php
header('Content-Type: application/json; charset=utf-8');

include('includes/conexionMysql.php');
include('includes/funciones.php');
require_once __DIR__ . '/../admin/includes/RamuchMailer.php';

define('LOG_FILE', __DIR__ . '/email_errors.log');
define('CONSENTIMIENTO_VERSION', '2026-10-10');

function responder($success, $message, $status = 200)
{
    http_response_code($status);
    echo json_encode(array('success' => $success, 'message' => $message), JSON_UNESCAPED_UNICODE);
    exit;
}

function logError($message)
{
    file_put_contents(LOG_FILE, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
}

function valorPost($nombre)
{
    return trim(isset($_POST[$nombre]) && !is_array($_POST[$nombre]) ? $_POST[$nombre] : '');
}

function limpiarNombre($valor)
{
    return preg_replace('/\s+/u', ' ', trim(strip_tags($valor)));
}

function rutValido($rut)
{
    $rut = strtoupper(preg_replace('/[^0-9K]/i', '', $rut));
    if (strlen($rut) < 2) return false;
    $cuerpo = substr($rut, 0, -1);
    $dv = substr($rut, -1);
    if (!ctype_digit($cuerpo)) return false;
    $suma = 0;
    $multiplo = 2;
    for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
        $suma += ((int)$cuerpo[$i]) * $multiplo;
        $multiplo = $multiplo === 7 ? 2 : $multiplo + 1;
    }
    $esperado = 11 - ($suma % 11);
    $esperado = $esperado === 11 ? '0' : ($esperado === 10 ? 'K' : (string)$esperado);
    return $dv === $esperado;
}

function verificarCaptcha($respuesta)
{
    if ($respuesta === '') return false;
    $datos = http_build_query(array(
        'secret' => '6LfEwTkqAAAAAGbm8VFwAxJXBGWFQ5bj3Al_aqUl',
        'response' => $respuesta,
        'remoteip' => getRealIP()
    ));
    $contexto = stream_context_create(array('http' => array(
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $datos,
        'timeout' => 10
    )));
    $resultado = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $contexto);
    $json = $resultado !== false ? json_decode($resultado, true) : null;
    return is_array($json) && !empty($json['success']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(false, 'Método no permitido.', 405);

$nombres = limpiarNombre(valorPost('nombres'));
$apellidoPaterno = limpiarNombre(valorPost('apellido_paterno'));
$apellidoMaterno = limpiarNombre(valorPost('apellido_materno'));
$nombre = trim($nombres . ' ' . $apellidoPaterno . ' ' . $apellidoMaterno);
$sexoGenero = valorPost('sexo_genero');
$rutIngresado = valorPost('rut');
$email = strtolower(valorPost('email'));
$emailConfirmacion = strtolower(valorPost('email2'));
$codigoPais = valorPost('telefono_pais');
$telefonoLocal = preg_replace('/\D/', '', valorPost('telefono'));
$passwordPlano = valorPost('password');
$passwordConfirmacion = valorPost('password2');
$tipoSocio = valorPost('tipo_socio');
$referencia = valorPost('referencia');
$otroClub = valorPost('otro_club_montanismo');
$nombreOtroClub = limpiarNombre(valorPost('nombre_otro_club'));
$motivacionOtro = limpiarNombre(valorPost('motivacion_otro'));
$terminos = valorPost('terminos');
$diaNacimiento = (int)valorPost('nacimiento_dia');
$mesNacimiento = (int)valorPost('nacimiento_mes');
$anioNacimiento = (int)valorPost('nacimiento_anio');
$fechaNacimiento = sprintf('%04d-%02d-%02d', $anioNacimiento, $mesNacimiento, $diaNacimiento);

$codigosPaisPermitidos = array('+56', '+54', '+51', '+591', '+57', '+593', '+1', '+34');
$sexosPermitidos = array('', 'Femenino', 'Masculino', 'Otro');
$motivacionesPermitidas = array('vinculos', 'formacion', 'beneficios', 'actividades', 'historia', 'otro');
$motivaciones = isset($_POST['motivaciones']) && is_array($_POST['motivaciones'])
    ? array_values(array_unique(array_intersect($_POST['motivaciones'], $motivacionesPermitidas)))
    : array();

if ($nombres === '' || $apellidoPaterno === '' || $apellidoMaterno === '') responder(false, 'Los nombres y ambos apellidos son obligatorios.', 422);
if (!in_array($sexoGenero, $sexosPermitidos, true)) responder(false, 'La opción de sexo/género no es válida.', 422);
if (!rutValido($rutIngresado)) responder(false, 'El RUT ingresado no es válido.', 422);
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email !== $emailConfirmacion) responder(false, 'Los correos electrónicos no son válidos o no coinciden.', 422);
if (!in_array($codigoPais, $codigosPaisPermitidos, true) || strlen($telefonoLocal) < 7 || strlen($telefonoLocal) > 15) responder(false, 'El teléfono ingresado no es válido.', 422);
if (!checkdate($mesNacimiento, $diaNacimiento, $anioNacimiento) || $fechaNacimiento > date('Y-m-d')) responder(false, 'La fecha de nacimiento no es válida.', 422);
if (!in_array($tipoSocio, array('profesional', 'estudiante'), true)) responder(false, 'Debes seleccionar Profesional o Estudiante.', 422);
if (strlen($passwordPlano) < 8 || !preg_match('/[a-z]/', $passwordPlano) || !preg_match('/[A-Z]/', $passwordPlano) || !preg_match('/[0-9]/', $passwordPlano) || $passwordPlano !== $passwordConfirmacion) responder(false, 'La contraseña no cumple los requisitos o no coincide con su confirmación.', 422);
if (!in_array($otroClub, array('', '0', '1'), true) || ($otroClub === '1' && $nombreOtroClub === '') || strlen($nombreOtroClub) > 50) responder(false, 'Completa el nombre del club anterior cuando corresponda.', 422);
if (count($motivaciones) > 3 || (in_array('otro', $motivaciones, true) && $motivacionOtro === '')) responder(false, 'Puedes seleccionar hasta tres motivaciones y debes completar “Otro” cuando corresponda.', 422);
if ($terminos !== '1') responder(false, 'Debes aceptar el protocolo, los estatutos y la autorización indicada.', 422);
if ($referencia === '' || ($referencia === 'Otro' && valorPost('otro_referencia') === '')) responder(false, 'Debes indicar dónde conociste RAMUCH.', 422);
if (!verificarCaptcha(valorPost('g-recaptcha-response'))) responder(false, 'No fue posible validar el captcha. Intenta nuevamente.', 422);

if ($referencia === 'Otro') $referencia = limpiarNombre(valorPost('otro_referencia'));

$tieneArchivo = isset($_FILES['archivo'], $_FILES['archivo']['error']) && $_FILES['archivo']['error'] !== UPLOAD_ERR_NO_FILE;
if ($tipoSocio === 'estudiante' && !$tieneArchivo) responder(false, 'El certificado de alumno regular es obligatorio para estudiantes.', 422);

$extensionArchivo = '';
if ($tieneArchivo) {
    if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK || $_FILES['archivo']['size'] > 10 * 1024 * 1024) responder(false, 'No fue posible recibir el certificado o supera el máximo de 10 MB.', 422);
    $tiposPermitidos = array('application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png');
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $_FILES['archivo']['tmp_name']) : '';
    if ($finfo) finfo_close($finfo);
    if (!isset($tiposPermitidos[$mime])) responder(false, 'El certificado debe ser PDF, JPG o PNG.', 422);
    $extensionArchivo = $tiposPermitidos[$mime];
}

$rut = formatea_rut($rutIngresado);
$telefono = $codigoPais . $telefonoLocal;
$tipoInscripcion = $tipoSocio === 'estudiante' ? 3 : 1;
$idPlanMatricula = $tipoInscripcion;
$password = md5($passwordPlano);
$hoy = date('Y-m-d');
$ahora = date('Y-m-d H:i:s');
$hora = date('H:i:s');
$ip = getRealIP();
$token = md5($password . date('Y-m-d-H-i-s') . $email . random_int(9999, 999999));
$archivoGuardado = '';

$mysql = new mysql;
$conexion = $mysql->connect();
if (!$conexion) responder(false, 'No fue posible conectar con el sistema. Intenta nuevamente.', 500);
$esc = function ($valor) use ($conexion) { return mysqli_real_escape_string($conexion, $valor); };

$emailSql = $esc($email);
$rutSql = $esc($rut);
$duplicadoEmail = $mysql->query("SELECT id_usuario FROM usuario WHERE email='$emailSql' AND email!='' LIMIT 1");
$duplicadoRut = $mysql->query("SELECT id_perfil FROM perfil WHERE rut='$rutSql' AND rut!='' LIMIT 1");
if (($duplicadoEmail && $mysql->f_num($duplicadoEmail) > 0) || ($duplicadoRut && $mysql->f_num($duplicadoRut) > 0)) responder(false, 'El RUT o el correo electrónico ya se encuentra registrado.', 409);

mysqli_begin_transaction($conexion);
try {
    $nombreSql = $esc($nombre);
    $passwordSql = $esc($password);
    $ipSql = $esc($ip);
    $tokenSql = $esc($token);
    $referenciaSql = $esc($referencia);
    $sqlUsuario = "INSERT INTO usuario (id_company, id_rol, nombre_usuario, email, password, fecha_registro, hora_registro, ip_registro, fecha_actualizacion, hora_actualizacion, ip_actualizacion, estado, token, referencia) VALUES ('1', '8', '$nombreSql', '$emailSql', '$passwordSql', '$hoy', '$hora', '$ipSql', '$hoy', '$hora', '$ipSql', 'Por confirmar email', '$tokenSql', '$referenciaSql')";
    if (!$mysql->query($sqlUsuario)) throw new Exception('No fue posible crear el usuario.');
    $ultimoId = $mysql->ultimo_id();

    if ($tieneArchivo) {
        $archivoGuardado = date('Ymd_His') . '_certificado_' . random_int(9999, 99999999) . '_' . $ultimoId . '.' . $extensionArchivo;
        $destino = __DIR__ . '/../admin/components/socios/archivos/' . $archivoGuardado;
        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) throw new Exception('No fue posible guardar el certificado.');
    }

    $consultaPlan = $mysql->query("SELECT valor FROM plan_matricula WHERE id_plan_matricula='$idPlanMatricula' LIMIT 1");
    $plan = $consultaPlan ? $mysql->f_obj($consultaPlan) : null;
    if (!$plan) throw new Exception('No se encontró el plan de matrícula seleccionado.');
    $valorMatricula = (int)$plan->valor;
    $glosa = $tipoInscripcion === 3 ? "Matrícula Estudiante $nombre" : "Matrícula Profesional $nombre";
    $glosaSql = $esc($glosa);
    $tokenDeuda = $esc(md5(random_int(9999, 999999) . $nombre . $ahora . $ultimoId));
    $sqlDeuda = "INSERT INTO deudas (id_usuario, id_usuario_deuda, nombre_deudor, sub_cuenta, fecha, monto, glosa, estado, fecha_insercion, token) VALUES (0, '$ultimoId', '$nombreSql', 'matricula', '$hoy', '$valorMatricula', '$glosaSql', 'por confirmar email', '$hoy', '$tokenDeuda')";
    if (!$mysql->query($sqlDeuda)) throw new Exception('No fue posible crear la matrícula.');

    $tokenPerfil = $esc(md5(random_int(999, 999999) . date('Y-m-d-H-i-s') . $ultimoId));
    $sexoSql = $esc($sexoGenero);
    $telefonoSql = $esc($telefono);
    $archivoSql = $esc($archivoGuardado);
    $otroClubSql = $otroClub === '' ? 'NULL' : "'$otroClub'";
    $nombreClubSql = $esc($otroClub === '1' ? $nombreOtroClub : '');
    $motivacionesSql = $esc(json_encode($motivaciones, JSON_UNESCAPED_UNICODE));
    $motivacionOtroSql = $esc(in_array('otro', $motivaciones, true) ? $motivacionOtro : '');
    $versionSql = $esc(CONSENTIMIENTO_VERSION);
    $sqlPerfil = "INSERT INTO perfil (id_usuario, nombre, tipo_inscripcion, id_plan_matricula, fono, mail, rut, fecha_nacimiento, certificado_estudios, sexo_genero, otro_club_montanismo, nombre_otro_club, motivaciones_ingreso, motivacion_otro, consentimiento_convenios, consentimiento_fecha, consentimiento_version, token) VALUES ('$ultimoId', '$nombreSql', '$tipoInscripcion', '$idPlanMatricula', '$telefonoSql', '$emailSql', '$rutSql', '$fechaNacimiento', '$archivoSql', '$sexoSql', $otroClubSql, '$nombreClubSql', '$motivacionesSql', '$motivacionOtroSql', '1', '$ahora', '$versionSql', '$tokenPerfil')";
    if (!$mysql->query($sqlPerfil)) throw new Exception('No fue posible guardar el perfil.');
    mysqli_commit($conexion);
} catch (Throwable $e) {
    mysqli_rollback($conexion);
    if ($archivoGuardado !== '') @unlink(__DIR__ . '/../admin/components/socios/archivos/' . $archivoGuardado);
    logError('Registro rechazado: ' . $e->getMessage());
    responder(false, 'No fue posible completar el registro. Intenta nuevamente.', 500);
}

$hostActual = strtolower(preg_replace('/:\d+$/', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'www.ramuch.cl'));
$hostPermitido = $hostActual === 'staging.ramuch.cl' ? 'staging.ramuch.cl' : 'www.ramuch.cl';
$ruta = 'https://' . $hostPermitido . '/registro';
$urlVerificacion = $ruta . '/verificacion.php?token=' . rawurlencode($token);
$nombreHtml = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
$messageHtml = "<html><body style='font-family:Arial,sans-serif;font-size:14px;color:#1a1a1a'><p><strong>Completa tu registro</strong></p><p>Estimado(a) $nombreHtml:</p><p>Para completar tu registro en RAMUCH debes verificar tu correo haciendo clic en el siguiente enlace:</p><p><a href='$urlVerificacion'>Verificar mi cuenta</a></p><p>También puedes copiar y pegar esta dirección en tu navegador:<br>$urlVerificacion</p><p>Muchas gracias.</p></body></html>";
$messagePlain = "Completa tu registro\n\nEstimado(a) $nombre:\n\nVerifica tu correo en el siguiente enlace:\n$urlVerificacion\n\nMuchas gracias.";

try {
    $mail = crearMailerRamuch(true);
    $mail->setFrom('no-responder@ramuch.cl', 'RAMUCH');
    $mail->addAddress($email);
    $mail->addReplyTo('directiva@ramuch.cl', 'RAMUCH');
    $mail->isHTML(true);
    $mail->Subject = 'Registro de Usuario RAMUCH';
    $mail->Body = $messageHtml;
    $mail->AltBody = $messagePlain;
    $mail->send();
} catch (Throwable $e) {
    logError('Registro creado, pero falló el correo para usuario ' . $ultimoId . ': ' . $e->getMessage());
}

responder(true, 'Registro creado. Revisa tu correo para verificar la cuenta.');
