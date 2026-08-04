<div class="card">
    <div class="card-header">
        <i class="fas fa-boxes"></i> <strong>Inventario de Equipo</strong>
        <a href="index.php?component=equipo&view=equipo&origen=inventario" class="btn btn-primary ml-3">
            <i class="fas fa-plus"></i> Agregar nuevo Equipo
        </a>
        <a href="javascript:document.location.reload();" class="float-right">
            <span class="badge badge-primary" style="padding:6px;"><i class="fas fa-sync"></i> Recargar datos</span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabla-inventario" class="table table-striped table-hover dt-responsive display" style="width:100%;">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Imagen</th>
                        <th>Nombre de Equipo</th>
                        <th>ID Único</th>
                        <th>Estado</th>
                        <th>Disponibilidad</th>
                        <th>Prestado a</th>
                        <th>Fecha devolución</th>
                        <th>Ver detalle</th>
                        <th>Editar Equipo</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="detalleInventarioModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Equipo</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="detalleInventarioBody">Cargando...</div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imagen del Equipo</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center"><img id="modalImage" src="" alt="Equipo" class="img-fluid"></div>
        </div>
    </div>
</div>
