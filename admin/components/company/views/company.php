<h2>Mi Comercio</h2>             
			 
            <form name="formulario5" id="formulario5" method="post" action="javascript: enviar();">

                <div class="row">

                    <div class="col-md-6">

						<div class="form-group">
                        <label >Nombre de su Comercio:</label>
                            <input type="text" class="form-control" name="nombre_fantasia" id="nombre_fantasia" placeholder="Nombre del Comercios(obligatorio)" required data-validation-required autofocus value="<?php echo @$result->nombre_fantasia;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  >
                        </div>
						
							
						<div class="form-group">
                        <label >Correo de Contacto:</label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="Correo de contacto (obligatorio)" required data-validation-required value="<?php echo @$result->email;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  >
                        </div>
						
						<div class="form-group">
                        <label >Teléfono de Contacto:</label>
                            <input type="text" class="form-control" name="telefono_1" id="telefono_1" placeholder="Teléfono de contacto(obligatorio) Ej:+56912345678" required data-validation-required value="<?php echo @$result->telefono_1;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  >
                        </div>
						 
						<div class="form-group">
                        <label >Razón Social:</label>
                            <input type="text" class="form-control" name="razon" id="razon" placeholder="Razón Social" value="<?php echo @$result->razon_social;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  >
                        </div>
						
					    <div class="form-group">
                        <label >Rut:</label>
                            <input type="text" class="form-control" name="rut" id="rut" placeholder="Número de Rut (válido para Chile)" value="<?php echo @$result->rut;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  >
                       <div id="errorrut"></div>
					   </div>

						<div class="form-group">
                        <label >Giro Comercial:</label>
                            <input type="text" class="form-control" name="giro" id="gito" placeholder="Giro Comercial" value="<?php echo @$result->giro;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  >
                        </div>						
     
						<div class="form-group">
                        <label >Direccion de Facturación:</label>
                            <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Tu direccion (obligatorio)" required data-validation-required value="<?php echo @$result->direccion;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  >
                        </div>
                       
					
								
						<div class="form-group">
                        <label >Sitio Web:</label>
                            <input type="text" class="form-control" name="web" id="web" placeholder="Ej: www.mipagina.com" value="<?php echo @$result->web;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"   >
                        </div>
						
						<div class="form-group">
                        <label >Facebook:</label>
                            <input type="text" class="form-control" name="facebook" id="facebook" data-toggle="tooltip" title="Ingrese su nombre de usuario de Facebook" placeholder="Ej: minombre" value="<?php echo @$result->facebook;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"   >
                        </div>
						
						<div class="form-group">
                        <label >Instagram:</label>
                            <input type="text" class="form-control" name="instagram" id="instagram" data-toggle="tooltip" title="Ingrese su nombre de usuario de instagram. Ej.: minombre" placeholder="Ej: minombre" value="<?php echo @$result->instagram;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"   >
                        </div>
						
						<div class="form-group">
                        <label >Twitter:</label>
                            <input type="text" class="form-control" name="twitter" id="twitter" data-toggle="tooltip" title="Ingrese su nombre de usuario de Twitter. Ej.: minombre" placeholder="Ej: minombre" value="<?php echo @$result->twitter;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"   >
                        </div>
						
						<div class="form-group">
                        <label >Logo de la Empresa (el tamaño se ajustará a 90px de alto):</label>
                        <input type="file" name="imagen" id="imagen" <?php echo $imagen_requerida;?> class="form-control" onChange="validaImagen(this);" style="padding: 4px; " /><div id="errorarchivo" style="min-height:5px;"></div>
                        <?php echo $imagen;?>
                        
                        </div>
						
				

                    <div class="col-lg-12 text-center">
                        <button type="submit" class="btn btn-primary" > Guardar </button>
                    </div>
                    
                </div>

            </form>
