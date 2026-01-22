<?php
$view = @$_GET["view"];

$rol = $_SESSION["usuario_rol"];

if($view=="dashboard" && $rol!=8){
include("models/dashboard.php");
include("views/dashboard.php");
}

?>