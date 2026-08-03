<?php
$view = @$_GET["view"];

if($view == "" || $view == "equipo_list"){
    include("views/equipo_list.php");
}
?>
