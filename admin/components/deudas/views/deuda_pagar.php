<?php echo $mensaje; ?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-dollar-sign"></i> Pagar Deuda
    </div>
    <div class="card-body">
        <form name="formulario" id="formulario" method="post" action="javascript: pagar();" enctype="multipart/form-data">
            <input id="token" name="token" type="hidden" value="<?php echo $token; ?>">

            <div class="col-sm-12">
                <div class="row">
                    <div class="col-lg-4 form-group">
                        <label class="col-form-label"><span class="obligatorio">*</span>Medio de Pago:</label>
                        <select id="medio" name="medio" class="form-control">
                            <option value="">Seleccionar Medio de Pago</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="col-lg-8 form-group">
                        <label class="col-form-label">Observación:</label>
                        <input type="text" class="form-control" name="observacion" id="observacion" placeholder="Motivo" value="" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                    </div>

                    <div class="col-lg-12 form-group">
                        <label class="col-form-label">Documento de respaldo:</label>
                        <input id="archivo" name="archivo" type="file" onChange="validaDocumento(this);" class="btn btn-success btn-xs" />
                        <div><?php echo @$documento; ?></div>
                    </div>

                    <div class="col-lg-12 text-center">
                        <br>
                        <a href="index.php?component=deudas&view=deudas_list">
                            <button type="button" class="btn btn-secondary" style="margin-top:10px; width:180px;margin-right:10px;">
                                ir al listado de deudas
                            </button>
                        </a>

                        <button type="submit" class="btn btn-primary" style="margin-top:10px;width:180px;margin-right:10px;">
                            Pagar deuda
                        </button>
                        <br><br><br>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>