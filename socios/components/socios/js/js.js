// Document Ready Functions
$(document).ready(function() {
    // Inicialización de DataTables
    var tipoSocio = "";
    var tipoCuota = "";

    if($("#tipo").val() != "") {
        $('#tipos option[value="'+$("#tipo").val()+'"]').prop("selected", true);
        tipoSocio = $("#tipo").val();
    }
    
    if($("#cuota").val() != "") {
        $('#cuotas option[value="'+$("#cuota").val()+'"]').prop("selected", true);
        tipoCuota = $("#cuota").val();
    }

    $('#tabla').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[0, "asc"]],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "pageLength": 25,
        "columnDefs": [{orderable: false, targets: [5,6,7]}],
        "ajax": {
            "url": "components/socios/models/socios_list_procesa.php?tipo="+tipoSocio+"&cuota="+tipoCuota,
            "type": "POST"
        }
    });

    $('#tablapagos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[0, "desc"]],
    });

    $('#tabladeudas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[0, "desc"]],
    });

    $('#tablaprestamos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[0, "desc"]],
    });

    // RUT validation
    $("#rut")
    .rut({formatOn: 'blur', validateOn: 'blur'})
    .on('rutInvalido', function(){ 
        $(this).parents(".control-group").addClass("errorClass");
        $(this).css("border-color","red");
        $("#errorrut2").html("Rut inválido. Debe ingresar un Rut válido.");
        $("#rut").addClass("rutnovalido");
    })
    .on('rutValido', function(){ 
        $(this).parents(".control-group").removeClass("errorClass")
        $(this).css("border-color","#ccc");
        $("#errorrut2").html("");
        $("#rut").removeClass("rutnovalido");
    });

    // Plan initialization
    if($("#hplan").val() != "") {
        $('#tipoInscripcion option[value="'+$("#hplan").val()+'"]').prop("selected", true);
    }

    // Password requirements
    $(".pr-password").passwordRequirements({
        numCharacters: 8,
        useLowercase: true,
        useUppercase: true,
        useNumbers: true,
        useSpecial: false
    });
});

// Utility Functions
function goTabPass() {
    $(".tab-pass").click(); 
}

function selCuotas(e) {
    var tipo = $("#tipo").val();
    document.location.href = "index.php?component=socios&view=socios_list&cuota="+e.value+"&tipo="+tipo;
}

function selTipo(e) {
    var cuota = $("#cuota").val();
    document.location.href = "index.php?component=socios&view=socios_list&tipo="+e.value+"&cuota="+cuota;
}

// Validation Functions
function validaCertificado(e) {
    var fileExtension = ['png','jpeg','jpg','docx','doc','pdf','xls','xlsx'];
    if($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        BootstrapDialog.alert('El Archivo debe ser un PDF, imagen o word.');
        $(e).val("");
        return false;
    } else {
        return true;
    }
}

function validaImagen(e) {
    var fileExtension = ['png','jpeg','jpg','gif'];
    if($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        BootstrapDialog.alert('El Archivo debe ser una imagen.');
        $(e).val("");
        return false;
    } else {
        return true;
    }
}

// Image Upload Function
function subirImagen() {
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
    var formData = new FormData(document.getElementById("formulario"));
    
    if(validaImagen($("#foto")) == true) {
        url = "components/socios/models/subir_imagen.php";
        
        $.ajax({
            url: url,
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        }).done(function(res) {
            var retorno = res.split('|');
            var subida = retorno[1]; 
            $("#foto-perfil").attr("src","images/img_perfil/"+subida);
            document.location.reload();
        });
    }
}

