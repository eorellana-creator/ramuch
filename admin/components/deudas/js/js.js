$(document).ready(function() {
    var subcuenta = $("#subcuenta").val();

    $('#tabla').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[0, "desc"]],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "pageLength": 25,
        "columnDefs": [{ orderable: false, targets: [2,3,4,5,7,8,9,10,11] }],
        "ajax": {
            "url": "components/deudas/models/deudas_list_procesa.php",
            "type": "POST"
        }
    });

    $(".usuarios-tags").select2({
        tags: true
    });
});




function enviar(){
    var error = 0;
    var mensaje = "";

    if($("#nombre").val() == "") {
        error = 1;
        $("#nombre").addClass("is-invalid");
    } else {
        $("#nombre").removeClass("is-invalid");
    }

    if($("#sub_cuenta").val() == "") {
        error = 1;
        $("#sub_cuenta").addClass("is-invalid");
    } else {
        $("#sub_cuenta").removeClass("is-invalid");
    }

    if($("#glosa").val() == "") {
        error = 1;
        $("#glosa").addClass("is-invalid");
    } else {
        $("#glosa").removeClass("is-invalid");
    }

    if($("#fecha").val() == "") {
        error = 1;
        $("#fecha").addClass("is-invalid");
    } else {
        $("#fecha").removeClass("is-invalid");
    }

    if($("#medio").val() == "") {
        error = 1;
        $("#medio").addClass("is-invalid");
    } else {
        $("#medio").removeClass("is-invalid");
    }

    if($("#monto").val() == "") {
        error = 1;
        $("#monto").addClass("is-invalid");
    } else {
        $("#monto").removeClass("is-invalid");
    }

    if(error == "1") {
    } else {
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var formData = new FormData(document.getElementById("formulario"));
        url	= "components/deudas/models/insert_update.php";

        $.ajax({
            url: url,
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(res){
            var retorno = res.split('|');
            var token = retorno[1];
            document.location.href = "index.php?component=deudas&view=deuda&token="+token;
        });
    }
}

function deleteItem(token){
    BootstrapDialog.confirm('Confirma la eliminación de la deuda. No se puede deshacer.', function(result){
        if(result) {
            $.ajax({
                type: 'POST',
                url: "components/deudas/models/delete_deuda.php",
                data: "token="+token,
                success: function(resp){
                    var retorno = resp.split('|');
                    var resultado = retorno[1];
                    
                    if(resultado=="1"){
                        BootstrapDialog.show({
                            message: "La deuda se ha eliminado.",
                            type: BootstrapDialog.TYPE_PRIMARY,
                            title: "Atención",
                            buttons: [{
                                label: 'Aceptar',
                                cssClass: 'btn-primary',
                                action: function(dialogItself){
                                    dialogItself.close();
                                    document.location.reload();
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.alert("No se puede eliminar la deuda del sistema. Existen datos relacionado al item.");
                    }
                }
            }); 
        }
    });
}


function activa_deuda(token){
    BootstrapDialog.confirm('Confirma la activacion de la deuda?', function(result){
        if(result) {
            $.ajax({
                type: 'POST',
                url: "components/deudas/models/desactiva_deuda.php",
                data: "token="+token,
                success: function(resp){
                    var retorno = resp.split('|');
                    var resultado = retorno[1];
                    
                    if(resultado=="1"){
                        BootstrapDialog.show({
                            message: "La deuda se ha activado.",
                            type: BootstrapDialog.TYPE_PRIMARY,
                            title: "Atención",
                            buttons: [{
                                label: 'Aceptar',
                                cssClass: 'btn-primary',
                                action: function(dialogItself){
                                    dialogItself.close();
                                    document.location.reload();
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.alert("No se puede activar la deuda del sistema. Existen datos relacionado al item.");
                    }
                }
            }); 
        }
    });
}


function desactiva_deuda(token){
    BootstrapDialog.confirm('Confirma la desactivacion de la deuda?', function(result){
        if(result) {
            $.ajax({
                type: 'POST',
                url: "components/deudas/models/desactiva_deuda.php",
                data: "token="+token,
                success: function(resp){
                    var retorno = resp.split('|');
                    var resultado = retorno[1];
                    
                    if(resultado=="1"){
                        BootstrapDialog.show({
                            message: "La deuda se ha desactivado.",
                            type: BootstrapDialog.TYPE_PRIMARY,
                            title: "Atención",
                            buttons: [{
                                label: 'Aceptar',
                                cssClass: 'btn-primary',
                                action: function(dialogItself){
                                    dialogItself.close();
                                    document.location.reload();
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.alert("No se puede desactivar la deuda del sistema. Existen datos relacionado al item.");
                    }
                }
            }); 
        }
    });
}


function validaDocumento(e){
    var fileExtension = ['png','jpeg','jpg','gif','docx','doc','pdf','xls','xlsx'];
    if ($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        BootstrapDialog.alert('El Archivo debe ser un PDF, imagen o word.');
        $(e).val("");
        return false;
    } else {
        return true;
    }
}

function borrarDocumento(token){
    BootstrapDialog.confirm('Confirma la eliminación del Documento. No se puede deshacer.', function(result){
        if(result) {
            $.ajax({
                type: 'POST',
                url: "components/deudas/models/delete_documento.php",
                data: "token="+token,
                success: function(resp){
                    var retorno = resp.split('|');
                    var resultado = retorno[1];
                    
                    if(resultado=="1"){
                        BootstrapDialog.show({
                            message: "El documento se ha eliminado.",
                            type: BootstrapDialog.TYPE_PRIMARY,
                            title: "Atención",
                            buttons: [{
                                label: 'Aceptar',
                                cssClass: 'btn-primary',
                                action: function(dialogItself){
                                    dialogItself.close();
                                    document.location.reload();
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.alert("No se puede eliminar el documento del sistema. Existen datos relacionado al documento.");
                    }
                }
            }); 
        }
    });
}


function ejecutarAccion() {
    var accion = $('#accion').val();
    
    if (accion === 'eliminar') {
        eliminarD();
    } else if (accion === 'condonar') {
        condonar();
    }
}

function eliminarD(){
    var error = 0;
    var mensaje = "";

    if($("#observacion").val() == "") {
        error = 1;
        $("#observacion").addClass("is-invalid");
    } else {
        $("#observacion").removeClass("is-invalid");
    }

    if(error == "1") {
    } else {
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var formData = new FormData(document.getElementById("formulario"));
        url	= "components/deudas/models/eliminar.php";

        $.ajax({
            url: url,
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(res){
            var retorno = res.split('|');
            var token = retorno[1];
            document.location.href = "index.php?component=deudas&view=deudas_list";
        });
    }
}

function condonar(){
    var error = 0;
    var mensaje = "";

    if($("#observacion").val() == "") {
        error = 1;
        $("#observacion").addClass("is-invalid");
    } else {
        $("#observacion").removeClass("is-invalid");
    }

    if(error == "1") {
    } else {
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var formData = new FormData(document.getElementById("formulario"));
        url	= "components/deudas/models/condonar.php";

        $.ajax({
            url: url,
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(res){
            var retorno = res.split('|');
            var token = retorno[1];
            document.location.href = "index.php?component=deudas&view=deudas_list";
        });
    }
}

function pagar(){
    var error = 0;
    var mensaje = "";

    if($("#medio").val() == "") {
        error = 1;
        $("#medio").addClass("is-invalid");
    } else {
        $("#medio").removeClass("is-invalid");
    }

    if(error == "1") {
    } else {
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var formData = new FormData(document.getElementById("formulario"));
        url	= "components/deudas/models/pagar.php";

        $.ajax({
            url: url,
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(res){
            var retorno = res.split('|');
            var token = retorno[1];
            document.location.href = "index.php?component=deudas&view=deudas_list";
        });
    }
}
