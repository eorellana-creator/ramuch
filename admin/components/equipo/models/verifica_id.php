<?php
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token				= $_POST['token'];
$id_unico			= $_POST['idunico'];

$config 	= new Config;


$mysql 		= new mysql;
$mysql->connect();

echo "SELECT id_equipo FROM equipo WHERE token!='$token' AND id_unico='$id_unico' AND id_inico!='' ;";

$sql 			= $mysql->query("SELECT id_equipo FROM equipo WHERE token!='$token' AND id_unico='$id_unico' AND id_unico!='' ;");
$existe         = $mysql->f_num($sql);
$result 		= $mysql->f_obj($sql);
$id_equipo		= @$result->id_equipo;


if($existe>0){
    echo "|1|";
}else{
    echo "|0|";
}//if($existe>0)


?>