// Form Submission Functions
function enviar() {
    var error = 0;
    var mensaje = "";

    if($("#nombre").val() == "") {
        error = 1;
        $("#nombre").addClass("is-invalid");
    } else {
        $("#nombre").removeClass("is-invalid");
    }

    if($("#rut").val() == "") {
        error = 1;
        $("#rut").addClass("is-invalid");
    } else {
        $("#rut").removeClass("is-invalid");
    }

    if($("#mail").val() == "") {
        error = 1;
        $("#mail").addClass("is-invalid");
    } else {
        $("#mail").removeClass("is-invalid");
    }
    
    if($("#tipoInscripcion").val() == "") {
        error = 1;
        $("#tipoInscripcion").addClass("is-invalid");
    } else {
        $("#tipoInscripcion").removeClass("is-invalid");
    }

    if(error == "1") {
        $(".tab-datos").click(); 
    } else {
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var formData = new FormData(document.getElementById("formulario"));
        url = "components/socios/models/insert_update.php";

        $.ajax({
            url: url,
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        }).done(function(res) {
            var retorno = res.split('|');
            var token = retorno[1];
            document.location.href = "index.php?component=socios&view=socios&token="+token;
        });
    }
}

// Existence Check Functions
function rutExiste(e) {
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
    var rut = e.value;	
    var token = $("#token").val();
    url = "components/socios/models/rut_existe.php?rut="+rut+"&token="+token;

    $.ajax({
        url: url,
        type: "post",
        dataType: "html",
        data: "",
        cache: false,
        contentType: false,
        processData: false
    }).done(function(res) {
        var retorno = res.split('|');
        var existe = retorno[1]; 
        if(existe == "1") {
            $("#myModal").modal('show');
            e.value = "";
        }
    });
}

function mailExiste(e) {
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
    var mail = e.value;	
    var token = $("#token").val();
    url = "components/socios/models/mail_existe.php?mail="+mail+"&token="+token;

    $.ajax({
        url: url,
        type: "post",
        dataType: "html",
        data: "",
        cache: false,
        contentType: false,
        processData: false
    }).done(function(res) {
        var retorno = res.split('|');
        var existe = retorno[1]; 
        if(existe == "1") {
            $("#myModal2").modal('show');
            e.value = "";
        }
    });
}

// Password Functions
function savePassword() {
    var pass1 = $("#password").val();
    var pass2 = $("#password2").val();
    var error = 0;
    var mensaje = "";

    if(pass1.length <= 7) {
        error = 1;
        mensaje = "La contraseña debe tener un largo mínimo de 8 caracteres";
    }

    if((pass1 != pass2) && error == "0") {
        error = 1;
        mensaje = "La contraseñas no coinciden. Deben ser iguales.";
    }

    var minusc = new RegExp('[a-z]');
    var mayusc = new RegExp('[A-Z]');
    var numero = new RegExp('[0-9]');

    if(!(minusc.test(pass1)) && error == "0") {
        error = 1;
        mensaje = "La contraseñas debe contener a lo menos una letra minúscula.";
    }

    if(!(mayusc.test(pass1)) && error == "0") {
        error = 1;
        mensaje = "La contraseñas debe contener a lo menos una letra mayúscula.";
    }

    if(!(numero.test(pass1)) && error == "0") {
        error = 1;
        mensaje = "La contraseñas debe contener a lo menos un número.";
    }

    if(error == "1") {
        $(".error-pass").html(mensaje);
    } else {
        $(".error-pass").html("");
        actualizaPass();
    }
}

function actualizaPass() {
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
    var pass = $("#password").val();
    var token = $("#token").val();
    
    $.ajax({
        type: 'POST',
        url: "components/socios/models/actualiza_pass.php",
        data: "&pass="+pass+"&token="+token,
        success: function(resp) {
            var retorno = resp.split('|');
            var existe = retorno[0];
            document.location.reload();
        }
    });	
}

// Inscription Functions
function actualizaInscripcion() {
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
    var tipo = $("#tipoInscripcion").val();
    var token = $("#token").val();
    
    $.ajax({
        type: 'POST',
        url: "components/socios/models/actualiza_inscripcion.php",
        data: "&tipo="+tipo+"&token="+token,
        success: function(resp) {
            var retorno = resp.split('|');
            var existe = retorno[0];
            document.location.reload();
        }
    });	
}

