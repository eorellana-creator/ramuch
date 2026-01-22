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


if( $component=="socios" AND in_array("15", $menu_autorizado) ){
include("components/socios/socios.php");
}//if( @$_GET["component"]=="socios" )

if( $component=="plan" AND in_array("41", $menu_autorizado) ){
    include("components/plan/plan.php");
    }//if( @$_GET["component"]=="socios" )

if( $component=="ingresos"  ){
    include("components/ingresos/ingresos.php");
    }//if( @$_GET["component"]=="ingresos" )

if( $component=="egresos"  ){
    include("components/egresos/egresos.php");
    }//if( @$_GET["component"]=="egresos" )

if( $component=="maestra"  ){
    include("components/maestra/maestra.php");
    }//if( @$_GET["component"]=="maestra" )

if( $component=="equipo"  ){
    include("components/equipo/equipo.php");
    }//if( @$_GET["component"]=="equipo" )

if( $component=="mercado"  ){
        include("components/mercado/mercado.php");
        }//if( @$_GET["component"]=="equipo" )

if( $component=="curso"  ){
    include("components/curso/curso.php");
    }//if( @$_GET["component"]=="curso" )

if( $component=="deudas"  ){
    include("components/deudas/deudas.php");
    }//if( @$_GET["component"]=="deudas" )


if( $component=="rol" AND in_array("9", $menu_autorizado) ){
include("components/rol/rol.php");
}//if( @$_GET["component"]=="rol" )


if( $component=="equipo_solicitudes"  ){
    include("components/equipo_solicitudes/equipo_solicitudes.php");
    }//if( @$_GET["component"]=="equipo_solicitudes" )


    if( $component=="comision_prestamo"  ){
        include("components/comision_prestamo/comision_prestamo.php");
        }//if( @$_GET["component"]=="comision_prestamo" )

	
//***************************************************************************

if( $component=="company" AND in_array("7", $menu_autorizado) ){
include("components/company/company.php");
}//if( @$_GET["component"]=="company" )
	
		



if( $component=="user" AND in_array("13", $menu_autorizado) ){
include("components/user/user.php");
}//if( @$_GET["component"]=="user" )

if( $component=="tutoriales"  ){
    include("components/tutoriales/tutoriales.php");
    }//if( @$_GET["component"]=="tutoriales" )
	
	

	
?>