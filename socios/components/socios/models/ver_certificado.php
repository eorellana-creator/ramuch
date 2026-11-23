<?php
// Descarga protegida del certificado propio desde el portal de socios.
// El socio solo puede ver su propio certificado: se identifica por la sesión.
session_start();
ini_set('display_errors', '0');

if (($_SESSION['usuario_valido_socio_ramuch'] ?? '') !== 'true') {
    http_response_code(403);
    exit('Acceso no autorizado.');
}

include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
require_once __DIR__ . '/../../../../admin/includes/certificados.php';

$token = preg_match('/^[A-Za-z0-9]{1,100}$/', $_SESSION['usuario_token'] ?? '') ? $_SESSION['usuario_token'] : '';
if ($token === '') {
    http_response_code(403);
    exit('Acceso no autorizado.');
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
