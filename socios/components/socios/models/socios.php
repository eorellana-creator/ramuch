<?php
@include("../../includes/sql_inyection.php");

error_reporting(E_ALL);
ini_set('display_errors', '1');

error_log("errores: ");

$mysql->connect();

$token = @$_SESSION["usuario_token"];

$sql7 = $mysql->query("SELECT * FROM usuario WHERE token ='$token' AND token!='' ;");
$result7 = $mysql->f_obj($sql7);
$id_usuario = @$result7->id_usuario;

$sql5 = $mysql->query("SELECT * FROM perfil WHERE id_usuario = '$id_usuario' AND id_usuario!='' ;");
$result5 = $mysql->f_obj($sql5);

$imagen_perfil = "../admin/images/icono-300.png";
if(@$result5->img_perfil!="")
$imagen_perfil = "../admin/images/img_perfil/$result5->img_perfil";

$id_plan_matricula = @$result5->id_plan_matricula;

$certificado = "";
if(@$result5->certificado_estudios!="")
$certificado = "<a href='../admin/components/socios/archivos/$result5->certificado_estudios' target='_blank'><i class='fas fa-search-plus'></i> ver certificado actual</a>";

$estado_estudios = "<option value='' selected >Seleccionar</option>";

switch(@$result5->estado_estudios){
    case 0:
        $estado_estudios = "<option value='0' selected >Cursando</option>";
        break;
    case 1:
        $estado_estudios = "<option value='1' selected >Congelado</option>";
        break;
    case 2:
        $estado_estudios = "<option value='2' selected >Egresado</option>";
        break;
    case 3:
        $estado_estudios = "<option value='3' selected >Titulado</option>";
        break;
    case 99:
        $estado_estudios = "<option value='99' selected >No informado</option>";
        break;
}

$anos_cursados = "<option value='0' selected >0</option>";
if(@$result5->anos_cursados >0 && @$result5->anos_cursados<15)
$anos_cursados = "<option value='$result5->anos_cursados' selected >$result5->anos_cursados</option>";

$anos_carrera = "<option value='0' selected >0</option>";
if(@$result5->anos_carrera >0 && @$result5->anos_carrera<15)
$anos_carrera = "<option value='$result5->anos_carrera' selected >$result5->anos_carrera</option>";

$tipo_sangre = "<option value=''>Seleccionar</option>";
if(@$result5->tipo_sangre!="")
$tipo_sangre = "<option value='$result5->tipo_sangre' selected >$result5->tipo_sangre</option>";

$donante = "<option value='0' selected >No</option><option value='1' >Si</option>";
if(@$result5->donante=="1")
$donante = "<option value='1' selected >Si</option> <option value='0' >No</option>";

$tipo_inscripcion = "";

switch(@$result5->tipo_inscripcion){
  case 1:
    $tipo_inscripcion = "Profesional";
    break;
  case 3:
    $tipo_inscripcion = "Estudiante";
    break;
  case 6:
    $tipo_inscripcion = "Congelado";
    break;
  case 2:
    $tipo_inscripcion = "Honorario";
    break;
  case 7:
    $tipo_inscripcion = "Desvinculado";
    break;
  case 8:
    $tipo_inscripcion = "Eliminado";
    break;
}

if($tipo_inscripcion!="")
$tipo_inscripcion = ": $tipo_inscripcion";

// PAGOS
$datos_pagos = "";
$total_pagos = 0;

$sql = $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda = '$id_usuario' AND estado = 'pagada' ORDER BY id_deuda DESC;");

while($result = $mysql->f_obj($sql)){
    $fecha = date("d-m-Y", strtotime($result->fecha));
    $total_pagos = $total_pagos + $result->monto;
    $valor = number_format($result->monto, 0, '', '.');
    $fecha_orden = strtotime($result->fecha);

    $fechaxx = '01-01-1900';
    if (!empty($result->documento_respaldo)) {
        $documento_flow = $result->documento_respaldo;
        $sqlxx = $mysql->query("SELECT * FROM flow WHERE flow_order = '$documento_flow' AND flow_status = 2;");
        $resultxx = $mysql->f_obj($sqlxx);
        if ($resultxx && property_exists($resultxx, 'fecha')) {
            $fechaxx = date("d-m-Y", strtotime($resultxx->fecha));
        }
    }

    $observacion = isset($result->observacion) ? $result->observacion : '';
    $documento_respaldo = isset($result->documento_respaldo) ? $result->documento_respaldo : '';
    $medio = $observacion . " : " . $documento_respaldo . " : " . $fechaxx;

    $sub_cuenta = isset($result->sub_cuenta) ? $result->sub_cuenta : '';
    $glosa = isset($result->glosa) ? $result->glosa : '';
    $descripcion_pago = $sub_cuenta . " : " . $glosa;

    $datos_pagos .= "<tr>
                        <td>$result->id_deuda</td>
                        <td data-sort='$fecha_orden'>$fecha</td>
                        <td>$medio</td>
                        <td>$descripcion_pago</td>
                        <td>$valor</td>
                    </tr>";
}

