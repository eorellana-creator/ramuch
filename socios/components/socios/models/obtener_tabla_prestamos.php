<?php
session_start();
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Obtener ID de usuario desde POST o SESSION
$id_usuario = $_POST['id_usuario'] ?? $_SESSION['usuario_id'] ?? 0;

// Si no tenemos ID de usuario, intentar obtenerlo del token
if ($id_usuario == 0 && isset($_SESSION['usuario_token'])) {
    $token = $_SESSION['usuario_token'];
    $sql7 = $mysql->query("SELECT * FROM usuario WHERE token ='$token' AND token!='' ;");
    $result7 = $mysql->f_obj($sql7);
    $id_usuario = @$result7->id_usuario;
}

// Conectar a la base de datos
//$mysql->connect();

$mysql = new mysql;
$mysql->connect();

// FUNCIÓN PARA FORMATEAR ESTADOS DE EXTENSIÓN
if (!function_exists('formatoEstadoExtension')) {
    function formatoEstadoExtension($tipo, $estado, $fecha_propuesta = null) {
        $iconos = [
            'pendiente' => 'fa-clock text-warning',
            'aprobada' => 'fa-check-circle text-success',
            'rechazada' => 'fa-times-circle text-danger'
        ];
        
        $textos = [
            'pendiente' => 'Pendiente',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada'
        ];
        
        if (empty($estado) || $estado == 'no solicitada') {
            return "<small class='text-muted d-block'><i class='fas fa-minus-circle'></i> $tipo: No solicitada</small>";
        }
        
        $icono = $iconos[$estado] ?? 'fa-question-circle text-secondary';
        $texto = $textos[$estado] ?? $estado;
        $fecha_texto = $fecha_propuesta ? fecha_mysql_a_normal($fecha_propuesta) : '';
        $fecha_display = $fecha_texto ? " hasta: $fecha_texto" : '';
        
        return "<small class='d-block'><i class='fas $icono'></i> $tipo: $texto$fecha_display</small>";
    }
}

// GENERAR TABLA DE PRÉSTAMOS
$datos_prestamo = "";
$imagen_equipo = "";

