<?php
$view = @$_GET["view"];

if($view=="login"){
include("views/login.php");
}

if($view=="dashboard"){
include("views/changepassword.php");
}

?>