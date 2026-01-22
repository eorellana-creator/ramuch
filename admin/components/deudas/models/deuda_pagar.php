<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];


$sql 	= $mysql->query("SELECT * FROM deudas WHERE token ='$token' AND token!='' ;");
$result = $mysql->f_obj($sql);







$mensaje = @$_SESSION["deuda_actualizado"];

$_SESSION["deuda_actualizado"] = "";




?>