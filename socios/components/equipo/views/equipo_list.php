<?php echo $mensaje;?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-hiking"></i> <strong>Equipos</strong> 
        <a href="javascript:document.location.reload();"><span class="badge badge-primary float-right" style='padding:6px;margin-bottom:6px;'><i class="fas fa-sync"></i> Recargar datos</span></a> 
    </div>  
    <div class="card-body">
        <!-- Date range inputs for all selected equipment -->
        <div class="mb-4 date-range-container" style="display: none;">
            <div class="row">
                <div class="col-md-5">
                    <label>Fecha desde:</label>
                    <input class="form-control" id="fecha_global_desde" type="date" name="fecha_global_desde">
                </div>
                <div class="col-md-5">
                    <label>Fecha hasta:</label>
                    <input class="form-control" id="fecha_global_hasta" type="date" name="fecha_global_hasta">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary btn-block" onClick="solicitarMultiplesEquipos()">Solicitar Equipos</button>
                </div>
            </div>
        </div>

        <div class="table-responsive"> 
            <table id="tabla" class="table table-striped table-hover dt-responsive display" style="width:100%;">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes()"></th>
                        <th>N°</th>
                        <th>Imagen</th>
                        <th>Nombre de Equipo</th>
                        <th>ID Único</th>
                        <th>Estado</th>
                        <th style="min-width:130px;">Disponibilidad</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Rest of your existing modals -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateToday = new Date();
    const maxDate = dateToday.toISOString().split('T')[0];
    
    document.getElementById('fecha_global_desde').setAttribute('min', maxDate);
    document.getElementById('fecha_global_hasta').setAttribute('min', maxDate);
});

function toggleAllCheckboxes() {
    const mainCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('input[name="equipment_checkbox"]');
    checkboxes.forEach(checkbox => {
        if (!checkbox.disabled) {
            checkbox.checked = mainCheckbox.checked;
        }
    });
    updateDateRangeVisibility();
}

function updateDateRangeVisibility() {
    const checkboxes = document.querySelectorAll('input[name="equipment_checkbox"]:checked');
    const dateRangeContainer = document.querySelector('.date-range-container');
    dateRangeContainer.style.display = checkboxes.length > 0 ? 'block' : 'none';
}

function solicitarMultiplesEquipos() {
    const fecha1 = document.getElementById('fecha_global_desde').value;
    const fecha2 = document.getElementById('fecha_global_hasta').value;
    
    if (!fecha1 || !fecha2) {
        BootstrapDialog.alert("Se deben seleccionar las fechas desde y hasta, para solicitar el préstamo.");
        return;
    }

    const selectedEquipment = [];
    document.querySelectorAll('input[name="equipment_checkbox"]:checked').forEach(checkbox => {
        selectedEquipment.push(checkbox.value);
    });

    if (selectedEquipment.length === 0) {
        BootstrapDialog.alert("Debe seleccionar al menos un equipo para solicitar.");
        return;
    }

    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

    $.ajax({
        url: "components/equipo/models/solicitar_equipo.php",
        type: "get",
        data: {
            fecha1: fecha1,
            fecha2: fecha2,
            tokens: selectedEquipment
        },
        success: function(response) {
            window.scrollTo(0, 0);
            document.location.reload();
        }
    });
}
</script>