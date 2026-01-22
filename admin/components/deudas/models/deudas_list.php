<?php
@include("../../includes/sql_inyection.php");

echo "";


$mensaje = @$_SESSION["deuda_mensaje"];


$_SESSION["deuda_mensaje"]="";

?>