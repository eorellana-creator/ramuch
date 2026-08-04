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
$columnas = [0 => 'id_solicitud', 1 => 'fecha_solicitud', 2 => 'solicitante_nombre', 4 => 'estado', 5 => 'valor', 6 => 'pagado'];
$campo = $columnas[$orden] ?? 'id_solicitud';

$sql = $mysql->query("SELECT * FROM intranet_solicitud $where ORDER BY $campo $direccion, id_solicitud DESC LIMIT $inicio,$fin;");
$sqlTotal = $mysql->query('SELECT COUNT(*) cantidad FROM intranet_solicitud;');
$total = $mysql->f_obj($sqlTotal);
$sqlFiltrado = $mysql->query("SELECT COUNT(*) cantidad FROM intranet_solicitud $where;");
$filtrado = $mysql->f_obj($sqlFiltrado);

$nombresEstado = [
    'solicitada' => ['Solicitada', 'secondary'], 'valorizada' => ['Valorizada', 'warning'],
    'aprobada' => ['Aprobada', 'info'], 'en_desarrollo' => ['En desarrollo', 'primary'],
    'realizada' => ['Realizada / por revisar', 'warning'], 'finalizada' => ['Finalizada', 'success'],
    'rechazada' => ['Rechazada', 'danger']
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
    $pago = (int)$item->pagado === 1 ? "<span class='badge badge-success'>Pagado</span>" : "<span class='badge badge-secondary'>Pendiente</span>";
    $acciones = "<button class='btn btn-sm btn-light historial-intranet' data-token='$token'><i class='fas fa-history'></i> Historial</button>";

    if ($rolIntranet === 'desarrollador') {
        if ($estado === 'solicitada') $acciones .= "<button class='btn btn-sm btn-warning accion-intranet' data-token='$token' data-accion='valorizar' data-titulo='Valorizar solicitud' data-label='Detalle de la valorización:'>Valorizar</button>";
        if ($estado === 'aprobada') $acciones .= "<button class='btn btn-sm btn-primary accion-intranet' data-token='$token' data-accion='iniciar' data-titulo='Iniciar desarrollo' data-label='Comentario de inicio:'>Iniciar</button>";
        if ($estado === 'en_desarrollo') $acciones .= "<button class='btn btn-sm btn-success accion-intranet' data-token='$token' data-accion='realizar' data-titulo='Marcar como realizada' data-label='Detalle de lo realizado:'>Marcar realizada</button>";
    }
    if ($rolIntranet === 'directiva') {
        if ($estado === 'valorizada') {
            $acciones .= "<button class='btn btn-sm btn-success accion-intranet' data-token='$token' data-accion='aprobar' data-titulo='Aprobar valorización' data-label='Observación de Directiva:'>Aprobar</button>";
            $acciones .= "<button class='btn btn-sm btn-danger accion-intranet' data-token='$token' data-accion='rechazar' data-titulo='Rechazar solicitud' data-label='Motivo del rechazo:'>Rechazar</button>";
        }
        if ($estado === 'realizada') {
            $acciones .= "<button class='btn btn-sm btn-success accion-intranet' data-token='$token' data-accion='finalizar' data-titulo='Dar aprobación final' data-label='Comentario final:'>OK final</button>";
            $acciones .= "<button class='btn btn-sm btn-warning accion-intranet' data-token='$token' data-accion='observar' data-titulo='Solicitar correcciones' data-label='Correcciones requeridas:'>Observar</button>";
        }
        if ($item->valor !== null) {
            $accionPago = (int)$item->pagado === 1 ? 'pago_pendiente' : 'pagar';
            $tituloPago = (int)$item->pagado === 1 ? 'Marcar pago como pendiente' : 'Confirmar pago';
            $acciones .= "<button class='btn btn-sm btn-dark accion-intranet' data-token='$token' data-accion='$accionPago' data-titulo='$tituloPago' data-label='Referencia u observación de pago:'>$tituloPago</button>";
        }
    }

    $datos[] = [(int)$item->id_solicitud, $fecha, $solicitante, $texto, $badgeEstado, $valor, $pago, $acciones];
}

intranetJson(['draw' => $draw, 'recordsTotal' => (int)($total->cantidad ?? 0), 'recordsFiltered' => (int)($filtrado->cantidad ?? 0), 'data' => $datos]);
?>
