<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$id_company = $_SESSION["company_id"];

$sql 	= $mysql->query("SELECT * FROM company WHERE id_company='$id_company';");
$result = $mysql->f_obj($sql);

$id_company = @$result->id_company;

if(@$result->logo!=""){
$imagen = "<img src='images/company/$result->logo' height='90'>";
$imagen_requerida = "";
}else{
$imagen = "<img src='images/sin_imagen.jpg' height='90'>";
$imagen_requerida = " required ";
}


?>