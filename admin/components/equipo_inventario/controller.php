<?php
$view = @$_GET["view"];

if($view == "" || $view == "inventario"){
    include("views/inventario.php");
}
?>
