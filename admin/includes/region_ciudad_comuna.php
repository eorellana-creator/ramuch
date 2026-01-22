<?php
include("sql_inyection.php");
include("conexionMysql.php");


$accion			= $_POST['accion'];
$valor			= $_POST['valor'];

$valor = explode("|",$valor);
$valor = $valor[0];

$valorregion			= @$_POST['valorregion'];
$valorprovincia			= @$_POST['valorciudad'];
$valorcomuna			= @$_POST['valorcomuna'];

//accion: 0=cargar regiones, 1=cargar provincias, 2=cargar comunas
//valor=id de region o provincia


$mysql 		= new mysql;
$mysql->connect();


//*************************************************************************************************
if($accion=="0"){
	
$sql 	= $mysql->query("SELECT region_id, region_nombre FROM region ORDER BY region_id ASC;");

$regiones = "";
while($result = $mysql->f_obj($sql)){
$selected = " ";
if($valorregion=="$result->region_id|$result->region_nombre")
$selected = " selected ";
$regiones = $regiones . "
<option value='$result->region_id|$result->region_nombre' $selected >$result->region_nombre</option>";

}//while($result = $mysql->f_obj($sql))

$regiones = "<select id='region' name='region'  required data-validation-required  onChange='seteaProvinciaComuna(\"1\",this.value);' class='form-control' >
<option value=''>Selecciona la Región</option>
".$regiones."
</select>";

//*************************************************************************************************
$provincias = "<select id='ciudad' name='ciudad'  style='color:#6d6c6c' required data-validation-required disabled  class='form-control'  ><option value=''>Antes selecciona la Región</option></select>";

$valor	= explode("|",$valorregion);
$valor	= $valor[0];
if($valorregion!="" && $valorprovincia!=""){
	
$sql 	= $mysql->query("SELECT provincia_id, provincia_nombre FROM provincia WHERE provincia_region_id='$valor' ORDER BY provincia_nombre ASC;");

$provincias = "";
while($result = $mysql->f_obj($sql)){
	
$selected = " ";
if($valorprovincia=="$result->provincia_id|$result->provincia_nombre")
$selected = " selected ";

$provincias = $provincias . "
<option value='$result->provincia_id|$result->provincia_nombre' $selected >$result->provincia_nombre</option>";

}//while($result = $mysql->f_obj($sql))

$provincias = "<select id='provincia' name='provincia'  required data-validation-required onChange='seteaProvinciaComuna(\"2\",this.value);' class='form-control'  >
<option value=''>Selecciona la Provincia/Ciudad</option>
".$provincias."
</select>";

}
//*************************************************************************************************
$comunas = "<select id='comuna' name='comuna' style='color:#6d6c6c' required data-validation-required disabled  class='form-control'  ><option value=''>Antes selecciona la Región</option></select> ";

$valor	= explode("|",$valorprovincia);
$valor	= $valor[0];
if($valorregion!="" && $valorprovincia!=""){
	
$sql 	= $mysql->query("SELECT comuna_id, comuna_nombre FROM comuna WHERE comuna_provincia_id='$valor' ORDER BY comuna_nombre ASC;");

$comunas = "";
while($result = $mysql->f_obj($sql)){
	
$selected = " ";
if($valorcomuna=="$result->comuna_id|$result->comuna_nombre")
$selected = " selected ";

$comunas = $comunas . "
<option value='$result->comuna_id|$result->comuna_nombre' $selected >$result->comuna_nombre</option>";

}//while($result = $mysql->f_obj($sql))

$comunas = "<select id='comuna' name='comuna'  required data-validation-required class='form-control'  >
<option value=''>Selecciona la Comuna</option>
".$comunas."
</select>";
	
}

//**************************************************************************************************


echo "xxx,
$regiones
xxx,
$provincias
xxx,
$comunas
xxx,";

}//if($accion=="0")

//*************************************************************************************************

if($accion=="1"){
	
$sql 	= $mysql->query("SELECT provincia_id, provincia_nombre FROM provincia WHERE provincia_region_id='$valor' ORDER BY provincia_nombre ASC;");

$provincias = "";
while($result = $mysql->f_obj($sql)){

$provincias = $provincias . "
<option value='$result->provincia_id|$result->provincia_nombre'>$result->provincia_nombre</option>";

}//while($result = $mysql->f_obj($sql))

echo "<select id='provincia' name='provincia'  required data-validation-required onChange='seteaProvinciaComuna(\"2\",this.value);' class='form-control'  >
<option value=''>Selecciona la Provincia/Ciudad</option>
".$provincias."
</select>";

}//if($accion=="1")

//*************************************************************************************************

if($accion=="2"){
	
$sql 	= $mysql->query("SELECT comuna_id, comuna_nombre FROM comuna WHERE comuna_provincia_id='$valor' ORDER BY comuna_nombre ASC;");

$comunas = "";
while($result = $mysql->f_obj($sql)){

$comunas = $comunas . "
<option value='$result->comuna_id|$result->comuna_nombre'>$result->comuna_nombre</option>";

}//while($result = $mysql->f_obj($sql))

echo "<select id='comuna' name='comuna'  required data-validation-required class='form-control'  >
<option value=''>Selecciona la Comuna</option>
".$comunas."
</select>";


}//if($accion=="2")

//*****************************************************************************************

if($accion=="load"){
	
}//if($accion=="load")


?>