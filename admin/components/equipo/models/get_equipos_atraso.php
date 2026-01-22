<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Configurar logging de errores
ini_set('log_errors', 1);
ini_set('error_log', 'equipos_atraso_errors.log');
ini_set('display_errors', 0);

$mysql = new mysql;
$mysql->connect();

// Función para loggear eventos
function logEvent($message, $type = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$type] $message" . PHP_EOL;
    error_log($logMessage, 3, 'equipos_atraso.log');
}

// Función para loggear errores
function logError($message, $exception = null) {
    $timestamp = date('Y-m-d H:i:s');
    $errorMessage = "[$timestamp] [ERROR] $message";
    
    if ($exception) {
        $errorMessage .= " - Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine();
    }
    
    $errorMessage .= PHP_EOL;
    error_log($errorMessage, 3, 'equipos_atraso_errors.log');
}

try {
    logEvent("Iniciando consulta de equipos con atraso - Usuario: " . ($_SESSION['usuario_id'] ?? 'Desconocido'));

    // Obtener fechas del filtro
    $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
    $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');

    logEvent("Fechas recibidas - Inicio: $fecha_inicio, Fin: $fecha_fin");

    // Validar fechas
    if (empty($fecha_inicio) || empty($fecha_fin)) {
        $errorMsg = "Fechas inválidas recibidas: Inicio='$fecha_inicio', Fin='$fecha_fin'";
        logError($errorMsg);
        echo '<tr><td colspan="9" class="text-center text-danger">Fechas inválidas</td></tr>';
        exit;
    }

    // Validar formato de fechas
    if (!strtotime($fecha_inicio) || !strtotime($fecha_fin)) {
        $errorMsg = "Formato de fechas inválido: Inicio='$fecha_inicio', Fin='$fecha_fin'";
        logError($errorMsg);
        echo '<tr><td colspan="9" class="text-center text-danger">Formato de fechas inválido</td></tr>';
        exit;
    }

    // Consulta para obtener equipos con atraso - CORREGIDA
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

    logEvent("Ejecutando consulta SQL: " . substr($sql, 0, 200) . "...");

    $result = $mysql->query($sql);
    
    if (!$result) {
        $errorMsg = "Error en la consulta: " . $mysql->error;
        logError($errorMsg);
        echo '<tr><td colspan="9" class="text-center text-danger">Error en la consulta de base de datos</td></tr>';
        exit;
    }

    $contador = 1;
    $hayResultados = false;
    $totalRegistros = 0;

    logEvent("Procesando resultados de la consulta");

    while ($row = $mysql->f_obj($result)) {
        $hayResultados = true;
        $totalRegistros++;
        
        try {
            // Calcular días de atraso - CORREGIDO
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
                $fecha_actual = new DateTime();
                
                // Solo calcular atraso si la fecha que debía devolver ya pasó
                if ($fecha_actual > $fecha_debe_devolver) {
                    $dias_atraso = $fecha_actual->diff($fecha_debe_devolver)->days;
                } else {
                    $dias_atraso = 0; // No hay atraso aún
                }
            }

            // Solo mostrar filas que tengan al menos 1 día de atraso
            if ($dias_atraso < 1) {
                // Saltar esta fila si no tiene atraso
                continue;
            }

            // Aplicar estilo según días de atraso
            $estilo_atraso = '';
            if ($dias_atraso >= 7) {
                $estilo_atraso = 'style="background-color: #ffcccc; font-weight: bold;"';
            } elseif ($dias_atraso >= 3) {
                $estilo_atraso = 'style="background-color: #fff3cd;"';
            }
            $estilo_atraso = '';
            
            // Formatear fechas
            $fecha_prestamo = fecha_mysql_a_normal($row->fecha_prestamo);
            $fecha_debe_devolver_formateada = fecha_mysql_a_normal($row->fecha_debe_devolver);
            
            // Mostrar fecha de devolución efectiva o "Pendiente"
            if (!empty($row->fecha_devolucion_efectiva)) {
                $fecha_devolucion_efectiva_formateada = fecha_mysql_a_normal($row->fecha_devolucion_efectiva);
            } else {
                $fecha_devolucion_efectiva_formateada = 'Pendiente';
            }
            
            // Determinar estado del préstamo para mostrar
            $estado_mostrar = $row->estado;
            if ($row->estado == 'prestado') {
                $estado_mostrar = '<span class="text-warning">Prestado</span>';
            } else if ($row->estado == 'devuelto') {
                $estado_mostrar = '<span class="text-success">Devuelto</span>';
            }
            
            echo "
            <tr $estilo_atraso>
                <td>$contador</td>
                <td>{$row->nombre}</td>
                <td>{$row->id_unico}</td>
                <td>{$row->prestado_a_nombre}</td>
                <td>$fecha_prestamo</td>
                <td>$fecha_debe_devolver_formateada</td>
                <td>$fecha_devolucion_efectiva_formateada</td>
                <td><strong>$dias_atraso días</strong></td>
                <td>$estado_mostrar</td>
            </tr>
            ";
            
            $contador++;
            
        } catch (Exception $e) {
            logError("Error procesando fila ID Equipo: {$row->id_equipo}", $e);
            // Continuar con la siguiente fila
            continue;
        }
    }

    logEvent("Consulta completada - Registros encontrados: $totalRegistros");

    if (!$hayResultados) {
        logEvent("No se encontraron equipos con atraso para las fechas: $fecha_inicio a $fecha_fin");
        echo '<tr><td colspan="9" class="text-center text-muted">No se encontraron equipos con atraso en el rango de fechas seleccionado</td></tr>';
    }

} catch (Exception $e) {
    logError("Error general en get_equipos_atraso.php", $e);
    echo '<tr><td colspan="9" class="text-center text-danger">Error interno del sistema</td></tr>';
} finally {
    if (isset($mysql)) {
        $mysql->close();
    }
    logEvent("Proceso finalizado");
}
?>