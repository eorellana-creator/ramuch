<?php
session_start();
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token			= $_POST['token'];
$tipo			= $_POST['tipo'];
$nombre_usuario	= $_SESSION["usuario_nombre"];
//$id_usuario		= $_SESSION["usuario_id"];	
$mysql 		= new mysql;
$mysql->connect();

if($tipo!=""){
    $sql3 	= $mysql->query("SELECT id_usuario, nombre_usuario FROM usuario WHERE token ='$token';");
    $result = $mysql->f_obj($sql3);
    $id_usuario = $result->id_usuario;
    $nombre_usuario = $result->nombre_usuario;

    //echo                  "UPDATE perfil SET  tipo_inscripcion ='$tipo', id_plan_matricula='$tipo' WHERE id_usuario ='$id_usuario';";
    $sql 	= $mysql->query("UPDATE perfil SET  tipo_inscripcion ='$tipo', id_plan_matricula='$tipo' WHERE id_usuario ='$id_usuario';");
    $_SESSION["socio_actualizado"] = "<div class='alert alert-success' role='alert'>Los datos se han actualizado.$tipo </div>";
    $_SESSION["script_final"] = " <script> $(document).ready(function() { $('.tab-inscripcion').click();  } ); </script> ";

    //  si lo congelamos tiene que generar deuda de congelacion y quitar las otras deudas, debe permanecer el congelado 
    if($tipo=="6"){
        // tabla a modificar deudas, campo estado cambiar a "desactivada" de todas las deudas activas
        $sqlA 	= $mysql->query("UPDATE deudas SET  estado='desactivada' WHERE id_usuario_deuda ='$id_usuario' and estado = 'activa';");

        // tabla a modificar deudas, crear deuda con valor de congelado. con año actual, sub_cuenta "otros", observacion de congelado, estado activa.
        $hoy 	= date("Y-m-d");
        $precio = 20000;
        $token_deuda = md5(rand(99989, 99999979).$nombre.date("Y m d H s").$token_nuevo);

        // primero elimina si existe otra cuota de congelacion desactivada, antes de insertar
        $sqlA1 	= $mysql->query("DELETE FROM deudas WHERE id_usuario_deuda ='$id_usuario' and estado = 'desactivada' and sub_cuenta = 'otros' and glosa = 'Cuota de congelación';");
        
        //inserta la deuda de congelado
        $sqlB 	= $mysql->query("INSERT INTO deudas (id_usuario_deuda, nombre_deudor, sub_cuenta, fecha,   monto, glosa, estado, observacion, token) 
		                 	            VALUES('$id_usuario', '$nombre_usuario', 'otros', '$hoy', '$precio', 'Cuota de congelación', 'activa', '', '$token_deuda' ) ;");

        // tabla a modificar usuario, campo estado a "congelado"
        $sqlC = $mysql->query("UPDATE usuario SET estado = 'congelado' WHERE id_usuario ='$id_usuario' ;");

    }

    //  si lo desvinculamos elimninar las deudas para que no exista el descuadre.
    if($tipo=="7"){
                // tabla a modificar deudas, eliminamos las deudas activas
                $sqlA 	= $mysql->query("DELETE FROM deudas WHERE id_usuario_deuda ='$id_usuario' and estado = 'activa';");
    }

    //  si lo eliminamos, eliminar sus deudas y cambiar los estados de las tablas que corresponden
    if($tipo=="8"){
                // tabla a modificar deudas, eliminamos las deudas activas
                $sqlA 	= $mysql->query("DELETE FROM deudas WHERE id_usuario_deuda ='$id_usuario' and estado = 'activa';");
    }

    // si estaba desvinculado o eliminado y lo volvemos a dejar como profesional o estudiante y debe cambiar los valores de la deuda y crear las deudas
    if($tipo=="1" || $tipo=="3" ){  // si lo guardamos como profesional o estudiante
                // tabla a modificar usuario, campo estado a "vigente"
                $sqlD = $mysql->query("UPDATE usuario SET estado = 'Vigente' WHERE id_usuario ='$id_usuario' ;");

                // actualizaremos tabla de deudas, con todo lo que tenga estado 'desactivada' a activa
                $sqlE 	= $mysql->query("UPDATE deudas SET estado='activa' WHERE id_usuario_deuda ='$id_usuario' and estado = 'desactivada';");

                // desactivo la deuda congelada si existe activa
                $sqlE 	= $mysql->query("UPDATE deudas SET estado='desactivada' WHERE id_usuario_deuda ='$id_usuario' and estado = 'activa' and sub_cuenta = 'otros' and glosa = 'Cuota de congelación' ;");

                // agregar si se cambia entre profesional o estudiando para que se cambien las deudas con los valores actuales para cada mes.
                // primero consultar el valor de la cuota 
                //$sqlK 	= $mysql->query("SELECT tipo_inscripcion FROM usuario WHERE id_usuario ='$id_usuario' ;");
                //$resultK = $mysql->f_obj($sqlK);
                //$tipo_inscripcion_actual = $resultK->tipo_inscripcion;

                $sqlK2 	= $mysql->query("SELECT valor FROM plan WHERE id_plan_matricula ='$tipo' and periodo = 'mensual';");
                $resultK2 = $mysql->f_obj($sqlK2);
                $nuevo_valor = $resultK2->valor;

                $sqlE 	= $mysql->query("UPDATE deudas SET monto=$nuevo_valor WHERE id_usuario_deuda ='$id_usuario' and estado = 'activa' ;");
    }

}//if($tipo!="")
 
echo "||";

?>