<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];

//echo "<br><br><br><br>SELECT * FROM cuenta_maestra WHERE token ='$token' AND token!='' ;";

$sql 	= $mysql->query("SELECT * FROM cuenta_maestra WHERE token ='$token' AND token!='' ;");
$result = $mysql->f_obj($sql);


if(@$result->documento_respaldo!=""){
$documento = " <br><a href='images/ingresos/$result->documento_respaldo' target='_blank'> <i class='fas fa-file-alt'></i> Ver documento</a> &nbsp; <a href='javascript:borrarDocumento(\"$result->token\");'><i class='fas fa-trash'></i> borrar documento</a>";
}else{
$documento = " ";
}

$medio = "<option value=''>Seleccionar medio de ingreso</option>";
if(@$result->medio!="")
$medio = "<option value='$result->medio'>$result->medio</option>";


$mensaje = @$_SESSION["ingreso_actualizado"];

$_SESSION["ingreso_actualizado"] = "";


$option_usuarios = "";
//Usuarios para el select *************************************************************************
$sql21 	= $mysql->query("SELECT id_usuario, nombre_usuario FROM usuario WHERE estado ='Vigente' ORDER BY nombre_usuario ASC ;");
  while($resultU = $mysql->f_obj($sql21)){
    $resultU->nombre_usuario = str_replace("|","", $resultU->nombre_usuario);
    $option_usuarios = $option_usuarios . "<option value='$resultU->id_usuario|$resultU->nombre_usuario' >$resultU->nombre_usuario</option>";
  }//while($result = $mysql->f_obj($sql))

  $option_deudor = " <option value='' selected >Nombre socio/a...</option>";
  if($token!=""){
      $option_deudor = "<option value='$result->id_usuario_deuda|$result->nombre_deudor' >$result->nombre_deudor</option>";
  }
  
    $option_usuarios = "
    <select class='usuarios-tags form-control' name='nombre' id='nombre'   style='width:100%;' >
    $option_deudor
    $option_usuarios
    </select>
    " ;

?>