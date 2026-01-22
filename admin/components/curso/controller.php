<?php
$view = @$_GET["view"];

if($view=="curso_list"){
    include("models/curso_list.php");
    include("views/curso_list.php");
}

if($view=="curso"){
    include("models/curso.php");
    include("views/curso.php");
}


?>