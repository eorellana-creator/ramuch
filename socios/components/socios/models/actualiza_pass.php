<?php
session_start();
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token				= $_POST['token'];
$pass				= $_POST['pass'];

$nombre_usuario	= $_SESSION["usuario_nombre"];
//$id_usuario		= $_SESSION["usuario_id"];	


$mysql 		= new mysql;
$mysql->connect();



if($pass!=""){

$pass = md5( $pass );

$sql 	= $mysql->query("UPDATE usuario SET  password='$pass' WHERE token ='$token';");

$_SESSION["socio_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos se han actualizado.</div>";

$_SESSION["script_final"] = "
<script>
$(document).ready(function() {
$('.tab-pass').click(); 
} );
</script>
";



}//if($pass!="")

 

 
echo "||";

?>