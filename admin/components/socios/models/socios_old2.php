<?php
@include("../../includes/sql_inyection.php");

ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

$mysql->connect();

$token	= @$_GET["token"];

$sql7 	= $mysql->query("SELECT * FROM usuario WHERE token ='$token' AND token!='' ;");
$result7 = $mysql->f_obj($sql7);
$id_usuario = @$result7->id_usuario;



$sql5 	= $mysql->query("SELECT * FROM perfil WHERE id_usuario = '$id_usuario' AND id_usuario!='' ;");
$result5 = $mysql->f_obj($sql5);

$imagen_perfil = "images/icono-300.png";
if(@$result5->img_perfil!="")
$imagen_perfil = "images/img_perfil/$result5->img_perfil";

$id_plan_matricula = @$result5->id_plan_matricula;

$certificado = "";
if(@$result5->certificado_estudios!="")
$certificado = "<a href='components/socios/archivos/$result5->certificado_estudios' target='_blank'><i class='fas fa-search-plus'></i> ver certificado actual</a>";

$estado_estudios = "<option value='' selected >Seleccionar</option>";

switch(@$result5->estado_estudios){
    case 0:
        $estado_estudios = "<option value='0' selected >Cursando</option>";
        break;
    case 1:
        $estado_estudios = "<option value='1' selected >Congelado</option>";
        break;
    case 2:
        $estado_estudios = "<option value='2' selected >Egresado</option>";
        break;
    case 3:
        $estado_estudios = "<option value='3' selected >Titulado</option>";
        break;
    case 99:
        $estado_estudios = "<option value='99' selected >No informado</option>";
        break;
}

$anos_cursados = "<option value='0' selected >0</option>";
if(@$result5->anos_cursados >0 && @$result5->anos_cursados<15)
$anos_cursados = "<option value='$result5->anos_cursados' selected >$result5->anos_cursados</option>";

$anos_carrera = "<option value='0' selected >0</option>";
if(@$result5->anos_carrera >0 && @$result5->anos_carrera<15)
$anos_carrera = "<option value='$result5->anos_carrera' selected >$result5->anos_carrera</option>";


$tipo_sangre = "<option value=''>Seleccionar</option>";
if(@$result5->tipo_sangre!="")
$tipo_sangre = "<option value='$result5->tipo_sangre' selected >$result5->tipo_sangre</option>";



$donante = "<option value='0' selected >No</option><option value='1' >Si</option>";
if(@$result5->donante=="1")
$donante = "<option value='1' selected >Si</option> <option value='0' >No</option>";


$tipo_inscripcion = "";

switch(@$result5->tipo_inscripcion){
  case 1:
    $tipo_inscripcion = "Profesional";
    break;
  case 3:
    $tipo_inscripcion = "Estudiante";
    break;
  case 6:
    $tipo_inscripcion = "Congelado";
    break;
  case 2:
    $tipo_inscripcion = "Honorario";
    break;
  case 7:
    $tipo_inscripcion = "Desvinculado";
    break;

  case 8:
    $tipo_inscripcion = "Eliminado";
    break;
}//switch

if($tipo_inscripcion!="")
$tipo_inscripcion = ": $tipo_inscripcion";


//PAGOS*****************************************************************************************************************
$datos_pagos = "";
//$sql 	= $mysql->query("SELECT * FROM cuenta_maestra WHERE id_usuario_movimiento = '$id_usuario' AND tipo='ingreso' AND estado='activo' ORDER BY id_cuenta_maestra DESC ;");
$total_pagos = 0;

$sql = $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda = '$id_usuario' AND estado = 'pagada' ORDER BY id_deuda DESC ;");

// DEBUG: Verificar consulta de deudas
error_log("DEBUG - Consulta deudas para usuario $id_usuario: " . print_r($mysql->query("EXPLAIN SELECT * FROM deudas WHERE id_usuario_deuda = '$id_usuario' AND estado = 'pagada'"), true));

