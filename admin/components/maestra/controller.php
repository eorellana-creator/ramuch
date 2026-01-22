<?php
$view = @$_GET["view"];

if($view=="maestra_list"){
    include("models/maestra_list.php");
    include("views/maestra_list.php");
}




?>