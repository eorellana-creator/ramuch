$(document).ready(function() {
    function mostrarError(xhr) {
        var mensaje = 'No se pudo completar la operación.';
        if (xhr.responseJSON && xhr.responseJSON.error) {
            mensaje = xhr.responseJSON.error;
        } else if (xhr.responseText) {
            var texto = $('<div>').html(xhr.responseText).text().trim();
            if (texto && texto.length <= 500) mensaje += '\n\nDetalle: ' + texto;
        }
        mensaje += '\n\nCódigo HTTP: ' + (xhr.status || 'sin respuesta');
        BootstrapDialog.alert(mensaje);
    }

    var config = window.INTRANET_CONFIG || {};
    var tabla = $('#tabla-intranet').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' }, processing: true,
        serverSide: true, responsive: false, order: [[0, 'desc']], pageLength: 25,
        columnDefs: [{ orderable: false, targets: [3, 6] }],
        ajax: { url: 'components/intranet/models/listar.php', type: 'POST' }
    });
    function recargar() { tabla.ajax.reload(null, false); $.getJSON('components/intranet/models/resumen.php', function(data) { Object.keys(data).forEach(function(k) { $('[data-resumen="' + k + '"]').text(data[k]); }); }); }
    recargar(); $('#recargar-intranet').on('click', recargar);
    $('#guardar-solicitud').on('click', function() { var texto=$('#nueva-solicitud-texto').val().trim(); if(!texto){BootstrapDialog.alert('Debes escribir la solicitud.');return;} var b=$(this).prop('disabled',true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...'); $.post('components/intranet/models/crear.php',{csrf:config.csrf,texto:texto},function(r){if(!r||r.ok!==true){BootstrapDialog.alert('El servidor no confirmó el guardado.');return;} $('#modalNuevaSolicitud').modal('hide');$('#nueva-solicitud-texto').val('');recargar();BootstrapDialog.alert('Solicitud guardada correctamente.');},'json').fail(mostrarError).always(function(){b.prop('disabled',false).html('Guardar solicitud');}); });
    $('#tabla-intranet').on('click','.editar-intranet',function(){var token=$(this).data('token');$.getJSON('components/intranet/models/obtener.php',{token:token},function(d){$('#editar-solicitud-token').val(d.token);$('#editar-solicitud-texto').val(d.texto);$('#modalEditarSolicitud').modal('show');}).fail(mostrarError);});
    $('#guardar-edicion-solicitud').on('click',function(){var texto=$('#editar-solicitud-texto').val().trim();if(!texto){BootstrapDialog.alert('Debes escribir la solicitud.');return;}$.post('components/intranet/models/editar.php',{csrf:config.csrf,token:$('#editar-solicitud-token').val(),texto:texto},function(){ $('#modalEditarSolicitud').modal('hide');recargar();},'json').fail(mostrarError);});
    $('#tabla-intranet').on('click','.accion-intranet',function(){var b=$(this);$('#proceso-token').val(b.data('token'));$('#proceso-accion').val(b.data('accion'));$('#titulo-proceso-intranet').text(b.data('titulo'));$('#label-comentario').text(b.data('label')||'Comentario:');$('#proceso-comentario,#proceso-valor').val('');$('#campo-valor').toggle(b.data('accion')==='valorizar');$('#modalProcesoIntranet').modal('show');});
    $('#confirmar-proceso').on('click',function(){var b=$(this).prop('disabled',true);$.post('components/intranet/models/transicion.php',{csrf:config.csrf,token:$('#proceso-token').val(),accion:$('#proceso-accion').val(),comentario:$('#proceso-comentario').val(),valor:$('#proceso-valor').val()},function(r){$('#modalProcesoIntranet').modal('hide');recargar();if(r.warning)BootstrapDialog.alert(r.warning);},'json').fail(mostrarError).always(function(){b.prop('disabled',false);});});
    $('#tabla-intranet').on('click','.historial-intranet',function(){$('#historial-intranet-body').html('Cargando...');$('#modalHistorialIntranet').modal('show');$('#historial-intranet-body').load('components/intranet/models/historial.php?token='+encodeURIComponent($(this).data('token')));});
});
