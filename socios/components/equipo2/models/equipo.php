<?php
//@include("../../includes/sql_inyection.php");

$mysql->connect();

$token	= @$_GET["token"];


$sql 	= $mysql->query("SELECT * FROM equipo WHERE token ='$token' AND token!='' ;");
$result = $mysql->f_obj($sql);
$id_equipo = @$result->id_equipo;

if(@$result->imagen!=""){
$imagen = " <img src='images/equipo/$result->imagen' alt='' width='250'> ";
}else{
$imagen = " <img src='images/equipo_sin_imagen.jpg' alt='' width='90'> ";
}

$estado = "<option value=''>Seleccionar</option>";
if(@$result->estado!="")
$estado = "<option value='$result->estado'>$result->estado</option>";


$mensaje = @$_SESSION["equipo_actualizado"];

$_SESSION["equipo_actualizado"] = "";




//HISTORIAL******************************************************************* */
$div_historial = "";

$sql2 	= $mysql->query("SELECT * FROM equipo_prestamo WHERE id_equipo='$id_equipo' ORDER BY fecha_prestamo DESC ;");
while($result2 = $mysql->f_obj($sql2)){

    $fecha_prestamo     = "";
    $fecha_compromiso   = "";
    $fecha_devolucion   = "";


    $fecha_2    = strtotime($result2->fecha_devolucion_efectiva);
    $fecha_1    = strtotime($result2->fecha_debe_devolver);

    $color_fecha = "";
    if($fecha_2 > $fecha_1)
    $color_fecha = " style='color:#ff0000;' ";



    if($result2->fecha_prestamo>"0000-00-00")
    $fecha_prestamo = fecha_mysql_a_normal($result2->fecha_prestamo);

    if($result2->fecha_debe_devolver>"0000-00-00")
    $fecha_compromiso = fecha_mysql_a_normal($result2->fecha_debe_devolver);

    if($result2->fecha_devolucion_efectiva>"0000-00-00")
    $fecha_devolucion = fecha_mysql_a_normal($result2->fecha_devolucion_efectiva);

    $sql5 	= $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_prestamo'  ;");
    $result5 = $mysql->f_obj($sql5);
    $nombre_prestamo = @$result5->nombre_usuario;

    $sql6 	= $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_responsable'  ;");
    $result6 = $mysql->f_obj($sql6);
    $nombre_responsable = @$result6->nombre_usuario;

    $sql7 	= $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_recepciono'  ;");
    $result7 = $mysql->f_obj($sql7);
    $nombre_recepciono = @$result7->nombre_usuario;



    $div_historial = $div_historial . " <tr>
                                            <td>$fecha_prestamo</td>
                                            <td>$fecha_compromiso</td>
                                            <td $color_fecha>$fecha_devolucion</td>
                                            <td>$nombre_prestamo</td>
                                            <td>$nombre_responsable</td>
                                            <td>$nombre_recepciono</td>
                                            <td>$result2->comentario</td>
                                            <td>$result2->estado_devolucion</td>
                                        </tr>";


}//while



if($div_historial!=""){
    $div_historial = "<div id='divtablah'> 
                        <table class='blueTable'> 
                            <thead>
                                <tr>
                                    <th>Fecha Préstamo</th>
                                    <th>Fecha compromiso devolución</th>
                                    <th>Fecha efectiva de devolución</th>
                                    <th>Prestado a</th>
                                    <th>Responsable del Préstamo</th>
                                    <th>Recepcionó la devolución</th>
                                    <th>Observación</th>
                                    <th>Estado de la devolución</th>
                                </tr>
                            </thead>
                            <tbody>
                                $div_historial
                            </tbody>
                        </table>
                    </div>
";
}else{
    $div_historial = "<strong>Sin historial de préstamo.</strong>";
}



//ACTUALIZO 0 y 1 a estado 0= Con detalles 1= En buen estado************************************************************************ */
/*
$sql2 	= $mysql->query("SELECT * FROM equipo  ;");

while($result2 = $mysql->f_obj($sql2)){
    $estado_update = "";
    if($result2->estado=="0")
    $estado_update = "Con detalles";

    if($result2->estado=="1")
    $estado_update = "En buen estado";

    $token_nuevo = md5( rand(9999,9999999).$result2->id_equipo . $result2->nombre . date("Y-m-d h i s")   );

    if($result2->token=="")
    $sql4 	= $mysql->query("UPDATE equipo SET token ='$token_nuevo' WHERE id_equipo ='$result2->id_equipo' ;");

    if($estado_update!="")
    $sql4 	= $mysql->query("UPDATE equipo SET estado ='$estado_update' WHERE id_equipo ='$result2->id_equipo' ;");



}//while
*/

?>