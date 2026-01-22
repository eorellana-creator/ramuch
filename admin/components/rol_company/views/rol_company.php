               
            <form name="formulario4" id="formulario4" method="post" action="javascript: enviar();">

                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">
                        <label >Nombre nuevo rol:</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="El nombre del nuevo rol a crear (obligatorio)" required data-validation-required autofocus value="<?php echo @$result->nombre;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  >
                        </div>

						<input type="hidden" name="id_rol" value="<?php echo @$result->id_rol;?>">
						
						<input type="hidden" name="token" value="<?php echo @$result->token;?>">

                                                       
                    <div class="col-lg-12 text-center">
                        <button type="submit" class="btn btn-primary" > Aceptar </button>
                    </div>
                    
                </div>
				
            </form>