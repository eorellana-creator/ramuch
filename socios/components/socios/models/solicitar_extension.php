<?php
session_start();
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

error_log("🎯 SOLICITAR_EXTENSION - INICIANDO");
error_log("POST recibido: " . print_r($_POST, true));
error_log("GET recibido: " . print_r($_GET, true));

// DEBUG INICIAL

error_log("🎯 GET DATA: " . print_r($_GET, true));

// 🔥 PROCESAR EXACTAMENTE COMO EL EJEMPLO QUE FUNCIONA
$tokens = [];
if (!empty($_GET['tokens'])) {
    $tokens = is_array($_GET['tokens']) ? $_GET['tokens'] : [$_GET['tokens']];
} elseif (!empty($_POST['tokens'])) {
    $tokens = is_array($_POST['tokens']) ? $_POST['tokens'] : [$_POST['tokens']];
}

$nuevaFecha = $_GET['nueva_fecha'] ?? $_POST['nueva_fecha'] ?? '';
$motivo = $_GET['motivo'] ?? $_POST['motivo'] ?? '';
$tipo_extension = $_GET['tipo_extension'] ?? $_POST['tipo_extension'] ?? '1';

error_log("🎯 TOKENS: " . print_r($tokens, true));
error_log("🎯 CANTIDAD TOKENS: " . count($tokens));
error_log("🎯 FECHA: $nuevaFecha");
error_log("🎯 TIPO: $tipo_extension");

// Validaciones básicas
if (empty($tokens)) {
    echo json_encode(['success' => false, 'mensaje' => 'No se han seleccionado productos para extensión']);
    exit;
}

if (empty($nuevaFecha) || empty($motivo)) {
    echo json_encode(['success' => false, 'mensaje' => 'Todos los campos son requeridos']);
    exit;
}

$mysql = new mysql;
$mysql->connect();

$id_usuario_sesion = (int)($_SESSION['usuario_id'] ?? 0);
if ($id_usuario_sesion <= 0 && !empty($_SESSION['usuario_token'])) {
    $token_usuario = str_replace("'", "''", $_SESSION['usuario_token']);
    $resultadoUsuario = $mysql->query("SELECT id_usuario FROM usuario WHERE token='$token_usuario' LIMIT 1");
    $usuarioSesion = $mysql->f_obj($resultadoUsuario);
    $id_usuario_sesion = (int)($usuarioSesion->id_usuario ?? 0);
}

$fechaObjeto = DateTime::createFromFormat('!Y-m-d', $nuevaFecha);
$fechaValida = $fechaObjeto && $fechaObjeto->format('Y-m-d') === $nuevaFecha;

if ($id_usuario_sesion <= 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'mensaje' => 'Sesión no válida']);
    exit;
}

if (!in_array((string)$tipo_extension, ['1', '2'], true) || !$fechaValida || $nuevaFecha < date('Y-m-d')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensaje' => 'Tipo de extensión o fecha no válida']);
    exit;
}

$exitosos = 0;
$fallidos = 0;
$errores = [];
$equipos_procesados = [];

foreach ($tokens as $token) {
    try {
        if (!preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
            throw new Exception('Token de préstamo no válido');
        }

        error_log("🔄 Procesando token: $token");
        $mysql->query("START TRANSACTION");
        
        // Obtener datos del préstamo
        $sql = "SELECT ep.*, e.nombre as nombre_equipo, e.id_unico 
                FROM equipo_prestamo ep 
                INNER JOIN equipo e ON ep.id_equipo = e.id_equipo 
                WHERE ep.token = '$token'
                AND ep.id_usuario_prestamo = '$id_usuario_sesion'
                AND ep.estado = 'prestado'
                FOR UPDATE";
        $result = $mysql->query($sql);
        
        if (!$result) {
            throw new Exception('Error al verificar préstamo');
        }

        $prestamo = $mysql->f_obj($result);
        
        if (!$prestamo) {
            throw new Exception('Préstamo no encontrado');
        }

        // Validar límite de extensiones
        if ($prestamo->extensiones_solicitadas >= 2) {
            throw new Exception('Límite de extensiones alcanzado');
        }

        // Escapar motivo
        $motivoSeguro = str_replace("'", "''", $motivo);
        
        // Procesar según tipo de extensión
        if ($tipo_extension == '1') {
            // Validar primera extensión
            if ($prestamo->estado_extension == 'pendiente') {
                throw new Exception('Ya tiene una solicitud de extensión pendiente');
            }
            if ($prestamo->estado_extension == 'aprobada') {
                throw new Exception('Ya tiene una extensión aprobada');
            }
            if ($nuevaFecha <= $prestamo->fecha_debe_devolver) {
                throw new Exception('La primera extensión debe ser posterior a la fecha vigente de devolución');
            }
            
            $nuevoContador = $prestamo->extensiones_solicitadas + 1;
            $sqlUpdate = "UPDATE equipo_prestamo SET
                        fecha_solicitud_extension = NOW(),
                        fecha_propuesta_extension = '$nuevaFecha',
                        motivo_extension = '$motivoSeguro',
                        estado_extension = 'pendiente',
                        extensiones_solicitadas = $nuevoContador
                        WHERE token = '$token'";
        } else {
            // Validar segunda extensión
            if ($prestamo->estado_extension != 'aprobada') {
                throw new Exception('Debe tener primera extensión aprobada');
            }
            if ($prestamo->estado_extension2 == 'pendiente') {
                throw new Exception('Ya tiene segunda extensión pendiente');
            }
            if ($prestamo->estado_extension2 == 'aprobada') {
                throw new Exception('Ya tiene segunda extensión aprobada');
            }
            if (empty($prestamo->fecha_propuesta_extension) || $nuevaFecha <= $prestamo->fecha_propuesta_extension) {
                throw new Exception('La segunda extensión debe ser posterior a la primera');
            }
            
            $nuevoContador = $prestamo->extensiones_solicitadas + 1;
            $sqlUpdate = "UPDATE equipo_prestamo SET
                        fecha_solicitud_extension2 = NOW(),
                        fecha_propuesta_extension2 = '$nuevaFecha',
                        motivo_extension2 = '$motivoSeguro',
                        estado_extension2 = 'pendiente',
                        extensiones_solicitadas = $nuevoContador
                        WHERE token = '$token'";
        }

        // Ejecutar actualización
        $updateResult = $mysql->query($sqlUpdate);
        
        if (!$updateResult) {
            throw new Exception('Error al actualizar');
        }

        $mysql->query("COMMIT");
        $exitosos++;
        $equipos_procesados[] = [
            'nombre' => $prestamo->nombre_equipo,
            'id_unico' => $prestamo->id_unico
        ];
        
        error_log("✅ Éxito para: {$prestamo->nombre_equipo}");

    } catch (Exception $e) {
        $mysql->query("ROLLBACK");
        $fallidos++;
        $errores[] = $e->getMessage();
        error_log("❌ Error: " . $e->getMessage());
    }
}

