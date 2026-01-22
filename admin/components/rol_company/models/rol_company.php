<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];
$sql 	= $mysql->query("SELECT * FROM rol_empresa WHERE token ='$token' ;");
$result = $mysql->f_obj($sql);

$id_rol_usuario = @$result->id_rol;

?>