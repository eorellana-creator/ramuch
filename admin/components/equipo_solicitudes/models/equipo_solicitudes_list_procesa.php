<?php
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$draw = (int)($_POST["draw"] ?? 0);
$inicio = max(0, (int)($_POST["start"] ?? 0));
$fin = (int)($_POST["length"] ?? 25);
if ($fin <= 0 || $fin > 100) {
    $fin = 25;
}

$busquedaTexto = trim($_POST["search"]["value"] ?? "");
$orden = (int)($_POST["order"][0]["column"] ?? 0);
$direccion = strtolower($_POST["order"][0]["dir"] ?? "desc") === "asc" ? "ASC" : "DESC";

$config = new Config;
$mysql = new mysql;
$mysql->connect();

$where = " WHERE ep.estado='solicitado' ";
if ($busquedaTexto !== "") {
    $busquedaSql = str_replace(["\\", "'", "%", "_"], ["\\\\", "''", "\\%", "\\_"], $busquedaTexto);
    $where .= " AND (
        ep.id_equipo LIKE '%$busquedaSql%'
        OR e.id_unico LIKE '%$busquedaSql%'
        OR e.nombre LIKE '%$busquedaSql%'
        OR u.nombre_usuario LIKE '%$busquedaSql%'
    ) ";
}

$columnasOrden = [
    0 => "e.id_equipo",
    2 => "e.id_unico",
    3 => "e.nombre",
    4 => "u.nombre_usuario",
    5 => "ep.fecha_prestamo"
];
$campoOrden = $columnasOrden[$orden] ?? "ep.id_equipo_prestamo";
$orderby = " ORDER BY $campoOrden $direccion, ep.id_equipo_prestamo DESC ";

$from = " FROM equipo_prestamo ep
    INNER JOIN equipo e ON e.id_equipo = ep.id_equipo
    LEFT JOIN usuario u ON u.id_usuario = ep.id_usuario_prestamo ";

$sql = $mysql->query("SELECT
        ep.id_equipo_prestamo,
        ep.id_usuario_prestamo,
        ep.fecha_prestamo,
        ep.fecha_debe_devolver,
        ep.token,
        e.id_equipo,
        e.id_unico,
        e.nombre AS equipo_nombre,
        e.estado AS equipo_estado,
        e.descripcion,
        e.observacion,
        e.talla,
        e.imagen,
        u.nombre_usuario AS solicitud_nombre
    $from $where $orderby LIMIT $inicio,$fin;");

$sqlTotal = $mysql->query("SELECT COUNT(*) AS cantidad $from WHERE ep.estado='solicitado';");
$resultTotal = $mysql->f_obj($sqlTotal);
$cantidadRegistros = (int)($resultTotal->cantidad ?? 0);

$sqlFiltrados = $mysql->query("SELECT COUNT(*) AS cantidad $from $where;");
$resultFiltrados = $mysql->f_obj($sqlFiltrados);
$cantidadFiltrados = (int)($resultFiltrados->cantidad ?? 0);

$datos = [];
while ($result = $mysql->f_obj($sql)) {
    $nombre = htmlspecialchars($result->equipo_nombre ?? "", ENT_QUOTES, "UTF-8");
    $estado = htmlspecialchars($result->equipo_estado ?? "", ENT_QUOTES, "UTF-8");
    $descripcion = htmlspecialchars($result->descripcion ?? "", ENT_QUOTES, "UTF-8");
    $observacion = htmlspecialchars($result->observacion ?? "", ENT_QUOTES, "UTF-8");
    $talla = htmlspecialchars($result->talla ?? "", ENT_QUOTES, "UTF-8");
    $idUnico = htmlspecialchars($result->id_unico ?? "", ENT_QUOTES, "UTF-8");
    $token = htmlspecialchars($result->token ?? "", ENT_QUOTES, "UTF-8");

    $nombreEquipo = "$nombre <br> $descripcion <br> $estado <br> $talla <br> $observacion";

    $imagen = htmlspecialchars($result->imagen ?? "", ENT_QUOTES, "UTF-8");
    $imgUrl = "<img src='images/equipo_sin_imagen.jpg' width='90' height='120' alt='Sin imagen'>";
    if ($imagen !== "") {
        $imgUrl = "<img src='images/equipo/$imagen' width='90' height='120' alt='Equipo'>";
    }

    $usuarioValido = (int)$result->id_usuario_prestamo > 0 && trim((string)$result->solicitud_nombre) !== "";
    if ($usuarioValido) {
        $usuarioSolicitud = htmlspecialchars($result->solicitud_nombre, ENT_QUOTES, "UTF-8");
        $aceptarRechazar = "<button type='button' class='btn btn-success' onClick='aceptaRechaza(1,\"$token\");'>Aceptar</button> "
            . "<button type='button' class='btn btn-danger' onClick='seteaTokenPrestamo(\"$token\")' data-toggle='modal' data-target='#primaryModal'>Rechazar</button>";
    } else {
        $usuarioSolicitud = "<span class='text-danger'><strong>Solicitud inválida</strong><br>Usuario ID: " . (int)$result->id_usuario_prestamo . "</span>";
        $aceptarRechazar = "<button type='button' class='btn btn-default' disabled>Solicitud inválida</button>";
    }

    $desde = fecha_mysql_a_normal($result->fecha_prestamo);
    $hasta = fecha_mysql_a_normal($result->fecha_debe_devolver);
    $periodo = "Desde el $desde <br>al $hasta";

    $datos[] = [
        (int)$result->id_equipo,
        $imgUrl,
        $idUnico,
        $nombreEquipo,
        $usuarioSolicitud,
        $periodo,
        $aceptarRechazar
    ];
}

header("Content-Type: application/json; charset=UTF-8");
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $cantidadRegistros,
    "recordsFiltered" => $cantidadFiltrados,
    "data" => $datos
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
