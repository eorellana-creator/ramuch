<script><?php echo $mensaje;?></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php
// --- CONEXIÓN A LA BASE DE DATOS SEGÚN TUS ARCHIVOS --- //
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- CONSULTA PARA VERIFICAR EL ESTADO DEL PRÉSTAMO --- //
$prestamo_activo = 0; // Valor por defecto (0 = desactivado)
$sql = $mysql->query("SELECT valor FROM valores WHERE id = 1");
if ($mysql->f_num($sql) > 0) {
    $row = $mysql->f_array($sql);
    $prestamo_activo = $row['valor'];
}
?>

<script>
<?php echo isset($mensaje) ? $mensaje : ''; ?>
</script>

<div class="row">
    <!-- Tarjeta 1: Perfil de Socio -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-primary">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fa fa-user fa-3x"></i>
                </button>
                <div class="text-value"><?php echo isset($_SESSION["usuario_nombre"]) ? $_SESSION["usuario_nombre"] : ''; ?></div>
                <div>Socio(a) Ramuch</div>
            </div>
            <div class="card-footer">
                <a href="index.php?component=socios&view=socios">
                    <span class="pull-left">Ir a Mi Perfil</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta 2: Préstamo de Equipo -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-orange">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-hiking fa-3x"></i>
                </button>
                <div class="text-value">Equipo Ramuch</div>
                <div><?php echo ($prestamo_activo == 1) ? 'Solicitar préstamo de equipo' : 'Préstamo desactivado temporalmente'; ?></div>
            </div>
            
            <?php if ($prestamo_activo == 1): ?>
                <!-- Botón activo -->
                <div class="card-footer">
                    <a href="index.php?component=equipo&view=equipo_list">
                        <span class="pull-left">Ir a equipos</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </a>
                </div>
            <?php else: ?>
                <!-- Mensaje de desactivación -->
                <div class="card-footer">
                    <span class="pull-left text-white">Disponible próximamente</span>
                    <span class="pull-right"><i class="fa fa-times-circle"></i></span>
                    <div class="clearfix"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tarjeta 3: Pagar -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-success">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-hand-holding-usd fa-3x"></i>
                </button>
                <div class="text-value">Pagar</div>
                <div>Paga desde aquí Matrícula, Cuotas y otros</div>
            </div>
            <div class="card-footer">
                <a target='_blank' href="https://ramuch.cl/pagar/index.php?rut=<?php echo isset($_SESSION["usuario_rut"]) ? $_SESSION["usuario_rut"] : ''; ?>&email=<?php echo isset($_SESSION["usuario_email"]) ? $_SESSION["usuario_email"] : ''; ?>">
                    <span class="pull-left">Ir a pagar</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta 4: Contacto -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-purple">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-envelope fa-3x"></i>
                </button>
                <div class="text-value">Contáctanos</div>
                <div>Contacta a la directiva Ramuch</div>
            </div>
            <div class="card-footer">
                <a href="index.php?component=contacto&view=contacto">
                    <span class="pull-left">Ir a formulario de contacto</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjeta 5: Mercado Ramuch 
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-red">
            <div class="card-body pb-0">
                <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="fas fa-store fa-3x"></i>
                </button>
                <div class="text-value">Mercado Ramuch</div>
                <div>Ingresar al Mercado Ramuch</div>
            </div>
            <div class="card-footer">
                <a href="index.php?component=mercado&view=mercado">
                    <span class="pull-left">Ir al Mercado</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
    </div> -->
</div>

<div class="clear:both; width:100%; font-size:10px !important;"></div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Documentos Compartidos</h4>
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

<script>

$(document).ready(function() {
    // Cargar documentos al iniciar
    let currentComisionId = 0;
    let currentTipoId = 0;
    cargarDocumentos();


    // Función para cargar documentos con filtro
    function cargarDocumentos(filtroComision = 0, filtrotipodoc = 0) {
        const tbody = $('#tablaDocumentos tbody');
        
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
                    console.log(response);
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
                                    <a href="https://www.ramuch.cl/admin/components/dashboard/models/descarga.php?id=${doc.id_documento}" 
                                    class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i>
                                    </a>
                                   
                                </td>
                            </tr>
                        `);
                    });
                    
                } else {
                    tbody.html('<tr><td colspan="5" class="text-center text-danger">Error al cargar los documentos</td></tr>');
                    console.log(response);
                }
            },
            error: function() {
                tbody.html('<tr><td colspan="5" class="text-center text-danger">Error de conexión al cargar documentos</td></tr>');
                console.log(response);
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

        // Función para escapar HTML
    function escapeHtml(unsafe) {
        return unsafe ? unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;") : '';
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