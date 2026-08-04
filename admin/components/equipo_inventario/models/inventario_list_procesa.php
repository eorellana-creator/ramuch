<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$draw = (int)($_POST["draw"] ?? 0);
$inicio = max(0, (int)($_POST["start"] ?? 0));
$fin = (int)($_POST["length"] ?? 50);
if ($fin <= 0 || $fin > 100) {
    $fin = 50;
}

$busqueda = trim($_POST["search"]["value"] ?? "");
$orden = (int)($_POST["order"][0]["column"] ?? 2);
$direccion = strtolower($_POST["order"][0]["dir"] ?? "asc") === "desc" ? "DESC" : "ASC";

$mysql = new mysql;
$mysql->connect();

$where = " WHERE 1=1 ";
if ($busqueda !== "") {
    $busquedaSql = str_replace(["\\", "'", "%", "_"], ["\\\\", "''", "\\%", "\\_"], $busqueda);
    $where .= " AND (
        id_equipo LIKE '%$busquedaSql%'
        OR nombre LIKE '%$busquedaSql%'
        OR id_unico LIKE '%$busquedaSql%'
        OR estado LIKE '%$busquedaSql%'
        OR prestado_a_nombre LIKE '%$busquedaSql%'
    ) ";
}

$columnasOrden = [
    0 => "id_equipo",
    2 => "nombre",
    3 => "id_unico",
    4 => "estado",
    5 => "prestado_a_id_usuario",
    6 => "prestado_a_nombre",
    7 => "fecha_devolucion"
];
$campoOrden = $columnasOrden[$orden] ?? "nombre";

$sql = $mysql->query("SELECT * FROM equipo $where ORDER BY $campoOrden $direccion, id_equipo ASC LIMIT $inicio,$fin;");
$sqlTotal = $mysql->query("SELECT COUNT(*) AS cantidad FROM equipo;");
$total = $mysql->f_obj($sqlTotal);
$sqlFiltrados = $mysql->query("SELECT COUNT(*) AS cantidad FROM equipo $where;");
$filtrados = $mysql->f_obj($sqlFiltrados);

$datos = [];
while ($equipo = $mysql->f_obj($sql)) {
    $id = (int)$equipo->id_equipo;
    $token = htmlspecialchars($equipo->token ?? "", ENT_QUOTES, "UTF-8");
    $nombre = htmlspecialchars($equipo->nombre ?? "", ENT_QUOTES, "UTF-8");
    $idUnico = htmlspecialchars($equipo->id_unico ?? "", ENT_QUOTES, "UTF-8");
    $estado = htmlspecialchars($equipo->estado ?? "", ENT_QUOTES, "UTF-8");
    $prestadoA = htmlspecialchars($equipo->prestado_a_nombre ?? "", ENT_QUOTES, "UTF-8");
    $imagen = htmlspecialchars($equipo->imagen ?? "", ENT_QUOTES, "UTF-8");

    $img = "<img src='images/equipo_sin_imagen.jpg' width='90' height='120' alt='Sin imagen'>";
    if ($imagen !== "") {
        $img = "<button type='button' class='btn btn-link p-0' data-toggle='modal' data-target='#imageModal' data-img='images/equipo/$imagen'><img src='images/equipo/$imagen' width='90' height='120' alt='Equipo'></button>";
    }

    $estaPrestado = (int)$equipo->prestado_a_id_usuario > 0;
    $disponibilidad = $estaPrestado
        ? "<span class='badge badge-warning'>Prestado</span>"
        : "<span class='badge badge-success'>Disponible</span>";
    $fechaDevolucion = "";
    if ($estaPrestado && !empty($equipo->fecha_devolucion) && $equipo->fecha_devolucion !== "0000-00-00") {
        $fechaDevolucion = fecha_mysql_a_normal($equipo->fecha_devolucion);
    }

    $detalle = "<button type='button' class='btn btn-link' data-toggle='modal' data-target='#detalleInventarioModal' data-token='$token'><i class='fas fa-search-plus'></i> Ver equipo</button>";
    $editar = "<a href='index.php?component=equipo&view=equipo&origen=inventario&token=$token'><i class='fas fa-edit'></i> Editar</a>";

    $datos[] = [$id, $img, $nombre, $idUnico, $estado, $disponibilidad, $prestadoA, $fechaDevolucion, $detalle, $editar];
}

header("Content-Type: application/json; charset=UTF-8");
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => (int)($total->cantidad ?? 0),
    "recordsFiltered" => (int)($filtrados->cantidad ?? 0),
    "data" => $datos
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
