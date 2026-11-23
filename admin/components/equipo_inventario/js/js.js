$(document).ready(function() {
    var $tabla = $('#tabla-inventario');

    $tabla.on('processing.dt', function(e, settings, processing) {
        $tabla.closest('.dataTables_wrapper').toggleClass('tabla-procesando', processing);
    });

    $tabla.DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
            processing: '<span class="spinner-border spinner-border-sm" role="status"></span> Procesando...'
        },
        ordering: true,
        processing: true,
        serverSide: true,
        responsive: false,
        order: [[2, 'asc']],
        pageLength: 50,
        lengthMenu: [[25, 50, 100], [25, 50, 100]],
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row mb-2'<'col-sm-12'p>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        columnDefs: [{ orderable: false, targets: [1, 8, 9] }],
        ajax: {
            url: 'components/equipo_inventario/models/inventario_list_procesa.php',
            type: 'POST'
        }
    });

    $('#imageModal').on('show.bs.modal', function(event) {
        $(this).find('#modalImage').attr('src', $(event.relatedTarget).data('img'));
    });

    $('#detalleInventarioModal').on('show.bs.modal', function(event) {
        var modal = $(this);
        modal.find('#detalleInventarioBody').html('Cargando...');
        $.ajax({
            url: 'components/equipo/models/get_equipo_details.php',
            type: 'GET',
            data: { token: $(event.relatedTarget).data('token') }
        }).done(function(response) {
            modal.find('#detalleInventarioBody').html(response);
        }).fail(function() {
            modal.find('#detalleInventarioBody').html('<div class="alert alert-danger">No se pudo cargar el equipo.</div>');
        });
    });
});
