$(document).ready(function() {
    $('#tabla').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "ordering": true,
        "processing": true,
        "serverSide": true,
        "responsive": false,
        "order": [[7, 'desc']],
        "pageLength": 25,
        "initComplete": function(settings, json) {
            $('.sel2-basic-single').select2();
            initializeModalHandlers();
            initializeExtensionHandlers();
        },
        "columnDefs": [{ orderable: false, targets: [1,8,9] }, { 'visible': false, 'targets': [7] }],
        "ajax": {
            "url": "components/equipo/models/equipo_list_procesa.php",
            "type": "POST",
            "dataSrc": function(json) {
                initializeModalHandlers();
                return json.data;
            }
        }
    });

    var table = $('#tabla').DataTable();

    table.on('draw', function() {
        $('.sel2-basic-single').select2();

        var dateToday = new Date();
        var month = dateToday.getMonth() + 1;
        var day = dateToday.getDate();
        var year = dateToday.getFullYear();
    
        if (month < 10) month = '0' + month.toString();
        if (day < 10) day = '0' + day.toString();
    
        var maxDate = year + '-' + month + '-' + day;
        $('.campofecha').attr('min', maxDate);
        
        // Reinitialize modals after table redraw
        initializeModalHandlers();
        initializeExtensionHandlers(); 
    });
    
    $('[data-toggle="tooltip"]').tooltip(); 
    $("i.fa").popover({'trigger':'hover'});

    // Initialize image modal
    $('#imageModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var imageUrl = button.data('img');
        var modal = $(this);
        modal.find('#modalImage').attr('src', imageUrl);
    });
});

function initializeModalHandlers() {
    // Handler para modal de detalles de equipo
    $('.btn-ver-equipo').off('click').on('click', function(e) {
        e.preventDefault();
        var token = $(this).data('token');
        
        $.ajax({
            url: 'components/equipo/models/equipo.php',
            type: 'GET',
            data: { token: token },
            success: function(response) {
                $('#equipoDetalleModal .modal-body').html(response);
                $('#equipoDetalleModal').modal('show');
            },
            error: function() {
                alert('Error al cargar los detalles del equipo');
            }
        });
    });
    
    // Handler para modal de imagen
    $('#imageModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var imageUrl = button.data('img');
        var modal = $(this);
        modal.find('#modalImage').attr('src', imageUrl);
    });
}


