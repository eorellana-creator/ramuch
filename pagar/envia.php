<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

$rut 	      = @$_POST["rut"];
$token 	      = @$_POST["token"];
$rut = formatea_rut($rut);
$deuda = 0;
$tabla = "";
$mysql 		= new mysql;
$mysql->connect();

//Selecciono al usuario
$sql 					= $mysql->query("SELECT id_usuario, nombre, token FROM perfil WHERE rut='$rut' OR ( token='$token' AND token!='' );");
$result				= @$mysql->f_obj($sql);
$id_usuario   = @$result->id_usuario;
$nombre_usuario = @$result->nombre;
$token_usuario  = @$result->token;

//Selección tipo de inscripción
$sql 					= $mysql->query("SELECT tipo_inscripcion, id_plan_matricula FROM perfil WHERE id_usuario='$id_usuario' ;");
$result				= @$mysql->f_obj($sql);
$id_plan_matricula   = @$result->id_plan_matricula;

//Selecciono valor del pago semestral para los combos 
$sql 					= $mysql->query("SELECT valor, nombre, fecha_cierre1, fecha_cierre2 FROM plan WHERE id_plan_matricula='$id_plan_matricula' and periodo = 'semestral' and publico_privado='publico' ;");
$result				= @$mysql->f_obj($sql);
$valor_semestral   = @$result->valor;

// kop
$nombre_semestral   = @$result->nombre;
$fecha_cierre1m = @$result->fecha_cierre1; 
$fecha_cierre2m = @$result->fecha_cierre2;
// kop

// kop
//Selecciono valor del pago combo anual 
$sql          = $mysql->query("SELECT valor, nombre, fecha_cierre1 FROM plan WHERE id_plan_matricula='$id_plan_matricula' and periodo = 'anual' and publico_privado='publico' ;");
$result       = @$mysql->f_obj($sql);
$valor_anual   = @$result->valor;
$nombre_anual   = @$result->nombre;
$fecha_cierre1a = @$result->fecha_cierre1; 
// kop

function verifica_rango($date_inicio, $date_fin, $date_nueva) {
  $date_inicio = strtotime($date_inicio);
  $date_fin = strtotime($date_fin);
  $date_nueva = strtotime($date_nueva);
  if (($date_nueva >= $date_inicio) && ($date_nueva <= $date_fin))
    return true;
  return false;
}


$total_deuda = 0;

// consultar si confirmo su mail o no, si aun no lo hace no aparecera ninguna deura y se le envia un mensaje
$sqlconfirma 	= $mysql->query("SELECT * FROM usuario WHERE id_usuario='$id_usuario';");
$resultconfirma	= @$mysql->f_obj($sqlconfirma);
$confirmacorreo = @$resultconfirma->estado;


