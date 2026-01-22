<?php
@include("../../includes/sql_inyection.php");

echo "";


$subcuenta = @$_GET["view"];



$agregar_ingreso = "";
if($subcuenta!="all"){
  $eliminar = "-";
  $editar   = "-";
}else{
  $agregar_ingreso = "<a href='index.php?component=ingresos&amp;view=ingreso'><button type='button' class='btn btn-primary'><i class='fas fa-plus' aria-hidden='true'></i> Agregar Ingreso</button></a>";
}





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