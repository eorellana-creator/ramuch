<?php
session_start();
set_time_limit(180);
ini_set('max_execution_time', 180);

include("../../includes/sql_inyection_salto_textarea.php");
include("../../configuration.php");
include("../../includes/conexionMysql.php");
include("../../includes/funciones.php");



$token				= @$_GET['token'];


$config 	= new Config;

date_default_timezone_set("$config->zona_horaria");

$mysql 		= new mysql;
$mysql->connect();


$agno_inicial	= date("Y");
$mes_inicial	= "1";
$dia_inicial	= "1";

$hoy 			= date("Y-m-d");

$hoy_agno		= date("Y");
$hoy_mes		= date("n");//mes sin cero inicial
$hoy_dia		= date("j");//dia sin cero inicial


//*********************************************************** */

$agno_inicial	= date("Y");
$mes_inicial	= date("n");
$dia_inicial	= date("j");

$hoy 			= date("Y-m-d");

$hoy_agno		= date("Y");
$hoy_mes		= date("n");//mes sin cero inicial
$hoy_dia		= date("j");//dia sin cero inicial

//*********************************************************** */

$mes_final = 12;

$sql 			= $mysql->query("SELECT * FROM usuario WHERE estado='Vigente' ;");
$n=0;
while($result = $mysql->f_obj($sql)){
	$n++;
		$id_usuario 	= $result->id_usuario;
		$nombre			= $result->nombre_usuario;
		$fecha_ingreso 	= $result->fecha_registro;

	//Solo se hace para los Estudiantes y Profesionales, los demás no acumulan cuotas de mensualidades
	$sqlP 			= $mysql->query("SELECT * FROM perfil WHERE id_usuario='$id_usuario' AND (tipo_inscripcion='1' OR tipo_inscripcion='3') ;");
	$verificar 		= $mysql->f_num($sqlP);

	if($verificar>0){
					//Consultar cual es el valor cuota del usuario
					$sqlC 				= $mysql->query("SELECT id_plan_matricula FROM perfil WHERE id_usuario='$id_usuario'  ;");
					$resultC 			= $mysql->f_obj($sqlC);
					$id_plan_matricula 	= $resultC->id_plan_matricula;

					$sqlP 				= $mysql->query("SELECT valor FROM plan WHERE id_plan_matricula='$id_plan_matricula' AND periodo='mensual'  ;");
					$resultP 			= $mysql->f_obj($sqlP);
					$valor_cuota 		= $resultP->valor;

					//Recorrer mes a mes: consultar si está ingresada la deuda:
						//Recorrer años
						for($agno = $hoy_agno; $agno<=$hoy_agno; $agno++){

							//Recorrer los meses
							for($mes=$mes_inicial; $mes<=$mes_final;$mes++){

										//Se comenta porque siempre se calcularán las cuotas hasta fin de año
										//if(  ($agno ==$hoy_agno) && ($mes==$hoy_mes)   ) //para parar en el mes actual el año en curso
										//$mes_final=$hoy_mes;
										
										//Consultar si la deuda ya fue ingresada y su estado es activa, pagada, caducada o condonada:
										$existe = 0;
										$fecha_consulta = "$agno-$mes-01";
										$fecha_consulta_20  = "$agno-$mes-20";

										$sql3 			= $mysql->query("SELECT * FROM deudas WHERE id_usuario_deuda = '$id_usuario' AND fecha = '$fecha_consulta' AND sub_cuenta='cuota' AND ( estado='activa' OR estado='pagada' OR estado='condonada' OR estado='caducada'  OR estado='eliminada' ) ;");
										$existe        	= $mysql->f_num($sql3);

										$existep = $existe;

										$fecha_ingreso_time 	= strtotime($fecha_ingreso);
										$fecha_consulta_time 	= strtotime($fecha_consulta);
										$fecha_consulta_20_time = strtotime($fecha_consulta_20);
										
										if( $fecha_ingreso_time > $fecha_consulta_20_time ){
											$existe = 1;
										}

										//Si ingresó despues del dá 20 de ese mes/año, esa cuota no se cobra
										//if(  $fecha_entrada > $fecha_20  )
										//$existe=1;
										
										if($existe==0){
											
											$token = md5( rand(999,999999) . $id_usuario  . $hoy  );
											$meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
											$mes_texto = $meses[$mes - 1];
											$glosa = "Cuota mes $mes_texto de $agno, $nombre";
											$sql4 			= $mysql->query("INSERT INTO deudas (id_usuario, id_usuario_deuda, nombre_deudor, sub_cuenta, fecha,           monto,  glosa, estado, fecha_insercion, token) 
																						VALUES (      0,        '$id_usuario',  '$nombre',     'cuota',   '$fecha_consulta','$valor_cuota','$glosa','activa','$hoy','$token')	 ;");

										}//if($existe==0)
							}
							$mes_inicial = 1;
						}//for($agno = $agno_inicial; $agno<=$hoy_agno; $agno++)

		 
			// generar deuda si esta en estado congelado, solo debe ocurrir en enero para hacer un inser en tabla de deudas.
		if($hoy_mes==1 && $hoy_dia == 1){

			$sqlperfil 	= $mysql->query("SELECT * FROM perfil WHERE id_usuario='$id_usuario' AND tipo_inscripcion = 6 ;");
			$existe2    = $mysql->f_num($sqlperfil);

			if( $existe2 == 1){
				$hoy 	= date("Y-m-d");
				$precio = 20000;
				$token_deuda = md5(rand(99989, 99999979).$nombre.date("Y m d H s").$token_nuevo);

				$sqlX 	= $mysql->query("SELECT * FROM deudas WHERE id_usuario='$id_usuario' AND sub_cuenta = 'otros' AND glosa = 'Cuota de congelación' AND estado = 'activa'  ;");
				$esta 	= $mysql->f_num($sqlX);

				if($esta == 0){  // inserta solo si aun no existe
					//inserta la deuda de congelado
					$sqlB 	= $mysql->query("INSERT INTO deudas (id_usuario_deuda, nombre_deudor, sub_cuenta, fecha, monto, glosa, estado, observacion, token) 
													VALUES('$id_usuario', '$nombre', 'otros', '$hoy', '$precio', 'Cuota de congelación', 'activa', '', '$token_deuda' ) ;");
				}
			}
		}

	}//if($verificar>0)

}//while($result = $mysql->f_obj($sql))

mail('eorellana@gmail.com', 'Cron Ramuch', "El cron se encuentra trabajando");



?>