if( $confirmacorreo!="Por confirmar email" ){

    if( $id_usuario!="" ){
        $hoy = date("Y-m-d");
        $agno_actual    = date("Y");
        $aplicar_primer_combo  = 0;
        $aplicar_segundo_combo = 0;

        //Seleccion de deuda del usuario
        $sqlD 	= $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda='$id_usuario' AND fecha<='$hoy' AND estado='activa' ORDER BY fecha ASC ;");
        //$sqlD = $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda='$id_usuario' AND estado='activa' ORDER BY fecha ASC ;");

        $i=0;
        while($resultD = @$mysql->f_obj($sqlD)){
            $i++; 
            $resultD->fecha = fecha_mysql_a_normal($resultD->fecha);
            $total_deuda = $total_deuda + $resultD->monto ;
            $monto = number_format($resultD->monto, 0, '', '.');

            $tabla = $tabla . "<tr>
                                    <td><input type='checkbox' id='pago$i' name='pago$i' value='$resultD->token' class='form-control checkPagos checkCuota' onClick=\"actualizaTotalPago('pago$i',$resultD->monto);\"></td>
                                    <td>$resultD->fecha</td>
                                    <td>$resultD->glosa</td>
                                    <td style='text-align:right;'>$monto</td>
                                </tr>";
        }//while($resultD = @$mysql->f_obj($sqlD))

        // kop
        // traer la descripcion correcta desde la table, ya que esta fijo el "combo" primer y segundo semestre.
        // crear una nueva linea de la tabla si el combo anual esta activo o cualquier otro combo creado, esto esta todo fijo a 2 combos y no se mostrara nada mas.
        // asi que se debe mostrar todo lo que este como publico en la tabla de PLAN

        //Lógica de los combos**************************************
        
        $sqlS1	= $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-01-01' OR fecha='$agno_actual-02-01' OR fecha='$agno_actual-03-01' OR fecha='$agno_actual-04-01' OR fecha='$agno_actual-05-01' OR fecha='$agno_actual-06-01' ) ;");
        $aplicar_primer_combo = $mysql->f_num($sqlS1);

        $sqlS2	= $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-07-01' OR fecha='$agno_actual-08-01' OR fecha='$agno_actual-09-01' OR fecha='$agno_actual-10-01' OR fecha='$agno_actual-11-01' OR fecha='$agno_actual-12-01' ) ;");
        $aplicar_segundo_combo = $mysql->f_num($sqlS2);

        //kop
        // para agregar a la lista de deudas el combo anual automaticamente si debe los 12 meses.
        $sqlS2  = $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda='$id_usuario'  AND estado='activa' AND sub_cuenta='cuota' AND ( fecha='$agno_actual-01-01' OR fecha='$agno_actual-02-01' OR fecha='$agno_actual-03-01' OR fecha='$agno_actual-04-01' OR fecha='$agno_actual-05-01' OR fecha='$agno_actual-06-01' or fecha='$agno_actual-07-01' OR fecha='$agno_actual-08-01' OR fecha='$agno_actual-09-01' OR fecha='$agno_actual-10-01' OR fecha='$agno_actual-11-01' OR fecha='$agno_actual-12-01' ) ;");
        $aplicar_anual_combo = $mysql->f_num($sqlS2);
        //kop


        $hoy = date("Y-m-d");
        $agno_actual = date("Y");
        $fechaFormateada = date("d-m-Y", strtotime($hoy));

        //Si estamos en el Rango del Primer Semestre***********************************************************************************
        // se debe agregar la condicion si el combo esta activo como publico

        $formatted_fecha_cierre1 = !empty($fecha_cierre1m) ? date("-m-d", strtotime($fecha_cierre1m)) : null;

       // Verificar si tiene deudas de meses anteriores al primer semestre (años anteriores o meses anteriores a enero)
        $sql_deuda_anterior_primer_sem = $mysql->query("SELECT COUNT(*) as total FROM deudas 
                                      WHERE id_usuario_deuda='$id_usuario' 
                                      AND estado='activa' 
                                      AND (YEAR(fecha) < '$agno_actual' OR 
                                          (YEAR(fecha) = '$agno_actual' AND MONTH(fecha) < 1))");

        $result_deuda_anterior_primer_sem = @$mysql->f_obj($sql_deuda_anterior_primer_sem);
        $tiene_deuda_anterior_primer_sem = ($result_deuda_anterior_primer_sem->total > 0);

        if( verifica_rango(date("Y")."-01-01",date("Y").$formatted_fecha_cierre1,"$hoy") ){
            if($valor_semestral>0){
                if($aplicar_primer_combo==6 && !$tiene_deuda_anterior_primer_sem){
                    $texto_valor_semestral = number_format($valor_semestral, 0, '', '.');
                    $tabla = $tabla . "<tr>
                        <td><input type='checkbox' id='semestre1' name='semestre1' value='semestre1$agno_actual' class='form-control checkPagos' onClick=\"actualizaTotalPago('semestre1',$valor_semestral); marcaCombo($valor_semestral);\"></td>
                        <td><strong>$fechaFormateada</strong></td>
                        <td>$nombre_semestral $agno_actual $nombre_usuario</td>
                        <td style='text-align:right;'>$texto_valor_semestral</td>
                    </tr>";
                }
            }
        }//if(  verifica_rango("","","") ){
        //Fin Rango Primer semestre*****************************************************************************************************


        $formatted_fecha_cierre2 = !empty($fecha_cierre2m) ? date("-m-d", strtotime($fecha_cierre2m)) : null;

        // Verificar si tiene deudas de meses anteriores al segundo semestre (antes de julio del año actual)
        $sql_deuda_anterior_segundo_sem = $mysql->query("SELECT COUNT(*) as total FROM deudas 
                                        WHERE id_usuario_deuda='$id_usuario' 
                                        AND estado='activa' 
                                        AND (YEAR(fecha) < '$agno_actual' OR 
                                            (YEAR(fecha) = '$agno_actual' AND MONTH(fecha) < 7))");

        $result_deuda_anterior_segundo_sem = @$mysql->f_obj($sql_deuda_anterior_segundo_sem);
        $tiene_deuda_anterior_segundo_sem = ($result_deuda_anterior_segundo_sem->total > 0);

        // Mensaje de diagnóstico
        /*
        $debug_msg = "<div style='background:#f8f8f8; border:1px solid #ddd; padding:10px; margin:10px 0; font-family:monospace;'>";
        $debug_msg .= "<strong>DEBUG INFO - Segundo Semestre</strong><br>";
        $debug_msg .= "Fecha cierre 2: ".($fecha_cierre2m ?? 'NULL')."<br>";
        $debug_msg .= "Formatted fecha cierre 2: ".($formatted_fecha_cierre2 ?? 'NULL')."<br>";
        $debug_msg .= "Hoy: $hoy<br>";
        $debug_msg .= "Rango verificado: ".date("Y")."-07-01 a ".date("Y").$formatted_fecha_cierre2."<br>";
        $debug_msg .= "Verifica rango: ".(verifica_rango(date("Y")."-07-01",date("Y").$formatted_fecha_cierre2,"$hoy") ? 'TRUE' : 'FALSE')."<br>";
        $debug_msg .= "Valor semestral: $valor_semestral<br>";
        $debug_msg .= "Aplicar segundo combo: $aplicar_segundo_combo (necesita 6)<br>";
        $debug_msg .= "Tiene deuda anterior: ".($tiene_deuda_anterior_segundo_sem ? 'SÍ' : 'NO')."<br>";
        $debug_msg .= "Total deudas encontradas: ".$result_deuda_anterior_segundo_sem->total."<br>";
        $debug_msg .= "</div>";
        
        // Mostrar el mensaje de diagnóstico (puedes comentar esta línea en producción)
        $tabla = $tabla . $debug_msg;
        */
        if( verifica_rango(date("Y")."-07-01",date("Y").$formatted_fecha_cierre2,"$hoy") ){
            if($valor_semestral>0){
                if($aplicar_segundo_combo==6 && !$tiene_deuda_anterior_segundo_sem){
                    $texto_valor_semestral = number_format($valor_semestral, 0, '', '.');
                    $tabla = $tabla . "<tr>
                        <td><input type='checkbox' id='semestre2' name='semestre2' value='semestre2$agno_actual' class='form-control checkPagos' onClick=\"actualizaTotalPago('semestre2',$valor_semestral); marcaCombo($valor_semestral);\"></td>
                        <td><strong>$fechaFormateada</strong></td>
                        <td>$nombre_semestral $agno_actual $nombre_usuario</td>
                        <td style='text-align:right;'>$texto_valor_semestral</td>
                    </tr>";
                }
            }
        }        
/*
        //Si estamos en el Rango del Segundo Semestre***********************************************************************************
        // se debe agregar la condicion si el combo esta activo como publico
        $formatted_fecha_cierre2 = !empty($fecha_cierre2m) ? date("-m-d", strtotime($fecha_cierre2m)) : null;

        // Verificar si tiene deudas de meses anteriores al segundo semestre (antes de julio del año actual)
        $sql_deuda_anterior_segundo_sem = $mysql->query("SELECT COUNT(*) as total FROM deudas 
                                     WHERE id_usuario_deuda='$id_usuario' 
                                     AND estado='activa' 
                                     AND (YEAR(fecha) < '$agno_actual' OR 
                                         (YEAR(fecha) = '$agno_actual' AND MONTH(fecha) < 7))");

        $result_deuda_anterior_segundo_sem = @$mysql->f_obj($sql_deuda_anterior_segundo_sem);
        $tiene_deuda_anterior_segundo_sem = ($result_deuda_anterior_segundo_sem->total > 0);

        if( verifica_rango(date("Y")."-07-01",date("Y").$formatted_fecha_cierre2,"$hoy") ){
            if($valor_semestral>0){
                if($aplicar_segundo_combo==6 && !$tiene_deuda_anterior_segundo_sem){
                    $texto_valor_semestral = number_format($valor_semestral, 0, '', '.');
                    $tabla = $tabla . "<tr>
                        <td><input type='checkbox' id='semestre2' name='semestre2' value='semestre2$agno_actual' class='form-control checkPagos' onClick=\"actualizaTotalPago('semestre2',$valor_semestral); marcaCombo($valor_semestral);\"></td>
                        <td><strong>$fechaFormateada</strong></td>
                        <td>$nombre_semestral $agno_actual $nombre_usuario</td>
                        <td style='text-align:right;'>$texto_valor_semestral</td>
                    </tr>";
                }
            }
        }//if(  verifica_rango("","","") ){
        //Fin Rango del Segundo Semestre***********************************************************************************
*/
        //kop
        //Si esta activo el combo anual ***********************************************************************************
        // se debe agregar la condicion si el combo esta activo como publico
        $formatted_fecha_cierre1a = !empty($fecha_cierre1a) ? date("-m-d", strtotime($fecha_cierre1a)) : null;

        // si tiene deuda anterior al año actual no aparecera el combo anual
        // buscar si tiene una deuda del año anterior al presente.
        $agno_anterior = date("Y") - 1;
        $sql_deuda_anterior = $mysql->query("SELECT COUNT(*) as total FROM deudas 
                                        WHERE id_usuario_deuda='$id_usuario' 
                                        AND estado='activa' 
                                        AND YEAR(fecha) = '$agno_anterior'");
        $result_deuda_anterior = @$mysql->f_obj($sql_deuda_anterior);
        $tiene_deuda_anterior = ($result_deuda_anterior->total > 0);
        
        if( verifica_rango(date("Y")."-01-01",date("Y").$formatted_fecha_cierre1a,"$hoy") ){
            if($valor_anual>0){
                if($aplicar_anual_combo==12 && !$tiene_deuda_anterior){
                    $texto_valor_semestral = number_format($valor_anual, 0, '', '.');
                    $tabla = $tabla . "<tr>
                        <td><input type='checkbox' id='semestre1semestre2' name='semestre1semestre2' value='semestre1semestre2$agno_actual' class='form-control checkPagos' onClick=\"actualizaTotalPago('semestre1semestre2',$valor_anual); marcaCombo($valor_anual);\"></td>
                        <td><strong>$fechaFormateada</strong></td>
                        <td>$nombre_anual $agno_actual $nombre_usuario</td>
                        <td style='text-align:right;'>$texto_valor_semestral</td>
                    </tr>";
                }
            }
        }
        //kop

        //fin lógica combos***************************************

        $tabla = "<table class='blueTable'>
        <thead>
        <tr>
        <th style='width:25px'><input type='checkbox' id='selectAll' name='selectAll' value='0' class='form-control' onClick='seleccionarTodo();' ></th>
        <th>Fecha</th>
        <th>Motivo</th>
        <th style='width:60px; text-align:right;'>Valor</th>
        </tr>
        </thead>

        <tbody>
        $tabla

        <tr>
        <td></td>
        <td></td>
        <td style='text-align:right;'><strong>Total</strong></td>
        <td style='text-align:right;'><div id='total-pagar'>$0</div></td>
        </tr>

        </table>
        <input id='totalDeuda' name='totalDeuda' type='hidden' value='$total_deuda' >
        <input id='ultimocheck' name='ultimocheck' type='hidden' value='$i' >
        <br>
            <div style='font-size:12px; color:#ff6d26;'>Si presentas alguna discrepancia con los valores tu deuda, escríbenos a directiva@ramuch.cl explicando la situación.</div>
        <br>
        <button id='botonPago' class='btn btn-block btn-success' disabled onClick='pagarFlow();' type='button'> Pagar </button>
        ";

        echo "|$tabla|";


    }else{
    $tabla = "<h2>El Rut indicado<br>no presenta deudas.</h2>";
    }//if( $id_usuario!="" )

}else{
$tabla = "<h3><strong>Debes confirmar el link que se envio a tu correo antes de pagar.</strong></h3>";
}//if( $confirmacorreo!="Por confirmar email" )


echo "|$tabla|";



?>