if($sql) {
    while($result = $mysql->f_obj($sql)){
        if($result && property_exists($result, 'fecha')) {
            $fecha = date("d-m-Y", strtotime($result->fecha));
            $total_pagos = $total_pagos + $result->monto;
            $valor = $result->monto;
            $valor = number_format($valor, 0, '', '.');
            $medio_pago = "";
            $fecha_orden  =  strtotime($result->fecha);
            $medio = $result->observacion . " : ". @ $result->documento_respaldo;
            $descripcion_pago = @$result->sub_cuenta . " : ". @$result->glosa ;

            // para buscar la fecha real que pago por flow
            $documento_flow = $result->documento_respaldo;
            $sqlxx 	= $mysql->query("SELECT * FROM flow WHERE flow_order = '$documento_flow' AND flow_status = 2 ;");
            $resultxx = $mysql->f_obj($sqlxx);
            $fechaxx = date("d-m-Y", strtotime($resultxx->fecha));
            $medio = $result->observacion . " : ". @ $result->documento_respaldo . " : ". $fechaxx;

            $datos_pagos = $datos_pagos . " <tr>
                                                <td>$result->id_deuda</td>
                                                <td data-sort='$fecha_orden'>$fecha</td>
                                                <td>$medio</td>
                                                <td>$descripcion_pago</td>
                                                <td>$valor</td>
                                            </tr>";
        }
    }//while
}

$total_pagos =  number_format($total_pagos, 0, '', '.');

//FIN PAGOS*****************************************************************************************************************

//DEUDAS*****************************************************************************************************************
$datos_deudas   = "";
$observacion    = "";
$hoy            = date("Y-m-d");

$sql 	= $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda = '$id_usuario' AND fecha<='$hoy' AND (estado = 'activa' ) ORDER BY id_deuda DESC ;");
$total_deudas = 0;
$deudas_efectivas = 0;


while($result = $mysql->f_obj($sql)){
   $fecha = date("d-m-Y", strtotime($result->fecha));
   //$fecha =$result->fecha;
    if($result->estado == "condonada"){
        $observacion = "Condonada - $result->observacion";
    }

    if($result->estado == "activa"){
        $deudas_efectivas = $deudas_efectivas + $result->monto;
        $total_deudas = $total_deudas + $result->monto;
    }

    $valor = $result->monto;
    $valor = number_format($valor, 0, '', '.');
    $tipo_pago = @$result->sub_cuenta;
    $fecha_orden  =  strtotime($result->fecha);

    $obs = $result->observacion;

    //busca en tabla usuario para saber el tipo de inscripcion
    $sqlx 	= $mysql->query("SELECT * FROM perfil WHERE id_usuario = '$id_usuario';");
    $resultx = $mysql->f_obj($sqlx);
    
    if ($resultx->tipo_inscripcion == 1) {
        $obs .= " Profesional ";
    } else {
        $obs = "";
    }
    
    if ($resultx->tipo_inscripcion == 3) {
        $obs .= " Estudiante ";
    } else {
        $obs = "";
    }
       
    $datos_deudas = $datos_deudas . " <tr>
                                        <td>$result->id_deuda</td>
                                        <td data-sort='$fecha_orden' >$fecha</td>
                                        <td>$result->glosa</td>
                                        <td>$obs</td>
                                        <td>$valor</td>
                                      </tr>";
}//while

$total_deudas =  number_format($total_deudas, 0, '', '.');

$texto_deudas = "Sin deuda";

if($deudas_efectivas > 0)
$texto_deudas = "Con deuda";

$deudas_efectivas =  number_format($deudas_efectivas, 0, '', '.');


//*************************************************************************************************************** */

$sql21 	= $mysql->query("SELECT * FROM plan_matricula WHERE activa = '1' ORDER BY nombre ASC ;");
$select_inscripcion = "";
$seleccionar_inscripcion = "<option value='' selected >Seleccionar</option>";

while($result21 = $mysql->f_obj($sql21)){

            $selected = "";

            if($result21->id_plan_matricula == $id_plan_matricula){
            $seleccionar_inscripcion = "";
            $selected = " selected ";
            }

            $select_inscripcion = $select_inscripcion ."<option value='$result21->id_plan_matricula' $selected >$result21->nombre</option>";
}//while

  


