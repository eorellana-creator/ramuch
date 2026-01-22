<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$id_company 	= $_SESSION["company_id"];
$id_usuario 	= $_SESSION["usuario_id"];


$nombre		        = $_GET['nombre'];
$precio 			= $_GET['precio'];
$comentario			= $_GET['comentario'];

$token				= @$_GET['token'];



$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();

$sql 			= $mysql->query("SELECT * FROM curso WHERE token='$token';");
$result 		= $mysql->f_obj($sql);
$id_curso		= @$result->id_curso;
$nombre_curso	= @$result->nombre;
$tipo_curso		= @$result->tipo;


$hoy 	= date("Y-m-d");


$token_nuevo = md5(rand(99999, 99999999).$nombre.date("Y m d H s").$token);

$id_participante = 0;

$nombre_sistema = explode("|",$nombre);

if(  is_numeric($nombre_sistema[0] )  ){

	$id_participante = $nombre_sistema[0];
	$nombre = $nombre_sistema[1];

}//if(  is_numeric($nombre)  )

$ultimo_id_deuda = 0;
$estado_pago = "Pendiente";

if($precio>0 ){
	$estado_pago = "Pendiente";
	$token_deuda = md5(rand(99989, 99999979).$nombre.date("Y m d H s").$token_nuevo);
	$sql 	= $mysql->query("INSERT INTO deudas (id_usuario,    sub_cuenta, fecha,   monto,          glosa,                             estado, observacion, token) 
		                 	            VALUES('$id_participante', 'curso', '$hoy', '$precio', '$tipo_curso : $nombre_curso - $nombre', 'activa', '', '$token_deuda' ) ;");

$ultimo_id_deuda = $mysql->ultimo_id(); 

}else{
$estado_pago = "Pagado";	
}//if($precio>0)


 


$sql 	= $mysql->query("INSERT INTO curso_participantes (id_curso, id_participante, nombre_participante, fecha_inscripcion, precio_a_pagar, estado_pago, comentario,id_deuda, id_pago, token) 
		                 	     VALUES('$id_curso', '$id_participante', '$nombre', '$hoy', '$precio', '$estado_pago', '$comentario', '$ultimo_id_deuda', '0', '$token_nuevo' ) ;");

$ultimo_id = $mysql->ultimo_id(); 






$_SESSION["curso_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos del curso se han guardado.</div>";


echo "|$token|";



?>