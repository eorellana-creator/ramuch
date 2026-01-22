<?php
$view = @$_GET["view"];
 

if($view=="tutoriales" ){
include("models/tutoriales.php");
include("views/tutoriales.php");
}

?>