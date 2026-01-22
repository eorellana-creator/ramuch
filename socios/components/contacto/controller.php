<?php
$view = @$_GET["view"];

if($view=="contacto"){
include("models/contacto.php");
include("views/contacto.php");
}

 

?>