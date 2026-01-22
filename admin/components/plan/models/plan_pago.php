<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];
$tokenPlanPago	= @$_GET["tokenPlanPago"];


$sql 	= $mysql->query("SELECT * FROM plan_matricula WHERE token ='$token' AND token!='' ;");
$result = $mysql->f_obj($sql);
$id_plan_matricula = $result->id_plan_matricula;

$sql7 	= $mysql->query("SELECT * FROM plan WHERE token ='$tokenPlanPago' AND token!='' ;");
$result7 = $mysql->f_obj($sql7);

$titulo = "Plan de Pago";
if($tokenPlanPago!=""   )
$titulo = "$result->nombre : $result7->nombre  ";

$actualizado = @$_SESSION["plan_actualizado"];

$_SESSION["plan_actualizado"] = "";

 
echo $actualizado;
 

?>