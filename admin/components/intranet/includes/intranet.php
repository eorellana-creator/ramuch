<?php
function intranetRol($mysql) {
    $idUsuario = (int)($_SESSION['usuario_id'] ?? 0);
    if ($idUsuario <= 0 || ($_SESSION['usuario_valido_bastro_ruta'] ?? '') !== 'true') return '';
    if ($idUsuario === 1) return 'desarrollador';
    $sql = $mysql->query("SELECT r.nombre FROM usuario u INNER JOIN rol r ON r.id_rol=u.id_rol WHERE u.id_usuario='$idUsuario' AND u.estado='Vigente' LIMIT 1;");
    $usuario = $mysql->f_obj($sql);
    return $usuario && trim(strtolower($usuario->nombre)) === 'administrador de socios' ? 'directiva' : '';
}

function intranetExigirAcceso($mysql) {
    $rol = intranetRol($mysql);
    if ($rol === '') { http_response_code(403); exit('Acceso no autorizado'); }
    return $rol;
}

function intranetAgregarColumnasFaltantes($mysql, $tabla, array $columnas) {
    foreach ($columnas as $nombre => $definicion) {
        $nombreSeguro = preg_replace('/[^a-z0-9_]/i', '', $nombre);
        $consulta = $mysql->query("SHOW COLUMNS FROM `$tabla` LIKE '$nombreSeguro';");
        if ($consulta === false) return false;
        if ($mysql->f_num($consulta) === 0 && $mysql->query("ALTER TABLE `$tabla` ADD COLUMN `$nombreSeguro` $definicion;") === false) {
            error_log("Intranet: no fue posible agregar $tabla.$nombreSeguro: " . mysqli_error($mysql->conexion));
            return false;
        }
    }
    return true;
}

function intranetCrearTablas($mysql) {
    $tablaSolicitudes = $mysql->query("CREATE TABLE IF NOT EXISTS intranet_solicitud (
        id_solicitud INT NOT NULL AUTO_INCREMENT, token VARCHAR(32) NOT NULL, id_solicitante INT NOT NULL,
        solicitante_nombre VARCHAR(255) NOT NULL, texto TEXT NOT NULL, estado VARCHAR(30) NOT NULL DEFAULT 'solicitado',
        valor INT NULL, detalle_valorizacion TEXT NULL, observacion_directiva TEXT NULL,
        observacion_desarrollo TEXT NULL, observacion_final TEXT NULL, pagado TINYINT(1) NOT NULL DEFAULT 0,
        fecha_solicitud DATETIME NOT NULL, fecha_actualizacion DATETIME NOT NULL,
        PRIMARY KEY (id_solicitud), UNIQUE KEY token (token), KEY estado (estado), KEY id_solicitante (id_solicitante)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    $tablaHistorial = $mysql->query("CREATE TABLE IF NOT EXISTS intranet_solicitud_historial (
        id_historial INT NOT NULL AUTO_INCREMENT, id_solicitud INT NOT NULL, id_usuario INT NOT NULL,
        usuario_nombre VARCHAR(255) NOT NULL, accion VARCHAR(60) NOT NULL, estado_anterior VARCHAR(30) NULL,
        estado_nuevo VARCHAR(30) NOT NULL, comentario TEXT NULL, fecha DATETIME NOT NULL,
        PRIMARY KEY (id_historial), KEY id_solicitud (id_solicitud)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    if (!$tablaSolicitudes || !$tablaHistorial) return false;

    // CREATE TABLE IF NOT EXISTS no actualiza instalaciones antiguas. Esta migración
    // permite que el flujo nuevo funcione conservando todas las solicitudes existentes.
    $solicitudOk = intranetAgregarColumnasFaltantes($mysql, 'intranet_solicitud', [
        'detalle_valorizacion' => 'TEXT NULL', 'observacion_directiva' => 'TEXT NULL',
        'observacion_desarrollo' => 'TEXT NULL', 'observacion_final' => 'TEXT NULL',
        'pagado' => 'TINYINT(1) NOT NULL DEFAULT 0', 'fecha_actualizacion' => 'DATETIME NULL'
    ]);
    $historialOk = intranetAgregarColumnasFaltantes($mysql, 'intranet_solicitud_historial', [
        'accion' => "VARCHAR(60) NOT NULL DEFAULT ''", 'estado_anterior' => 'VARCHAR(30) NULL',
        'estado_nuevo' => "VARCHAR(30) NOT NULL DEFAULT ''", 'comentario' => 'TEXT NULL'
    ]);
    if (!$solicitudOk || !$historialOk) return false;

    $normalizaEstados = $mysql->query("UPDATE intranet_solicitud SET estado=CASE estado
        WHEN 'solicitada' THEN 'solicitado' WHEN 'valorizada' THEN 'valorado' WHEN 'aprobada' THEN 'aprobado'
        WHEN 'en_desarrollo' THEN 'aprobado' WHEN 'realizada' THEN 'aprobado' WHEN 'finalizada' THEN 'finalizado'
        WHEN 'rechazada' THEN 'descartado' WHEN 'descartada' THEN 'descartado' ELSE estado END
        WHERE estado IN ('solicitada','valorizada','aprobada','en_desarrollo','realizada','finalizada','rechazada','descartada');");
    $normalizaPagos = $mysql->query("UPDATE intranet_solicitud SET estado='pagado' WHERE pagado=1 AND estado='aprobado';");
    return $normalizaEstados !== false && $normalizaPagos !== false;
}

function intranetJson($datos, $codigo = 200) {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
?>
