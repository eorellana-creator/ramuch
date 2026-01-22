<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

 
$sql 	= $mysql->query("SELECT c.*, c.token as ctoken, u.* FROM comision_prestamo c INNER JOIN usuario u ON c.id_usuario=u.id_usuario ORDER BY u.nombre_usuario ;");
 
$comision = "";
while($result = $mysql->f_obj($sql)){

 
	$comision = $comision . "<tr> <td style='padding:4px; width:200px;'>$result->nombre_usuario</td>  <td style='padding:4px;width:250px;'>$result->email</td>  <td style='padding:4px;'><a href='javascript:sacar(\"$result->ctoken\");'><i class='fas fa-trash'></i></a></td> </tr>";
	
}//while($result2 = $mysql->f_obj($sql2))


$comision = "<table border='1px' cellpadding='1' cellspacing='0' >
<tr> <td style='padding:4px; width:200px;'>Integrante</td>  <td style='padding:4px;width:250px;'>Email</td>  <td style='padding:4px;'>Eliminar</td> </tr>
$comision</table>";


//************************************************************************************************************** */

$option_usuarios = "";
//Usuarios para el select de préstamos*************************************************************************
$sql21 	= $mysql->query("SELECT id_usuario, nombre_usuario FROM usuario WHERE estado ='Vigente' AND (web_matricula_pagada IS NULL OR web_matricula_pagada!='No')  ORDER BY nombre_usuario ASC ;");
  while($resultU = $mysql->f_obj($sql21)){


	$ya_agregado = 0;
	$sql5 	= $mysql->query("SELECT id_usuario FROM comision_prestamo WHERE id_usuario='$resultU->id_usuario' ;");
	$ya_agregado = $mysql->f_num($sql5);


	if($ya_agregado == 0){
    $resultU->nombre_usuario = str_replace("|","", $resultU->nombre_usuario);
    $option_usuarios = $option_usuarios . "<option value='$resultU->id_usuario' >$resultU->nombre_usuario</option>";
	}




  }//while($result = $mysql->f_obj($sql))

  $option_usuarios = "<option value='' selected >Agregar a...</option>".  $option_usuarios;

  $option_usuarios = "<select id='agregar' name='agregar' class='form-control sel2-basic-single'   >$option_usuarios</select>";

//********************************************************************************************************** */








?>