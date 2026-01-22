<?php echo $mensaje;?>

<div class="card">
    <div class="card-header"><i class="fas fa-compass"></i> <strong>Cursos y Talleres</strong></div>
        <div class="card-body">
            <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data" >
            <input id="token" name="token" type="hidden" value="<?php echo $token;?>">

            <div class="col-sm-12">
                <div class="row">

                <div class="col-lg-5 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Nombre del Curso/Taller:</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre del equipo"  value="<?php echo @$result->nombre;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  maxlength="255" >
                </div>

                <div class="col-lg-2 form-group"> 
                                <label class="col-form-label"><span class="obligatorio">*</span>Fecha Inicio:</label>
                                <input class="form-control" id="fechaInicio" type="date" name="fechaInicio" placeholder="date" value="<?php echo @$result->fecha_inicio;?>">
                </div>



                <div class="col-lg-2 form-group"> 
                                <label class="col-form-label"><span class="obligatorio">*</span>Fecha Finalización:</label>
                                <input class="form-control" id="fechaFin" type="date" name="fechaFin" placeholder="date" value="<?php echo @$result->fecha_fin;?>">
                </div>







                <div class="col-lg-2 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Tipo:</label>
                    <select id="tipo" name="tipo" class="form-control"  >
                        <?php echo @$tipo;?>
                    </select>
                </div>


                <div class="col-lg-2 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Precio General:</label>
                    <input type="text" class="form-control" name="precio" id="precio" placeholder="Precio general"  value="<?php echo @$result->precio;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);elimina_puntos(this);solo_numeros(this);"  maxlength="255" >
                </div>

                <div class="col-lg-2 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Capacidad de Participantes:</label>
                    <input type="text" class="form-control" name="capacidad" id="capacidad" placeholder="Precio general"  value="<?php echo @$result->capacidad;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);elimina_puntos(this);solo_numeros(this);"  maxlength="255" >
                </div>

               


                <div class="col-lg-12 form-group"> 
               <strong>Participantes</strong>
              <?php echo $boton_inscribir;?>
               <br>
               <?php echo $div_participantes;?>
                </div>



                <div class="col-lg-12 text-center">
                    <br>
                        <a href="index.php?component=curso&view=curso_list"><button type="button" class="btn btn-secondary" style="margin-top:10px; width:240px;margin-right:10px;"> ir al listado de Cursos y Talleres </button></a>
                      
                        <button type="submit" class="btn btn-primary" style="margin-top:10px;width:240px;margin-right:10px;"> Guardar Curso </button>
						<br> <br>  <br> 
                </div>





                </div>
            <div>

            </form>
        </div>
    </div>

</div>






<div class="modal fade" id="primaryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Inscripción de participante</h4>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
                <div class="modal-body">
                <span class="obligatorio">*</span><label>Nombre del Participante:</label><br>

                  <?php echo $option_usuarios;?>
<div style="width:100%; height:10px;"></div>
<span class="obligatorio">*</span><label>Precio a Pagar:</label>
                  <input type="text" class="form-control" name="participantePrecio" id="participantePrecio" placeholder="Precio Participante"  value="<?php echo @$result->precio;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);elimina_puntos(this);solo_numeros(this);"  maxlength="255" >

                  <label>Comentario:</label>
                  <input type="text" class="form-control" name="participanteComentario" id="participanteComentario" placeholder="Ingresar comentario"  value="" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  maxlength="255" >

                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" type="button" data-dismiss="modal" >Cancelar</button>
                  <button class="btn btn-primary" type="button" onClick="inscribir('<?php echo @$result->token;?>');" >Inscribir en <?php echo @$result->tipo;?></button>
                </div>
              </div>
              <!-- /.modal-content-->
            </div>
            <!-- /.modal-dialog-->
          </div>
                

           


 