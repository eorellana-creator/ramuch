<?php
$view = @$_GET["view"];

if($view=="ingresos"){
    include("models/ingresos_list.php");
    include("views/ingresos_list.php");
}

if($view=="cuotas"){
    include("models/ingresos_list.php");
    include("views/ingresos_list.php");
}

if($view=="cursos"){
    include("models/ingresos_list.php");
    include("views/ingresos_list.php");
}

if($view=="otros"){
    include("models/ingresos_list.php");
    include("views/ingresos_list.php");
}

if($view=="all"){
    include("models/ingresos_list.php");
    include("views/ingresos_list.php");
}



if($view=="ingreso"){
    include("models/ingreso.php");
    include("views/ingreso.php");
}

?>