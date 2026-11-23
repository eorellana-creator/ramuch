<?php
include('_bootstrap.php');

$draw = (int)($_POST['draw'] ?? 0);
$inicio = max(0, (int)($_POST['start'] ?? 0));
$fin = (int)($_POST['length'] ?? 25);
if ($fin <= 0 || $fin > 100) $fin = 25;
$buscar = intranetTextoSql($_POST['search']['value'] ?? '', 255);
$orden = (int)($_POST['order'][0]['column'] ?? 0);
$direccion = strtolower($_POST['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$where = ' WHERE 1=1 ';
if ($buscar !== '') {
    $where .= " AND (texto LIKE '%$buscar%' OR solicitante_nombre LIKE '%$buscar%' OR estado LIKE '%$buscar%') ";
}
$columnas = [0 => 'id_solicitud', 1 => 'fecha_solicitud', 2 => 'solicitante_nombre', 4 => 'estado', 5 => 'valor'];
$campo = $columnas[$orden] ?? 'id_solicitud';

$sql = $mysql->query("SELECT * FROM intranet_solicitud $where ORDER BY $campo $direccion, id_solicitud DESC LIMIT $inicio,$fin;");
$sqlTotal = $mysql->query('SELECT COUNT(*) cantidad FROM intranet_solicitud;');
$total = $mysql->f_obj($sqlTotal);
$sqlFiltrado = $mysql->query("SELECT COUNT(*) cantidad FROM intranet_solicitud $where;");
$filtrado = $mysql->f_obj($sqlFiltrado);

$nombresEstado = [
    'solicitado' => ['Solicitado', 'secondary'],
    'valorado' => ['Valorado', 'info'],
    'aprobado' => ['Aprobado', 'success'],
    'pagado' => ['Pagado', 'intranet-pagado'],
    'finalizado' => ['Finalizado', 'intranet-finalizado'],
    'descartado' => ['Descartado', 'danger']
];
$datos = [];
while ($item = $mysql->f_obj($sql)) {
    $token = htmlspecialchars($item->token, ENT_QUOTES, 'UTF-8');
    $estado = $item->estado;
    $infoEstado = $nombresEstado[$estado] ?? [$estado, 'secondary'];
    $badgeEstado = "<span class='badge badge-{$infoEstado[1]}'>{$infoEstado[0]}</span>";
    $texto = "<div class='texto-solicitud'>" . nl2br(htmlspecialchars($item->texto, ENT_QUOTES, 'UTF-8')) . '</div>';
    $solicitante = htmlspecialchars($item->solicitante_nombre, ENT_QUOTES, 'UTF-8');
    $fecha = date('d-m-Y H:i', strtotime($item->fecha_solicitud));
    $valor = $item->valor === null ? '-' : '$ ' . number_format((int)$item->valor, 0, ',', '.');
    $acciones = "<button class='btn btn-sm btn-light historial-intranet' data-token='$token'><i class='fas fa-history'></i> Historial</button>";

    if ($estado === 'solicitado') {
        $acciones .= "<button class='btn btn-sm btn-info editar-intranet' data-token='$token'><i class='fas fa-edit'></i> Editar</button>";
    }
    if (!in_array($estado, ['finalizado', 'descartado'], true)) {
        $acciones .= "<button class='btn btn-sm btn-outline-danger accion-intranet' data-token='$token' data-accion='descartar' data-titulo='Descartar solicitud' data-label='Motivo para descartar:'><i class='fas fa-ban'></i> Descartar</button>";
    }

    if ($rolIntranet === 'desarrollador') {
        if ($estado === 'solicitado') $acciones .= "<button class='btn btn-sm btn-info accion-intranet' data-token='$token' data-accion='valorizar' data-titulo='Valorizar solicitud' data-label='Detalle de la cotización (opcional):'>Valorizar</button>";
    }
    if ($rolIntranet === 'directiva') {
        if ($estado === 'valorado') $acciones .= "<button class='btn btn-sm btn-success accion-intranet' data-token='$token' data-accion='aprobar' data-titulo='Aprobar cotización' data-label='Observación de Directiva (opcional):'>Aprobar</button>";
        if ($estado === 'aprobado') $acciones .= "<button class='btn btn-sm btn-warning accion-intranet' data-token='$token' data-accion='pagar' data-titulo='Registrar pago' data-label='Referencia u observación de pago (opcional):'>Registrar pago</button>";
        if ($estado === 'pagado') $acciones .= "<button class='btn btn-sm btn-success accion-intranet' data-token='$token' data-accion='finalizar' data-titulo='Finalizar solicitud' data-label='Observación final (opcional):'>Finalizar</button>";
    }

    $datos[] = [(int)$item->id_solicitud, $fecha, $solicitante, $texto, $badgeEstado, $valor, $acciones];
}

intranetJson(['draw' => $draw, 'recordsTotal' => (int)($total->cantidad ?? 0), 'recordsFiltered' => (int)($filtrado->cantidad ?? 0), 'data' => $datos]);
?>
