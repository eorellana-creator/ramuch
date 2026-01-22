<?php

//include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

$tokenRamuch 	        = @$_GET["tokenRamuch"];

$mysql 		= new mysql;
$mysql->connect();

$ids = "";

$sql 		= $mysql->query("SELECT * FROM flow WHERE token='$tokenRamuch' ;");
$result		= @$mysql->f_obj($sql);

$orden 			= $result->id_flow;
$ids 			= $result->ids_deudas;
$rut 			= $result->rut;
$email 			= $result->email;
$monto 			= $result->monto;

$rut = str_replace(".","", $rut);

$ids = explode("|",$ids);

require(__DIR__ . "/flow/lib/FlowApi.class.php");

//Para datos opcionales campo "optional" prepara un arreglo JSON
$optional = array(
	"rut" => "$rut",
	"Orden" => "$orden",
	"tokenRamuch" => "$tokenRamuch"
);
$optional = json_encode($optional);


//Prepara el arreglo de datos
$params = array(
	"commerceOrder" => $orden,
	"subject" => "Pago Ramuch",
	"currency" => "CLP",
	"amount" => $monto,
	"email" => "$email",
	"paymentMethod" => 9,
	"urlConfirmation" => Config::get("BASEURL") . "/confirm_flow.php",
	"urlReturn" => Config::get("BASEURL") ."/result_flow.php",
	"optional" => $optional
);
//Define el metodo a usar
$serviceName = "payment/create";

//var_dump($params);

try {

	// Instancia la clase FlowApi
	$flowApi = new FlowApi;
	// Ejecuta el servicio
	$response = $flowApi->send($serviceName, $params,"POST");
	//var_dump($response);

	$orden = @$response["flowOrder"];

	$sql 		= $mysql->query("UPDATE flow SET flow_order='$orden' WHERE token='$tokenRamuch' ;");

	//Prepara url para redireccionar el browser del pagador
	//var_dump($response);

	$redirect = $response["url"] . "?token=" . $response["token"];

	header("location:$redirect");


} catch (Exception $e) {
	echo $e->getCode() . " - " . $e->getMessage();
}

?>