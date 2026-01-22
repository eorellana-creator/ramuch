<?php
$view = @$_GET["view"];

if($view=="equipo_list"){
    include("models/equipo_list.php");
    include("views/equipo_list.php");
}

if($view=="equipo"){
    include("models/equipo.php");
    include("views/equipo.php");
}


?>