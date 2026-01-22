<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Configurar logging de errores
ini_set('log_errors', 1);
ini_set('error_log', 'export_atraso_errors.log');
ini_set('display_errors', 0);

// Función para loggear eventos
function logEvent($message, $type = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$type] $message" . PHP_EOL;
    error_log($logMessage, 3, 'export_atraso.log');
}

// Función para loggear errores
function logError($message, $exception = null) {
    $timestamp = date('Y-m-d H:i:s');
    $errorMessage = "[$timestamp] [ERROR] $message";
    
    if ($exception) {
        $errorMessage .= " - Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine();
    }
    
    $errorMessage .= PHP_EOL;
    error_log($errorMessage, 3, 'export_atraso_errors.log');
}

try {
    logEvent("Iniciando exportación Excel de equipos con atraso - Usuario: " . ($_SESSION['usuario_id'] ?? 'Desconocido'));

    $mysql = new mysql;
    $mysql->connect();

    // Obtener fechas del filtro
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

    logEvent("Fechas para exportación - Inicio: $fecha_inicio, Fin: $fecha_fin");

    // Validar fechas
    if (empty($fecha_inicio) || empty($fecha_fin)) {
        $errorMsg = "Fechas inválidas en exportación: Inicio='$fecha_inicio', Fin='$fecha_fin'";
        logError($errorMsg);
        die("Fechas inválidas");
    }

    // Consulta para obtener equipos con atraso - Mismo SQL que get_equipos_atraso.php
    $sql = "
        SELECT 
            ep.id_equipo,
            e.nombre,
            e.id_unico,
            e.prestado_a_nombre,
            ep.fecha_debe_devolver,
            ep.fecha_devolucion_efectiva,
            e.nombre_responsable_prestamo,
            ep.fecha_prestamo,
            ep.estado
        FROM equipo_prestamo ep
        INNER JOIN equipo e ON ep.id_equipo = e.id_equipo
        WHERE e.prestado_a_id_usuario > 0 
        AND ep.estado IN ('devuelto', 'prestado')
        AND (
            -- Préstamos devueltos con atraso: fecha_devolucion_efectiva > fecha_debe_devolver
            (ep.estado = 'devuelto' AND ep.fecha_devolucion_efectiva IS NOT NULL AND ep.fecha_devolucion_efectiva > ep.fecha_debe_devolver)
            OR
            -- Préstamos activos con atraso: fecha_debe_devolver < fecha_actual y aún no devueltos
            (ep.estado = 'prestado' AND ep.fecha_debe_devolver < CURDATE() AND ep.fecha_devolucion_efectiva IS NULL)
        )
        AND (
            ep.fecha_debe_devolver BETWEEN '$fecha_inicio' AND '$fecha_fin'
            OR 
            ep.fecha_devolucion_efectiva BETWEEN '$fecha_inicio' AND '$fecha_fin'
        )
        ORDER BY 
            CASE 
                WHEN ep.fecha_devolucion_efectiva IS NOT NULL THEN ep.fecha_devolucion_efectiva
                ELSE ep.fecha_debe_devolver
            END ASC
    ";

    logEvent("Ejecutando consulta para exportación Excel");

    $result = $mysql->query($sql);
    if (!$result) {
        $errorMsg = "Error en consulta de exportación: " . $mysql->error;
        logError($errorMsg);
        die("Error en la consulta");
    }

    // Configurar headers para descarga Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=equipos_atraso_" . date('Y-m-d') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Generar contenido Excel
    echo "<table border='1'>";
    echo "<tr style='background-color: #ff9900; color: #000000; font-weight: bold;'>";
    echo "<th>N°</th>";
    echo "<th>Equipo</th>";
    echo "<th>ID Equipo</th>";
    echo "<th>Socio</th>";
    echo "<th>Fecha Préstamo</th>";
    echo "<th>Fecha Debe Devolver</th>";
    echo "<th>Fecha Devolución Efectiva</th>";
    echo "<th>Días de Atraso</th>";
    echo "<th>Estado Préstamo</th>";
    echo "<th>Responsable Préstamo</th>";
    echo "</tr>";

    $contador = 1;
    $fecha_actual = date('Y-m-d');
    $totalExportados = 0;

    while ($row = $mysql->f_obj($result)) {
        $totalExportados++;
        
        try {
            // Calcular días de atraso - Misma lógica que get_equipos_atraso.php
            if ($row->estado == 'devuelto' && !empty($row->fecha_devolucion_efectiva)) {
                // Ya fue devuelto: calcular diferencia entre fecha_debe_devolver y fecha_devolucion_efectiva
                $fecha_debe_devolver = new DateTime($row->fecha_debe_devolver);
                $fecha_devolucion_efectiva = new DateTime($row->fecha_devolucion_efectiva);
                
                // Solo calcular atraso si la fecha efectiva es posterior a la fecha que debía devolver
                if ($fecha_devolucion_efectiva > $fecha_debe_devolver) {
                    $dias_atraso = $fecha_devolucion_efectiva->diff($fecha_debe_devolver)->days;
                } else {
                    $dias_atraso = 0;
                }
            } else {
                // Aún no devuelto: calcular diferencia entre fecha_debe_devolver y fecha actual
                $fecha_debe_devolver = new DateTime($row->fecha_debe_devolver);
                $fecha_actual_obj = new DateTime($fecha_actual);
                
                // Solo calcular atraso si la fecha que debía devolver ya pasó
                if ($fecha_actual_obj > $fecha_debe_devolver) {
                    $dias_atraso = $fecha_actual_obj->diff($fecha_debe_devolver)->days;
                } else {
                    $dias_atraso = 0; // No hay atraso aún
                }
            }

            // Solo exportar filas que tengan al menos 1 día de atraso
            if ($dias_atraso < 1) {
                continue;
            }

            // Formatear fechas
            $fecha_prestamo = fecha_mysql_a_normal($row->fecha_prestamo);
            $fecha_debe_devolver_formateada = fecha_mysql_a_normal($row->fecha_debe_devolver);
            
            // Mostrar fecha de devolución efectiva o "Pendiente"
            if (!empty($row->fecha_devolucion_efectiva)) {
                $fecha_devolucion_efectiva_formateada = fecha_mysql_a_normal($row->fecha_devolucion_efectiva);
            } else {
                $fecha_devolucion_efectiva_formateada = 'Pendiente';
            }
            
            // Determinar estado del préstamo
            $estado_mostrar = $row->estado;
            if ($row->estado == 'prestado') {
                $estado_mostrar = 'Prestado';
            } else if ($row->estado == 'devuelto') {
                $estado_mostrar = 'Devuelto';
            }
            
            echo "<tr>";
            echo "<td>$contador</td>";
            echo "<td>{$row->nombre}</td>";
            echo "<td>{$row->id_unico}</td>";
            echo "<td>{$row->prestado_a_nombre}</td>";
            echo "<td>$fecha_prestamo</td>";
            echo "<td>$fecha_debe_devolver_formateada</td>";
            echo "<td>$fecha_devolucion_efectiva_formateada</td>";
            echo "<td>$dias_atraso días</td>";
            echo "<td>$estado_mostrar</td>";
            echo "<td>{$row->nombre_responsable_prestamo}</td>";
            echo "</tr>";
            
            $contador++;
            
        } catch (Exception $e) {
            logError("Error procesando fila para exportación ID Equipo: {$row->id_equipo}", $e);
            continue;
        }
    }

    if ($contador === 1) {
        logEvent("No se encontraron equipos con atraso para exportar en fechas: $fecha_inicio a $fecha_fin");
        echo "<tr><td colspan='10' style='text-align: center;'>No se encontraron equipos con atraso</td></tr>";
    }

    echo "</table>";

    logEvent("Exportación Excel completada - Registros exportados: $totalExportados");

} catch (Exception $e) {
    logError("Error general en exportar_atraso_excel.php", $e);
    die("Error interno del sistema durante la exportación");
} finally {
    if (isset($mysql)) {
        $mysql->close();
    }
    logEvent("Proceso de exportación finalizado");
}
?>