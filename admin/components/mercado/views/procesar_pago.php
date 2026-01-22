<?php
session_start();
//include("../../../includes/sql_inyection_salto_textarea.php");
//include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
//include("../../../includes/funciones.php");
//$token				= $_POST['tokenramuch'];

$nombre_usuario	="";
$nombre_usuario	= $_SESSION["usuario_nombre"];
$id_usuario = $_SESSION["usuario_id"];

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Habilitar logs de errores en un archivo
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log'); // Guardar los errores en un archivo llamado error.log

// Establecer el encabezado para devolver una respuesta JSON
header('Content-Type: application/json');

try {
    // Incluir la clase FlowApi
    require_once __DIR__ . '/../flow/lib/FlowApi.class.php';

    // Depuración: Verificar que el archivo FlowApi.class.php se incluya correctamente
    if (!class_exists('FlowApi')) {
        throw new Exception('No se pudo cargar la clase FlowApi');
    }

    // Recibir los datos del carrito desde el cliente
    $input = json_decode(file_get_contents('php://input'), true);

    // Verificar si los datos del carrito fueron recibidos correctamente
    if (empty($input['items']) || empty($input['total'])) {
        throw new Exception('Datos del carrito incompletos o no válidos.');
    }

    // Obtener el total del carrito
    $total = $input['total'];

    //recupera datos de usuario
    $mysql = new mysql;
    $mysql->connect();

    $sql1 = $mysql->query("SELECT * FROM usuario WHERE id_usuario='$id_usuario';");
    $result1 = $mysql->f_obj($sql1);
    $nombre_usuario = $result1->nombre_usuario;
    $token = $result1->token;
    $email = $result1->email;

    $sql2 = $mysql->query("SELECT * FROM perfil WHERE id_usuario='$id_usuario';");
    $result2 = $mysql->f_obj($sql2);
    $rut = $result2->rut;

    // Generar un token único para la orden
    $fecha = date("Y-m-d");
    $token_nuevo = md5(rand(999, 999999) . $fecha);

    // Guardar el carrito en la base de datos
    $carrito_json = json_encode($input['items']); // Convertir el carrito a JSON
    $fecha_creacion = date("Y-m-d H:i:s");

    $sql_carrito = $mysql->query("
        INSERT INTO carritos (id_usuario, token_flow, carrito, fecha_creacion)
        VALUES ('$id_usuario', '$token_nuevo', '$carrito_json', '$fecha_creacion')
    ");

    if (!$sql_carrito) {
        error_log("Error al guardar el carrito en la base de datos.");
        throw new Exception("Error al guardar el carrito en la base de datos.");
    }

    // Configurar los datos opcionales
    $optional = [
        "rut" => $rut,
        "token" => $token,
        "usuario" => $nombre_usuario,
        "id_user" => $id_usuario,
        "token_carrito" => $token_nuevo,
    ];
    $optional = json_encode($optional);

    // Configurar los parámetros para la solicitud a Flow
    $params = [
        "commerceOrder" => 'ORD' . time(), // Identificador único de la orden
        "subject" => "Pago Mercado Ramuch",
        "currency" => "CLP",
        "amount" => $total, // Usar el total del carrito
        "email" => $email,
        "paymentMethod" => 9, // Método de pago (9 es para Webpay)
        "urlConfirmation" => 'https://www.ramuch.cl/admin/components/mercado/views/confirmacion.php', // URL de confirmación
        "urlReturn" => 'https://www.ramuch.cl/admin/components/mercado/views/retorno.php', // URL de retorno
        "optional" => $optional // Datos opcionales
    ];

    // Definir el servicio a usar
    $serviceName = "payment/create";

    // Instanciar la clase FlowApi
    $flowApi = new FlowApi();

    // Ejecutar el servicio
    $response = $flowApi->send($serviceName, $params, "POST");

    // Verificar si la solicitud fue exitosa
    if (isset($response['url'])) {
        // Devolver una respuesta JSON con la URL de redirección
        echo json_encode([
            'success' => true,
            'redirectUrl' => $response['url'],
            'token' => $response['token']
        ]);
    } else {
        // Manejar el error de Flow
        error_log('Error al crear la orden en Flow: ' . print_r($response, true));
        throw new Exception('Error al crear la orden en Flow: ' . print_r($response, true));
    }

} catch (Exception $e) {
    // Manejar excepciones y devolver un mensaje de error en formato JSON
    error_log('Excepción capturada: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>