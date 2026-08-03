<div class="card">
    <div class="card-header">
        <i class="fas fa-calendar-plus"></i> <strong>Solicitar Equipo</strong>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            La solicitud se registrará a nombre de <strong><?php echo htmlspecialchars($_SESSION["usuario_nombre"] ?? "", ENT_QUOTES, "UTF-8"); ?></strong>.
        </div>

        <div class="mb-4" id="fecha-reserva-container" style="display:none;">
            <div class="row">
                <div class="col-md-5">
                    <label>Fecha desde:</label>
                    <input class="form-control" id="fecha_reserva_desde" type="date">
                </div>
                <div class="col-md-5">
                    <label>Fecha hasta:</label>
                    <input class="form-control" id="fecha_reserva_hasta" type="date">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary btn-block" onclick="solicitarEquiposAdmin()">Solicitar</button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tabla-reserva" class="table table-striped table-hover" style="width:100%;">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="seleccionar-todos-reserva"></th>
                        <th>N°</th>
                        <th>Imagen</th>
                        <th>Nombre de Equipo</th>
                        <th>ID Único</th>
                        <th>Estado</th>
                        <th>Disponibilidad</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Equipo</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center"><img src="" alt="Equipo" class="img-fluid"></div>
        </div>
    </div>
</div>

<script>
(function () {
    var hoy = new Date().toISOString().split('T')[0];
    $('#fecha_reserva_desde, #fecha_reserva_hasta').attr('min', hoy);

    var tabla = $('#tabla-reserva').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' },
        ordering: false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: { url: '../socios/components/equipo/models/equipo_list_procesa.php', type: 'POST' },
        columnDefs: [{ orderable: false, targets: [0, 1, 2, 3, 4, 5, 6] }]
    });

    function actualizarFechas() {
        $('#fecha-reserva-container').toggle($('input[name="equipment_checkbox"]:checked').length > 0);
    }

    $('#tabla-reserva').on('change', 'input[name="equipment_checkbox"]', actualizarFechas);
    $('#seleccionar-todos-reserva').on('change', function () {
        var marcado = this.checked;
        $('#tabla-reserva input[name="equipment_checkbox"]:not(:disabled)').prop('checked', marcado);
        actualizarFechas();
    });

    $('#imageModal').on('show.bs.modal', function (event) {
        $(this).find('img').attr('src', $(event.relatedTarget).data('img'));
    });

    window.solicitarEquiposAdmin = function () {
        var fecha1 = $('#fecha_reserva_desde').val();
        var fecha2 = $('#fecha_reserva_hasta').val();
        var tokens = $('input[name="equipment_checkbox"]:checked').map(function () { return this.value; }).get();

        if (!fecha1 || !fecha2 || tokens.length === 0) {
            BootstrapDialog.alert('Selecciona al menos un equipo y ambas fechas.');
            return;
        }

        $.blockUI({ message: '<h4>Procesando solicitud...</h4>' });
        $.ajax({
            url: '../socios/components/equipo/models/solicitar_equipo.php',
            type: 'GET',
            data: { fecha1: fecha1, fecha2: fecha2, tokens: tokens }
        }).done(function () {
            BootstrapDialog.alert('La solicitud fue registrada correctamente.');
            tabla.ajax.reload(null, false);
            $('#fecha-reserva-container').hide();
            $('#seleccionar-todos-reserva').prop('checked', false);
        }).fail(function (xhr) {
            var mensaje = xhr.status === 401
                ? 'La sesión administrativa no es válida. Vuelve a iniciar sesión.'
                : 'No se pudo registrar la solicitud. Intenta nuevamente.';
            BootstrapDialog.alert(mensaje);
        }).always(function () {
            $.unblockUI();
        });
    };
})();
</script>
