<?php echo $mensaje;?>

<div class="card">
    <div class="card-header"><i class="fas fa-dollar-sign"></i> Eliminación de Deuda </div>
        <div class="card-body">
            <form name="formulario" id="formulario" method="post" action="javascript: eliminarD();" enctype="multipart/form-data" >
                <input id="token" name="token" type="hidden" value="<?php echo $token;?>">

                <div class="col-sm-12">
                    <div class="row">

                    <div class="col-lg-12 form-group"> 
                        <label class="col-form-label"><span class="obligatorio">*</span>Motivo de la Eliminación:</label>
                        <input type="text" class="form-control" name="observacion" id="observacion" placeholder="Motivo"  value="" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"  maxlength="255" >
                    </div>

                    <div class="col-lg-12 text-center">
                        <br>
                            <a href="index.php?component=deudas&view=deudas_list"><button type="button" class="btn btn-secondary" style="margin-top:10px; width:180px;margin-right:10px;"> ir al listado de deudas </button></a>
                        
                            <button type="submit" class="btn btn-primary" style="margin-top:10px;width:180px;margin-right:10px;"> Eliminar deuda </button>
                            <br> <br>  <br> 
                    </div>

                    </div>
                <div>

            </form>
        </div>
    </div>

</div>
                

           


 