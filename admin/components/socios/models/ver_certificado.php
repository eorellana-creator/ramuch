<?php
// Descarga protegida del certificado de un socio desde la administración.
session_start();
ini_set('display_errors', '0');

if (($_SESSION['usuario_valido_bastro_ruta'] ?? '') !== 'true' || (int)($_SESSION['usuario_id'] ?? 0) <= 0) {
    http_response_code(403);
    exit('Acceso no autorizado.');
}

include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
require_once __DIR__ . '/../../../includes/certificados.php';

$token = preg_match('/^[A-Za-z0-9]{1,100}$/', $_GET['token'] ?? '') ? $_GET['token'] : '';
if ($token === '') {
    http_response_code(400);
    exit('Solicitud inválida.');
}

$mysql = new mysql;
$mysql->connect();

$sql = $mysql->query("SELECT p.certificado_estudios
    FROM usuario AS u
    INNER JOIN perfil AS p ON p.id_usuario = u.id_usuario
    WHERE u.token = '$token' AND u.token != ''
    LIMIT 1;");
$perfil = $sql ? $mysql->f_obj($sql) : null;

ramuchEnviarCertificado($perfil ? $perfil->certificado_estudios : '');