function initializeExtensionHandlers() {
    $('.btn-extension').off('click').on('click', function() {
        // Obtener el token directamente del botón
        const token = $(this).attr('data-token'); 
        
        // Depuración
        console.log("Token capturado del botón:", token); 
        console.log("Todos los data-* del botón:", $(this).data());
        
        if (!token) {
            console.error("Error: El botón no tiene data-token", this);
            return;
        }
        
        // Guardar el token en un campo oculto del modal
        $('#tokenExtension').val(token);
        $('#tokenDebug').text(token);

        // Resetear el modal
        $('input[name="accionExtension"][value="aprobar"]').prop('checked', true);
        $('#motivoExtension').val('');
        $('#fechaExtension').val('');
        $('#fechaPropuestaOriginal').val('');
        $('#fechaOriginalText').text('Cargando...');
        $('#debugInfo').html('<div class="alert alert-info">Cargando datos de extensión...</div>');
        
        // Cargar datos ANTES de abrir el modal
        $.ajax({
            url: 'components/equipo/models/obtener_datos_extension.php',
            type: 'GET',
            data: { 
                token: token,
                debug: true
            },
            dataType: 'json',
            success: function(response) {
                console.log("Respuesta completa del servidor:", response);
                
                if(response && response.success) {
                    // Mostrar información de depuración
                    $('#debugInfo').html(`
                        <div class="alert alert-success">
                            <strong>Datos cargados correctamente</strong><br>
                            Token: ${token}<br>
                            Estado: ${response.estado || 'N/A'}
                        </div>
                    `);
                    
                    // Manejar la fecha propuesta
                    if(response.fecha_propuesta) {
                        $('#fechaExtension').val(response.fecha_propuesta);
                        $('#fechaPropuestaOriginal').val(response.fecha_propuesta);
                        $('#fechaOriginalText').text(response.fecha_propuesta_formateada || response.fecha_propuesta);
                    } else {
                        console.warn("No se recibió fecha propuesta en la respuesta");
                        $('#fechaOriginalText').text("No disponible");
                        $('#debugInfo').append('<div class="alert alert-warning mt-2">No se encontró fecha propuesta en la respuesta</div>');
                    }
                    
                    // Establecer fecha mínima (hoy)
                    var today = new Date().toISOString().split('T')[0];
                    $('#fechaExtension').attr('min', today);
                    
                    // Ahora sí abrir el modal
                    $('#modalExtension').modal('show');
                } else {
                    const errorMsg = response && response.error ? response.error : 'Respuesta inválida del servidor';
                    console.error("Error en la respuesta:", errorMsg);
                    $('#debugInfo').html(`
                        <div class="alert alert-danger">
                            Error al cargar datos: ${errorMsg}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", status, error);
                $('#debugInfo').html(`
                    <div class="alert alert-danger">
                        Error en la comunicación con el servidor:<br>
                        ${status}: ${error}
                    </div>
                `);
            }
        });
    });
}

function enviar() {
    var error = 0;
    var mensaje = "";

    if($("#nombre").val() == "") {
        error = 1;
        $("#nombre").addClass("is-invalid");
    } else {
        $("#nombre").removeClass("is-invalid");
    }

    if(error == "1") return;
    
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);    
    var formData = new FormData(document.getElementById("formulario"));

    $.ajax({
        url: "components/equipo/models/insert_update.php",
        type: "post",
        dataType: "html",
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(res) {
        var retorno = res.split('|');
        var token = retorno[1];
        document.location.reload();
    });
}

function eliminarEquipo(token) {
    BootstrapDialog.confirm('¿Confirma la eliminación del Equipo? Esta acción no se puede deshacer.', function(result) {
        if(result) {
            $.ajax({
                type: 'POST',
                url: "components/equipo/models/borrar_equipo.php",
                data: { token: token },
                success: function(resp) {
                    var retorno = resp.split('|');
                    var resultado = retorno[1];
                    
                    if(resultado == "1") {
                        BootstrapDialog.show({
                            message: "El equipo se ha eliminado.",
                            type: BootstrapDialog.TYPE_PRIMARY,
                            title: "Atención",
                            buttons: [{
                                label: 'Aceptar',
                                cssClass: 'btn-primary',
                                action: function(dialogItself) {
                                    dialogItself.close();
                                    document.location.reload();
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.alert("No se puede eliminar el equipo del sistema. Existen datos relacionados al equipo.");
                    }
                }
            }); 
        }
    });
}

function fechaPrestamo(e, token, capa, fecha) {
    if(e.value != "") {
        $("#"+capa).css("display", "block");
    } else {
        $("#"+capa).css("display", "none");
    }
}

function prestar(f, token, u) {
    var fecha = document.getElementById(f).value;
    var user = document.getElementById(u).value;

    if(fecha != "" && user != "") {
        $.ajax({
            url: "components/equipo/models/prestar_equipo.php",
            type: "GET",
            data: {
                fecha: fecha,
                user: user,
                token: token
            },
            success: function(res) {
                var retorno = res.split('|');
                var tokenRetorno = retorno[1];

                if(tokenRetorno == "0") {
                    BootstrapDialog.alert("El socio seleccionado tiene 3 o más meses de deuda. No se le puede prestar equipo.");
                } else {
                    
                }
                document.location.reload();
            }
        });
    } else {
        BootstrapDialog.alert("Se deben seleccionar todos los datos para realizar el préstamo.");
    }
}

function seteaTokenEquipo(tokenEquipo) {
    $("#tokenEquipo").val(tokenEquipo);
}

function devolverEquipo_old_no_usar() {
    var tokenEquipo = $("#tokenEquipo").val();
    var observacion = $("#observacion").val();
    var estado = $("#estado").val();
    var devolverTodo = $("#devolverTodo").is(":checked"); // Verificar si el checkbox está marcado
    var idUsuario = $("#idUsuario").val(); // Obtener el ID del usuario

    // Depuración: Imprime los datos en la consola del navegador
    console.log("Datos que llegan al js.js:", { tokenEquipo, observacion, estado, devolverTodo, idUsuario });
    
    // Validar que si se selecciona "Devolver todo", el ID del usuario no esté vacío
    if (devolverTodo && !idUsuario) {
        alert("Error: No se ha seleccionado un usuario.");
        return;
    }

    if (idUsuario != "") {
        $.ajax({
            url: "components/equipo/models/devolver_equipo.php",
            type: "POST", // Cambiamos a POST para enviar más datos
            data: {
                tokenEquipo: tokenEquipo,
                observacion: observacion,
                estado: estado,
                devolverTodo: devolverTodo, // Indicador de si se devuelve todo
                idUsuario: idUsuario // ID del usuario para devolver todos los equipos
            },
            success: function(res) {
                alert(res.message); // Mostrar mensaje de éxito o error
                $('#primaryModal').modal('hide'); // Cerrar el modal
                document.location.reload(); // Recargar la página para reflejar los cambios
            },
            error: function() {
                alert("Error al registrar la devolución.");
            }
        });
    } else {
       // documentalert("usuario en blanco en js.js .");
    }
}

function abrirModalDevolucion(tokenEquipo, nombreUsuario, idUsuario) {
    // Establecer el token del equipo en el campo oculto
    $("#tokenEquipo").val(tokenEquipo);

    // Establecer el nombre del usuario en el label del checkbox
    $("#nombreUsuario").text(nombreUsuario);

    // Establecer el ID del usuario en el campo oculto
    $("#idUsuario").val(idUsuario);

    // Abrir el modal
    $("#primaryModal").modal("show");
}

function validaImagen(e) {
    var fileExtension = ['png', 'jpeg', 'jpg', 'gif'];
    if ($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        BootstrapDialog.alert('El archivo debe ser una imagen.');
        $(e).val("");
        return false;
    }
    return true;
}

function verificaid(token, idunico) {
    $.ajax({
        type: 'POST',
        url: "components/equipo/models/verifica_id.php",
        data: {
            token: token,
            idunico: idunico
        },
        success: function(resp) {
            var retorno = resp.split('|');
            var resultado = retorno[1];
            
            if(resultado == "1") {
                BootstrapDialog.show({
                    message: "El identificador ya existe. Ingresa otro identificador para el equipo.",
                    type: BootstrapDialog.TYPE_PRIMARY,
                    title: "Atención",
                    buttons: [{
                        label: 'Aceptar',
                        cssClass: 'btn-primary',
                        action: function(dialogItself) {
                            dialogItself.close();
                        }
                    }]
                });
            }
        }
    });
}

///////////////////////////////////////////////

// Checkbox para aplicar a todos
$('#aplicarATodos').change(function() {
    if($(this).is(':checked')) {
        $.ajax({
            url: 'obtener_equipos_usuario.php',
            type: 'POST',
            data: { 
                token: $('#tokenExtension').val(),
                accion: $('#accionExtension').val()
            },
            success: function(response) {
                $('#contenidoEquipos').html(response);
                $('#listaEquipos').show();
            }
        });
    } else {
        $('#listaEquipos').hide();
    }
});


// Funciones para solicitudes (no extensiones)
function gestionarSolicitud(token, accion) {
    if (accion === 'aprobar') {
        if (confirm('¿Está seguro de aprobar esta solicitud?')) {
            procesarSolicitud(token, 'aprobar');
        }
    }
}


function mostrarModalRechazo(token) {
    $('#tokenRechazo').val(token);
    $('#motivoRechazo').val('');
    $('#modalRechazo').modal('show');
}

function confirmarRechazo() {
    const token = $('#tokenRechazo').val();
    const motivo = $('#motivoRechazo').val();
    
    if (!motivo) {
        alert('Por favor ingrese un motivo de rechazo');
        return;
    }
    
    procesarSolicitud(token, 'rechazar', motivo);
}



// Mover las otras funciones relacionadas fuera del manejador de eventos
$(document).on('change', 'input[name="accionExtension"]', function() {
    if ($(this).val() === 'aprobar') {
        $('#fechaExtensionGroup').show();
        $('#labelMotivo').text('Motivo (opcional):');
    } else {
        $('#fechaExtensionGroup').hide();
        $('#labelMotivo').text('Motivo del rechazo (requerido):');
    }
});

function restaurarFechaOriginal() {
    var fechaOriginal = $('#fechaPropuestaOriginal').val();
    if(fechaOriginal) {
        $('#fechaExtension').val(fechaOriginal);
    } else {
        console.warn("No hay fecha original para restaurar");
    }
}

// Función para confirmar la gestión (actualizada)
function confirmarGestionExtension() {
    const token = $('#tokenExtension').val();
    const accion = $('input[name="accionExtension"]:checked').val();
    const motivo = $('#motivoExtension').val();
    const aplicarATodos = $('#aplicarATodos').is(':checked');
    const nuevaFecha = $('#fechaExtension').val();
    const fechaOriginal = $('#fechaPropuestaOriginal').val();
    
    // Validaciones
    if (accion === 'rechazar' && !motivo.trim()) {
        alert('Por favor ingrese un motivo para el rechazo');
        return;
    }
    
    if (accion === 'aprobar') {
        if (!nuevaFecha) {
            alert('Por favor seleccione una fecha de devolución');
            return;
        }
        
        // Opcional: Validar que la nueva fecha es posterior a la actual
        if (new Date(nuevaFecha) <= new Date()) {
            if (!confirm('La fecha seleccionada es hoy o una fecha pasada. ¿Desea continuar?')) {
                return;
            }
        }
    }

    // Enviar datos al servidor
    $.ajax({
        url: 'components/equipo/models/procesar_extension.php',
        type: 'POST',
        data: {
            token: token,
            accion: accion,
            motivo: motivo,
            aplicar_a_todos: aplicarATodos,
            nueva_fecha: nuevaFecha,
            fecha_original: fechaOriginal
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#modalExtension').modal('hide');
                $('#tabla').DataTable().ajax.reload(null, false);
                alert(response.message);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al procesar la solicitud');
        }
    });
}