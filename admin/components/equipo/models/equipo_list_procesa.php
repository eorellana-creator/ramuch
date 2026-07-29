<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@$draw = $_POST["draw"];
@$inicio = $_POST["start"];
@$fin = $_POST["length"];
@$busqueda = $_POST["search"]["value"];
@$orden = $_POST["order"][0]["column"];
@$direccion = $_POST["order"][0]["dir"];

$id_usuario_sesion = @$_SESSION["usuario_id"];

$filtro_prestados = " prestado_a_id_usuario > 0 ";
$where = " WHERE $filtro_prestados ";

if($busqueda != "") {
    $busqueda = str_replace("'", "''", $busqueda);
    $where .= " AND ( id_equipo LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR id_unico LIKE '%$busqueda%' OR prestado_a_nombre LIKE '%$busqueda%' ) ";
}

if($inicio == "") $inicio = 0;

$config = new Config;
$mysql = new mysql;
$mysql->connect();

$usuarios = "";
$datos = "";
$imagen = "";

// Obtener usuarios para el select de préstamos
$option_usuarios = "";
//$option_usuarios = "<option value='' selected>Prestar a...</option>";
$sql21 = $mysql->query("SELECT id_usuario, nombre_usuario FROM usuario WHERE estado ='Vigente' AND (web_matricula_pagada IS NULL OR web_matricula_pagada!='No') ORDER BY nombre_usuario ASC;");

$fechaActual = date('Y-m-d');
$fecha3mesesAtras = strtotime('-3 month', strtotime($fechaActual));
$fecha3mesesAtras = date('Y-m-d', $fecha3mesesAtras);

while($resultU = $mysql->f_obj($sql21)) {
    $sql49 = $mysql->query("SELECT id_deuda FROM deudas WHERE id_usuario_deuda='$resultU->id_usuario' AND estado='activa' AND fecha<'$fecha3mesesAtras'");
    $cantidad_deuda_atrasada_usuario = $mysql->f_num($sql49);

    if($cantidad_deuda_atrasada_usuario == 0) {
        $resultU->nombre_usuario = str_replace("|", "", $resultU->nombre_usuario);
        //$option_usuarios .= "<option value='$resultU->id_usuario'>$resultU->nombre_usuario</option>";
    }
}

$direccion = strtolower((string)$direccion) === 'desc' ? 'DESC' : 'ASC';
$orderby = " ORDER BY prestado_a_nombre ASC, nombre ASC";

if($orden == 0) $orderby = " ORDER BY id_equipo $direccion ";
if($orden == 2) $orderby = " ORDER BY nombre $direccion ";
if($orden == 3) $orderby = " ORDER BY id_unico $direccion ";
if($orden == 4) $orderby = " ORDER BY estado $direccion ";
if($orden == 5) $orderby = " ORDER BY prestado_a_nombre $direccion, nombre ASC ";
if($orden == 6) $orderby = " ORDER BY nombre_responsable_prestamo $direccion ";

$sql = $mysql->query("SELECT * FROM equipo $where $orderby LIMIT $inicio,$fin;");
$sql2 = $mysql->query("SELECT id_equipo FROM equipo $where;");
$cantidad_filtrados = $mysql->f_num($sql2);
$sql3 = $mysql->query("SELECT id_equipo FROM equipo WHERE $filtro_prestados;");
$cantidad_registros = $mysql->f_num($sql3);

$coma = 0;
$signo_coma = "";