$sql2 = $mysql->query("SELECT * FROM equipo_prestamo WHERE id_usuario_prestamo='$id_usuario' ORDER BY fecha_prestamo DESC ;");
while($result2 = $mysql->f_obj($sql2)){
    $sql12 = $mysql->query("SELECT * FROM equipo WHERE id_equipo ='$result2->id_equipo' ;");
    $result12 = $mysql->f_obj($sql12);
    $id_equipo = @$result12->id_equipo;
    $nombre_equipo = @$result12->nombre;
    
    if(@$result12->imagen!=""){
        $imagen_equipo = " <img src='https://ramuch.cl/admin/images/equipo/$result12->imagen' alt='' width='90'> ";
    }else{
        $imagen_equipo = " <img src='https://ramuch.cl/admin/images/equipo/equipo_sin_imagen.jpg' alt='' width='90'> ";
    }

    $fecha_prestamo = "";
    $fecha_compromiso = "";
    $fecha_devolucion = "";

    $fecha_2 = !empty($result2->fecha_devolucion_efectiva) 
    ? strtotime($result2->fecha_devolucion_efectiva) 
    : strtotime('1900-01-01');
    
    $fecha_1 = strtotime($result2->fecha_debe_devolver);

    $color_fecha = "";
    if($fecha_2 > $fecha_1)
    $color_fecha = " style='color:#ff0000;' ";
    
    if($result2->fecha_prestamo>"0000-00-00")
    $fecha_prestamo = fecha_mysql_a_normal($result2->fecha_prestamo);

    if($result2->fecha_debe_devolver>"0000-00-00")
    $fecha_compromiso = fecha_mysql_a_normal($result2->fecha_debe_devolver);

    if($result2->fecha_devolucion_efectiva>"0000-00-00")
    $fecha_devolucion = fecha_mysql_a_normal($result2->fecha_devolucion_efectiva);
    
    $nombre_prestamo = '';
    $nombre_responsable = '';
    if (!empty($result2->id_usuario_responsable)) {
        $sql6 = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_responsable'");
        $result6 = $mysql->f_obj($sql6);
        $nombre_responsable = ($result6 && isset($result6->nombre_usuario)) ? $result6->nombre_usuario : '';
    }

    $nombre_recepciono = '';
    if (!empty($result2->id_usuario_recepciono)) {
        $sql7 = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_recepciono'");
        $result7 = $mysql->f_obj($sql7);
        $nombre_recepciono = ($result7 && isset($result7->nombre_usuario)) ? $result7->nombre_usuario : '';
    }

    if($result2->estado == "solicitado")
    $result2->estado = "<span style='color:#ff0000;'>Solicitud en trámite</span><br><button type='button' class='btn btn-danger' onClick='cancelarSolicitud(\"$result2->token\");'>Cancelar Solicitud</button>";
    
    $boton_extension = '';
    $boton_extension2 = '';

    // Fecha y hora actual
    $hoy = date("Y-m-d");
    $hora_actual = date("H");
    $dia_semana = date("N"); // 1 (lunes) a 7 (domingo)

    // Convertir la fecha_compromiso de formato d-m-Y a Y-m-d para comparación
    $fecha_compromiso_sql = date("Y-m-d", strtotime($fecha_compromiso));
    
    // Calculamos 2 días antes de la fecha de compromiso (en formato Y-m-d)
    $fecha_2dias_antes = date("Y-m-d", strtotime($fecha_compromiso . " -2 days"));
    
    // Inicializamos la variable en 0 (desactivado por defecto)
    $activa_el_boton_2dias_antes = 0;

    // Condición 1: ¿Estamos dentro del rango de 2 días antes hasta el viernes?
    if ($hoy >= $fecha_2dias_antes && $hoy <= $fecha_compromiso_sql ) {
        // Condición 2: debe ser antes de las 20:00 hrs
        if (!($hora_actual >= 01)) {
            $activa_el_boton_2dias_antes = 1;
        }
    }

    // ========== DEPURACIÓN: MOSTRAR VALORES ==========
    $debug_info = "";

    // ========== MOSTRAR ESTADOS DE EXTENSIONES ==========
    $estados_extensiones_html = "";
    
    if ($result2->estado == "prestado") {
        $total_extensiones_solicitadas = (int)$result2->extensiones_solicitadas;
        $extensiones_restantes = 2 - $total_extensiones_solicitadas;
        
        // Construir HTML de estados de extensiones
        $estados_extensiones_html .= "<div style='font-size:13px; border-top:1px solid #ddd; padding-top:5px; margin-top:5px;'>";
        $estados_extensiones_html .= "<strong>Estados de Extensiones:</strong><br>";
        
        // Primera extensión
        $estado_ext1 = $result2->estado_extension ?: 'no solicitada';
        $fecha_ext1 = $result2->fecha_propuesta_extension ?? null;
        $estados_extensiones_html .= formatoEstadoExtension("1ra Extensión", $estado_ext1, $fecha_ext1);
        
        // Segunda extensión
        $estado_ext2 = $result2->estado_extension2 ?: 'no solicitada';
        $fecha_ext2 = $result2->fecha_propuesta_extension2 ?? null;
        $estados_extensiones_html .= formatoEstadoExtension("2da Extensión", $estado_ext2, $fecha_ext2);
        
        // Extensiones restantes
        $estados_extensiones_html .= "<small class='text-info d-block'><i class='fas fa-info-circle'></i> Extensiones disponibles: $extensiones_restantes/2</small>";
        $estados_extensiones_html .= "</div>";
        
        // ========== LÓGICA DE BOTONES ==========
        // PRIMERA EXTENSIÓN - Mostrar estados siempre
        switch($result2->estado_extension) {
            case 'pendiente':
                $fecha_propuesta = !empty($result2->fecha_propuesta_extension) ? 
                                fecha_mysql_a_normal($result2->fecha_propuesta_extension) : 'pendiente';
                $boton_extension = "<small class='text-warning d-block mb-1'><i class='fas fa-clock'></i> Primera Solicitud extensión <br> pendiente: <br> $fecha_propuesta</small>";
                break;
                
            case 'aprobada':
                $fecha_aprobada = !empty($result2->fecha_propuesta_extension) ? 
                                fecha_mysql_a_normal($result2->fecha_propuesta_extension) : 'fecha no definida';
                
                // SEGUNDA EXTENSIÓN - Solo si la primera está aprobada
                if ($extensiones_restantes > 0) {
                    $fecha_primera_extension = !empty($result2->fecha_propuesta_extension) ? $result2->fecha_propuesta_extension : '';
                    if ($fecha_primera_extension) {
                        $fecha_2dias_antes_extension2 = date("Y-m-d", strtotime($fecha_primera_extension . " -2 days"));
                        
                        // Mostrar estados de segunda extensión siempre
                        switch($result2->estado_extension2) {
                            case 'pendiente':
                                $fecha_propuesta2 = !empty($result2->fecha_propuesta_extension2) ? 
                                                fecha_mysql_a_normal($result2->fecha_propuesta_extension2) : 'pendiente';
                                $boton_extension2 = "<small class='text-warning d-block mb-1'><i class='fas fa-clock'></i> Solicitud 2da extensión <br> pendiente: <br> $fecha_propuesta2</small>";
                                break;
                                
                            case 'aprobada':
                                $fecha_aprobada2 = !empty($result2->fecha_propuesta_extension2) ? 
                                                fecha_mysql_a_normal($result2->fecha_propuesta_extension2) : 'fecha no definida';
                                $boton_extension2 = "<small class='text-success d-block mb-1'><i class='fas fa-check-circle'></i> 2da Extensión <br> aprobada hasta: <br> $fecha_aprobada2</small>";
                                break;
                                
                            case 'rechazada':
                                $boton_extension2 = "<small class='text-danger d-block mb-1'><i class='fas fa-times-circle'></i> Solicitud 2da extensión <br> rechazada</small>";
                                
                                // Mostrar botón para nueva solicitud solo si estamos en el rango de fechas
                                if ($extensiones_restantes > 0 && $hoy >= $fecha_2dias_antes_extension2 && $hoy <= $fecha_primera_extension) {
                                    $boton_extension2 .= "<button type='button' class='btn btn-warning btn-sm mt-1' onClick='sol_ext2(\"$result2->token\");'>
                                                        Solicitar 2da Extensión <br> ($extensiones_restantes/2)</button>";
                                }
                                break;
                                
                            default:
                                // Cuando no hay solicitud previa de segunda extensión
                                // Mostrar botón solo si estamos en el rango de fechas
                                if ($extensiones_restantes > 0 && $hoy >= $fecha_2dias_antes_extension2 && $hoy <= $fecha_primera_extension) {
                                    $boton_extension2 = "<button type='button' class='btn btn-warning btn-sm mt-1' onClick='sol_ext2(\"$result2->token\");'>
                                                    Solicitar 2da Extensión <br> ($extensiones_restantes/2)</button>";
                                }
                        }
                    }
                }
                break;
                
            case 'rechazada':
                $boton_extension = "<small class='text-danger d-block mb-1'><i class='fas fa-times-circle'></i> Solicitud extensión <br> rechazada</small>";
                
                // Mostrar botón para nueva solicitud solo si estamos en el rango de fechas
                if ($extensiones_restantes > 0 && $hoy >= $fecha_2dias_antes && $hoy <= $fecha_compromiso_sql) {
                    $boton_extension .= "<button type='button' class='btn btn-primary btn-sm mt-1' onClick='sol_ext1(\"$result2->token\");'>
                                        Solicitar Extensión <br> ($extensiones_restantes/2)</button>";
                }
                break;
                
            default:
                // Cuando no hay solicitud previa de primera extensión
                // Mostrar botón solo si estamos en el rango de fechas
                if ($extensiones_restantes > 0 && $hoy >= $fecha_2dias_antes && $hoy <= $fecha_compromiso_sql) {
                    $boton_extension = "<button type='button' class='btn btn-success' onClick='sol_ext1(\"$result2->token\");'>
                                    Solicitar Extensión <br> ($extensiones_restantes/2)</button>";
                }
        }
        
        // Mostrar mensaje de límite alcanzado
        if ($extensiones_restantes <= 0) {
            //$boton_extension = "<small class='text-primary d-block'><i class='fas fa-ban'></i> Límite de <br> extensiones <br> alcanzado</small>";
        }
    }

    $datos_prestamo = $datos_prestamo . "<tr>
                                        <td>$imagen_equipo</td>
                                        <td>$nombre_equipo</td>
                                        <td>$fecha_prestamo</td>
                                        <td>$fecha_compromiso</td>
                                        <td $color_fecha>$fecha_devolucion</td>
                                        <td>Responsable:<br>$nombre_responsable<br><br>Recepcionó:<br>$nombre_recepciono</td>
                                        <td>$result2->comentario</td>
                                        <td>$result2->estado_devolucion</td>
                                        <td>$result2->estado<br>$estados_extensiones_html<br>$debug_info<br>$boton_extension<br>$boton_extension2</td>
                                        </tr>";

}//while

// Enviar solo el HTML de la tabla
echo $datos_prestamo;
exit;
?>