<?php
$view = @$_GET["view"];

if($view=="egresos"){
    include("models/egresos.php");
    include("views/egresos.php");
}



if($view=="all"){
    include("models/egresos_list.php");
    include("views/egresos_list.php");
}





?>