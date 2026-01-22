<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];

//echo "<br><br><br><br>SELECT * FROM cuenta_maestra WHERE token ='$token' AND token!='' ;";

$sql 	= $mysql->query("SELECT * FROM cuenta_maestra WHERE token ='$token' AND token!='' ;");
$result = $mysql->f_obj($sql);


if(@$result->documento_respaldo!=""){
$documento = " <br><a href='images/egresos/$result->documento_respaldo' target='_blank'> <i class='fas fa-file-alt'></i> Ver documento</a> &nbsp; <a href='javascript:borrarDocumento(\"$result->token\");'><i class='fas fa-trash'></i> borrar documento</a>";
}else{
$documento = " ";
}

$medio = "<option value=''>Seleccionar medio de pago</option>";
if(@$result->medio!="")
$medio = "<option value='$result->medio'>$result->medio</option>";


$mensaje = @$_SESSION["egreso_actualizado"];

$_SESSION["egreso_actualizado"] = "";




?>