$select_inscripcion = "<select id='tipoInscripcion' name='tipoInscripcion' class='form-control' >
                        $seleccionar_inscripcion
                        $select_inscripcion
                        </select>";



if($token==""){
    $select_inscripcion_nuevo  = $select_inscripcion;
    $oculta_inscripcion_primera_vez = "
    <div style='width:100%;height:1px; margin-top:20px; '></div>
                    <h4>Tipo de Inscripción</h4>
                    <div class='linea-horizontal'></div>
                    <div class='col-lg-4 form-group'> 
                        <label class='col-form-label'>Tipo de Inscripción:</label>
                       $select_inscripcion_nuevo
                    </div>
    ";
    $select_inscripcion = "";
}else{
    $oculta_inscripcion_primera_vez = " ";
    $select_inscripcion_nuevo  = "";
}



//PRÉSTAMOS DE EQUIPO*****************************************************************************************************************
 
$datos_prestamo = "";

$sql2 	= $mysql->query("SELECT * FROM equipo_prestamo WHERE id_usuario_prestamo='$id_usuario' ORDER BY fecha_prestamo DESC ;");
while($result2 = $mysql->f_obj($sql2)){

                $sql12 	= $mysql->query("SELECT * FROM equipo WHERE id_equipo ='$result2->id_equipo' ;");
                $result12 = $mysql->f_obj($sql12);
                $id_equipo = @$result12->id_equipo;
                $nombre_equipo = @$result12->nombre;
                
                if(@$result12->imagen!=""){
                $imagen_equipo = " <img src='images/equipo/$result12->imagen' alt='' width='90'> ";
                }else{
                $imagen_equipo = " <img src='images/equipo_sin_imagen.jpg' alt='' width='90'> ";
                }


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

    /*
    $sql5 	= $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_prestamo'  ;");
    $result5 = $mysql->f_obj($sql5);
    $nombre_prestamo = @$result5->nombre_usuario;
    */

    $sql6 	= $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_responsable'  ;");
    $result6 = $mysql->f_obj($sql6);
    $nombre_responsable = @$result6->nombre_usuario;

    $sql7 	= $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_recepciono'  ;");
    $result7 = $mysql->f_obj($sql7);
    $nombre_recepciono = @$result7->nombre_usuario;


    if( $result2->estado == "solicitado")
    $result2->estado = "<span style='color:#ff0000;'>Solicitud en trámite</span>";



    $datos_prestamo = $datos_prestamo . " <tr>
                                            <td>$imagen_equipo</td>
                                            <td>$nombre_equipo</td>
                                            <td>$fecha_prestamo</td>
                                            <td>$fecha_compromiso</td>
                                            <td $color_fecha>$fecha_devolucion</td>
                                            <td>$nombre_prestamo</td>
                                            <td>$result2->comentario</td>
                                            <td>$result2->estado_devolucion</td>
                                            <td>$result2->estado</td>
                                        </tr>";


}//while



//*************************************************************************************************************** */


/*
Esto para  hacer la homologación de tablas usuario->perfil


-> esto solo si deseamos limpiar --->>>>>  $sql3 	= $mysql->query("DELETE FROM usuario WHERE id_usuario>1 ");

$sql 	= $mysql->query("SELECT * FROM perfil WHERE id_usuario='0';");

while($result = $mysql->f_obj($sql)){

$token = md5( rand(9999,999999999) . date("y-m-d-h-i-s") . rand(55,55555) . $result->id_perfil );

if($result->fecha_ingreso=="")
$result->fecha_ingreso = "2000-01-01";

echo "INSERT INTO usuario (id_company, id_rol, nombre_usuario, email, password, fecha_registro, fecha_actualizacion, estado, token ) VALUES ('1','3','$result->nombre','$result->mail','$token','$result->fecha_ingreso','2022-04-30','Vigente','$token') <br>";

    $sql2 	= $mysql->query("INSERT INTO usuario (id_company, id_rol, nombre_usuario, email, password, fecha_registro, fecha_actualizacion, estado, token ) VALUES ('1','3','$result->nombre','$result->mail','$token','$result->fecha_ingreso','2022-04-30','Vigente','$token') ");
    $ultimo_id = $mysql->ultimo_id();

    $sql3 	= $mysql->query("UPDATE perfil SET  id_usuario='$ultimo_id', id_plan_matricula='$result->tipo_inscripcion' WHERE id_perfil='$result->id_perfil' ");
    


}//while($result = $mysql->f_obj($sql))



*/

