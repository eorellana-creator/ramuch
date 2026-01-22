<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];


$token		= @$_POST['token'];



$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

$sql 					= $mysql->query("SELECT * FROM curso_participantes WHERE token='$token';");
$result 				= $mysql->f_obj($sql);
$id_curso_participantes	= @$result->id_curso_participantes;
$id_curso				= @$result->id_curso;
$id_participante		= @$result->id_participante;
$nombre_participante	= @$result->nombre_participante;
$precio_a_pagar			= @$result->precio_a_pagar;
$id_deuda				= @$result->id_deuda;
$id_pago				= @$result->id_pago;
$comentario				= @$result->comentario;

$sql 			= $mysql->query("SELECT * FROM curso WHERE id_curso='$id_curso';");
$result 		= $mysql->f_obj($sql);
$id_curso		= @$result->id_curso;
$nombre_curso	= @$result->nombre;
$tipo_curso		= @$result->tipo;


$sql2 					= $mysql->query("SELECT * FROM deudas WHERE id_deuda='$id_deuda';");
$result2 				= @$mysql->f_obj($sql2);
$id_deuda				= @$result2->id_deuda;
$subcuenta				= @$result2->sub_cuenta;
$monto					= @$result2->monto;
$glosa					= @$result2->glosa;
$observacion			= @$result2->observacion;

$hoy 	= date("Y-m-d H:i:s");


$sql 	= $mysql->query("UPDATE pagos SET eliminado='1', observacion='Pago eliminado $glosa' WHERE id_pago ='$id_pago' ;");

$sql 	= $mysql->query("UPDATE deudas SET estado='eliminada', observacion='Deuda eliminada $glosa' WHERE id_deuda='$id_deuda' ;");

$estado_pago = "Pagado";
$ultimo_id_deuda = 0;

if($precio_a_pagar>0){
$estado_pago = "Pendiente";

	$token_deuda = md5(rand(99989, 99999979).$id_participante.date("Y m d H s").$id_curso_participantes);
	$sql 	= $mysql->query("INSERT INTO deudas (id_usuario,    sub_cuenta, fecha,   monto,          glosa,                             estado, observacion, token) 
		                 	            VALUES('$id_participante', 'curso', '$hoy', '$precio_a_pagar', '$tipo_curso : $nombre_curso - $nombre_participante', 'activa', '', '$token_deuda' ) ;");

$ultimo_id_deuda = $mysql->ultimo_id(); 





}

$sql 	= $mysql->query("UPDATE curso_participantes SET estado_pago='$estado_pago', id_deuda='$ultimo_id_deuda', id_pago='0' WHERE token='$token' ;");

$sql 	= $mysql->query("UPDATE cuenta_maestra SET estado='eliminado', id_usuario_sistema='$id_usuario', observacion='Se deshizo el pago del curso del participante.'  WHERE id_transaccion='$token' ;");

 


$_SESSION["curso_actualizado"] = "<div class='alert alert-success' role='alert'>El pago se ha eliminado.</div>";


echo "|1|";



?>