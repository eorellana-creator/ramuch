<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de parámetros recibidos
//error_log("Exportar Excel - Parámetros recibidos: " . print_r($_GET, true));

include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Obtener parámetros de filtro
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';
$tipos = $_GET['tipo'] ?? array();
$subcuenta = $_GET['subcuenta'] ?? '';
$medio = $_GET['medio'] ?? '';

// Log de parámetros procesados
//error_log("Filtros aplicados - Desde: $fecha_desde, Hasta: $fecha_hasta, Subcuenta: $subcuenta, Medio: $medio");
//error_log("Tipos: " . print_r($tipos, true));

// Construir consulta base
$where = "WHERE 1=1";

// Filtro por fechas
if (!empty($fecha_desde)) {
    $where .= " AND fecha >= '$fecha_desde'";
}
if (!empty($fecha_hasta)) {
    $where .= " AND fecha <= '$fecha_hasta'";
}

// Filtro por tipos
if (!empty($tipos) && is_array($tipos)) {
    $tipos_str = implode("','", $tipos);
    $where .= " AND tipo IN ('$tipos_str')";
}

// Filtro por subcuenta
if (!empty($subcuenta)) {
    $where .= " AND sub_cuenta = '$subcuenta'";
}

// Filtro por medio
if (!empty($medio)) {
    $where .= " AND medio = '$medio'";
}

// Log de consulta final
//error_log("Consulta SQL: SELECT * FROM cuenta_maestra $where ORDER BY fecha DESC");

$mysql = new mysql;
$mysql->connect();

// Obtener datos filtrados
$sql = $mysql->query("SELECT * FROM cuenta_maestra $where ORDER BY fecha DESC");

if (!$sql) {
    error_log("Error en consulta: " . $mysql->error);
    die("Error al generar el reporte");
}

// Configurar headers para descarga CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=informe_club_montana_' . date('Y-m-d') . '.xls');

// Crear output
$output = fopen('php://output', 'w');

// Escribir encabezados
fputcsv($output, array('ID', 'Fecha', 'Usuario', 'Glosa', 'Observación', 'Medio', 'Documento Respaldo', 'Estado', 'Monto', 'Tipo'));

// Escribir datos
$contador = 0;
while($result = $mysql->f_obj($sql)) {
    $sql_user = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario ='$result->id_usuario_movimiento'");
    $user = $mysql->f_obj($sql_user);
    $nombre_usuario = $user->nombre_usuario ?? '';
    
    $fila = array(
        $result->id_cuenta_maestra,
        fecha_mysql_a_normal($result->fecha),
        $nombre_usuario,
        $result->glosa,
        $result->observacion,
        $result->medio,
        $result->documento_respaldo,
        $result->estado,
        $result->monto,
        $result->tipo
    );
    
    fputcsv($output, $fila);
    $contador++;
}

fclose($output);

// Log de resultados
//error_log("Exportación completada. Registros exportados: $contador");
exit();
?>