// Request Functions
function cancelarSolicitud(token) {
    BootstrapDialog.confirm('Confirma que la eliminación de la Solicitud de equipo. No se puede deshacer.', function(result) {
        if(result) {
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
            
            $.ajax({
                type: 'POST',
                url: "components/socios/models/cancela_solicitud.php?token="+token,
                data: "token="+token,
                success: function(resp) {
                    var retorno = resp.split('|');
                    var resultado = retorno[1];
                    BootstrapDialog.show({
                        message: "La solicitud de ha cancelado.",
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
                }
            }); 
        }
    });
}


// Función para verificar y mostrar el modal de extensión
function solicitarextension(token) {
    // Mostrar indicador de carga
    $('#loadingExtension').show();
    $('#formExtension').hide();

    $.ajax({
        url: 'components/socios/models/verificar_extensiones.php',
        type: 'POST',
        data: { token: token },
        dataType: 'json', // Esperamos JSON
        success: function(response) {
            // Verificar si la respuesta es válida
            if (!response || typeof response !== 'object') {
                throw new Error('Respuesta inválida del servidor');
            }

            if (response.success !== true) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.mensaje || 'Error desconocido',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (response.extensiones_restantes <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Límite alcanzado',
                    text: 'Ya has utilizado el máximo de extensiones permitidas (2)',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Configurar modal
            $('#tokenPrestamo').val(token);
            $('#modalExtension .modal-title').html(
                `Solicitar Extensión (${response.extensiones_restantes} de 2 disponibles)`
            );
            
            $('#loadingExtension').hide();
            $('#formExtension').show();
            $('#modalExtension').modal('show');
        },
        error: function(xhr, status, error) {
            let errorMsg = 'Error en la solicitud';
            
            try {
                const response = xhr.responseJSON;
                if (response && response.mensaje) {
                    errorMsg = response.mensaje;
                }
            } catch (e) {
                errorMsg = 'Error al procesar la respuesta';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg,
                confirmButtonText: 'OK'
            });
        },
        complete: function() {
            $('#loadingExtension').hide();
        }
    });
}

// Función para enviar la solicitud de extensión
function enviarSolicitudExtension() {
    const $btn = $('#btnEnviarExtension');
    const token = $('#tokenPrestamo').val();
    const nuevaFecha = $('#nuevaFecha').val();
    const motivo = $('#motivo').val();

    // Validación básica
    if (!nuevaFecha || !motivo) {
        Swal.fire({
            icon: 'error',
            title: 'Campos incompletos',
            text: 'Por favor complete todos los campos',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    // Deshabilitar botón y mostrar spinner
    $btn.prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...'
    );

    // Enviar solicitud AJAX
    $.ajax({
        url: 'components/socios/models/solicitar_extension.php',
        type: 'POST',
        dataType: 'json',
        data: {
            token: token,
            nueva_fecha: nuevaFecha,
            motivo: motivo
        },
        success: function(response) {
            console.log('Respuesta:', response); // Para depuración
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.mensaje,
                    confirmButtonText: 'OK',
                    willClose: () => {
                        $('#modalExtension').modal('hide');
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.mensaje || 'Error desconocido',
                    confirmButtonText: 'Entendido'
                });
                $btn.prop('disabled', false).html('Solicitar Extensión');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en AJAX:', xhr.responseText); // Para depuración
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'Ocurrió un error al enviar la solicitud. Por favor intente nuevamente.',
                confirmButtonText: 'OK'
            });
            $btn.prop('disabled', false).html('Solicitar Extensión');
        }
    });
}

$(document).ready(function() {
    // Establecer fecha mínima como mañana
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    $('#nuevaFecha').attr('min', tomorrowStr);
});