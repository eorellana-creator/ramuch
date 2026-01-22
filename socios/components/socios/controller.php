<?php
$view = @$_GET["view"];

if($view=="socios"){
include("models/socios.php");
include("views/socios.php");
}

if($view=="socios_list"){
include("models/socios_list.php");
include("views/socios_list.php");
}

?>