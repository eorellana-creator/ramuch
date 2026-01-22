<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

@$draw = $_POST["draw"];
@$inicio = $_POST["start"];
@$fin = $_POST["length"];
@$busqueda = $_POST["search"]["value"];
@$orden = $_POST["order"][0]["column"];
@$direccion = $_POST["order"][0]["dir"];

$id_company = $_SESSION["company_id"];
$id_usuario = $_SESSION["usuario_id"];

$config = new Config;
$mysql = new mysql;
$mysql->connect();

// Check for overdue debts
$fechaActual = date('Y-m-d');
$fecha3mesesAtras = date('Y-m-d', strtotime('-3 month'));
$cantidad_deuda_atrasada = 0;

$sql0 = $mysql->query("SELECT id_deuda FROM deudas WHERE id_usuario_deuda='$id_usuario' AND estado='activa' AND fecha<'$fecha3mesesAtras';");
$cantidad_deuda_atrasada = $mysql->f_num($sql0);

$busqueda = $busqueda ? " WHERE estado NOT IN ('Extraviado','Dado de baja','Inutilizable') AND ( id_equipo LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR id_unico LIKE '%$busqueda%' )" : "";

$orderby = " ORDER BY fecha_devolucion DESC, nombre ASC";

$sql = $mysql->query("SELECT * FROM equipo $busqueda $orderby LIMIT $inicio,$fin;");
$sql2 = $mysql->query("SELECT id_equipo FROM equipo $busqueda;");
$cantidad_filtrados = $mysql->f_num($sql2);
$sql3 = $mysql->query("SELECT id_equipo FROM equipo $busqueda;");
$cantidad_registros = $mysql->f_num($sql3);

$datos = "";
$coma = 0;

while($result = $mysql->f_obj($sql)) {
    if (!in_array($result->estado, ['Extraviado', 'Dado de baja', 'Inutilizable',''])) {
        $signo_coma = $coma ? "," : "";
        $coma = 1;

        // Check if equipment is available
        $sqlp = $mysql->query("SELECT * FROM equipo_prestamo WHERE id_equipo = $result->id_equipo AND (estado = 'solicitado' OR estado = 'prestado');");
        $se_presto = $mysql->f_num($sqlp);
        $resultp = $mysql->f_obj($sqlp);

        $disabled = "";
        $status_text = "Disponible";
        
        if($se_presto > 0) {
            $sqlu = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario = " . $resultp->id_usuario_prestamo);
            $resultu = $mysql->f_obj($sqlu);
            $status_text = "<span style='color:#ff0000;'>No disponible - " . ($resultp->estado == 'solicitado' ? 'Solicitado por ' : 'Prestado a ') . 
                          "$resultu->nombre_usuario hasta el $resultp->fecha_debe_devolver</span>";
            $disabled = "disabled";
        }

        if($cantidad_deuda_atrasada > 0) {
            $status_text = "<span class='badge badge-danger'>No disponible - Deudas pendientes</span>";
            $disabled = "disabled";
        }

        $img_url = $result->imagen ? 
            "<img src='https://ramuch.cl/admin/images/equipo/$result->imagen' width='90' height='120'>" :
            "<img src='https://ramuch.cl/admin/images/equipo_sin_imagen.jpg' width='90' height='120'>";
        
        $img_url2 = $result->imagen ?
            "<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#imageModal' data-img='https://ramuch.cl/admin/images/equipo/$result->imagen'> $img_url</button>" :
            $img_url;

        $checkbox = "<input type='checkbox' name='equipment_checkbox' value='$result->token' $disabled onchange='updateDateRangeVisibility()'>";

        $datos .= "$signo_coma
        [
            \"$checkbox\",
            \"$result->id_equipo\",
            \"$img_url2\",
            \"$result->nombre\",
            \"$result->id_unico\",
            \"$result->estado\",
            \"$status_text\"
        ]";
    }
}

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $cantidad_registros,
    "recordsFiltered" => $cantidad_filtrados,
    "data" => json_decode("[$datos]")
]);
?>