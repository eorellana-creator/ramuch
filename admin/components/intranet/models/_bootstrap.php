<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../includes/intranet.php");

$mysql = new mysql;
$mysql->connect();
$rolIntranet = intranetExigirAcceso($mysql);
intranetCrearTablas($mysql);
$idUsuarioIntranet = (int)($_SESSION['usuario_id'] ?? 0);
$nombreUsuarioIntranet = $_SESSION['usuario_nombre'] ?? '';

function intranetValidarCsrf() {
    $recibido = $_POST['csrf'] ?? '';
    $esperado = $_SESSION['intranet_csrf'] ?? '';
    if ($esperado === '' || !hash_equals($esperado, $recibido)) {
        intranetJson(['error' => 'La sesión venció. Recarga la página.'], 419);
    }
}

function intranetTextoSql($texto, $maximo = 5000) {
    $texto = trim(strip_tags((string)$texto));
    $texto = function_exists('mb_substr') ? mb_substr($texto, 0, $maximo, 'UTF-8') : substr($texto, 0, $maximo);
    return str_replace("'", "''", $texto);
}
?>
