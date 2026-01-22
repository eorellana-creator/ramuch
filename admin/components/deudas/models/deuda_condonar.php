<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];


$mensaje = @$_SESSION["deuda_condonada"];

$_SESSION["deuda_condonada"] = "";




?>