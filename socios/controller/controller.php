<?php
$mysql 		= new mysql;
$mysql->connect();

$usuario_token  = @$_SESSION["usuario_token"]; 
$usuario_rol    = @$_SESSION["usuario_rol"];
$company_token	= @$_SESSION["company_token"]; 

$sql 	= $mysql->query("SELECT id_menu FROM menu_rol WHERE id_rol = '$usuario_rol' AND activo='1' ");

$menu_autorizado = array();
while($result = $mysql->f_obj($sql)){
array_push($menu_autorizado, $result->id_menu);
}//while


if( $component=="login"  ){
   include("components/login/login.php");
}//if( @$_GET["component"]=="login" )

if( $component=="dashboard" ){
   include("components/dashboard/dashboard.php");
}//if( @$_GET["component"]=="dashboard" )
	
if( $component=="profile" ){
   include("components/profile/profile.php");
}//if( @$_GET["component"]=="profile" )


if( $component=="changepassword" ){
   include("login/changepassword.php");
}//if( @$_GET["component"]=="company" )


if( $component=="socios"  ){
   include("components/socios/socios.php");
}//if( @$_GET["component"]=="socios" )


if( $component=="equipo"  ){
   include("components/equipo/equipo.php");
 }//if( @$_GET["component"]=="equipo" )


if( $component=="contacto"  ){
   include("components/contacto/contacto.php");
}//if( @$_GET["component"]=="equipo" )


if( $component=="mercado"  ){
   include("components/mercado/mercado.php");
}//if( @$_GET["component"]=="equipo" )



	
?>