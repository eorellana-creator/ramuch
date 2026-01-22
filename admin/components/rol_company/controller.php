<?php
$view = @$_GET["view"];

if($view=="rol_company"){
include("models/rol_company.php");
include("views/rol_company.php");
}

if($view=="rol_company_list"){
include("models/rol_company_list.php");
include("views/rol_company_list.php");
}

?>