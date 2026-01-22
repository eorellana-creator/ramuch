<?php echo $mensaje;?>

<div class="card">
    <div class="card-header"><i class="fas fa-sign-out-alt"></i> Ingreso</div>
        <div class="card-body">
            <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data" >
            <input id="token" name="token" type="hidden" value="<?php echo $token;?>">

            <div class="col-sm-12">
                <div class="row">

                <div class="col-lg-3 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Nombre Socia/o :</label>
                    <?php echo $option_usuarios;?>
                </div>

                <div class="col-lg-3 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Motivo del Ingreso:</label>
                    <input type="text" class="form-control" name="glosa" id="glosa" placeholder="Motivo del Ingreso"  value="<?php echo @$result->glosa;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  maxlength="100" >
                </div>

                <div class="col-lg-2 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Tipo de Ingreso:</label>
                    <select id="tipo_ingreso" name="tipo_ingreso" class="form-control">
                        <option value="matricula">Matrícula</option>
                        <option value="cuota">Cuota</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div class="col-lg-2 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Fecha del Ingreso:</label>
                    <input class="form-control" id="fecha" type="date" name="fecha" placeholder="date" value="<?php echo @$result->fecha;?>">
                </div>

                <div class="col-lg-2 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Monto del Ingreso:</label>
                    <input type="text" class="form-control" name="monto" id="monto" placeholder="Monto"  value="<?php echo @$result->monto;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);elimina_puntos(this);solo_numeros(this);"  maxlength="255" >
                </div>

                <div class="col-lg-3 form-group"> 
                    <label class="col-form-label"><span class="obligatorio">*</span>Medio de Ingreso:</label>
                    <select id="medio" name="medio" class="form-control" >
                        <?php echo @$medio?>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Transferencia">Depósito</option>
                        <option value="Pago Online">Pago Online</option>
                        <option value="Tarjeta de débito">Tarjeta débito</option>
                        <option value="Tarjeta de crédito">Tarjeta crédito</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>

                <div class="col-lg-6 form-group"> 
                    <label class="col-form-label">Observación del ingreso:</label>
                    <input type="text" class="form-control" name="observacion" id="observacion" placeholder="Observación del egreso"  value="<?php echo @$result->observacion;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  maxlength="255" >
                </div>

                <div class="col-lg-12 form-group"> 
                    <label class="col-form-label">Documento de respaldo:</label>
                     <input id="archivo" name="archivo" type="file" onChange="validaDocumento(this);"  class="btn btn-success btn-xs" />
                    <div><?php echo @$documento;?></div>
                </div>

                <div class="col-lg-12 text-center">
                    <br>
                        <a href="index.php?component=ingresos&view=all"><button type="button" class="btn btn-secondary" style="margin-top:10px; width:180px;margin-right:10px;"> ir al listado de ingresos </button></a>
                      
                        <button type="submit" class="btn btn-primary" style="margin-top:10px;width:180px;margin-right:10px;"> Guardar ingreso </button>
						<br> <br>  <br> 
                </div>

            </div>
            <div>

            </form>
        </div>
    </div>

</div>