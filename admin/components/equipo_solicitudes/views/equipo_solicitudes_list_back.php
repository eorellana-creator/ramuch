<script> 

</script>    

<?php echo $mensaje;?>

        <div class="card">
                    <div class="card-header">
                    <i class="fas fa-hiking"></i> <strong>Solicitudes de equipos</strong> &nbsp; &nbsp; 
                       
                        <a href="javascript:document.location.reload();"><span class="badge badge-primary float-right" style='padding:6px;margin-bottom:6px;'><i class="fas fa-sync"></i> Recargar datos</span></a> 
            
                    </div>  
                <div class="card-body">
    
                <div class="table-responsive"  > 
                <table id="tabla" class="  table table-striped table-hover dt-responsive display   "  style="width:100%;"  >
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Nombre de Equipo</th>
                                <th>Solicitado por:</th>
                                <th>Periodo de Solicitud:</th>
                                <th>Aceptar/Rechazar</th>
                            </tr>
                        </thead>
                    
                    </table>
</div>

                </div>
        </div>




        <input id="tokenPrestamo" name="tokenPrestamo" type="hidden" value="">
       

        <div class="modal fade" id="primaryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-danger" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Rechazo de Préstamo de Equipo</h4>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
                <div class="modal-body">
                <label>Ingresa el Motivo del rechazo</label>

                  <input type="text" class="form-control" name="observacion" id="observacion" placeholder="Observación del rechazo" value="" onblur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                <div style="width:100%; height:10px;"></div>
         

                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" type="button" data-dismiss="modal" onClick="seteaTokenPrestamo('');">Cancelar</button>
                  <button class="btn btn-danger" type="button" onClick="rechaza();" >Registrar rechazo</button>
                </div>
              </div>
              <!-- /.modal-content-->
            </div>
            <!-- /.modal-dialog-->
          </div>