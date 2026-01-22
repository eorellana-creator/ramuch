<?php
//include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Obtener parámetros de la solicitud
@$draw = $_POST["draw"];
@$inicio = $_POST["start"];
@$fin = $_POST["length"];
@$busqueda = $_POST["search"]["value"];
@$orden = $_POST["order"][0]["column"];
@$direccion = $_POST["order"][0]["dir"];

$subcuenta = @$_GET["subcuenta"];

// Construir la cláusula WHERE para la búsqueda
if ($busqueda != "") {
    $busqueda = " WHERE (id_cuenta_maestra LIKE '%$busqueda%' OR nombre_usuario_sistema LIKE '%$busqueda%')";
} else {
    $busqueda = "";
}

// Establecer el inicio de la paginación
if ($inicio == "") {
    $inicio = 0;
}

// Instanciar clases y conectar a la base de datos
$config = new Config;
$mysql = new mysql;
$mysql->connect();

// Construir la cláusula ORDER BY
$orderby = " ORDER BY fecha_insercion desc";
if ($orden == 0) {
    $orderby = " ORDER BY fecha_insercion $direccion";
}

// Consulta para obtener los datos filtrados y paginados
$sql = $mysql->query("SELECT * FROM cuenta_maestra $busqueda $orderby LIMIT $inicio,$fin;");

// Consulta para contar los registros filtrados
$sql2 = $mysql->query("SELECT id_cuenta_maestra FROM cuenta_maestra $busqueda;");
$cantidad_filtrados = $mysql->f_num($sql2);

// Consulta para contar todos los registros
$sql3 = $mysql->query("SELECT id_cuenta_maestra FROM cuenta_maestra $busqueda;");
$cantidad_registros = $mysql->f_num($sql3);

// Variables para construir el JSON de respuesta
$coma = 0;
$signo_coma = "";
$saldo = 0;
$datos = "";

// Procesar cada fila de resultados
while ($result = $mysql->f_obj($sql)) {
    if ($coma == 1) {
        $signo_coma = ",";
    }
    $coma = 1;

    // Formatear la fecha
    $result->fecha = fecha_mysql_a_normal($result->fecha);

    // Obtener el nombre del usuario asociado al movimiento
    $sql3 = $mysql->query("SELECT id_usuario, nombre_usuario, token FROM usuario WHERE id_usuario ='$result->id_usuario_movimiento';");
    $result3 = $mysql->f_obj($sql3);
    $nombre_usuario = @$result3->nombre_usuario;

    // Calcular el saldo y formatear montos
    $submonto = $result->monto;
    $result->monto = number_format($result->monto, 0, '', '.');

    $monto_ingreso = "";
    $monto_egreso = "";

    if ($result->tipo == "ingreso") {
        $monto_ingreso = $result->monto;
        $saldo = $saldo + $submonto;
    }

    if ($result->tipo == "egreso") {
        $monto_egreso = $result->monto;
        $saldo = $saldo - $submonto;
    }

    $saldo_imprime = number_format($saldo, 0, '', '.');

    // Estilos para registros eliminados
    $span1 = "";
    $span2 = "";
    if (@$result->estado == "eliminado") {
        $span1 = "<span style='color:#ff0000;'>";
        $span2 = "</span>";
    }

    // Enlace al documento de respaldo (si existe)
    if (@$result->documento_respaldo != "") {
        $documento = "<a href='images/ingresos/$result->documento_respaldo' target='_blank'><i class='fas fa-file-alt'></i> Ver documento</a>";
    } else {
        $documento = " ";
    }

    // Construir la fila de datos para el JSON
    $datos = "
        [
            \"$span1$result->id_cuenta_maestra$span2\",
            \"$span1$result->fecha$span2\",
            \"$span1$nombre_usuario$span2\",
            \"$span1$result->glosa$span2\",
            \"$span1$result->medio$span2\",
            \"$span1$documento$span2\",
            \"$span1$result->estado$span2\",
            \"$span1$monto_ingreso$span2\",
            \"$span1$monto_egreso$span2\"
        ]
        $signo_coma
        " . $datos;

    // Limpiar saltos de línea en el JSON
    $datos = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $datos);
}

// Construir y devolver la respuesta JSON
echo "
{
    \"draw\": $draw,
    \"recordsTotal\": $cantidad_registros,
    \"recordsFiltered\": $cantidad_filtrados,
    \"data\": [
        $datos
    ]
}
";
?>