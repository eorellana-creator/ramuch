<?php
include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");


$token      = @$_POST["token"];
$password 	= @$_POST["password"];

$password = md5($password);

$token_nuevo = md5( $password . date("Y-m-d-h-i-s"). $token . rand(9999,999999) );

$mysql 		= new mysql;
$mysql->connect();

if($token!="" && $password!=""){
    $existe = 1;

    echo "UPDATE usuario SET password='$password', token ='$token_nuevo' WHERE token='$token' AND token!=''  ; ";

    $sql 	= $mysql->query("UPDATE usuario SET password='$password', token ='$token_nuevo' WHERE token='$token' AND token!=''  ; ");

    echo "|1|";

}else{
    echo "|0|"; 
}//if($token!="" && $password!="")

?>