<?php
$view = @$_GET["view"];

if($view=="rol"){
include("models/rol.php");
include("views/rol.php");
}

if($view=="rol_list"){
include("models/rol_list.php");
include("views/rol_list.php");
}

?>