// 🔥 ENVIAR EMAIL (EXACTO como el ejemplo)
if ($exitosos > 0) {
    try {
        require_once("../../../includes/PHPMailer2/PHPMailerAutoload.php");
        $mail = new PHPMailer;
        
        // Configuración SMTP (igual al ejemplo)
        $mail->isSMTP();
        $mail->Host = "mail.montanauchile.cl";
        $mail->SMTPAuth = true;
        $mail->Username = "no-responder@montanauchile.cl";
        $mail->Password = "123ramuchchile2022";
        $mail->Port = 25;
        $mail->CharSet = 'UTF-8';
        
        $mail->setFrom("no-responder@montanauchile.cl", "Sistema Ramuch");
        
        // Destinatarios
        $mail->addAddress("equipo@ramuch.cl", "Comisión de Equipos");
        //$mail->addAddress("montana.uchile@gmail.com", "Ramuch");
        
        //$mail->addAddress("eorellana@gmail.com", "Comisión de Equipos");
        

        // Obtener info usuario
        $token_usuario = $_SESSION['usuario_token'] ?? '';
        $nombre_usuario = 'Usuario';
        $email_usuario = '';
        
        if ($token_usuario) {
            $sql_user = $mysql->query("SELECT p.nombre, p.mail FROM perfil p 
                                     INNER JOIN usuario u ON p.id_usuario = u.id_usuario 
                                     WHERE u.token = '$token_usuario'");
            $user_data = $mysql->f_obj($sql_user);
            if ($user_data) {
                $nombre_usuario = $user_data->nombre ?? $nombre_usuario;
                $email_usuario = $user_data->mail ?? $email_usuario;
                if ($email_usuario) {
                    $mail->addAddress($email_usuario, $nombre_usuario);
                }
            }
        }
        
        // Crear contenido email
        $tipo_texto = $tipo_extension == '1' ? 'Primera Extensión' : 'Segunda Extensión';
        $nueva_fecha_formato = date("d-m-Y", strtotime($nuevaFecha));
        
        $equipos_lista = "";
        foreach ($equipos_procesados as $equipo) {
            $equipos_lista .= "- {$equipo['nombre']} (ID: {$equipo['id_unico']})<br>";
        }
        
        $cuerpo = "
        <html>
        <body>
            <p>
                <strong>Solicitud de $tipo_texto</strong>
                <br><br>
                <strong>$nombre_usuario</strong> ha solicitado $tipo_texto para los siguientes equipos hasta el <strong>$nueva_fecha_formato</strong>:<br><br>
                $equipos_lista
                <br>
                <strong>Motivo:</strong> $motivo
                <br><br>
                <strong>Sistema Ramuch</strong>
            </p>
        </body>
        </html>";
        
        $mail->Subject = "Solicitud de $tipo_texto - $nombre_usuario";
        $mail->msgHTML($cuerpo);
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        if ($mail->send()) {
            error_log("✅ Email enviado correctamente");
        } else {
            error_log("⚠️ Error enviando email: " . $mail->ErrorInfo);
        }
    } catch (Exception $e) {
        error_log("⚠️ Error en envío de email: " . $e->getMessage());
    }
}

// Respuesta final
if ($exitosos > 0) {
    $mensaje = ($tipo_extension == '1') 
        ? "Primera extensión solicitada para $exitosos producto(s)" 
        : "Segunda extensión solicitada para $exitosos producto(s)";
    
    if ($fallidos > 0) {
        $mensaje .= ". Fallaron $fallidos solicitud(es)";
    }
    
    echo json_encode([
        'success' => true,
        'mensaje' => $mensaje,
        'exitosos' => $exitosos,
        'fallidos' => $fallidos
    ]);
} else {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Todas las solicitudes fallaron: ' . implode('; ', $errores)
    ]);
}

exit;
?>
