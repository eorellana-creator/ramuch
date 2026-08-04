<?php
if (empty($_SESSION['intranet_csrf'])) {
    $_SESSION['intranet_csrf'] = bin2hex(random_bytes(24));
}
$csrfIntranet = $_SESSION['intranet_csrf'];
$esDirectiva = $rolIntranet === 'directiva';
$esDesarrollador = $rolIntranet === 'desarrollador';
?>
<div class="row mb-3" id="resumen-intranet">
    <div class="col-md-3"><div class="card text-white bg-info"><div class="card-body"><div class="h3" data-resumen="solicitada">0</div><div>Solicitadas</div></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-warning"><div class="card-body"><div class="h3" data-resumen="valorizada">0</div><div>Por aprobar</div></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-primary"><div class="card-body"><div class="h3" data-resumen="desarrollo">0</div><div>En ejecución</div></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-success"><div class="card-body"><div class="h3" data-resumen="finalizada">0</div><div>Finalizadas</div></div></div></div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center">
        <i class="fas fa-network-wired mr-2"></i> <strong>Intranet — Solicitudes de actualizaciones</strong>
        <button class="btn btn-primary ml-3" data-toggle="modal" data-target="#modalNuevaSolicitud"><i class="fas fa-plus"></i> Nueva solicitud</button>
        <button class="btn btn-sm btn-info ml-auto" id="recargar-intranet"><i class="fas fa-sync"></i> Recargar</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabla-intranet" class="table table-striped table-hover" style="width:100%;">
                <thead><tr><th>N°</th><th>Fecha</th><th>Solicitado por</th><th>Solicitud</th><th>Estado</th><th>Valor</th><th>Pago</th><th>Acciones</th></tr></thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5>Nueva solicitud</h5><button class="close" data-dismiss="modal">&times;</button></div>
    <div class="modal-body"><label>Describe detalladamente la actualización solicitada:</label><textarea id="nueva-solicitud-texto" class="form-control" rows="7" maxlength="5000"></textarea></div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary" id="guardar-solicitud">Guardar solicitud</button></div>
</div></div></div>

<div class="modal fade" id="modalEditarSolicitud" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5>Editar solicitud</h5><button class="close" data-dismiss="modal">&times;</button></div>
    <div class="modal-body">
        <input type="hidden" id="editar-solicitud-token">
        <label>Texto de la solicitud:</label><textarea id="editar-solicitud-texto" class="form-control" rows="7" maxlength="5000"></textarea>
        <small class="text-muted">La edición quedará registrada con tu usuario y fecha.</small>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary" id="guardar-edicion-solicitud">Guardar cambios</button></div>
</div></div></div>

<div class="modal fade" id="modalProcesoIntranet" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 id="titulo-proceso-intranet">Actualizar solicitud</h5><button class="close" data-dismiss="modal">&times;</button></div>
    <div class="modal-body">
        <input type="hidden" id="proceso-token"><input type="hidden" id="proceso-accion">
        <div id="campo-valor" style="display:none;"><label>Valor estimado (CLP):</label><input id="proceso-valor" type="number" min="0" class="form-control mb-3"></div>
        <label id="label-comentario">Comentario:</label><textarea id="proceso-comentario" class="form-control" rows="5" maxlength="5000"></textarea>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary" id="confirmar-proceso">Confirmar</button></div>
</div></div></div>

<div class="modal fade" id="modalHistorialIntranet" tabindex="-1"><div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-header"><h5>Historial de la solicitud</h5><button class="close" data-dismiss="modal">&times;</button></div>
    <div class="modal-body" id="historial-intranet-body">Cargando...</div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div>
</div></div></div>

<script>
window.INTRANET_CONFIG = <?php echo json_encode([
    'csrf' => $csrfIntranet,
    'rol' => $rolIntranet
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>
