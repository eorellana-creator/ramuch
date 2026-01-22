<?php
$view = @$_GET["view"];

if($view=="equipo_solicitudes_list"){
    include("models/equipo_solicitudes_list.php");
    include("views/equipo_solicitudes_list.php");
}
 


?>