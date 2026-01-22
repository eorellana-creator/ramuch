<?php
$email_user	= $_SESSION["usuario_email"];

?>
<form role="form" action="" method="post" name="formChangePass" id="formChangePass" class="changePass-form">
<input type="hidden" name="accion" value="a9c5c54a0bed5ecd0340dbc718225efc">
	<div class="form-group">
		<label class="sr-only" for="email">Email</label>
		<input type="email" name="email" id="email" value="<?php echo $email_user;?>" placeholder="T&uacute; Email (obligatorio)" required data-validation-required disabled class="form-control" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);">
	</div>
	<div class="form-group">
		<label class="sr-only" for="password_act">Clave Actual</label>
		<input type="password" name="password_act" id="password_act" placeholder="Clave Actual (obligatorio)" required data-validation-required class="form-control" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);largoPass(this);">
	</div>
	<div class="form-group">
		<label class="sr-only" for="password_new">Nueva Clave</label>
		<input type="password" name="password_new" id="password_new" placeholder="Nueva Clave (obligatorio)" required data-validation-required class="form-control" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);largoPass(this);">
	</div>
	<div class="form-group">
		<label class="sr-only" for="password_new_rep">Repetir Nueva Clave</label>
		<input type="password" name="password_new_rep" id="password_new_rep" placeholder="Repetir Nueva Clave (obligatorio)" required data-validation-required class="form-control" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);largoPass(this);">
	</div>
	<button type="submit" class="btn">Cambiar Clave</button>
</form>
	
	<!-- Javascript -->
	<script src="js/validadores.js"></script>
	<script src="login/js/js.js"></script>
	
	
	<!--[if lt IE 10]>
		<script src="login/js/placeholder.js"></script>
	<![endif]-->
</body>
</html>
