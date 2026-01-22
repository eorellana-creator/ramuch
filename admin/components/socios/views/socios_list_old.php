<script>
</script>

<!-- Campos ocultos para almacenar valores de tipo y cuota -->
<input id="tipo" name="tipo" type="hidden" value="<?php echo @$_GET['tipo']; ?>" />
<input id="cuota" name="cuota" type="hidden" value="<?php echo @$_GET['cuota']; ?>" />

<!-- Contenedor principal de la tarjeta -->
<div class="card">
    <!-- Encabezado de la tarjeta -->
    <div class="card-header">
        <i class="fa fa-user"></i>
        <!-- Enlaces para descargar correos y archivo Excel -->
        <a href="components/socios/models/lista_correos.txt" download target="_blank">
            <i class="fas fa-envelope" title="descargar correos"></i>
        </a>
        <a href="components/socios/excel/lista_usuarios_excel<?php echo $_SESSION['usuario_id']; ?>.xls" download target="_blank">
            <i class="fas fa-file-excel"></i>
        </a>
        Listado de Socios
        <a href="index.php?component=socios&amp;view=socios"> | </a>
        <a href="components/socios/models/listado_al_dia.php" target="_blank">
            <i class="fas fa-file-invoice-dollar"></i> Socios al día (hasta 3 meses deuda)
        </a>
        |
        <a href="components/socios/models/listado_al_dia6.php" target="_blank">
            <i class="fas fa-file-invoice-dollar"></i> Socios deuda más de 6 meses
        </a>
        |
        <!-- Botón para agregar nuevo socio -->
        <a href="index.php?component=socios&view=socios">
            <button type="button" class="btn btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> Agregar nuevo Socio
            </button>
        </a>
        <!-- Botón para abrir el modal de emisión de listados -->
        <button type="button" class="btn btn-success ml-2" data-toggle="modal" data-target="#emisionListadosModal">
            <i class="fas fa-list-alt"></i> Emisión de Listados
        </button>

        <!-- Botón para recargar datos -->
        <a href="javascript:document.location.reload();">
            <span class="badge badge-primary float-right" style="padding:6px;margin-bottom:6px;">
                <i class="fas fa-sync"></i> Recargar datos
            </span>
        </a>

        <!-- Filtros desplegables -->
        <div class="float-right">
            <select id="cuotas" name="cuotas" style="margin-right:20px;width:250px;margin-bottom:6px;" onChange="selCuotas(this)">
                <option value="1" selected>Con cuotas al día y atrasadas</option>
                <option value="2">Con cuotas atrasadas</option>
                <option value="3">Con cuotas al día</option>
            </select>

            <select id="tipos" name="tipos" style="margin-right:20px;width:250px;" onChange="selTipo(this)">
                <option value="10" selected>Profesionales y Estudiantes</option>
                <option value="1">Profesionales</option>
                <option value="3">Estudiantes</option>
                <option value="2">Honorarios</option>
                <option value="6">Congelado</option>
                <option value="7">Desvinculado</option>
                <option value="8">Eliminados</option>
                <option value="0123499">Todos</option>
            </select>
        </div>
    </div>

    <!-- Cuerpo de la tarjeta -->
    <div class="card-body">
        <!-- Tabla responsiva -->
        <div class="table-responsive">
            <table id="tabla" class="table table-striped table-hover dt-responsive display" style="width:100%;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Rol</th>
                        <th>Nombre</th>
                        <th>Rut</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Tipo de Inscripción</th>
                        <th>Estado</th>
                        <th>Fecha de Registro</th>
                        <th>Matricula Pagada</th>
                        <th>Monto Deudas</th>
                        <th>Ver</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal para emisión de listados -->
<div class="modal fade" id="emisionListadosModal" tabindex="-1" role="dialog" aria-labelledby="emisionListadosModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emisionListadosModalLabel">Emisión de Listados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="filtroListadosForm">
                    <div class="form-group">
                        <label for="fechaInicio">Fecha de Inicio:</label>
                        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" required />
                    </div>
                    <div class="form-group">
                        <label for="fechaFin">Fecha de Fin:</label>
                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" required />
                    </div>
                    <div class="form-group">
                        <label for="tipoListado">Tipo de Listado:</label>
                        <select class="form-control" id="tipoListado" name="tipoListado">
                            <option value="deudores">Deudores</option>
                            <option value="alDia">Socios al día</option>
                            <option value="todos">Todos los socios</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Inscripción:</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="tipoInscripcionTodos" name="tipoInscripcion[]" value="todos">
                                <label class="form-check-label" for="tipoInscripcionTodos">Todos</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="tipoInscripcion1" name="tipoInscripcion[]" value="1">
                                <label class="form-check-label" for="tipoInscripcion1">Profesional</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="tipoInscripcion2" name="tipoInscripcion[]" value="2">
                                <label class="form-check-label" for="tipoInscripcion2">Honorario</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="tipoInscripcion3" name="tipoInscripcion[]" value="3">
                                <label class="form-check-label" for="tipoInscripcion3">Estudiante</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="tipoInscripcion6" name="tipoInscripcion[]" value="6">
                                <label class="form-check-label" for="tipoInscripcion6">Congelado</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="tipoInscripcion7" name="tipoInscripcion[]" value="7">
                                <label class="form-check-label" for="tipoInscripcion7">Desvinculado</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="tipoInscripcion8" name="tipoInscripcion[]" value="8">
                                <label class="form-check-label" for="tipoInscripcion8">Eliminado</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarExcel()">Generar Excel</button>
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar la generación del Excel -->
<script>
// Función para controlar el comportamiento de "Todos"
document.getElementById('tipoInscripcionTodos').addEventListener('change', function () {
    const isChecked = this.checked;
    document.querySelectorAll('input[name="tipoInscripcion[]"]').forEach(checkbox => {
        if (checkbox.id !== 'tipoInscripcionTodos') {
            checkbox.checked = isChecked;
        }
    });
});

function generarExcel() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;
    const tipoListado = document.getElementById('tipoListado').value;

    // Obtener los tipos de inscripción seleccionados
    const tipoInscripcionInputs = document.querySelectorAll('input[name="tipoInscripcion[]"]:checked');
    const tipoInscripcion = Array.from(tipoInscripcionInputs).map(input => input.value);

    if (!fechaInicio || !fechaFin) {
        alert('Por favor, seleccione un rango de fechas válido.');
        return;
    }

    if (tipoInscripcion.length === 0) {
        alert('Por favor, seleccione al menos un tipo de inscripción.');
        return;
    }

    // Construir la URL o realizar la solicitud al servidor
    const url = `components/socios/models/generar_excel.php?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&tipoListado=${tipoListado}&tipoInscripcion=${tipoInscripcion.join(',')}`;
    window.open(url, '_blank');
}
</script>