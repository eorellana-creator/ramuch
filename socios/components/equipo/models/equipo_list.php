<?php
//@include("../../includes/sql_inyection.php");

echo "";
$id_usuario 	= $_SESSION["usuario_id"];

$mensaje = @$_SESSION["equipo_prestado"];

$_SESSION["equipo_prestado"] = "";




//Comprobamos si tiene deudas de más de 3 meses: *************************************************************

$fechaActual = date('Y-m-d');

$fecha3mesesAtras = strtotime ('-3 month', strtotime($fechaActual));

$fecha3mesesAtras = date('Y-m-d', $fecha3mesesAtras);
 
$cantidad_deuda_atrasada = 0;
if($id_usuario != 2287) {
    $sql0 	= $mysql->query("SELECT id_deuda FROM deudas  WHERE id_usuario_deuda='$id_usuario' AND estado='activa' AND fecha<'$fecha3mesesAtras' ;");
    $cantidad_deuda_atrasada = $mysql->f_num($sql0);

    if($cantidad_deuda_atrasada>0)
    echo "
    <script>
    alert('No puedes pedir equipo. Presentas deudas atrasadas de 3 meses o más.');
    </script>
    ";
}
//********************************************************************************************************** */

?>