<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];


$sql7 	= $mysql->query("SELECT * FROM plan_matricula WHERE token ='$token' AND token!='' ;");
$result7 = $mysql->f_obj($sql7);
 

$actualizado = @$_SESSION["plan_actualizado"];

$_SESSION["plan_actualizado"] = "";

 
echo $actualizado;
 

?>