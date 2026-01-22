<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];


$sql 	= $mysql->query("SELECT * FROM curso WHERE token ='$token' AND token!='' ;");
$result = $mysql->f_obj($sql);
$id_curso = @$result->id_curso;



$tipo = "<option value=''>Seleccionar</option>";
if(@$result->tipo!=""){
$tipo = "<option value='$result->tipo'>$result->tipo</option>";
}else{
    $tipo = "
    <option value=''>Seleccionar</option>
    <option value='Curso'>Curso</option>
    <option value='Taller'>Taller</option>";
}


$mensaje = @$_SESSION["curso_actualizado"];

$_SESSION["curso_actualizado"] = "";




//PARTICIPANTES******************************************************************* */
$div_participantes= "";
$select_pago = "";
$cantidad_inscritos = 0;

$sql2 	= $mysql->query("SELECT * FROM curso_participantes WHERE id_curso='$id_curso' ORDER BY id_curso DESC ;");
while($result2 = $mysql->f_obj($sql2)){
    $cantidad_inscritos++;

    $result2->fecha_inscripcion = fecha_mysql_a_normal($result2->fecha_inscripcion);
    $result2->precio_a_pagar = number_format($result2->precio_a_pagar, 0, '', '.');



        if($result2->estado_pago=="Pendiente"){
        $eliminar = "<a href='javascript:eliminarParticipante(\"$result2->token\")'><i class='fas fa-trash'></i> Eliminar</a>";

    $select_pago = "<select id='pago$result2->id_curso_participantes' name='pago$result2->id_curso_participantes' class='form-control' style='margin-bottom:6px; width:70%; float:left; margin-right:4px;' >
                        <option value=''>Seleccionar Medio de Pago</option>
                        <option value='Efectivo'>Efectivo</option>
                        <option value='Transferencia'>Transferencia</option>
                        <option value='Cheque'>Cheque</option>
                    </select> <button type='button' class='btn btn-primary' style='margin-bottom:6px;' onClick='pagar(\"$result2->id_curso_participantes\",\"$result2->token\");' >Ingresar Pago</button>";



        }else{
        $eliminar = "<span data-toggle=\"tooltip\" data-placement=\"top\" title=\"No se puede eliminar a un participante que ha pagado.\"   ><i class='fas fa-question-circle' style='color:#707070;'  ></i></span>";      
        $select_pago = "<button type='button' class='btn btn-primary' style='margin-bottom:6px;' onClick='deshacerPago(\"$result2->token\");' >Deshacer Pago</button>";    
    }

    if($result2->precio_a_pagar ==0){
       $select_pago = "sin cobro"; 
       $eliminar = "<a href='javascript:eliminarParticipante(\"$result2->token\")'><i class='fas fa-trash'></i> Eliminar</a>";
    }
    


    $div_participantes = $div_participantes . " <tr>
                                            <td>$result2->nombre_participante</td>
                                            <td>$result2->fecha_inscripcion</td>
                                            <td >$result2->precio_a_pagar</td>
                                            <td>$result2->estado_pago</td>
                                            <td style='min-width:350px;'>$select_pago</td>
                                            <td>$result2->comentario</td>
                                            <td>$eliminar</td>
                                        </tr>";


}//while

$boton_inscribir = "";


if( $token!="" && ($cantidad_inscritos<=$result->capacidad) ){


    $boton_inscribir = "<br><button type='button' class='btn btn-primary' data-toggle='modal' data-target='#primaryModal' >Inscribir Participante</button>";

}


if( $token!="" && ($cantidad_inscritos  >= @$result->capacidad) )
$boton_inscribir = "<br>El Curso/Taller se encuentra en su capacidad máxima de participantes. Si deseas agregar más participantes, debes cambiar la capacidad de participantes.";




if($div_participantes!=""){

    $div_participantes = "<br><div id='divtablah'> 
                        <table class='blueTable'> 
                            <thead>
                                <tr>
                                    <th>Nombre Participante</th>
                                    <th>Fecha Inscripción</th>
                                    <th>Valor</th>
                                    <th>Estado del Pago</th>
                                    <th>Marcar como pagado</th>
                                    <th>Comentario</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                $div_participantes
                            </tbody>
                        </table>
                    </div>
";
}else{
    $div_participantes = "<strong>Sin participantes inscritos.</strong>";
}



//USUARIOS***************************************************************************************



$option_usuarios = "";
//Usuarios para el select *************************************************************************
$sql21 	= $mysql->query("SELECT id_usuario, nombre_usuario FROM usuario WHERE estado ='Vigente' ORDER BY nombre_usuario ASC ;");
  while($resultU = $mysql->f_obj($sql21)){
    $resultU->nombre_usuario = str_replace("|","", $resultU->nombre_usuario);
    $option_usuarios = $option_usuarios . "<option value='$resultU->id_usuario|$resultU->nombre_usuario' >$resultU->nombre_usuario</option>";
  }//while($result = $mysql->f_obj($sql))

  $option_usuarios = "
  <select class='usuarios-tags form-control' name='participanteNombre' id='participanteNombre'   style='width:100%;' >
  <option value='' selected >inscribir a...</option>
  $option_usuarios
  </select>
  " ;

//********************************************************************************************************** */

$readonly = "";

if($token!="")
$readonly = " readonly='readonly' tabindex='-1' aria-disabled='true' ";

?>