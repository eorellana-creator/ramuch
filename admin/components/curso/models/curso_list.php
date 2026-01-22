<?php
@include("../../includes/sql_inyection.php");

echo "";


$mensaje = @$_SESSION["equipo_prestado"];

$_SESSION["equipo_prestado"] = "";

?>