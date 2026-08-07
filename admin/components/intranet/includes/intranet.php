<?php
function intranetRol($mysql) {
    $idUsuario = (int)($_SESSION['usuario_id'] ?? 0);
    if ($idUsuario <= 0 || ($_SESSION['usuario_valido_bastro_ruta'] ?? '') !== 'true') {
        return '';
    }
    if ($idUsuario === 1) {
        return 'desarrollador';
    }

    $sql = $mysql->query("SELECT r.nombre FROM usuario u INNER JOIN rol r ON r.id_rol=u.id_rol WHERE u.id_usuario='$idUsuario' AND u.estado='Vigente' LIMIT 1;");
    $usuario = $mysql->f_obj($sql);
    if ($usuario && trim(strtolower($usuario->nombre)) === 'administrador de socios') {
        return 'directiva';
    }
    return '';
}

function intranetExigirAcceso($mysql) {
    $rol = intranetRol($mysql);
    if ($rol === '') {
        http_response_code(403);
        exit('Acceso no autorizado');
    }
    return $rol;
}

function intranetCrearTablas($mysql) {
    $tablaSolicitudes = $mysql->query("CREATE TABLE IF NOT EXISTS intranet_solicitud (
        id_solicitud INT NOT NULL AUTO_INCREMENT,
        token VARCHAR(32) NOT NULL,
        id_solicitante INT NOT NULL,
        solicitante_nombre VARCHAR(255) NOT NULL,
        texto TEXT NOT NULL,
        estado VARCHAR(30) NOT NULL DEFAULT 'solicitada',
        valor INT NULL,
        detalle_valorizacion TEXT NULL,
        observacion_directiva TEXT NULL,
        observacion_desarrollo TEXT NULL,
        observacion_final TEXT NULL,
        pagado TINYINT(1) NOT NULL DEFAULT 0,
        fecha_solicitud DATETIME NOT NULL,
        fecha_actualizacion DATETIME NOT NULL,
        PRIMARY KEY (id_solicitud),
        UNIQUE KEY token (token),
        KEY estado (estado),
        KEY id_solicitante (id_solicitante)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $tablaHistorial = $mysql->query("CREATE TABLE IF NOT EXISTS intranet_solicitud_historial (
        id_historial INT NOT NULL AUTO_INCREMENT,
        id_solicitud INT NOT NULL,
        id_usuario INT NOT NULL,
        usuario_nombre VARCHAR(255) NOT NULL,
        accion VARCHAR(60) NOT NULL,
        estado_anterior VARCHAR(30) NULL,
        estado_nuevo VARCHAR(30) NOT NULL,
        comentario TEXT NULL,
        fecha DATETIME NOT NULL,
        PRIMARY KEY (id_historial),
        KEY id_solicitud (id_solicitud)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    if (!$tablaSolicitudes || !$tablaHistorial) {
        error_log('Intranet: no fue posible crear o verificar las tablas requeridas.');
        return false;
    }

    $verificaSolicitudes = $mysql->query("SELECT 1 FROM intranet_solicitud LIMIT 1;");
    $verificaHistorial = $mysql->query("SELECT 1 FROM intranet_solicitud_historial LIMIT 1;");
    if ($verificaSolicitudes === false || $verificaHistorial === false) {
        error_log('Intranet: las tablas requeridas no están disponibles para lectura.');
        return false;
    }

    return true;
}

function intranetJson($datos, $codigo = 200) {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
?>
