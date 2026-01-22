               
<div class="card">
                    <div class="card-header">
                        <i class="fa fa-user"></i> Permisos del Rol <?php echo @$result->nombre;?>
                    </div>  
                <div class="card-body">
			   
            <form name="formulario3" id="formulario3" method="post" action="javascript: enviar();">

                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">
                        <label >Nombre del Rol:</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="El nombre del nuevo rol a crear (obligatorio)" required data-validation-required autofocus value="<?php echo @$result->nombre;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  >
                        </div>
						<strong>Permisos:</strong><br>
						-Selecciona los módulos a los cuales tendrá acceso <?php echo @$result->nombre;?>:<br> 
						<?php echo $permisos;?>

						<input type="hidden" name="token" value="<?php echo @$result->token;?>">
                                                       
                    <div class="col-lg-12 text-center">
					
					<a href="index.php?component=rol&amp;view=rol_list">
                    <button type="button" class="btn btn-primary">  &lt;&lt; volver </button></a> &nbsp; &nbsp; 
					
					
                        <button type="submit" class="btn btn-primary" > Guardar </button>
                    </div>
                    
                </div>
				
            </form>


            </div>
        </div>