$(document).ready(function() {
    var config = window.INTRANET_CONFIG || {};
    var tabla = $('#tabla-intranet').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' },
        processing: true,
        serverSide: true,
        responsive: false,
        order: [[0, 'desc']],
        pageLength: 25,
        columnDefs: [{ orderable: false, targets: [3, 7] }],
        ajax: { url: 'components/intranet/models/listar.php', type: 'POST' }
    });

    function recargar() {
        tabla.ajax.reload(null, false);
        $.getJSON('components/intranet/models/resumen.php', function(data) {
            Object.keys(data).forEach(function(clave) {
                $('[data-resumen="' + clave + '"]').text(data[clave]);
            });
        });
    }
    recargar();
    $('#recargar-intranet').on('click', recargar);

    $('#guardar-solicitud').on('click', function() {
        var texto = $('#nueva-solicitud-texto').val().trim();
        if (!texto) { BootstrapDialog.alert('Debes escribir la solicitud.'); return; }
        $.post('components/intranet/models/crear.php', { csrf: config.csrf, texto: texto }, function() {
            $('#modalNuevaSolicitud').modal('hide'); $('#nueva-solicitud-texto').val(''); recargar();
        }, 'json').fail(mostrarError);
    });

    $('#tabla-intranet').on('click', '.accion-intranet', function() {
        var boton = $(this);
        $('#proceso-token').val(boton.data('token'));
        $('#proceso-accion').val(boton.data('accion'));
        $('#titulo-proceso-intranet').text(boton.data('titulo'));
        $('#label-comentario').text(boton.data('label') || 'Comentario:');
        $('#proceso-comentario').val(''); $('#proceso-valor').val('');
        $('#campo-valor').toggle(boton.data('accion') === 'valorizar');
        $('#modalProcesoIntranet').modal('show');
    });

    $('#confirmar-proceso').on('click', function() {
        $.post('components/intranet/models/transicion.php', {
            csrf: config.csrf,
            token: $('#proceso-token').val(),
            accion: $('#proceso-accion').val(),
            comentario: $('#proceso-comentario').val(),
            valor: $('#proceso-valor').val()
        }, function() { $('#modalProcesoIntranet').modal('hide'); recargar(); }, 'json').fail(mostrarError);
    });

    $('#tabla-intranet').on('click', '.historial-intranet', function() {
        $('#historial-intranet-body').html('Cargando...'); $('#modalHistorialIntranet').modal('show');
        $('#historial-intranet-body').load('components/intranet/models/historial.php?token=' + encodeURIComponent($(this).data('token')));
    });

    function mostrarError(xhr) {
        var mensaje = 'No se pudo completar la operación.';
        if (xhr.responseJSON && xhr.responseJSON.error) mensaje = xhr.responseJSON.error;
        BootstrapDialog.alert(mensaje);
    }
});
