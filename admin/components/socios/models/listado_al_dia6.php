<?php
session_start();
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$config 	= new Config;

$mysql 		= new mysql;
$mysql->connect(); 		

$usuarios	= "";
$datos		= "";
$lista_excel = "";

// Kop 
$sqlC 	= $mysql->query("
SELECT d.id_usuario_deuda as id_usuario, d.nombre_deudor as nombre_usuario, SUM(d.monto) as deuda, u.estado, p.mail as email,
CASE tipo_inscripcion
      WHEN 1 THEN 'Profesional'
      WHEN 3 THEN 'Estudiante'
      ELSE 'Otro'
  END AS tipo_inscripcion, p.fono, p.rut
FROM deudas d, usuario u, perfil p
WHERE d.id_usuario_deuda = u.id_usuario and d.id_usuario_deuda = p.id_usuario and u.estado = 'Vigente' and d.sub_cuenta = 'cuota' AND d.estado='activa' and d.fecha < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
and (u.web_matricula_pagada IS NULL OR u.web_matricula_pagada!='No') and (p.tipo_inscripcion ='1' OR p.tipo_inscripcion ='3' ) 
group by d.id_usuario_deuda
order by d.id_usuario_deuda asc, d.fecha asc;");

while($resultC = $mysql->f_obj($sqlC)){

  $lista_excel = $lista_excel."<tr>  <td>$resultC->id_usuario</td>  <td>$resultC->nombre_usuario</td> <td>$resultC->rut</td> <td>$resultC->fono</td> <td>$resultC->email</td> <td>$resultC->tipo_inscripcion</td>   </tr>";
  
}//while($resultC = $mysql->f_obj($sqlC))
// Kop



$id_usuario_sesion = @$_SESSION["usuario_id"]	;

$lista_excel = "<html lang='es'><head><meta charset='UTF-8'></head><body><table><tr style='background-color:#313131; color:#ffffff;padding:4px;'>  <td>ID</td>  <td>Nombre</td> <td>Rut</td> <td>Teléfono</td> <td>Email</td> </tr>$lista_excel</table></body></html>";

$archivo = "../excel/lista_socios_deuda_mas_de_6_meses_$id_usuario_sesion.xls";

$fp = fopen("../excel/lista_socios_deuda_mas_de_6_meses_$id_usuario_sesion.xls", 'w+');
fwrite($fp, $lista_excel);
fclose($fp);

 
 ?>

<html lang="es">
<head></head>
<body >

<?php if($id_usuario_sesion!=""){?>
  <div style="width:100%; padding:30px; text-align:center; font-size:16px; font-family: Arial;"> <img src="../images/tf.png" alt="Logo Ramuch" > <br> Descarga el listado <br><a href="<?php echo $archivo;?>">descárgalo haciendo click aquí</a></div>

<?php }?>
</body>

</html>