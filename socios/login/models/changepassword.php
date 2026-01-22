<?php
$config		= new Config;
$mysql 		= new mysql;
$mysql->connect();

$accion      = @$_POST["accion"];
$rut_empresa = @$_POST["rut_empresa"];
$email	     = @$_POST["email"];
$password	 = md5(@$_POST["password"]);

if($accion == "a3e59f6c4e9d76b062063e193b35610a"){ //ingresar

	$sql 	= $mysql->query("SELECT u.token           usuario_token,
									u.nombre_usuario  usuario_nombre,
									u.email           usuario_email,
									u.id_rol          usuario_rol,
									c.token			  company_token,
									c.nombre_fantasia company_nombre
							FROM usuario u,company c 
							WHERE u.password ='$password' 
							and u.email = '$email' 
							and u.id_company = c.id_company
							and c.rut = '$rut_empresa';");
	$result = $mysql->f_obj($sql);
	if($result!=NULL){
		$_SESSION["usuario_valido_socio_ramuch"]="true" ;
		$_SESSION["usuario_token"] =@$result->usuario_token; 
		$_SESSION["usuario_nombre"]=@$result->usuario_nombre; 
		$_SESSION["usuario_email"] =@$result->usuario_email; 
		$_SESSION["usuario_rol"]   =@$result->usuario_rol; 
		$_SESSION["company_token"] =@$result->company_token; 
		$_SESSION["company_nombre"] =@$result->company_nombre; 
		
		echo "ok";  
	}else{				     
	echo "102";  //usuario invalido
	}
}else{				     
echo "103"; //formulario invalido
}

?>