<?php
session_start();
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . 'error.log');
include("../../includes/sql_inyection.php");
include("../../configuration.php");
include("../../includes/conexionMysql.php");
include("../../includes/funciones.php");

$config		= new Config;
$mysql 		= new mysql;
$mysql->connect();
$url_base = $config->urlbase;

$accion      = @$_POST["accion"];
$email	     = @$_POST["email"];
$password	 = md5(@$_POST["password"]);

if($accion == "login"){ //ingresar
	$sql 	= $mysql->query("SELECT u.token           usuario_token,
									u.nombre_usuario  usuario_nombre,
									u.email           usuario_email,
									u.id_rol          usuario_rol,
									u.id_usuario      usuario_id,
									u.id_empleado     empleado_id,
									c.token			  company_token,
									c.id_company	  company_id,
									c.logo	  		  company_logo,
									c.email	          company_email,
									c.zona_horaria    company_zona,	
									c.nombre_fantasia company_nombre
							FROM usuario u,company c, perfil p
							WHERE u.password ='$password' 
							and u.email = '$email' and u.estado='Vigente' and (u.id_rol='1' OR u.id_rol='5' OR u.id_rol='6' OR u.id_rol='7' OR u.id_rol='8' OR u.id_rol='11'  )   						
							and u.id_company = c.id_company 
							;");
							
	$result = $mysql->f_obj($sql);
	$cantidad = $mysql->f_num($sql);	

	if($cantidad>0){
		unset($_SESSION["usuario_valido_bastro_ruta"]);
		$_SESSION["usuario_origen"] = "socios";
		$_SESSION["usuario_valido_socio_ramuch"]="true" ;
		$_SESSION["usuario_token"] 	=@$result->usuario_token; 
		$_SESSION["usuario_nombre"]	=@$result->usuario_nombre;
		$_SESSION["usuario_id"]		=@$result->usuario_id;
		$_SESSION["empleado_id"]	=@$result->empleado_id;		
		$_SESSION["usuario_email"] 	=@$result->usuario_email; 
		$_SESSION["usuario_rol"]   	=@$result->usuario_rol; 
		$_SESSION["company_token"] 	=@$result->company_token; 
		$_SESSION["company_id"] 	=@$result->company_id;
		$_SESSION["company_nombre"] =@$result->company_nombre;
		$_SESSION["company_email"]  =@$result->company_email;
		$_SESSION["company_zona"]  =@$result->company_zona;
	    $_SESSION["company_logo"]  =@$result->company_logo;	

		//Forzamos la entrada como socio aunque sea administrador
		$_SESSION["usuario_rol"]   	=8; 

		$sql2 	= $mysql->query("SELECT img_perfil, rut FROM perfil WHERE id_usuario='$result->usuario_id';");
		$result2 = $mysql->f_obj($sql2);

		$_SESSION["usuario_rut"] = @$result2->rut;

		$imagen_perfil = "images/user_no.png";
		if(@$result2->img_perfil!="")
		$imagen_perfil = "../admin/images/img_perfil/$result2->img_perfil";

		$_SESSION["img_perfil"]  = $imagen_perfil;	
		
		echo "|ok|";  
	}else{				     
	echo "|0|";  //usuario invalido
	}
}else{				     
echo "|x|"; //formulario invalido
}

?>
