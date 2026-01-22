<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ramuch</title>

	<!-- CSS -->
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
	<link rel="stylesheet" href="login/views/assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="login/views/assets/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="login/views/assets/css/form-elements.css">
	<link rel="stylesheet" href="login/views/assets/css/style.css">
	 <link href="template/css/style.css" rel="stylesheet">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

	<!-- Favicon and touch icons -->
	<link rel="shortcut icon" href="favicon.ico">
    
    <style>
	body{
		background-image:url(login/views/assets/img/backgrounds/1.jpg);
		background-size:100%;
		}
		
	button.btn{
	background: #df0009 !important;
	}
	
	
	
	@media (max-width: 991px){
	body{
		background-image:url(login/views/assets/img/backgrounds/2.png);
		background-size:100%;
		}
		
	}
	
	
	</style>

</head>
<body>

<div style="background-color:#000000; width:100%; height:70px;"><img src="images/logo-168-70.png" width="250" height="70" alt="Ramuch"/></div>
        <div class="top-content">
            <div class="inner-bg" style="padding:0px !important;">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 form-box">
                        	<div class="form-top">
                        		<div class="form-top-left" >
                        			<h3>Acceso Admin Ramuch</h3>
                            		
                        		</div>
                        		<div class="form-top-right">
                        			<i class="fa fa-lock"></i>
                        		</div>
                            </div>
                            <div class="form-bottom" style="padding-top:0px !important;">
								<form role="form" action="" method="post" name="formlogin" id="formlogin" class="login-form">
								<input type="hidden" name="accion" value="login">
			                    	<!--
									<div class="form-group" style="display:none;">
			                    		<label class="sr-only" for="rut_empresa">Empresa</label>
			                        	<input type="text" name="rut_empresa" id="rut_empresa" placeholder="Rut Empresa (obligatorio)" required data-validation-required autofocus class="form-control" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);">
									</div>
									-->
									<div class="form-group">
			                    		<label class="labelhome" for="email">Correo Electrónico:</label>
			                        	<input type="email" name="email" id="email" placeholder="T&uacute; Email (obligatorio)" required data-validation-required class="form-control" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);">
									</div>
			                        <div class="form-group">
			                        	<label class="labelhome" for="password">Contraseña:</label>
			                        	<input type="password" name="password" id="password" placeholder="Password (obligatorio)" required data-validation-required class="form-control" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);largoPass(this);">
									</div>
                                    
			                        <button type="submit" class="btn btn-lg btn-primary">Ingresar</button>
			                    </form>
		                    </div>
                        </div>
                    </div>
                </div>
            </div>
            
            </div>
	
	<!-- Javascript -->
	<script src="js/jquery-1.12.1.min.js"></script>
	<script src="js/bootstrap/js/bootstrap.min.js"></script>
	<script src="js/bootstrap/js/bootstrap-dialog.js"></script>
	<script src="js/jquery.backstretch.min.js"></script>
	<script src="js/validadores.js"></script>
	<script src="login/js/js.js"></script>
	
	
	<!--[if lt IE 10]>
		<script src="login/js/placeholder.js"></script>
	<![endif]-->
</body>
</html>
