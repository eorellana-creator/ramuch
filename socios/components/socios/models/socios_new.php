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

// PRÉSTAMOS DE EQUIPO - VERSIÓN MEJORADA
$datos_prestamo = "";
$contador_prestamos = 0;

$sql2 = $mysql->query("SELECT * FROM equipo_prestamo WHERE id_usuario_prestamo='$id_usuario' ORDER BY fecha_prestamo DESC ;");
while($result2 = $mysql->f_obj($sql2)){
    $contador_prestamos++;
    $sql12 = $mysql->query("SELECT * FROM equipo WHERE id_equipo ='$result2->id_equipo' ;");
    $result12 = $mysql->f_obj($sql12);
    $id_equipo = @$result12->id_equipo;
    $nombre_equipo = @$result12->nombre;
    
    // Imagen solo para desktop
    $imagen_celda = "<span class='d-none d-md-inline'>";
    if(@$result12->imagen!=""){
        $imagen_celda .= "<img src='https://ramuch.cl/admin/images/equipo/$result12->imagen' alt='' width='60'>";
    }else{
        $imagen_celda .= "<img src='https://ramuch.cl/admin/images/equipo/equipo_sin_imagen.jpg' alt='' width='60'>";
    }
    $imagen_celda .= "</span>";

    $fecha_prestamo = "";
    $fecha_compromiso = "";
    $fecha_devolucion = "";

    $fecha_2 = !empty($result2->fecha_devolucion_efectiva) 
        ? strtotime($result2->fecha_devolucion_efectiva) 
        : strtotime('1900-01-01');
    
    $fecha_1 = strtotime($result2->fecha_debe_devolver);

    $color_fecha = "";
    if($fecha_2 > $fecha_1) {
        $color_fecha = " style='color:#ff0000;' ";
    }
    
    if($result2->fecha_prestamo>"0000-00-00") {
        $fecha_prestamo = fecha_mysql_a_normal($result2->fecha_prestamo);
    }

    if($result2->fecha_debe_devolver>"0000-00-00") {
        $fecha_compromiso = fecha_mysql_a_normal($result2->fecha_debe_devolver);
    }

    if($result2->fecha_devolucion_efectiva>"0000-00-00") {
        $fecha_devolucion = fecha_mysql_a_normal($result2->fecha_devolucion_efectiva);
    }
    
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

    $estado_solicitud = $result2->estado;
    if($result2->estado == "solicitado") {
        $estado_solicitud = "<span style='color:#ff0000;'>Solicitud en trámite</span><br>
                            <button type='button' class='btn btn-danger btn-sm mt-1' onClick='cancelarSolicitud(\"$result2->token\");'>
                                <i class='fas fa-times'></i> Cancelar
                            </button>";
    }

    // Variables para botones de extensión
    $boton_extension = '';
    $boton_extension2 = '';
    $estados_extensiones_html = '';
    $debug_info = '';

    // Fecha y hora actual
    $hoy = date("Y-m-d");
    $hora_actual = date("H");
    $fecha_compromiso_sql = date("Y-m-d", strtotime($fecha_compromiso));
    $fecha_2dias_antes = date("Y-m-d", strtotime($fecha_compromiso . " -2 days"));
    
    $activa_el_boton_2dias_antes = 0;
    if ($hoy >= $fecha_2dias_antes && $hoy <= $fecha_compromiso_sql) {
        if (!($hora_actual >= 01)) {
            $activa_el_boton_2dias_antes = 1;
        }
    }

    // Construir estados de extensiones
    if ($result2->estado == "prestado") {
        $total_extensiones_solicitadas = (int)$result2->extensiones_solicitadas;
        $extensiones_restantes = 2 - $total_extensiones_solicitadas;
        
        // Primera extensión
        $estado_ext1 = $result2->estado_extension ?: 'no solicitada';
        $fecha_ext1 = $result2->fecha_propuesta_extension ?? null;
        
        // Segunda extensión
        $estado_ext2 = $result2->estado_extension2 ?: 'no solicitada';
        $fecha_ext2 = $result2->fecha_propuesta_extension2 ?? null;
        
        // Solo mostrar si hay información de extensiones
        if ($estado_ext1 != 'no solicitada' || $estado_ext2 != 'no solicitada') {
            $estados_extensiones_html .= "<div class='extension-status' style='font-size:12px; margin-top:5px;'>";
            $estados_extensiones_html .= "<strong>Extensiones:</strong><br>";
            
            if ($estado_ext1 != 'no solicitada') {
                $icono = $estado_ext1 == 'pendiente' ? 'fa-clock text-warning' : 
                        ($estado_ext1 == 'aprobada' ? 'fa-check-circle text-success' : 
                        ($estado_ext1 == 'rechazada' ? 'fa-times-circle text-danger' : 'fa-minus-circle text-muted'));
                $texto = $estado_ext1 == 'pendiente' ? 'Pendiente' : 
                        ($estado_ext1 == 'aprobada' ? 'Aprobada' : 
                        ($estado_ext1 == 'rechazada' ? 'Rechazada' : 'No solicitada'));
                $fecha_texto = $fecha_ext1 ? fecha_mysql_a_normal($fecha_ext1) : '';
                $fecha_display = $fecha_texto ? " hasta: $fecha_texto" : '';
                $estados_extensiones_html .= "<i class='fas $icono'></i> 1ra: $texto$fecha_display<br>";
            }
            
            if ($estado_ext2 != 'no solicitada') {
                $icono = $estado_ext2 == 'pendiente' ? 'fa-clock text-warning' : 
                        ($estado_ext2 == 'aprobada' ? 'fa-check-circle text-success' : 
                        ($estado_ext2 == 'rechazada' ? 'fa-times-circle text-danger' : 'fa-minus-circle text-muted'));
                $texto = $estado_ext2 == 'pendiente' ? 'Pendiente' : 
                        ($estado_ext2 == 'aprobada' ? 'Aprobada' : 
                        ($estado_ext2 == 'rechazada' ? 'Rechazada' : 'No solicitada'));
                $fecha_texto = $fecha_ext2 ? fecha_mysql_a_normal($fecha_ext2) : '';
                $fecha_display = $fecha_texto ? " hasta: $fecha_texto" : '';
                $estados_extensiones_html .= "<i class='fas $icono'></i> 2da: $texto$fecha_display<br>";
            }
            
            $estados_extensiones_html .= "</div>";
        }
        
        // LÓGICA DE BOTONES DE EXTENSIÓN
        switch($result2->estado_extension) {
            case 'pendiente':
                $fecha_propuesta = !empty($result2->fecha_propuesta_extension) ? 
                    fecha_mysql_a_normal($result2->fecha_propuesta_extension) : 'pendiente';
                $boton_extension = "<button type='button' class='btn btn-info btn-sm mb-1' disabled>
                                    <i class='fas fa-clock'></i> Extensión pendiente
                                    </button>";
                break;
                
            case 'aprobada':
                // Segunda extensión
                if ($extensiones_restantes > 0) {
                    $fecha_primera_extension = !empty($result2->fecha_propuesta_extension) ? $result2->fecha_propuesta_extension : '';
                    if ($fecha_primera_extension) {
                        $fecha_2dias_antes_extension2 = date("Y-m-d", strtotime($fecha_primera_extension . " -2 days"));
                        
                        switch($result2->estado_extension2) {
                            case 'pendiente':
                                $boton_extension2 = "<button type='button' class='btn btn-info btn-sm mb-1' disabled>
                                                    <i class='fas fa-clock'></i> 2da extensión pendiente
                                                    </button>";
                                break;
                                
                            case 'aprobada':
                                $boton_extension2 = "<button type='button' class='btn btn-success btn-sm mb-1' disabled>
                                                    <i class='fas fa-check-circle'></i> 2da extensión aprobada
                                                    </button>";
                                break;
                                
                            case 'rechazada':
                                if ($extensiones_restantes > 0 && $hoy >= $fecha_2dias_antes_extension2 && $hoy <= $fecha_primera_extension) {
                                    $boton_extension2 = "<button type='button' class='btn btn-warning btn-sm mb-1' onClick='sol_ext2(\"$result2->token\");'>
                                                        <i class='fas fa-plus'></i> Solicitar 2da Extensión
                                                        </button>";
                                }
                                break;
                                
                            default:
                                if ($extensiones_restantes > 0 && $hoy >= $fecha_2dias_antes_extension2 && $hoy <= $fecha_primera_extension) {
                                    $boton_extension2 = "<button type='button' class='btn btn-warning btn-sm mb-1' onClick='sol_ext2(\"$result2->token\");'>
                                                        <i class='fas fa-plus'></i> Solicitar 2da Extensión
                                                        </button>";
                                }
                        }
                    }
                }
                break;
                
            case 'rechazada':
                if ($extensiones_restantes > 0 && $hoy >= $fecha_2dias_antes && $hoy <= $fecha_compromiso_sql) {
                    $boton_extension = "<button type='button' class='btn btn-primary btn-sm mb-1' onClick='sol_ext1(\"$result2->token\");'>
                                        <i class='fas fa-plus'></i> Solicitar Extensión
                                        </button>";
                }
                break;
                
            default:
                if ($extensiones_restantes > 0 && $hoy >= $fecha_2dias_antes && $hoy <= $fecha_compromiso_sql) {
                    $boton_extension = "<button type='button' class='btn btn-primary btn-sm mb-1' onClick='sol_ext1(\"$result2->token\");'>
                                        <i class='fas fa-plus'></i> Solicitar Extensión
                                        </button>";
                }
        }
        
        // Límite alcanzado
        if ($extensiones_restantes <= 0) {
            $boton_extension = "<button type='button' class='btn btn-secondary btn-sm mb-1' disabled>
                                <i class='fas fa-ban'></i> Límite alcanzado
                                </button>";
        }
    }

    // Responsables compactos - VERSIÓN CORREGIDA
    $responsables_html = "<small>";
    if ($nombre_responsable) {
        $responsables_html .= "<strong>Entrega:</strong> " . substr($nombre_responsable ?: '', 0, 20) . "...<br>";
    }
    if ($nombre_recepciono) {
        $responsables_html .= "<strong>Recibió:</strong> " . substr($nombre_recepciono ?: '', 0, 20) . "...";
    }
    $responsables_html .= "</small>";

    // Columna de acciones unificada
    $acciones_html = "<div class='btn-group-vertical btn-group-sm w-100'>";
    
    // Botón de devolver si está prestado
    if ($result2->estado == "prestado") {
        $acciones_html .= "<button type='button' class='btn btn-success mb-1' onClick='devolverEquipo(\"$result2->token\");'>
                          <i class='fas fa-undo'></i> Devolver
                          </button>";
    }
    
    // Botones de extensión
    if ($boton_extension) {
        $acciones_html .= $boton_extension;
    }
    if ($boton_extension2) {
        $acciones_html .= $boton_extension2;
    }
    
    // Botón de detalle
    $acciones_html .= "<button type='button' class='btn btn-info mb-1' onClick='verDetallePrestamo(\"$result2->token\");'>
                      <i class='fas fa-eye'></i> Detalle
                      </button>";
    
    $acciones_html .= "</div>";
    
    // Agregar estados de extensiones debajo de los botones
    if ($estados_extensiones_html) {
        $acciones_html .= $estados_extensiones_html;
    }

    // Generar fila de la tabla
    $datos_prestamo .= "<tr>
        <td class='text-center'>$contador_prestamos</td>
        <td>
            $imagen_celda
            <strong>$nombre_equipo</strong>
        </td>
        <td><small>$fecha_prestamo</small></td>
        <td><small>$fecha_compromiso</small></td>
        <td $color_fecha><small>$fecha_devolucion</small></td>
        <td class='d-none d-md-table-cell'><small>$responsables_html</small></td>
        <td class='d-none d-lg-table-cell'><small>" . substr($result2->comentario ?: '', 0, 50) . "..." . "</small></td>
        <td><small>$result2->estado_devolucion</small></td>
        <td><small>$estado_solicitud</small></td>
        <td class='text-center'>$acciones_html</td>
    </tr>";

}//while

// Si no hay préstamos
if ($contador_prestamos == 0) {
    $datos_prestamo = "<tr><td colspan='10' class='text-center'>No hay préstamos registrados</td></tr>";
}


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