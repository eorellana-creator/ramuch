<?php
/**
 * Registro de ejecuciones de crons y procesos automáticos.
 *
 * Cada ejecución queda en la tabla proceso_automatico_log, y el Dashboard de
 * administradores la muestra. Si una ejecución termina con error, se avisa
 * por correo a la Directiva y al desarrollador. En staging, el aviso va solo
 * al buzón de pruebas.
 *
 * Uso:
 *   $proceso = ramuchProcesoIniciar($mysql, 'clave_proceso', 'Título visible');
 *   ...
 *   ramuchProcesoFinalizar($proceso, true, 'Descripción de lo que pasó');
 *
 * Si el script se cae antes de finalizar (error fatal, exit inesperado), queda
 * registrado como error de forma automática.
 */

require_once __DIR__ . '/RamuchMailer.php';

const RAMUCH_PROCESO_CORREOS_ERROR = ['directiva@ramuch.cl', 'eorellana@gmail.com'];
const RAMUCH_PROCESO_CORREO_PRUEBAS = 'eorellana@gmail.com';

function ramuchProcesoEsStaging()
{
    $host = strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));
    $ruta = str_replace('\\', '/', __DIR__);
    return $host === 'staging.ramuch.cl' || strpos($ruta, '/staging.ramuch.cl/') !== false;
}

