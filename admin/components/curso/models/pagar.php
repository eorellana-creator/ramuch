<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];
$nombre_usuario_sistema = $_SESSION["usuario_nombre"];


$tipopago	= @$_GET['tipopago'];
$token		= @$_GET['token'];



$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

$sql 					= $mysql->query("SELECT * FROM curso_participantes WHERE token='$token';");
$result 				= $mysql->f_obj($sql);
$id_curso_participantes	= @$result->id_curso_participantes;
$id_participante		= @$result->id_participante;
$nombre_participante	= @$result->nombre_participante;
$precio_a_pagar			= @$result->precio_a_pagar;
$id_deuda				= @$result->id_deuda;
$comentario				= @$result->comentario;


$sql2 					= $mysql->query("SELECT * FROM deudas WHERE id_deuda='$id_deuda';");
$result2 				= @$mysql->f_obj($sql2);
$id_deuda				= @$result2->id_deuda;
$subcuenta				= @$result2->sub_cuenta;
$monto					= @$result2->monto;
$glosa					= @$result2->glosa;
$observacion			= @$result2->observacion;

$hoy 	= date("Y-m-d H:i:s");


$token_nuevo = md5(rand(99999, 99999999).$id_curso_participantes.date("Y m d H s").$token);


$sql 	= $mysql->query("INSERT INTO pagos (id_usuario,        id_deuda,        id_transaccion,         sub_cuenta,    medio_pago, monto, valor,  fecha, glosa, observacion, token) 
                             VALUES ('$id_participante','$id_deuda',   '$id_curso_participantes','$subcuenta','$tipopago','$monto','$monto','$hoy','$glosa','$observacion','$token_nuevo') ;");


$ultimo_id = $mysql->ultimo_id(); 


$sql 	= $mysql->query("UPDATE deudas SET estado='pagada' WHERE id_deuda='$id_deuda' ;");

$sql 	= $mysql->query("UPDATE curso_participantes SET estado_pago='Pagado', id_pago='$ultimo_id' WHERE id_curso_participantes='$id_curso_participantes' ;");

$token_mestra = md5(rand(99959, 99999899).$id_curso_participantes.date("Y m d H s").$ultimo_id);

$fecha	= date("Y-m-d");

//id_transaccion usaremos el token de curso_participantes
$sql 	= $mysql->query("INSERT INTO cuenta_maestra (id_usuario_sistema, nombre_usuario_sistema, id_usuario_movimiento, nombre,  fecha, tipo, sub_cuenta, glosa, observacion, medio, id_transaccion, documento_respaldo, monto, estado, token)
											VALUES ( '$id_usuario', '$nombre_usuario_sistema',     '$id_participante', '$nombre_participante', '$fecha', 'ingreso', '$subcuenta','$glosa','$observacion','$tipopago','$token','','$monto','activo', '$token_mestra'  ) ;");



$_SESSION["curso_actualizado"] = "<div class='alert alert-success' role='alert'>El pago se ha guardado.</div>";


echo "|$token|";



?>