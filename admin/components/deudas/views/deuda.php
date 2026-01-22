<?php echo $mensaje;?>

<div class="card">
    <div class="card-header"><i class="fas fa-dollar-sign"></i> Deuda </div>
        <div class="card-body">
            <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data" >
            <input id="token" name="token" type="hidden" value="<?php echo $token;?>">

            <div class="col-sm-12">
                <div class="row">

                <div class="col-lg-3 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Nombre del Deudor:</label>
                    <?php echo $option_usuarios;?>
                </div>

                <div class="col-lg-2 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Tipo de Deuda:</label>
                    <select class="form-control" name="sub_cuenta" id="sub_cuenta">
                        <option value="matricula" <?php echo (@$result->sub_cuenta == 'matricula') ? 'selected' : ''; ?>>Matrícula</option>
                        <option value="curso" <?php echo (@$result->sub_cuenta == 'curso') ? 'selected' : ''; ?>>Curso</option>
                        <option value="cuota" <?php echo (@$result->sub_cuenta == 'cuota') ? 'selected' : ''; ?>>Cuota</option>
                        <option value="otros" <?php echo (@$result->sub_cuenta == 'otros') ? 'selected' : ''; ?>>Otros</option>
                    </select>
                </div>

                <div class="col-lg-3 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Motivo de la Deuda:</label>
                    <input type="text" class="form-control" name="glosa" id="glosa" placeholder="Motivo de la deuda"  value="<?php echo @$result->glosa;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  maxlength="100" >
                </div>

                <div class="col-lg-2 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Fecha de la deuda:</label>
                    <input class="form-control" id="fecha" type="date" name="fecha" placeholder="date" value="<?php echo @$result->fecha;?>">
                </div>

                <div class="col-lg-2 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Monto de la deuda:</label>
                    <input type="text" class="form-control" name="monto" id="monto" placeholder="Monto"  value="<?php echo @$result->monto;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);elimina_puntos(this);solo_numeros(this);"  maxlength="255" >
                </div>

                <div class="col-lg-12 form-group"> 
                    <label class="col-form-label">Documento de respaldo:</label>
                     <input id="archivo" name="archivo" type="file" onChange="validaDocumento(this);"  class="btn btn-success btn-xs" />
                    <div><?php echo @$documento;?></div>
                </div>

                <div class="col-lg-12 text-center">
                    <br>
                        <a href="index.php?component=deudas&view=deudas_list"><button type="button" class="btn btn-secondary" style="margin-top:10px; width:180px;margin-right:10px;"> ir al listado de deudas </button></a>
                      
                        <button type="submit" class="btn btn-primary" style="margin-top:10px;width:180px;margin-right:10px;"> Guardar deuda </button>
                        <br> <br>  <br> 
                </div>

                </div>
            <div>

            </form>
        </div>
    </div>
</div>