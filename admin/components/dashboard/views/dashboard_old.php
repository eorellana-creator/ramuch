<script><?php echo $mensaje;?></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php 
session_start();
// Define los IDs permitidos
$usuarios_permitidos = [1, 2143, 1978, 2150, 1752, 1794];
$mostrar_botones = in_array($_SESSION['usuario_id'], $usuarios_permitidos);


?>

<script>
// Debug en consola
window.addEventListener('DOMContentLoaded', () => {
    console.group('Debug de permisos');
    console.log('ID Usuario:', <?= json_encode($usuario_id) ?>);
    console.log('Usuarios permitidos:', <?= json_encode($usuarios_permitidos) ?>);
    console.log('Mostrar botones:', <?= json_encode($mostrar_botones) ?>);
    console.groupEnd();
});
console.log("Datos de Sesión:", <?= json_encode($_SESSION) ?>);

</script>

<div class="row">
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-primary">
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
        <div class="card text-white bg-info">
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
        <div class="card text-white bg-success">
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
        <div class="card text-white bg-danger">
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
        <div class="card text-white bg-purple">
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
        <div class="card text-white bg-orange">
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
        <div class="card text-white bg-success">
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
                                <td>${escapeHtml(doc.tipo_documento)}</td>
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