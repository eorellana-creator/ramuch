<?php
$view = @$_GET["view"];

// kop revisar si aqui es la diferecia entre un admin y un socio

if($view=="mercado"){
    include("models/mercado_listm.php");
    include("views/mercado_listv.php");
}

/*if($view=="equipo"){
    include("models/equipo.php");
    include("views/equipo.php");
}
*/


?>