<?php
$view = @$_GET["view"];

if($view=="comision_prestamo"){
include("models/comision_prestamo.php");
include("views/comision_prestamo.php");
}



?>