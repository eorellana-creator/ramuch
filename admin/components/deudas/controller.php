<?php
$view = @$_GET["view"];

if($view=="deudas_list"){
    include("models/deudas_list.php");
    include("views/deudas_list.php");
}

if($view=="deuda"){
    include("models/deuda.php");
    include("views/deuda.php");
}

if($view=="deuda_condonar"){
    include("models/deuda_condonar.php");
    include("views/deuda_condonar.php");
}


if($view=="deuda_pagar"){
    include("models/deuda_pagar.php");
    include("views/deuda_pagar.php");
}



?>