$total_pagos = number_format($total_pagos, 0, '', '.');

// DEUDAS
$datos_deudas = "";
$observacion = "";
$hoy = date("Y-m-d");

$sql = $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda = '$id_usuario' AND fecha<'$hoy' AND (estado = 'activa' OR estado='condonada' ) ORDER BY id_deuda DESC ;");
$total_deudas = 0;
$deudas_efectivas = 0;

while($result = $mysql->f_obj($sql)){
   $fecha = date("d-m-Y", strtotime($result->fecha));

    if($result->estado == "condonada"){
        $observacion = "Condonada - $result->observacion";
    }

    if($result->estado == "activa"){
        $deudas_efectivas = $deudas_efectivas + $result->monto;
        $total_deudas = $total_deudas + $result->monto;
    }

    $valor = $result->monto;
    $valor = number_format($valor, 0, '', '.');
    $tipo_pago = @$result->sub_cuenta;
    $fecha_orden = strtotime($result->fecha);
    $datos_deudas = $datos_deudas . " <tr>
                                         <td>$result->id_deuda</td>
                                        <td data-sort='$fecha_orden' >$fecha</td>
                                        <td>$result->glosa</td>
                                        <td>$result->observacion</td>
                                        <td>$valor</td>
                                    </tr>";
}

$total_deudas = number_format($total_deudas, 0, '', '.');

$texto_deudas = "Sin deuda";

if($deudas_efectivas > 0)
$texto_deudas = "Con deuda";

$deudas_efectivas = number_format($deudas_efectivas, 0, '', '.');

// PLANES DE MATRÍCULA
$sql21 = $mysql->query("SELECT * FROM plan_matricula WHERE activa = '1' ORDER BY nombre ASC ;");
$select_inscripcion = "";
$seleccionar_inscripcion = "<option value='' selected >Seleccionar</option>";

while($result21 = $mysql->f_obj($sql21)){
    $selected = "";

    if($result21->id_plan_matricula == $id_plan_matricula){
        $seleccionar_inscripcion = "";
        $selected = " selected ";
    }

    $select_inscripcion = $select_inscripcion ."<option value='$result21->id_plan_matricula' $selected >$result21->nombre</option>";
}

$select_inscripcion = "<select id='tipoInscripcion' name='tipoInscripcion' class='form-control' >
                        $seleccionar_inscripcion
                        $select_inscripcion
                        </select>";

if($token==""){
    $select_inscripcion_nuevo = $select_inscripcion;
    $oculta_inscripcion_primera_vez = "
    <div style='width:100%;height:1px; margin-top:20px; '></div>
                    <h4>Tipo de Inscripción</h4>
                    <div class='linea-horizontal'></div>
                    <div class='col-lg-4 form-group'> 
                        <label class='col-form-label'>Tipo de Inscripción:</label>
                       $select_inscripcion_nuevo
                    </div>
    ";
    $select_inscripcion = "";
}else{
    $oculta_inscripcion_primera_vez = " ";
    $select_inscripcion_nuevo = "";
}

// FUNCIÓN PARA FORMATEAR ESTADOS DE EXTENSIÓN (definida una sola vez)
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

