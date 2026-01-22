<?php
$view = @$_GET["view"];

if($view=="plan"){
include("models/plan.php");
include("views/plan.php");
}

if($view=="plan_list"){
include("models/plan_list.php");
include("views/plan_list.php");
}

if($view=="plan_pago"){
include("models/plan_pago.php");
include("views/plan_pago.php");
}

?>