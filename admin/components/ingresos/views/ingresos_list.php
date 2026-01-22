<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club de Montaña - Ingresos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.min.css">
    <style>
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn-group-header {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .modal-content {
            border-radius: 10px;
        }
        .filter-section {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .filter-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #0d6efd;
        }
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-3">
        <input id="subcuenta" name="subcuenta" type="hidden" value="<?php echo @$subcuenta;?>">

        <div class="card">
            <div class="card-header">
                <div>
                    <i class="fas fa-sign-in-alt"></i> Ingresos <?php echo @$subcuenta;?> 
                    <?php echo @$agregar_ingreso;?>

                    <!-- Botón para abrir modal de informes -->
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalInformes">
                        <i class="fas fa-file-excel"></i> Emitir Informes
                    </button>
                </div>
                <div class="btn-group-header">
                    <a href="javascript:document.location.reload();">
                        <span class="badge bg-primary" style='padding:6px;margin-bottom:6px;'>
                            <i class="fas fa-sync"></i> Recargar datos
                        </span>
                    </a>

                </div>
            </div>  
            <div class="card-body">
                <table id="tabla" class="table table-striped table-hover dt-responsive display" style="width:100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Glosa</th>
                            <th>Observación</th>
                            <th>Medio</th>
                            <th>Doc. Respaldo</th>
                            <th>Estado</th>
                            <th>Monto</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Informes -->
    <div class="modal fade" id="modalInformes" tabindex="-1" aria-labelledby="modalInformesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalInformesLabel">
                        <i class="fas fa-file-excel"></i> Generar Informe
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formInformes">
                        <div class="filter-section">
                            <div class="filter-title">Filtros de Fecha</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="fechaDesde" class="form-label">Desde</label>
                                    <input type="date" class="form-control" id="fechaDesde" name="fecha_desde">
                                </div>
                                <div class="col-md-6">
                                    <label for="fechaHasta" class="form-label">Hasta</label>
                                    <input type="date" class="form-control" id="fechaHasta" name="fecha_hasta">
                                </div>
                            </div>
                        </div>

                        <div class="filter-section">
                            <div class="filter-title">Tipo de Movimiento</div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="ingreso" id="tipoIngreso" name="tipo[]" checked>
                                <label class="form-check-label" for="tipoIngreso">
                                    Ingreso
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="egreso" id="tipoEgreso" name="tipo[]">
                                <label class="form-check-label" for="tipoEgreso">
                                    Egreso
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="otros" id="tipoOtros" name="tipo[]">
                                <label class="form-check-label" for="tipoOtros">
                                    Otros
                                </label>
                            </div>
                        </div>

                        <div class="filter-section">
                            <div class="filter-title">Subcuenta</div>
                            <select class="form-select" id="filtroSubcuenta" name="subcuenta">
                                <option value="">Todas las subcuentas</option>
                                <!-- Las opciones se llenarán dinámicamente -->
                            </select>
                        </div>

                        <div class="filter-section">
                            <div class="filter-title">Medio</div>
                            <select class="form-select" id="filtroMedio" name="medio">
                                <option value="">Todos los medios</option>
                                <!-- Las opciones se llenarán dinámicamente -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGenerarInforme">
                        <i class="fas fa-download"></i> Descargar Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>

<script>
    // Cargar opciones de filtros desde el backend
    function cargarOpcionesFiltros() {
        console.log("Iniciando carga de opciones de filtros...");
        
        // Cargar subcuentas disponibles
        $.post('components/ingresos/models/obtener_subcuentas.php', function(data) {
            console.log("Respuesta de obtener_subcuentas.php:", data);
            
            try {
                // Verificar si la respuesta es JSON válido
                if (typeof data === 'string') {
                    data = JSON.parse(data);
                }
                
                var filtroSubcuenta = $('#filtroSubcuenta');
                if (data.length > 0) {
                    $.each(data, function(index, subcuenta) {
                        filtroSubcuenta.append($('<option>', {
                            value: subcuenta,
                            text: subcuenta
                        }));
                    });
                    console.log("Subcuentas cargadas correctamente:", data.length);
                } else {
                    console.warn("No se encontraron subcuentas o el array está vacío");
                    filtroSubcuenta.append('<option value="">No hay subcuentas disponibles</option>');
                }
            } catch (error) {
                console.error("Error al procesar subcuentas:", error, "Respuesta:", data);
                $('#filtroSubcuenta').append('<option value="">Error al cargar subcuentas</option>');
            }
        })
        .fail(function(xhr, status, error) {
            console.error("Error en la petición de subcuentas:", status, error);
            $('#filtroSubcuenta').append('<option value="">Error de conexión</option>');
        });

        // Cargar medios disponibles
        $.post('components/ingresos/models/obtener_medios.php', function(data) {
            console.log("Respuesta de obtener_medios.php:", data);
            
            try {
                // Verificar si la respuesta es JSON válido
                if (typeof data === 'string') {
                    data = JSON.parse(data);
                }
                
                var filtroMedio = $('#filtroMedio');
                if (data.length > 0) {
                    $.each(data, function(index, medio) {
                        filtroMedio.append($('<option>', {
                            value: medio,
                            text: medio
                        }));
                    });
                    console.log("Medios cargados correctamente:", data.length);
                } else {
                    console.warn("No se encontraron medios o el array está vacío");
                    filtroMedio.append('<option value="">No hay medios disponibles</option>');
                }
            } catch (error) {
                console.error("Error al procesar medios:", error, "Respuesta:", data);
                $('#filtroMedio').append('<option value="">Error al cargar medios</option>');
            }
        })
        .fail(function(xhr, status, error) {
            console.error("Error en la petición de medios:", status, error);
            $('#filtroMedio').append('<option value="">Error de conexión</option>');
        });
    }

    // Llamar a la función para cargar opciones
    console.log("Ejecutando cargarOpcionesFiltros...");
    cargarOpcionesFiltros();

    // Función para exportar a Excel
    $('#btnGenerarInforme').on('click', function() {
        console.log("Preparando exportación a Excel...");
        
        // Obtener valores de los filtros
        var formData = $('#formInformes').serialize();
        console.log("Datos del formulario:", formData);
        
        // Validar fechas
        var fechaDesde = $('#fechaDesde').val();
        var fechaHasta = $('#fechaHasta').val();
        
        if (fechaDesde && fechaHasta && fechaDesde > fechaHasta) {
            alert("Error: La fecha 'Desde' no puede ser mayor que la fecha 'Hasta'");
            console.error("Error de validación: fechaDesde > fechaHasta", fechaDesde, fechaHasta);
            return;
        }
        
        // Agregar el tipo de acción
        formData += '&accion=exportar_excel';
        console.log("Datos finales para exportar:", formData);
        
        // Redireccionar a un script PHP que generará el Excel
        window.location.href = 'components/ingresos/models/exportar_excel.php?' + formData;
        
        // Cerrar el modal después de descargar
        $('#modalInformes').modal('hide');
    });

    // Al abrir el modal, establecer fechas por defecto (mes actual)
    $('#modalInformes').on('show.bs.modal', function () {
        console.log("Abriendo modal de informes...");
        
        var today = new Date();
        var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        var lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        $('#fechaDesde').val(firstDay.toISOString().slice(0, 10));
        $('#fechaHasta').val(lastDay.toISOString().slice(0, 10));
        
        console.log("Fechas establecidas: Desde", firstDay.toISOString().slice(0, 10), "Hasta", lastDay.toISOString().slice(0, 10));
    });
</script>
</body>
</html>