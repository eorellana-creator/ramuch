<?php
$view = @$_GET["view"];

if($view=="company"){
include("models/company.php");
include("views/company.php");
}

?>