 <script>
 



</script>

<?php echo @$mensaje;?>
   
   
<div class="card">
			<div class="card-header">
					<i class="fas fa-envelope"></i> <strong>Contacta a la directiva Ramuch</strong> 
			</div>  
	<div class="card-body">
    




   
<div class="container" style="width:100%;">
  	<form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data" >


		<div class="row">
  					<div class="col-lg-6 form-group"> 
                        <label class="col-form-label"><span class="obligatorio">*</span>Nombre:</label>
                        <input type="text" class="form-control" name="nombre" id="nombre"  readonly placeholder="Ingresa nombre" value="<?php echo @$_SESSION["usuario_nombre"];?>" onblur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                	</div>
		</div>

		<div class="row">
					<div class="col-lg-6 form-group"> 
                        <label class="col-form-label"><span class="obligatorio">*</span>Asunto:</label>
                        <input type="text" class="form-control" name="asunto" id="asunto" required  placeholder="Ingresa nombre" value="" onblur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                	</div>
		</div>

		<div class="row">
					<div class="col-lg-6 form-group"> 
                        <label class="col-form-label"><span class="obligatorio">*</span>Mensaje:</label>
                        <textarea id="mensaje" name="mensaje" class="form-control " required style="height:90px;" placeholder="Mensaje" onblur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"></textarea>
                	</div>
  		</div> 

		<div class="row">
			<div class="col-lg-6 form-group"> 
		  		<button type="submit" class="btn btn-primary"> Enviar Mensaje </button>
			</div>
		</div>



	</form>
 

 
	
	<br>  <br>
	
 
	 
	 
	 
	 
</div> <!-- container -->
   
   
   

	</div>
</div>
                

           