while($result = $mysql->f_obj($sql)) {
    if($coma == 1) $signo_coma = ",";
    $coma = 1;

    $img_url = "<img src='images/equipo_sin_imagen.jpg' width='90' height='120'>";
    if($result->imagen != "") {
        $img_url = "<button type='button' class='btn btn-link p-0' data-toggle='modal' data-target='#imageModal' data-img='images/equipo/$result->imagen'><img src='images/equipo/$result->imagen' width='90' height='120'></button>";
    }

    $detalle = "<button type='button' class='btn btn-link' data-toggle='modal' data-target='#detalleModal' data-token='$result->token'><i class='fas fa-search-plus'></i> ver equipo</button>";
    $edita = "<a href='index.php?component=equipo&view=equipo&token=$result->token'><i class='fas fa-search-plus'></i> Editar</a>";

    $eliminar = "";
    $hoy = date("Y-m-d");
    $date1 = new DateTime($result->fecha_ingreso);
    $date2 = new DateTime($hoy);
    $diff = $date1->diff($date2);
    $diferencia = $diff->days;

    if($diferencia <= 2) {
        $eliminar = "<a href='javascript:eliminarEquipo(\\\"$result->token\\\")'><i class='fas fa-trash'></i> Eliminar</a>";
    } else {
        $eliminar = "<span data-toggle=\\\"tooltip\\\" data-placement=\\\"top\\\" title=\\\"Han transcurrido más de 2 días desde que se creó el Equipo. No se puede eliminar.\\\"><i class='fas fa-question-circle' style='color:#707070;'></i></span>";
    }

    $prestado_a = "<select id='prestamo$result->id_equipo' name='prestamo$result->id_equipo' class='form-control sel2-basic-single' onChange='fechaPrestamo(this,\\\"$result->token\\\",\\\"capa$result->id_equipo\\\",\\\"fecha$result->id_equipo\\\");' style='width:100%;'>$option_usuarios</select><div id='capa$result->id_equipo' class='capasN' style='margin-top:8px; display:none;'><label>Fecha devolución:</label><input class='form-control campofecha' id='fecha$result->id_equipo' type='date' name='fecha$result->id_equipo' placeholder='date' value='' style='margin-bottom:6px;'><button type='button' class='btn btn-primary' onClick='prestar(\\\"fecha$result->id_equipo\\\",\\\"$result->token\\\",\\\"prestamo$result->id_equipo\\\")'>Realizar Préstamo</button></div>";
    
    $responsable = "";
    $fecha_devolucion = "";
    $color_fecha = "";
    $btn_devolver = "";
    $btn_extension = "";

    if ($result->prestado_a_id_usuario > 0) {
        // Obtener información de extensiones y fecha de devolución
        $sqlX2 = $mysql->query("SELECT token, fecha_debe_devolver,
                                   fecha_propuesta_extension, estado_extension,
                                   fecha_propuesta_extension2, estado_extension2
                            FROM equipo_prestamo 
                            WHERE id_equipo ='$result->id_equipo' AND estado = 'prestado'
                            ORDER BY id_equipo_prestamo DESC
                            LIMIT 1");
        $resultX2 = $mysql->f_obj($sqlX2);
        
        $tiene_extension1 = (!empty($resultX2->fecha_propuesta_extension));
        $tiene_extension2 = (!empty($resultX2->fecha_propuesta_extension2));
        
        // Usar fecha_debe_devolver en lugar de fecha_devolucion
        $fecha_debe_devolver = $resultX2->fecha_debe_devolver;
        
        // Verificar si está atrasado
        $fecha_actual = strtotime(date("Y-m-d", time()));
        $fecha_entrada = strtotime($fecha_debe_devolver);
        $esta_atrasado = ($fecha_actual > $fecha_entrada);
        
        // Determinar color base
        $color_base = "";
        if ($esta_atrasado && !$tiene_extension1 && !$tiene_extension2) {
            $color_base = " style='color:#ff0000;'";
        }
        
        $fecha_devolucion_formateada = fecha_mysql_a_normal($fecha_debe_devolver);

        // Construir contenido base
        $prestado_a = "<span $color_base> $result->prestado_a_nombre <br> Fecha Devolución: <br> $fecha_devolucion_formateada</span>";

        // Agregar extensiones si existen (siempre en azul)
        $color_extension = " style='color:#4169e1;'";
        if ($tiene_extension1) {
            $fecha_extension1 = fecha_mysql_a_normal($resultX2->fecha_propuesta_extension);
            $prestado_a .= " <span $color_extension> <br> Fecha extensión 1: <br> $fecha_extension1 </span> ";
        }
        if ($tiene_extension2) {
            $fecha_extension2 = fecha_mysql_a_normal($resultX2->fecha_propuesta_extension2);
            $prestado_a .= " <span $color_extension> <br> Fecha extensión 2: <br> $fecha_extension2 </span> ";
        }

        // ... el resto del código se mantiene igual ...
        $estado_solicitud = null;
        $sqlX = $mysql->query("SELECT estado, fecha_propuesta_extension FROM equipo_prestamo WHERE id_equipo ='$result->id_equipo' AND estado = 'solicitado' LIMIT 1");

        if ($mysql->f_num($sqlX) > 0) {
            $resultX = $mysql->f_obj($sqlX);
            $estado_solicitud = $resultX->estado;
        }
        
        if ($estado_solicitud == 'solicitado') {
            $btn_devolver = "Se debe Aceptar ó Rechazar esta solicitud.";
        } else {
            $btn_devolver = "<button type='button' class='btn btn-primary devolucion-btn' data-id='$result->prestado_a_id_usuario' data-nombre='$result->prestado_a_nombre' data-token='$result->token' data-toggle='modal' data-target='#primaryModal'>Devolución</button>";

            // El botón debe corresponder al mismo préstamo activo usado para
            // mostrar las fechas. No se deben considerar préstamos históricos.
            $extensionPendiente = (
                $resultX2->estado_extension2 === 'pendiente'
                || $resultX2->estado_extension === 'pendiente'
            );

            if ($extensionPendiente && !empty($resultX2->token)) {
                $btn_extension = "<button class='btn btn-warning btn-sm btn-extension' data-token='$resultX2->token' data-toggle='modal' data-target='#modalExtension'><i class='fas fa-clock'></i> Gestionar Extensión</button>";
            }
        }
    }



    $responsable = "$result->nombre_responsable_prestamo";

    // agrego para que no muestre filas de la tabla de kop pruebas, solo si es admin el usuario actual
    if($id_usuario_sesion == 1 || $result->prestado_a_id_usuario != 2287) {
        $datos = $datos."
        $signo_coma
        [
        \"$result->id_equipo\",
        \"$img_url\",
        \"$result->nombre\",
        \"$result->id_unico\",
        \"$result->estado\",
        \"$prestado_a\",
        \"$btn_devolver $btn_extension\",
        \"$responsable\",
        \"$detalle\",
        \"$edita\"]";

        $datos = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $datos);
    }
}

// Generar Excel
$lista_excel = "";
$sql = $mysql->query("SELECT * FROM equipo $where $orderby;");
while($result = $mysql->f_obj($sql)) {
    $prestado_a_excel = "";
    $responsable = "";

    if($result->prestado_a_id_usuario > 0) {
        $fecha_devolucion = fecha_mysql_a_normal($result->fecha_devolucion);
        $prestado_a_excel = "$result->prestado_a_nombre - Fecha devolución: $fecha_devolucion";
        $responsable = "$result->nombre_responsable_prestamo";
    }

    $lista_excel .= "<tr style='background-color:#ffffff; color:#000000;padding:4px;'><td>$result->id_equipo</td><td>$result->nombre</td><td>$result->id_unico</td><td>$result->estado</td><td>$prestado_a_excel</td><td>$responsable</td></tr>";
}

$lista_excel = "<table border='1' cellpadding='1' cellspacing='0'><tr style='background-color:#1d3e9d; color:#ffffff;padding:4px;'><td>ID</td><td>Nombre</td><td>ID Único</td><td>Estado</td><td>Prestado a:</td><td>Responsable del Préstamo</td></tr>$lista_excel</table>";

$fp = fopen("../excel/lista_equipo$id_usuario_sesion.xls", 'w');
fwrite($fp, $lista_excel);
fclose($fp);

echo "{\"draw\": $draw,\"recordsTotal\": $cantidad_registros,\"recordsFiltered\": $cantidad_filtrados,\"data\": [$datos]}";
?>
