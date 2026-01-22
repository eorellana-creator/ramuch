<?php
$view = @$_GET["view"];

if($view=="user"){
include("models/user.php");
include("views/user.php");
}

if($view=="user_list"){
include("models/user_list.php");
include("views/user_list.php");
}

?>