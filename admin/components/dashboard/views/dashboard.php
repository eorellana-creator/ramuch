<script><?php echo $mensaje;?></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<style>
    /* Tarjetas de resumen: todas del mismo ancho y alto (toman el de la más alta),
       con el enlace siempre abajo. 1 columna en celular, 2 en tablet y 4 en escritorio. */
    .fila-resumen > [class*="col-"] { display: flex; }
    .fila-resumen .tarjeta-resumen { display: flex; flex-direction: column; width: 100%; }
    .fila-resumen .tarjeta-resumen .card-body { flex: 1 1 auto; }
    .fila-resumen .tarjeta-resumen .card-footer { margin-top: auto; }
    @media (min-width: 576px) {
        .fila-resumen { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-auto-rows: 1fr; }
        .fila-resumen > [class*="col-"] { max-width: none; }
    }
    @media (min-width: 992px) {
        .fila-resumen { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
</style>
<div class="row fila-resumen">
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-primary tarjeta-resumen">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fa fa-users fa-3x"></i>
                </button>
                <div class="text-value"><?php echo $cantidad_usuarios_activos;?></div>
                <div>Socios Activos</div>
            </div>
            <div class="card-footer">
                <a href="index.php?component=socios&view=socios_list">
                    <span class="pull-left">Ir al listado de Socios</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta Balance del Mes -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-info tarjeta-resumen">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-file-invoice-dollar fa-3x"></i>
                </button>
                <div>Balance de este mes</div>
                
                <!-- Tabla con texto blanco y tamaño original -->
                <table class="w-100 mt-2 text-white">
                    <tr>
                        <td>Ingresos</td>
                        <td class="text-right text-nowrap text-value">
                            <i class="fas fa-dollar-sign mr-1"></i><?php echo $ingresos_mes; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Egresos</td>
                        <td class="text-right text-nowrap text-value">
                            <i class="fas fa-dollar-sign mr-1"></i><?php echo $egresos_mes; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Utilidad</td>
                        <td class="text-right text-nowrap text-value">
                            <i class="fas fa-dollar-sign mr-1"></i><?php echo $total_mes; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="index.php?component=ingresos&view=all">
                    <span class="pull-left">Ir al listado de recaudaciones</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta Balance del Año (misma estructura) -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-success tarjeta-resumen">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-hand-holding-usd fa-3x"></i>
                </button>
                <div>Balance de este año</div>
                
                <!-- Tabla con texto blanco y tamaño original -->
                <table class="w-100 mt-2 text-white">
                    <tr>
                        <td>Ingresos</td>
                        <td class="text-right text-nowrap text-value">
                            <i class="fas fa-dollar-sign mr-1"></i><?php echo $ingresos_agno; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Egresos</td>
                        <td class="text-right text-nowrap text-value">
                            <i class="fas fa-dollar-sign mr-1"></i><?php echo $egresos_agno; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Utilidad</td>
                        <td class="text-right text-nowrap text-value">
                            <i class="fas fa-dollar-sign mr-1"></i><?php echo $total_agno; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="index.php?component=ingresos&view=all">
                    <span class="pull-left">Ir al listado de recaudaciones</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>


    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-danger tarjeta-resumen">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-exclamation-triangle fa-3x fa-3x"></i>
                </button>
                <div class="text-value"><?php echo $atrasados;?></div>
                <div>Total Deudas</div>
            </div>
            <div class="card-footer">
                <a href="index.php?component=deudas&view=deudas_list">
                    <span class="pull-left">Ir al listado de deudas</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-purple tarjeta-resumen">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-cash-register fa-3x fa-3x"></i>
                </button>
                <div class="text-value"><?php echo $saldo_caja;?></div>
                <div>Saldo en Caja</div>
            </div>
            <div class="card-footer">
                <a href="index.php?component=maestra&view=maestra_list">
                    <span class="pull-left">Ir a movimientos</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-orange tarjeta-resumen">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-hiking fa-3x fa-3x"></i>
                </button>
                <div class="text-value"><?php echo $equipo;?></div>
                <div>Equipo prestado</div>
            </div>
            <div class="card-footer">
                <a href="index.php?component=equipo&view=equipo_list">
                    <span class="pull-left">Ir a equipo prestado</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-dark tarjeta-resumen">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-file-invoice fa-3x fa-3x"></i>
                </button>
                <div class="text-value">Planes de Pago</div>
                <div></div>
            </div>
            <div class="card-footer">
                <a href="index.php?component=plan&view=plan_list">
                    <span class="pull-left">Ir al listado de Planes de Pago</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$mostrarGenero = !empty($distribucion_genero);
$mostrarProcesos = !empty($ver_procesos_automaticos);
if ($mostrarProcesos) {
    $hayErrorReciente = false;
    foreach ($procesos_estado as $estadoProceso) {
        if ($estadoProceso->resultado !== 'ok') { $hayErrorReciente = true; }
    }
    $fechaProceso = function ($fecha) { return date('d-m-Y H:i', strtotime($fecha)); };
}
?>
<?php if ($mostrarGenero || $mostrarProcesos): ?>
<style>
    .procesos-scroll { max-height: 420px; overflow-y: auto; }
    .procesos-tabla td { vertical-align: top; }
    @media (min-width: 992px) {
        /* Ambos recuadros terminan en la misma línea: el de procesos toma la altura
           del gráfico y hace scroll por dentro si tiene más contenido. */
        .fila-par { margin-bottom: 1.5rem; }
        .fila-par .dashboard-par-card { margin-bottom: 0; }
        .procesos-junto-grafico { position: relative; min-height: 300px; }
        .procesos-junto-grafico > .card { position: absolute; top: 0; left: 15px; right: 15px; bottom: 0; display: flex; flex-direction: column; }
        .procesos-junto-grafico .procesos-scroll { max-height: none; flex: 1 1 auto; min-height: 0; }
    }
</style>
<div class="row mt-2 fila-par">
<?php if ($mostrarGenero): ?>
    <div class="col-lg-6">
        <div class="card dashboard-par-card">
            <div class="card-header">
                <h4 class="mb-0">Socios activos por sexo/género</h4>
            </div>
            <div class="card-body">
                <div id="grafico-genero" style="width:100%; height:280px;"></div>
                <table class="table table-sm mt-3 mb-0">
                    <tbody>
                    <?php $totalGenero = array_sum($distribucion_genero); ?>
                    <?php foreach ($distribucion_genero as $etiquetaGenero => $cantidadGenero): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($etiquetaGenero, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-right"><?php echo number_format($cantidadGenero, 0, ',', '.'); ?></td>
                            <td class="text-right text-muted"><?php echo $totalGenero > 0 ? number_format($cantidadGenero * 100 / $totalGenero, 1, ',', '.') . '%' : '0%'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <small class="text-muted">"Sin información" corresponde a socios que no han indicado este dato (campo opcional).</small>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if ($mostrarProcesos): ?>
    <div class="col-lg-6<?php echo $mostrarGenero ? ' procesos-junto-grafico' : ''; ?>">
        <div class="card dashboard-par-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="mb-0"><i class="fa fa-cogs"></i> Procesos automáticos</h4>
                <?php if (empty($procesos_estado)): ?>
                    <span class="badge badge-secondary">Sin ejecuciones registradas</span>
                <?php elseif ($hayErrorReciente): ?>
                    <span class="badge badge-danger" style="font-size:90%;">Hay procesos con error</span>
                <?php else: ?>
                    <span class="badge badge-success" style="font-size:90%;">Todo funcionando</span>
                <?php endif; ?>
            </div>
            <div class="card-body procesos-scroll">
                <?php if (empty($procesos_automaticos)): ?>
                    <p class="text-muted mb-0">Todavía no hay ejecuciones registradas. Aparecerán aquí cuando corra el primer cron o se confirme un pago con Flow.</p>
                <?php else: ?>
                    <div class="mb-2">
                        <strong>Último resultado de cada proceso:</strong><br>
                        <?php foreach ($procesos_estado as $estadoProceso): ?>
                            <span class="badge <?php echo $estadoProceso->resultado === 'ok' ? 'badge-success' : 'badge-danger'; ?> mr-1 mb-1" style="font-size:85%; font-weight:normal; white-space:normal; text-align:left;">
                                <?php echo htmlspecialchars($estadoProceso->titulo, ENT_QUOTES, 'UTF-8'); ?>:
                                <?php echo $estadoProceso->resultado === 'ok' ? 'OK' : 'Error'; ?>,
                                <?php echo $fechaProceso($estadoProceso->fecha_fin); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <table class="table table-sm table-hover mb-1 procesos-tabla">
                        <thead>
                            <tr>
                                <th style="width:115px;">Fecha</th>
                                <th>Proceso y qué pasó</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($procesos_automaticos as $ejecucion): ?>
                            <tr class="<?php echo $ejecucion->resultado === 'ok' ? '' : 'table-danger'; ?>">
                                <td class="text-nowrap">
                                    <?php echo $fechaProceso($ejecucion->fecha_fin); ?><br>
                                    <?php if ($ejecucion->resultado === 'ok'): ?>
                                        <span class="badge badge-success">OK</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Error</span>
                                        <?php if ((int)$ejecucion->correo_enviado === 1): ?>
                                            <br><small class="text-muted">aviso enviado</small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($ejecucion->titulo, ENT_QUOTES, 'UTF-8'); ?></strong><br>
                                    <small><?php echo nl2br(htmlspecialchars($ejecucion->descripcion, ENT_QUOTES, 'UTF-8')); ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <small class="text-muted">Últimas 10 ejecuciones. Si un proceso termina con error, se avisa por correo a directiva@ramuch.cl.</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>
<?php if ($mostrarGenero): ?>
<script>
(function () {
    var datosGenero = <?php echo json_encode(array_map(null, array_keys($distribucion_genero), array_values($distribucion_genero)), JSON_UNESCAPED_UNICODE); ?>;
    function dibujarGraficoGenero() {
        var tabla = new google.visualization.DataTable();
        tabla.addColumn('string', 'Sexo/Género');
        tabla.addColumn('number', 'Socios');
        tabla.addRows(datosGenero);
        new google.visualization.PieChart(document.getElementById('grafico-genero')).draw(tabla, {
            pieHole: 0.45,
            legend: { position: 'right' },
            chartArea: { width: '90%', height: '85%' },
            colors: ['#c2185b', '#1976d2', '#7b1fa2', '#9e9e9e'],
            sliceVisibilityThreshold: 0
        });
    }
    if (window.google && google.charts) {
        google.charts.load('current', { packages: ['corechart'] });
        google.charts.setOnLoadCallback(dibujarGraficoGenero);
    }
})();
</script>
<?php endif; ?>
<?php endif; ?>

<div class="clear:both;width:100%; font-size:10px !important;"></div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Documentos Compartidos </h4>
                <?php if($mostrar_botones): ?>
                <button class="btn btn-primary" id="btnNuevoDocumento">
                    <i class="fas fa-plus"></i> Nuevo Documento
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Filtrar por comisión:</label>
                        <select class="form-control" id="filtroComision">
                            <option value="0">Todas las comisiones</option>
                            <?php foreach($comisiones as $c): ?>
                                <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Filtrar por tipo documento:</label>
                        <select class="form-control" id="filtrotipodoc">
                            <option value="0">Todos los tipos</option>
                            <?php foreach($tiposDocumento as $t): ?>
                                <option value="<?= $t->id_tipo ?>"><?= htmlspecialchars($t->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Ordenar por:</label>
                        <select class="form-control" id="ordenDocumentos">
                            <option value="fecha_subida DESC">Más recientes primero</option>
                            <option value="nombre_archivo ASC">Nombre (A-Z)</option>
                            <option value="nombre_archivo DESC">Nombre (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Buscar:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="buscadorDocumentos" placeholder="Nombre o descripción...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="btnLimpiarBusqueda">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de documentos -->
                <div class="table-responsive">
                    <table class="table table-striped" id="tablaDocumentos">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Comisión</th>
                                <th>Descripción</th>
                                <th>Tipo Documento</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los documentos se cargarán via AJAX -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <nav aria-label="Paginación documentos">
                    <ul class="pagination justify-content-center" id="paginacionDocumentos">
                        <!-- La paginación se generará via JS -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nuevo documento -->
<div class="modal fade" id="modalDocumento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir Nuevo Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Comisión</label>
                        <select class="form-control" name="id_comision" id="id_comision" required>
                            <option value="">Seleccione una comisión</option>
                            <?php foreach($comisiones as $c): ?>
                                <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tipo de Documento</label>
                        <select class="form-control" name="tipo_documento" id="tipo_documento" required>
                            <option value="">Seleccione un tipo</option>
                            <?php foreach($tiposDocumento as $tipo): ?>
                            <option value="<?= $tipo->id_tipo ?>"><?= htmlspecialchars($tipo->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Archivo (PDF, PPT, JPG, PNG, DOC, XLS - máximo 5MB)</label>
                        <input type="file" name="archivo" id="archivo" class="form-control-file" 
                               accept=".pdf,.ppt,.doc,.jpg,.png,.docx,.xls,.xlsx" required>
                    </div>
                    <div id="resultado" class="mt-3"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSubir">
                    <i class="fas fa-upload"></i> Subir Archivo
                </button>
            </div>
        </div>
    </div>
</div>

<script>

$(document).ready(function() {
    // Cargar documentos al iniciar

    let currentComisionId = 0;
    let currentTipoId = 0;
    cargarDocumentos();

    // Función para cargar documentos con filtro
    function cargarDocumentos(filtroComision = 0, filtrotipodoc = 0) {
        const tbody = $('#tablaDocumentos tbody');
        
        // Actualizar variables de estado
        currentComisionId = filtroComision;
        currentTipoId = filtrotipodoc ;

        // Mostrar mensaje de carga
        tbody.html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando documentos...</td></tr>');
        
        // Obtener el valor de ordenación
        const orden = $('#ordenDocumentos').val();
        const busqueda = $('#buscadorDocumentos').val();
        
        $.ajax({
            url: 'components/dashboard/models/get_documentos.php',
            type: 'GET',
            data: {
                filtro_comision: filtroComision, 
                filtro_tipodoc: filtrotipodoc,
                orden: orden
            },
            dataType: 'json',
            success: function(response) {
                if(response && response.success) {
                    tbody.empty();
                    
                    if(response.documentos.length === 0) {
                        tbody.append('<tr><td colspan="5" class="text-center">No hay documentos disponibles</td></tr>');
                        return;
                    }
                    
                    response.documentos.forEach(doc => {
                        tbody.append(`
                            <tr data-id="${doc.id_documento}">
                                <td>${escapeHtml(doc.nombre_archivo)}</td>
                                <td>${escapeHtml(doc.nombre_comision)}</td>
                                <td>${escapeHtml(doc.descripcion || '')}</td>
                                <td>${escapeHtml(doc.nombre_tipo || doc.tipo_documento)}</td>
                                <td>${formatearFecha(doc.fecha_subida)}</td>
                                <td>
                                    <a href="components/dashboard/models/descarga.php?id=${doc.id_documento}" 
                                    class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <?php if($mostrar_botones): ?>
                                    <button class="btn btn-sm btn-danger btn-eliminar" 
                                            data-id="${doc.id_documento}"
                                            data-token="${doc.token}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        `);
                    });
                    
                } else {
                    tbody.html('<tr><td colspan="5" class="text-center text-danger">Error al cargar los documentos</td></tr>');
                }
            },
            error: function() {
                tbody.html('<tr><td colspan="5" class="text-center text-danger">Error de conexión al cargar documentos</td></tr>');
            }
        });
    }

    
    // Evento para el filtro por tipo documento
    $('#filtrotipodoc').change(function() {
        const tipoId = $(this).val();
        cargarDocumentos(currentComisionId, tipoId);
    });

    // Evento para el filtro por comisión
    $('#filtroComision').change(function() {
        const comisionId = $(this).val();
        cargarDocumentos(comisionId, currentTipoId);
    });

    // Evento para el ordenamiento
    $('#ordenDocumentos').change(function() {
        // Mantener los filtros actuales al cambiar el orden
        cargarDocumentos(currentComisionId, currentTipoId);
    });


    // Función para formatear la fecha (opcional)
    function formatearFecha(fechaString) {
        const fecha = new Date(fechaString);
        return fecha.toLocaleDateString('es-CL', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    $('#uploadForm').on('submit', function(e) {
        if (!$('#tipo_documento').val()) {
            alert('Por favor seleccione un tipo de documento');
            e.preventDefault();
        }
    });

    // Función para eliminar documento
    $(document).on('click', '.btn-eliminar', function() {
    const id = $(this).data('id');
    const token = $(this).data('token');
    const $btn = $(this);
    const $fila = $btn.closest('tr');
    
        if(confirm('¿Estás seguro de eliminar este documento?')) {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: 'components/dashboard/models/delete_documento.php',
                type: 'POST',
                data: { id, token },
                dataType: 'json',
                success: function(response) {
                    if(response && response.success) {
                        // Opción 1: Eliminar la fila directamente
                        $fila.fadeOut(300, function() {
                            $(this).remove();
                            // Si quedó la tabla vacía, mostrar mensaje
                            if($('#tablaDocumentos tbody tr').length === 0) {
                                $('#tablaDocumentos tbody').html('<tr><td colspan="5" class="text-center">No hay documentos disponibles</td></tr>');
                            }
                        });
                        
                        // Opción 2: Recargar toda la lista (descomenta si prefieres esta opción)
                        // cargarDocumentos();
                    } else {
                        alert(response.error || 'Error al eliminar el documento');
                        $btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                    }
                },
                error: function() {
                    alert('Error en la conexión con el servidor');
                    $btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            });
        }
    });

   $(document).ready(function() {
    // Función optimizada para actualizar la lista
        function actualizarListaDocumentos() {
            return new Promise((resolve) => {
                $.ajax({
                    url: 'components/dashboard/models/get_documentos.php',
                    type: 'GET',
                    data: {
                        filtro_comision: $('#filtroComision').val(),
                        orden: $('#ordenDocumentos').val()
                    },
                    success: function(response) {
                        if(response && response.success) {
                            const tbody = $('#tablaDocumentos tbody');
                            tbody.empty();
                            
                            if(response.documentos.length > 0) {
                                response.documentos.forEach(doc => {
                                    tbody.append(`
                                        <tr data-id="${doc.id_documento}">
                                            <td>${doc.nombre_archivo}</td>
                                            <td>${doc.nombre_comision}</td>
                                            <td>${doc.descripcion || ''}</td>
                                            <td>${doc.tipo_documento}</td>
                                            <td>${new Date(doc.fecha_subida).toLocaleDateString()}</td>
                                            <td>
                                                <a href="components/dashboard/models/descarga.php?id=${doc.id_documento}" 
                                                class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger btn-eliminar" 
                                                        data-id="${doc.id_documento}"
                                                        data-token="${doc.token}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `);
                                });
                            } else {
                                tbody.append('<tr><td colspan="5" class="text-center">No hay documentos disponibles</td></tr>');
                            }
                        }
                        resolve();
                    },
                    error: function() {
                        console.error('Error al cargar documentos');
                        resolve(); // Asegurarnos de que siempre se resuelva la promesa
                    }
                });
            });
        }

        // Evento para subir documentos - Versión mejorada
        $('#btnSubir').click(async function() {
            // Validaciones básicas
            if (!$('#id_comision').val()) {
                $('#resultado').html('<div class="alert alert-warning">Selecciona una comisión</div>');
                return;
            }
            
            if (!$('#archivo').val()) {
                $('#resultado').html('<div class="alert alert-warning">Selecciona un archivo</div>');
                return;
            }

            const $btn = $(this);
            const formData = new FormData($('#uploadForm')[0]);
            
            // Deshabilitar botón durante la subida
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
            
            try {
                const response = await $.ajax({
                    url: 'components/dashboard/models/upload.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                });

                let html = '';
                if(response && response.success) {
                    html = `<div class="alert alert-success">
                        ${response.message}<br>
                        <a href="${response.file_url}" target="_blank">Ver archivo</a>
                    </div>`;
                    
                    $('#uploadForm')[0].reset();
                    
                    // Actualizar la lista y esperar a que complete
                    await actualizarListaDocumentos();
                    
                    // Cerrar el modal después de actualizar
                    $('#modalDocumento').modal('hide');
                } else {
                    const errorMsg = response.error || response.message || 'Error al subir archivo';
                    html = `<div class="alert alert-danger">${errorMsg}</div>`;
                }
                $('#resultado').html(html);
                
            } catch(xhr) {
                let error = 'Error en la conexión con el servidor';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    error = errorResponse.error || errorResponse.message || error;
                } catch(e) {
                    console.error('Error parsing response:', e);
                }
                $('#resultado').html(`<div class="alert alert-danger">${error}</div>`);
            } finally {
                $btn.prop('disabled', false).html('<i class="fas fa-upload"></i> Subir Archivo');
            }
        });
        
        // Limpiar resultados cuando se cierra el modal
        $('#modalDocumento').on('hidden.bs.modal', function() {
            $('#resultado').empty();
        });
    });
});


    
    // Función para escapar HTML
    function escapeHtml(unsafe) {
        return unsafe ? unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;") : '';
    }

   
    // Modal para nuevo documento - Versión mejorada
    $('#btnNuevoDocumento').off('click').on('click', function(e) {
        e.preventDefault();
        try {
            $('#modalDocumento').modal('show');
        } catch(e) {
            console.error('Error al mostrar modal:', e);
            alert('Error al abrir el formulario. Ver consola para detalles.');
        }
    });
    

// Función para filtrar documentos en el cliente
function filtrarDocumentosLocalmente(textoBusqueda) {
    const texto = textoBusqueda.toLowerCase();
    $('#tablaDocumentos tbody tr').each(function() {
        const $fila = $(this);
        const nombre = $fila.find('td:eq(0)').text().toLowerCase();
        const descripcion = $fila.find('td:eq(2)').text().toLowerCase();
        
        if (nombre.includes(texto) || descripcion.includes(texto)) {
            $fila.show();
        } else {
            $fila.hide();
        }
    });
}

// Evento para el buscador
$('#buscadorDocumentos').on('input', function() {
    filtrarDocumentosLocalmente($(this).val());
});

// Evento para limpiar búsqueda
$('#btnLimpiarBusqueda').click(function() {
    $('#buscadorDocumentos').val('');
    $('#tablaDocumentos tbody tr').show();
});


</script>
