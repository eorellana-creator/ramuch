<script> 

</script>    

<?php echo $mensaje;?>

        <div class="card">
                    <div class="card-header">
                    <i class="fas fa-hiking"></i> <strong>Equipos</strong> 
                       
                        <a href="javascript:document.location.reload();"><span class="badge badge-primary float-right" style='padding:6px;margin-bottom:6px;'><i class="fas fa-sync"></i> Recargar datos</span></a> 
            
                    </div>  
                <div class="card-body">
    
                <div class="table-responsive"  > 
                <table id="tabla" class="  table table-striped table-hover dt-responsive display   "  style="width:100%;"  >
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Imagen</th>
                                <th>Nombre de Equipo</th>
                                <th>ID Único</th>
                                <th>Estado</th>
                                <th style="min-width:130px;">Solicitar Prestamo</th>
                            </tr>
                        </thead>
                    
                    </table>
</div>

                </div>
        </div>




        <input id="tokenEquipo" name="tokenEquipo" type="hidden" value="">


        <div class="modal fade" id="primaryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Devolución de Equipo</h4>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
                <div class="modal-body">
                <label>¿Alguna observación que agregar a la devolución del equipo?</label>

                  <input type="text" class="form-control" name="observacion" id="observacion" placeholder="Observación de la devolución" value="" onblur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
<div style="width:100%; height:10px;"></div>
                  <label>Estado de la devolución:</label>
                    <select id="estado" name="estado" class="form-control" >
                        <option value="En el mismo estado">En el mismo estado</option>
                        <option value="Con detalles">Con detalles</option>
                        <option value="Extraviado">Extraviado</option>
                        <option value="Inutilizable">Inutilizable</option>
                    </select>

                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" type="button" data-dismiss="modal" onClick="seteaTokenEquipo('');">Cancelar</button>
                  <button class="btn btn-primary" type="button" onClick="devolverEquipo();" >Registrar devolución</button>
                </div>
              </div>
              <!-- /.modal-content-->
            </div>
            <!-- /.modal-dialog-->
          </div>

          <!-- Modal para mostrar la imagen ampliada y el nombre del equipo -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Imagen Ampliada" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#imageModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Botón que activó el modal
            var imageUrl = button.data('img'); // Extrae la información de los atributos data-*
            var modal = $(this);
            modal.find('.modal-title').text(button.data('title'));
            modal.find('.modal-body img').attr('src', imageUrl);
        });
    });
</script>