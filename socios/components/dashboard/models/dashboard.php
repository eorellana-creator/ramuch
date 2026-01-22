<?php
@include("../../includes/sql_inyection.php");

$mysql->connect();

$id_empleado =  $_SESSION["empleado_id"];



// Consulta de comisiones
// 3. Para carga inicial (solo comisiones)
$sql_comisiones = $mysql->query("SELECT id, nombre FROM comisiones WHERE estado = 1 ORDER BY nombre ASC");
$comisiones = [];
while($comision = $mysql->f_obj($sql_comisiones)) {
    $comisiones[] = $comision;
}

// Consulta de tipos de documento
$sql_tipos = $mysql->query("SELECT id_tipo, nombre FROM tipo_documento WHERE estado = 1 ORDER BY nombre ASC");
$tiposDocumento = [];
while($tipo = $mysql->f_obj($sql_tipos)) {
    $tiposDocumento[] = $tipo;
}


//********************************************************************

$sql 	= $mysql->query("SELECT u.id_usuario, u.nombre_usuario, u.email FROM usuario AS u LEFT JOIN perfil AS p ON u.id_usuario = p.id_usuario WHERE (p.tipo_inscripcion ='1' OR p.tipo_inscripcion ='2' OR p.tipo_inscripcion ='3') AND u.estado='Vigente' ;");
$cantidad_usuarios_activos = $mysql->f_num($sql);
$cantidad_usuarios_activos = number_format($cantidad_usuarios_activos,0,",",".");
//********************************************************************

 
//********************************************************************
$mes_inicio = date("Y-m");
$mes_inicio = $mes_inicio ."-01";
$hoy = date("Y-m-d");
$sql 	= $mysql->query("SELECT SUM(monto) AS ingresos_mes FROM cuenta_maestra WHERE fecha<='$hoy' AND fecha >='$mes_inicio' AND fecha>'2022-08-18' AND estado='activo' AND tipo='ingreso' ;");
$result = $mysql->f_obj($sql);
$ingresos_mes = number_format($result->ingresos_mes,0,",",".");


//********************************************************************


//********************************************************************
$mes_inicio     = date("Y") ."-01-01";
$mes_fin        = date("Y") ."-12-31";
$sql 	= $mysql->query("SELECT SUM(monto) AS ingresos_agno FROM cuenta_maestra WHERE fecha<='$hoy' AND fecha >='$mes_inicio' AND fecha>'2022-08-18' AND estado='activo' AND tipo='ingreso' ;");
$result = $mysql->f_obj($sql);
$ingresos_agno = number_format($result->ingresos_agno,0,",",".");


//********************************************************************

  
//********************************************************************
$fecha_actual = date("Y-m-d");
$fecha_atrasada = date("Y-m-d",strtotime($fecha_actual."- 1 days")); 
$sql 	= $mysql->query("SELECT SUM(monto) AS atrasados FROM deudas WHERE fecha<='$fecha_atrasada' AND  estado='activa' ;");
$result = $mysql->f_obj($sql);
$atrasados = number_format($result->atrasados,0,",",".");


//********************************************************************


//********************************************************************
$sql 	= $mysql->query("SELECT SUM(monto) AS ingresos_total FROM cuenta_maestra WHERE  estado='activo' AND tipo='ingreso' ;");
$result = $mysql->f_obj($sql);
$ingresos_total = $result->ingresos_total;


$sql 	= $mysql->query("SELECT SUM(monto) AS egresos_total FROM cuenta_maestra WHERE  estado='activo' AND tipo='egreso' ;");
$result = $mysql->f_obj($sql);
$egresos_total = $result->egresos_total;

$saldo_caja = $ingresos_total - $egresos_total;

$saldo_caja = number_format($saldo_caja,0,",",".");
//********************************************************************



//********************************************************************

$sql 	= $mysql->query("SELECT id_equipo FROM equipo WHERE prestado_a_id_usuario> 0  ;");
$equipo = $mysql->f_num($sql);
$equipo = number_format($equipo,0,",",".");
//********************************************************************





//*************************************************************************************************
$mensaje = "";



?>