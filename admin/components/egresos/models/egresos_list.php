<?php
@include("../../includes/sql_inyection.php");

echo "";


$subcuenta = @$_GET["view"];


switch ($subcuenta) {
    case "cuotas":
        $subcuenta = "cuota";
        break;
    case "cursos":
        $subcuenta = "curso";
        break;
    case "otros":
        $subcuenta = "otros";
        break;
    case "equipo":
        $subcuenta = "equipo";
        break;
    case "all":
        $subcuenta = "";
        break;
}


?>