// PRÉSTAMOS DE EQUIPO
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
    // $debug_info = "<div style='font-size:10px; background:#f0f0f0; padding:5px; margin:2px; border:1px solid #ccc;'>";
    // $debug_info .= "<strong>DEBUG:</strong> ";
    // $debug_info .= "Estado: " . $result2->estado . " | ";
    // $debug_info .= "Botón activo: " . $activa_el_boton_2dias_antes . " | ";
    // $debug_info .= "Hoy: " . $hoy . " | ";
    // $debug_info .= "Compromiso: " . $fecha_compromiso_sql . " | ";
    // $debug_info .= "2 días antes: " . $fecha_2dias_antes . " | ";
    // $debug_info .= "Hora: " . $hora_actual . " | ";
    // $debug_info .= "Ext. solicitadas: " . $result2->extensiones_solicitadas . " | ";
    // $debug_info .= "Estado ext1: " . ($result2->estado_extension ?? 'NULL') . " | ";
    // $debug_info .= "Estado ext2: " . ($result2->estado_extension2 ?? 'NULL') . " | ";
    // $debug_info .= "Fecha propuesta ext1: " . ($result2->fecha_propuesta_extension ?? 'NULL');
    // $debug_info .= "</div>";

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
                //$boton_extension = "<small class='text-success d-block mb-1'><i class='fas fa-check-circle'></i> Primera Extensión <br> aprobada hasta: <br> $fecha_aprobada</small>";
                
                // SEGUNDA EXTENSIÓN - Solo si la primera está aprobada
                if ($extensiones_restantes > 0) {
                    $fecha_primera_extension = !empty($result2->fecha_propuesta_extension) ? $result2->fecha_propuesta_extension : '';
                    if ($fecha_primera_extension) {
                        $fecha_2dias_antes_extension2 = date("Y-m-d", strtotime($fecha_primera_extension . " -2 days"));
                        
                        // DEPURACIÓN SEGUNDA EXTENSIÓN

                        // $debug_info .= "<div style='font-size:10px; background:#fffacd; padding:5px; margin:2px; border:1px solid #ffd700;'>";
                        // $debug_info .= "<strong>DEBUG EXT2:</strong> ";
                        // $debug_info .= "Fecha 1ra ext: " . $fecha_primera_extension . " | ";
                        // $debug_info .= "2 días antes ext2: " . $fecha_2dias_antes_extension2 . " | ";
                        // $debug_info .= "Hoy en rango ext2: " . (($hoy >= $fecha_2dias_antes_extension2 && $hoy <= $fecha_primera_extension) ? 'SÍ' : 'NO') . " | ";
                        // $debug_info .= "Ext. restantes: " . $extensiones_restantes;
                        // $debug_info .= "</div>";
                        
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


$actualizado = @$_SESSION["socio_actualizado"];

$_SESSION["socio_actualizado"] = "";

$go_tab_pass = "";
if(@$_GET["tab"] == "pass")
$go_tab_pass = "
<script>
goTabPass();
</script>
";

$oculta_tabs = "";
$campos_password = "";
$campos_password_nuevos = "";

if($token==""){
    $oculta_tabs = " style='display:none;' ";
    $campos_password_nuevos = '
    <div style="width:100%;height:1px; margin-top:20px;"></div>
                    <h4>Contraseña de acceso</h4>
                    <div class="linea-horizontal"></div>

    <div class="col-sm-10">
    <div class="row">
        <div class="col-lg-4 form-group"> 
            <label class="col-form-label">Ingresar Contraseña</label>
            <input class="form-control pr-password" id="password" name="password" type="password" placeholder="Ingresar Password...">
        </div>

        <div class="col-lg-4 form-group"> 
            <label class="col-form-label">Re-Ingresar Contraseña</label>
            <input class="form-control pr-password" id="password2" name="password2" type="password"placeholder="Re-Ingresar Password...">
        </div>

        <div class="col-lg-12 form-group"> 
        <div class="error-pass"></div>
        La contraseña debe contener al menos 1 número, 1 letra mayúscula, una letra minúscula y un mínimo de 8 caracteres.
        </div>
    </div>
    </div>
    ';
}else{
    $campos_password = '
    <div class="col-sm-10">
    <div class="row">
        <div class="col-lg-4 form-group"> 
            <label class="col-form-label">Ingresar Contraseña</label>
            <input class="form-control pr-password" id="password" name="password" type="password" placeholder="Ingresar Password...">
        </div>

        <div class="col-lg-4 form-group"> 
            <label class="col-form-label">Re-Ingresar Contraseña</label>
            <input class="form-control pr-password" id="password2" name="password2" type="password"placeholder="Re-Ingresar Password...">
        </div>

        <div class="col-lg-12 form-group"> 
        <div class="error-pass"></div>
        La contraseña debe contener al menos 1 número, 1 letra mayúscula, una letra minúscula y un mínimo de 8 caracteres.
        </div>
    </div>
    </div>
    ';
}
?>