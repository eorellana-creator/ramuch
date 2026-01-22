<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', 'error.log');

// Inclusión de archivos necesarios
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Recuperación de datos enviados por POST
@$draw = $_POST["draw"];
@$inicio = $_POST["start"];
@$fin = $_POST["length"];
@$busqueda = $_POST["search"]["value"];
@$orden = $_POST["order"][0]["column"];
@$direccion = $_POST["order"][0]["dir"];

// Construcción de la cláusula WHERE para la búsqueda
if ($busqueda != "") {
    $busqueda = " WHERE editable='1' AND (nombre LIKE '%$busqueda%')";
} else {
    $busqueda = " WHERE editable='1'";
}

// Inicialización de variables
if ($inicio == "") {
    $inicio = 0;
}

$config = new Config;
$mysql = new mysql;
$mysql->connect();

$datos = "";

// Construcción de la cláusula ORDER BY
$orderby = " ORDER BY nombre asc";
if ($orden == 0) {
    $orderby = " ORDER BY nombre $direccion";
}

// Consulta para obtener los datos paginados
$sql = $mysql->query("SELECT * FROM plan_matricula $busqueda $orderby LIMIT $inicio,$fin;");

// Consulta para contar los registros filtrados
$sql2 = $mysql->query("SELECT * FROM plan_matricula $busqueda;");
$cantidad_filtrados = $mysql->f_num($sql2);

// Consulta para contar todos los registros
$sql3 = $mysql->query("SELECT * FROM plan_matricula $busqueda;");
$cantidad_registros = $mysql->f_num($sql3);

// Procesamiento de los resultados
$coma = 0;
$signo_coma = "";
while ($result = $mysql->f_obj($sql)) {
    if ($coma == 1) {
        $signo_coma = ",";
    }
    $coma = 1;

    // Formas de pago de planes
    $planes = "";
    $planes2 = "";
    $eliminar = "";


    $sql7 = $mysql->query("SELECT * FROM plan WHERE id_plan_matricula='$result->id_plan_matricula' ORDER BY periodo DESC;");
    while ($result7 = $mysql->f_obj($sql7)) {
        $result7->valor = number_format($result7->valor, 0, '', '.');

        $planes = $planes . "<div class='card card-accent-primary'><div class='card-header'><strong>$result7->nombre</strong> <i onClick='deletePlanPago(\\\"$result7->token\\\")' class='fas fa-trash float-right' style='color:#20a8d8;cursor:pointer;'></i> <i onClick='document.location.href=\\\"index.php?component=plan&view=plan_pago&token=$result->token&tokenPlanPago=$result7->token\\\"' class='fas fa-edit float-right' style='color:#20a8d8;cursor:pointer;'></i> </div> <div class='card-body'> <strong>Periodo: </strong>$result7->periodo<br> <strong>Día de pago: </strong>$result7->dia_pago<br> <strong>Valor: </strong>$ $result7->valor.-<br> <strong>Tipo: </strong>$result7->publico_privado";

        // Configurar el idioma a español
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp');

        if ($result7->periodo == 'semestral') {
            // Validar y formatear fecha_cierre1
            $fecha_cierre1 = !empty($result7->fecha_cierre1)
                ? (new DateTime($result7->fecha_cierre1))->format('d \d\e F')
                : "Fecha no disponible";

            // Validar y formatear fecha_cierre2
            $fecha_cierre2 = !empty($result7->fecha_cierre2)
                ? (new DateTime($result7->fecha_cierre2))->format('d \d\e F')
                : "Fecha no disponible";

            // Agregar las fechas formateadas al texto
            $planes = $planes . "<br> <strong> Fechas Límites: </strong> " . $fecha_cierre1 . " y " . $fecha_cierre2 . " ";
        }

        if ($result7->periodo == 'anual') {
            // Validar y formatear fecha_cierre1
            $fecha_cierre1 = !empty($result7->fecha_cierre1)
                ? (new DateTime($result7->fecha_cierre1))->format('d \d\e F')
                : "Fecha no disponible";

            // Agregar la fecha formateada al texto
            $planes = $planes . "<br> <strong> Fecha Límite: </strong> " . $fecha_cierre1 . " ";
        }
        /*
        if ($result7->periodo == 'semestral') {
            $fecha_cierre1 = date("d F", strtotime($result7->fecha_cierre1));
            $fecha_cierre2 = date("d F", strtotime($result7->fecha_cierre2));
            $planes = $planes . "<br> <strong> Fechas Limites : </strong> " . $result7->fecha_cierre1 . " y " . $result7->fecha_cierre2 . " ";
        }
        if ($result7->periodo == 'anual') {
            $fecha_cierre1 = date("d F", strtotime($result7->fecha_cierre1));
            $planes = $planes . "<br> <strong> Fecha Limite  : </strong> " . $result7->fecha_cierre1 ." ";
        }
        */
        $planes = $planes . " </div></div>";
    }

    $planes = "<a href='index.php?component=plan&view=plan_pago&token=$result->token'><i class='fas fa-plus'></i> Añadir plan de pago </a>$planes";

    // Enlaces de ver y eliminar
    $ver = "<a href='index.php?component=plan&view=plan&token=$result->token'><i class='fas fa-edit'></i></a>";
    $eliminar = "<a href='javascript: deletePlan(\\\"$result->token\\\");'><i class='fas fa-trash-alt'></i></a>";
    //$eliminar = "<a href='javascript: deletePlan(\"$result->token\");'><i class='fas fa-trash-alt'></i></a>";

    $valor = number_format($result->valor, 0, '', '.');

    // Construcción de la fila de datos
    $datos = $datos . "
     $signo_coma
	 [
      \"<a href='index.php?component=plan&view=plan&token=$result->token' class='link-negro' >$result->nombre</a>\",
      \"$result->tipo\",
      \"$result->dia_pago_1\",
      \"$valor\",
      \"$result->publica_privada\",
      \"$planes\",
	  \"$ver\",
      \"$eliminar\"
    ]";

    $datos = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $datos);

}

// Respuesta en formato JSON
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