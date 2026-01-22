<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Parámetros de DataTables
$draw = isset($_POST["draw"]) ? intval($_POST["draw"]) : 1;
$inicio = isset($_POST["start"]) ? intval($_POST["start"]) : 0;
$fin = isset($_POST["length"]) ? intval($_POST["length"]) : 10;
$busqueda = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : "";
$orden = isset($_POST["order"][0]["column"]) ? intval($_POST["order"][0]["column"]) : 1;
$direccion = isset($_POST["order"][0]["dir"]) ? $_POST["order"][0]["dir"] : "ASC";

// Construir la cláusula WHERE
$where = "";
if($busqueda != "") {
    $where = " WHERE (productos.nombre LIKE '%$busqueda%' OR 
                     productos.marca LIKE '%$busqueda%' OR 
                     categorias.nombre LIKE '%$busqueda%')";
}

// Configurar la conexión
$config = new Config;
$mysql = new mysql;
$mysql->connect();

// Determinar el orden
$orderby = " ORDER BY productos.nombre ASC";
switch($orden) {
    case 1: $orderby = " ORDER BY productos.id $direccion "; break;
    case 3: $orderby = " ORDER BY productos.nombre $direccion "; break;
    case 4: $orderby = " ORDER BY productos.precio $direccion "; break;
    case 5: $orderby = " ORDER BY productos.stock $direccion "; break;
    case 6: $orderby = " ORDER BY categorias.nombre $direccion "; break;
    case 7: $orderby = " ORDER BY productos.marca $direccion "; break;
    case 8: $orderby = " ORDER BY productos.estado $direccion "; break;
}

// Consulta principal
$sql = $mysql->query("SELECT productos.*, categorias.nombre as categoria_nombre 
                     FROM productos 
                     LEFT JOIN categorias ON productos.categoria_id = categorias.id 
                     $where $orderby 
                     LIMIT $inicio,$fin");

// Consultas para conteos
$sql2 = $mysql->query("SELECT COUNT(*) as total FROM productos LEFT JOIN categorias ON productos.categoria_id = categorias.id $where");
$cantidad_filtrados = $mysql->f_obj($sql2)->total;

$sql3 = $mysql->query("SELECT COUNT(*) as total FROM productos");
$cantidad_registros = $mysql->f_obj($sql3)->total;

// Preparar datos
$datos = array();
while($result = $mysql->f_obj($sql)) {
    $imagen_path = $result->imagen_nombre ? "components/mercado/images/productos/$result->imagen_nombre" : "components/mercado/images/producto_sin_imagen.jpg";
    $img_url = "<img src='$imagen_path' width='90' height='120' style='cursor: pointer;' 
                onclick='openImageModal(\"$imagen_path\")'>";
    
    $precio_formato = number_format($result->precio, 0, ',', '.');
    
    $datos[] = array(
        $result->id,
        $img_url,
        htmlspecialchars($result->nombre),
        "$$precio_formato",
        $result->stock,
        htmlspecialchars($result->categoria_nombre),
        htmlspecialchars($result->marca),
        $result->estado,
//        "<button type='button' class='btn btn-link' data-toggle='modal' data-target='#detalleModal' data-token='$result->token'>
//            <i class='fas fa-search-plus'></i> ver producto
//        </button>",
        "<button type='button' class='btn btn-primary edit-product' data-product-id='$result->id'>
        <i class='fas fa-edit'></i> Editar
        </button>",
        "<button class='btn btn-sm btn-danger delete-product' data-product-id='" . $result->id . "' onclick='confirmDelete(this)'><i class='fas fa-trash-alt'></i> Eliminar</button>"
    );
}

// Preparar respuesta
$response = array(
    "draw" => $draw,
    "recordsTotal" => $cantidad_registros,
    "recordsFiltered" => $cantidad_filtrados,
    "data" => $datos
);

// Enviar respuesta
header('Content-Type: application/json');
echo json_encode($response);

$mysql->close();
?>