/*
Esto para homologar tablas deudas y pagos

Para deudas: 

$sql 	= $mysql->query("SELECT id_perfil FROM deudas ;");

    while($result = $mysql->f_obj($sql)){

        $sql2 	= $mysql->query("SELECT id_usuario FROM perfil WHERE id_perfil='$result->id_perfil' ;");
        $result2 = $mysql->f_obj($sql2);

        if($result2->id_usuario!="")
        $sql3 	= $mysql->query("UPDATE deudas SET id_usuario ='$result2->id_usuario' WHERE id_perfil='$result->id_perfil' ");





    }//while($result = $mysql->f_obj($sql))


    Para Pagos:


$sql 	= $mysql->query("SELECT id_perfil FROM pagos ;");

    while($result = $mysql->f_obj($sql)){

        $sql2 	= $mysql->query("SELECT id_usuario FROM perfil WHERE id_perfil='$result->id_perfil' ;");
        $result2 = $mysql->f_obj($sql2);

        if($result2->id_usuario!="")
        $sql3 	= $mysql->query("UPDATE pagos SET id_usuario ='$result2->id_usuario' WHERE id_perfil='$result->id_perfil' ");





    }//while($result = $mysql->f_obj($sql))


*/


$actualizado = @$_SESSION["socio_actualizado"];


$_SESSION["socio_actualizado"] = "";

$go_tab_pass = "";
if( @$_GET["tab"] == "pass" )
$go_tab_pass = "

<script>

goTabPass();
 
</script>
";



$oculta_tabs            = "";
$campos_password        = "";
$campos_password_nuevos = "";

if($token==""){
$oculta_tabs = " style='display:none;' ";
$campos_password_nuevos    = '

<div style="width:100%;height:1px; margin-top:20px;"></div>
                    <h4>Contraseña de acceso</h4>
                    <div class="linea-horizontal"></div>


<div class="col-sm-10">

<div class="row">
    

        <div class="col-lg-4 form-group"> 
            <label class="col-form-label">Ingresar Contraseña</label>
            <input class="form-control pr-password" id="password" name="password" type="password" placeholder="Ingresar Password...">
        </div>

        <div class="col-lg-4 form-group"> 
            <label class="col-form-label">Re-Ingresar Contraseña</label>
            <input class="form-control pr-password" id="password2" name="password2" type="password"placeholder="Re-Ingresar Password...">
        </div>

        <div class="col-lg-12 form-group"> 
        <div class="error-pass"></div>
        La contraseña debe contener al menos 1 número, 1 letra mayúscula, una letra minúscula y un mínimo de 8 caracteres.
        </div>



</div>


</div>
';

}else{
    $campos_password    = '
    <div class="col-sm-10">
    
    <div class="row">
        
    
            <div class="col-lg-4 form-group"> 
                <label class="col-form-label">Ingresar Contraseña</label>
                <input class="form-control pr-password" id="password" name="password" type="password" placeholder="Ingresar Password...">
            </div>
    
            <div class="col-lg-4 form-group"> 
                <label class="col-form-label">Re-Ingresar Contraseña</label>
                <input class="form-control pr-password" id="password2" name="password2" type="password"placeholder="Re-Ingresar Password...">
            </div>
    
            <div class="col-lg-12 form-group"> 
            <div class="error-pass"></div>
            La contraseña debe contener al menos 1 número, 1 letra mayúscula, una letra minúscula y un mínimo de 8 caracteres.
            </div>
    
    
    
    </div>
    
    
    </div>
    ';

}
 

?>