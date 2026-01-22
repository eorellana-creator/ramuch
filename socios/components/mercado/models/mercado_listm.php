<?php
@include("../../includes/sql_inyection.php");

echo "";

//kop
//$mensaje = @$_SESSION["equipo_prestado"];

//$_SESSION["equipo_prestado"] = "";

$mensaje = @$_SESSION["mercado"];

$_SESSION["mercado"] = "";

?>