<?php
$rut = @$_GET["rut"];
$email = @$_GET["email"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>Pagos Ramuch</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">

    <!-- Core CSS -->
    <link href="node_modules/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
    <link href="node_modules/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
    <link href="node_modules/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="node_modules/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="vendors/pace-progress/css/pace.min.css" rel="stylesheet">
    <link rel="stylesheet" href="js/validate-password/css/jquery.passwordRequirements.css" />

    <style>
        .app, app-dashboard, app-root {
            min-height: 0px !important;
        }
    </style>
</head>

<body class="app flex-row align-items-center">
    <div class="container" style="margin-top:40px;">
        <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data">
            <input id="total" name="total" type="hidden" value="0">
            <input id="rutpagador" name="rutpagador" type="hidden" value="0">
            <input id="emailpagador" name="emailpagador" type="hidden" value="0">

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card mx-4">
                        <div class="card-body p-4">
                            <h1><img src="images/tf.png" alt="Logo Ramuch"> &nbsp; Paga tu deuda</h1>
                            <p class="text-muted">Ingresa tu Rut para consultar la deuda.</p>
                            
                            <p class="text-muted">El primer combo semestral comienza en Enero y termina a mediados de abril.</p>
                            <p class="text-muted">El segundo combo semestral comienza en Julio y termina a mediados de Agosto.</p>
                            <p class="text-muted">El combo anual solo esta activo desde enero hasta mediados de abril.</p>
                            <p class="text-muted">Los combos no apareceran si existe una deuda previa.</p>

                            <div id="contenido">
                                <br>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-id-card-o" style="color:#9ea1a2;"></i>
                                        </span>
                                    </div>
                                    <input id="rut" name="rut" class="form-control" type="text" value="<?php echo $rut;?>" 
                                           placeholder="Tu Rut. Ej: 14.231.123-k" 
                                           onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);">
                                    <div id="errorrut2" class="errorcampo" style="width:100%;"></div>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <span style="color:#9ea1a2;">@</span>
                                        </span>
                                    </div>
                                    <input type="email" class="form-control" name="mail" id="mail" value="<?php echo $email;?>" 
                                           placeholder="Tu correo para notificar el pago" 
                                           onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" 
                                           maxlength="255">
                                    <div id="errormail" class="errorcampo" style="width:100%;"></div>
                                </div>

                                <br><br>
                                <button class="btn btn-block btn-success" onClick="enviar();" type="button">Consultar deuda</button>
                            </div>
                        </div>

                        <div class="card-footer p-4">
                            <div class="row">
                                <div class="col-6">
                                    <a href="index.php">
                                        <button class="btn btn-block btn-secondary color-bl" type="button">
                                            <span>volver a Pagos</span>
                                        </button>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="https://www.ramuch.cl/">
                                        <button class="btn btn-block btn-info color-bl" type="button">
                                            <span>volver a Ramuch</span>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="node_modules/pace-progress/pace.min.js"></script>
    <script src="node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
    <script src="node_modules/@coreui/coreui/dist/js/coreui.min.js"></script>
    <script src="js/jquery.blockUI.js"></script>
    <script src="js/validadores.js"></script>
    <script src="js/rut/jquery.rut.js"></script>
    <script src="js/validate-password/js/jquery.passwordRequirements.js"></script>

    <script>
        setTimeout(function(){
            document.getElementById("formulario").reset();
        }, 5);

        function enviar() {
            var error = 0;
            var mensaje = "";

            if($("#rut").val() == "") {
                error = 1;
                $("#rut").addClass("is-invalid");
            } else {
                $("#rut").removeClass("is-invalid");
            }

            var correo = document.getElementById("mail");

            if(valida_mail(correo) && correo.value != "") {
                $("#mail").removeClass("is-invalid");
                $("#errormail").html("");
                $("#errormail").html("xxx");
            } else {
                error = 1;
                $("#mail").addClass("is-invalid");
                $("#errormail").html("Debes ingresar un email válido");
            }

            if(error == "0") {
                var rutpagador = document.getElementById("rut").value;
                $("#rutpagador").val(rutpagador);
                $("#emailpagador").val(correo.value);

                $("alerta-invalido").html();

                var formData = new FormData(document.getElementById("formulario"));

                $("#contenido").html("<div style='width:100%; text-align: center;'><br><br><br><br>Enviando, un momento por favor...<br><img src='images/reload.gif'></div>");

                $.ajax({
                    url: "envia.php",
                    type: "post",
                    dataType: "html",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        console.log(resp);
                        var retorno = resp.split('|');
                        var resultado = retorno[1];
                        $('#contenido').html("<div style='width:100%; text-align: center; padding-top:30px;padding-bottom:90px'>" + resultado + "<br><br></div>");
                    }
                });

                window.parent.parent.scrollTo(0,0);
            } else {
                $("#alerta-invalido").html("<div class='alert alert-danger' role='alert'>Debe completar correctamente el Rut y el Correo para consultar la deuda.</div>");
            }
        }

        $(document).ready(function() {
            $("#rut")
                .rut({formatOn: 'blur', validateOn: 'blur'})
                .on('rutInvalido', function(){ 
                    $(this).parents(".control-group").addClass("errorClass");
                    $(this).css("border-color","red");
                    $("#errorrut2").html("Rut inválido. Debe ingresar un Rut válido.");
                    $(this).addClass("rutnovalido");
                })
                .on('rutValido', function(){ 
                    $(this).parents(".control-group").removeClass("errorClass")
                    $(this).css("border-color","#ccc");
                    $("#errorrut2").html("");
                    $(this).removeClass("rutnovalido");
                });

            $("#total").val("0");
        });

        function actualizaTotalPago(pago, monto) {
            var totalActual = $("#total").val();

            if ($("#"+pago).is(':checked')) {
                totalActual = parseInt(totalActual) + parseInt(monto);
            } else {
                totalActual = parseInt(totalActual) - parseInt(monto);
            }

            $("#total").val(totalActual);

            if(parseInt(totalActual) > 0) {
                $('#botonPago').prop('disabled', false);
            } else {
                $('#botonPago').prop('disabled', true);
            }

            totalActual = totalActual.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            $("#total-pagar").html("$" + totalActual);
        }

        function seleccionarTodo() {
            if ($('#selectAll').is(':checked')) {
                var totalDeuda = $("#totalDeuda").val();
                $('.checkPagos').prop('checked', true);
                $("#total").val(totalDeuda);
                totalDeuda = totalDeuda.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                $("#total-pagar").html("$" + totalDeuda);
                $('#botonPago').prop('disabled', false);
            } else {
                $('.checkPagos').prop('checked', false);
                $("#total").val("0");
                $("#total-pagar").html("$0");
                $('#botonPago').prop('disabled', true);
            }
        }

        function marcaCombo(valor) {
            if ($('#semestre1').is(':checked'))
                var totalDeuda = valor;

            if ($('#semestre2').is(':checked'))
                var totalDeuda = valor;

            if ($('#semestre1semestre2').is(':checked'))
                var totalDeuda = valor;

            if ($('#semestre1').is(':checked') || $('#semestre2').is(':checked')) {
                $('.checkCuota').prop('checked', false);
                $('.checkCuota').prop('disabled', true);
                $("#total").val(totalDeuda);
                totalDeuda = totalDeuda.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                $("#total-pagar").html("$" + totalDeuda);
                $('#botonPago').prop('disabled', false);
                $('#semestre1semestre2').prop('disabled', true);
                $('#semestre1semestre2').prop('checked', false);
            } else if(!$('#semestre1semestre2').is(':checked')) {
                $('.checkCuota').prop('disabled', false);
                $("#total").val("0");
                $("#total-pagar").html("$0");
                $('#botonPago').prop('disabled', true);
                $('#semestre1semestre2').prop('disabled', false);
                $('#semestre1semestre2').prop('checked', false);
            }

            if ($('#semestre1semestre2').is(':checked')) {
                $('#semestre1').prop('checked', false);
                $('#semestre1').prop('disabled', true);
                $('#semestre2').prop('checked', false);
                $('#semestre2').prop('disabled', true);
                $('.checkCuota').prop('checked', false);
                $('.checkCuota').prop('disabled', true);
                $("#total").val(totalDeuda);
                totalDeuda = totalDeuda.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                $("#total-pagar").html("$" + totalDeuda);
                $('#botonPago').prop('disabled', false);
            } else if(!$('#semestre1').is(':checked') && !$('#semestre2').is(':checked')) {
                $('#semestre1').prop('checked', false);
                $('#semestre1').prop('disabled', false);
                $('#semestre2').prop('checked', false);
                $('#semestre2').prop('disabled', false);
                $('.checkCuota').prop('disabled', false);
                $("#total").val("0");
                $("#total-pagar").html("$0");
                $('#botonPago').prop('disabled', true);
            }
        }

        function pagarFlow() {
            var formData = new FormData(document.getElementById("formulario"));
            $("#contenido").html("<div style='width:100%; text-align: center;'><br><br><br><br>Enviando, un momento por favor...<br><img src='images/reload.gif'></div>");

            $.ajax({
                url: "crear_pago_flow.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    var retorno = resp.split('|');
                    var resultado = retorno[1];
                    var token = resultado;
                    window.location.href = "pagar_flow.php?tokenRamuch=" + token;
                }
            });
        }
    </script>
</body>
</html>