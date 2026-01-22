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

$sqlC 	= $mysql->query("SELECT u.id_usuario, u.id_usuario, u.nombre_usuario, u.email, p.tipo_inscripcion, p.fono, p.rut FROM usuario AS u LEFT JOIN perfil AS p ON u.id_usuario = p.id_usuario WHERE  p.tipo_inscripcion ='1' OR p.tipo_inscripcion ='2' OR p.tipo_inscripcion ='3'  ;");

//echo "SELECT u.id_usuario, u.id_usuario, u.nombre_usuario, u.email, p.tipo_inscripcion, p.fono, p.rut FROM usuario AS u LEFT JOIN perfil AS p ON u.id_usuario = p.id_usuario WHERE  p.tipo_inscripcion ='1' OR p.tipo_inscripcion ='2' OR p.tipo_inscripcion ='3'  ;";
$k=0;
while($resultC = $mysql->f_obj($sqlC)){

 

    //**************************************** */

  $hoy = date("Y-m-d");
  $tres_meses_atras = date("Y-m-d",strtotime($hoy."- 3 month"));



  $sqlD 	= $mysql->query("SELECT SUM(monto) as deuda FROM deudas WHERE sub_cuenta = 'cuota' AND id_usuario_deuda='$resultC->id_usuario' AND fecha<'$tres_meses_atras' AND estado='activa' ;");
  $resultD = $mysql->f_obj($sqlD);
  //echo "$resultD->deuda SELECT SUM(monto) as deuda FROM deudas WHERE sub_cuenta = 'cuota' AND id_usuario_deuda='$resultC->id_usuario' AND fecha<'$tres_meses_atras' AND estado='activa' ;<br>";
 
if($resultD->deuda >0 ){
 
}else{
 
      $lista_excel = $lista_excel."<tr>  <td>$resultC->id_usuario</td>  <td>$resultC->nombre_usuario</td> <td>$resultC->rut</td> <td>$resultC->fono</td> <td>$resultC->email</td>   </tr>";
}
 



    //*************************************************/
 

}//while($resultC = $mysql->f_obj($sqlC))




$id_usuario_sesion = @$_SESSION["usuario_id"]	;

$lista_excel = "<html lang='es'><head><meta charset='UTF-8'></head><body><table><tr style='background-color:#313131; color:#ffffff;padding:4px;'>  <td>ID</td>  <td>Nombre</td> <td>Rut</td> <td>Teléfono</td> <td>Email</td>  </tr>$lista_excel</table></body></html>";

$archivo = "../excel/lista_socios_al_dia_hasta_3_meses_atras_$id_usuario_sesion.xls";

$fp = fopen("../excel/lista_socios_al_dia_hasta_3_meses_atras_$id_usuario_sesion.xls", 'w+');
fwrite($fp, $lista_excel);
fclose($fp);

 
 


?>

<html lang="es">
<head></head>
<body >

<?php if($id_usuario_sesion!=""){?>
<div style="width:100%; padding:30px; text-align:center; font-size:16px; font-family: Arial;"> Descarga el listado <br><a href="<?php echo $archivo;?>">descárgalo haciendo click aquí</a></div>

<?php }?>
</body>

</html>