function ramuchProcesoCrearTabla($mysql)
{
    static $lista = false;
    if ($lista) {
        return true;
    }
    if (!is_object($mysql) || !$mysql->conexion) {
        return false;
    }
    $ok = $mysql->query("CREATE TABLE IF NOT EXISTS proceso_automatico_log (
        id_log INT NOT NULL AUTO_INCREMENT,
        proceso VARCHAR(60) NOT NULL,
        titulo VARCHAR(200) NOT NULL,
        descripcion TEXT NULL,
        resultado VARCHAR(10) NOT NULL,
        fecha_inicio DATETIME NOT NULL,
        fecha_fin DATETIME NOT NULL,
        duracion_segundos INT NOT NULL DEFAULT 0,
        correo_enviado TINYINT(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (id_log),
        KEY fecha_fin (fecha_fin),
        KEY proceso (proceso)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;") !== false;
    if ($ok) {
        $lista = true;
    }
    return $ok;
}

function ramuchProcesoIniciar($mysql, $proceso, $titulo)
{
    $contexto = new stdClass();
    $contexto->mysql = $mysql;
    $contexto->proceso = $proceso;
    $contexto->titulo = $titulo;
    $contexto->inicio = time();
    $contexto->cerrado = false;

    register_shutdown_function(function () use ($contexto) {
        if ($contexto->cerrado) {
            return;
        }
        $ultimo = error_get_last();
        $detalle = $ultimo
            ? 'Error de PHP: ' . $ultimo['message'] . ' (' . basename($ultimo['file']) . ', línea ' . $ultimo['line'] . ')'
            : 'El proceso se detuvo antes de terminar y no informó su resultado.';
        ramuchProcesoFinalizar($contexto, false, 'El proceso terminó de forma inesperada. ' . $detalle);
    });

    return $contexto;
}

/**
 * Registra el resultado de la ejecución. Nunca lanza excepciones: un problema
 * en el registro no debe interrumpir el proceso que se está registrando.
 */
function ramuchProcesoFinalizar($contexto, $ok, $descripcion, $titulo = null)
{
    if (!$contexto || $contexto->cerrado) {
        return;
    }
    $contexto->cerrado = true;
    if ($titulo !== null) {
        $contexto->titulo = $titulo;
    }

    try {
        $resultado = $ok ? 'ok' : 'error';
        $inicio = date('Y-m-d H:i:s', $contexto->inicio);
        $fin = date('Y-m-d H:i:s');
        $duracion = max(0, time() - $contexto->inicio);

        $correoEnviado = false;
        if (!$ok) {
            $correoEnviado = ramuchProcesoNotificarError($contexto->titulo, $descripcion, $fin);
        }

        $mysql = $contexto->mysql;
        if (!ramuchProcesoCrearTabla($mysql)) {
            error_log('Procesos automáticos: no fue posible registrar "' . $contexto->titulo . '" (' . $resultado . '): ' . $descripcion);
            return;
        }

        $e = function ($valor) use ($mysql) {
            return mysqli_real_escape_string($mysql->conexion, (string)$valor);
        };
        $insertado = $mysql->query("INSERT INTO proceso_automatico_log
            (proceso, titulo, descripcion, resultado, fecha_inicio, fecha_fin, duracion_segundos, correo_enviado)
            VALUES ('" . $e(substr($contexto->proceso, 0, 60)) . "', '" . $e(mb_substr($contexto->titulo, 0, 200)) . "',
                    '" . $e($descripcion) . "', '$resultado', '$inicio', '$fin', " . (int)$duracion . ", " . ($correoEnviado ? 1 : 0) . ");");
        if ($insertado === false) {
            error_log('Procesos automáticos: falló el registro de "' . $contexto->titulo . '": ' . mysqli_error($mysql->conexion));
        }
    } catch (\Throwable $error) {
        error_log('Procesos automáticos: ' . $error->getMessage());
    }
}

function ramuchProcesoNotificarError($titulo, $descripcion, $fecha)
{
    try {
        $esStaging = ramuchProcesoEsStaging();
        $destinatarios = $esStaging ? [RAMUCH_PROCESO_CORREO_PRUEBAS] : RAMUCH_PROCESO_CORREOS_ERROR;
        $ambiente = $esStaging ? 'staging' : 'producción';
        $urlDashboard = ($esStaging ? 'https://staging.ramuch.cl' : 'https://www.ramuch.cl')
            . '/admin/index.php?component=dashboard&view=dashboard';

        $tituloHtml = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
        $descripcionHtml = nl2br(htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8'));

        $mail = crearMailerRamuch(true);
        $mail->setFrom('no-responder@ramuch.cl', 'Sistema Ramuch');
        foreach ($destinatarios as $destinatario) {
            $mail->addAddress($destinatario);
        }
        $mail->addReplyTo('directiva@ramuch.cl', 'Directiva Ramuch');
        $mail->Subject = ($esStaging ? '[STAGING] ' : '') . 'Error en proceso automático: ' . $titulo;
        $mail->msgHTML("<html><body style='font-family:Arial,sans-serif;color:#1a1a1a'>
            <h2 style='color:#c62828'>Error en proceso automático</h2>
            <p><strong>Proceso:</strong> $tituloHtml<br>
            <strong>Fecha:</strong> $fecha<br>
            <strong>Ambiente:</strong> $ambiente</p>
            <p><strong>Qué pasó:</strong><br>$descripcionHtml</p>
            <p><a href='$urlDashboard'>Ver el historial en el Dashboard</a></p>
            </body></html>");
        $mail->AltBody = "Error en proceso automático\n\nProceso: $titulo\nFecha: $fecha\nAmbiente: $ambiente\n\nQué pasó:\n$descripcion\n\n$urlDashboard";
        return $mail->send();
    } catch (\Throwable $error) {
        error_log('Procesos automáticos: no se pudo enviar el aviso de error: ' . $error->getMessage());
        return false;
    }
}

/** Últimas ejecuciones para el Dashboard (máximo 10). */
function ramuchProcesoUltimasEjecuciones($mysql, $limite = 10)
{
    $existe = $mysql->query("SHOW TABLES LIKE 'proceso_automatico_log';");
    if (!$existe || $mysql->f_num($existe) === 0) {
        return [];
    }
    $limite = max(1, min(10, (int)$limite));
    $sql = $mysql->query("SELECT titulo, descripcion, resultado, fecha_fin, duracion_segundos, correo_enviado
        FROM proceso_automatico_log ORDER BY fecha_fin DESC, id_log DESC LIMIT $limite;");
    $filas = [];
    while ($sql && ($fila = $mysql->f_obj($sql))) {
        $filas[] = $fila;
    }
    return $filas;
}

/** Último resultado de cada proceso, para ver de un vistazo si alguno falla o dejó de correr. */
function ramuchProcesoEstadoPorProceso($mysql)
{
    $existe = $mysql->query("SHOW TABLES LIKE 'proceso_automatico_log';");
    if (!$existe || $mysql->f_num($existe) === 0) {
        return [];
    }
    $sql = $mysql->query("SELECT l.proceso, l.titulo, l.resultado, l.fecha_fin
        FROM proceso_automatico_log l
        INNER JOIN (SELECT proceso, MAX(id_log) AS id_log FROM proceso_automatico_log GROUP BY proceso) u
            ON u.id_log = l.id_log
        ORDER BY l.fecha_fin DESC;");
    $filas = [];
    while ($sql && ($fila = $mysql->f_obj($sql))) {
        $filas[] = $fila;
    }
    return $filas;
}
