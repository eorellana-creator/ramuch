<?php
include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

$token = @$_GET["token"];

$mysql = new mysql;
$mysql->connect();

$existe = 0;
$mensaje = "<br> <br><br> <br>El enlace al que intentas acceder no existe o ha caducado.<br> <br><br>  <a href='../index.php'><button class='btn btn-block btn-primary' type='button'>Ir al Inicio</button></a><br><br> <br> <br> <br>";

$sql = $mysql->query("SELECT id_usuario FROM usuario WHERE token='$token' AND token!=''  ; ");
$existe = $mysql->f_num($sql);
$result = @$mysql->f_obj($sql);

if ($existe > 0) {
    $existe = 1;
    $sql = $mysql->query("UPDATE usuario SET estado='Vigente' WHERE token='$token' AND token!=''  ; ");
    $sql = $mysql->query("UPDATE deudas SET estado='activa' WHERE id_usuario_deuda='$result->id_usuario' AND estado='Por confirmar email' AND sub_cuenta='matricula' ; ");

    $mensaje = "<br> <br><h3>Gracias por completar tu registro.</h3> <br>Ahora puedes acceder al sistema de socios de Ramuch.<br><div style='font-size:14px; color:#ff6d26; text-align:center; padding:20px;'>No olvides pagar tu Matrícula de inscripción, puedes hacerlo <a href='https://ramuch.cl/pagar/' target='_blank'>haciendo click aqui</a></div> <br> <br> <a href='https://ramuch.cl'><button class='btn btn-block btn-primary' type='button'>Ir a Ramuch</button></a> <br> <br> <br>";
} //if($existe>0)
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
    <title>Ramuch</title>

    <!-- Icons-->
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">

    <link href="node_modules/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
    <link href="node_modules/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
    <link href="node_modules/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="node_modules/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="css/style.css" rel="stylesheet">
    <link href="vendors/pace-progress/css/pace.min.css" rel="stylesheet">
    <link rel="stylesheet" href="js/validate-password/css/jquery.passwordRequirements.css" />
</head>

<style>
    .app, app-dashboard, app-root {
        min-height: 0px !important;
    }
</style>

<body class="app flex-row align-items-center">
    <div class="container" style="margin-top:40px;">
        <form name="formulario" id="formulario" method="post" action="" enctype="multipart/form-data">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card mx-4">
                        <div class="card-body p-4">
                            <h1><img src="images/tf.png" alt="Logo Ramuch"> &nbsp;Ramuch</h1>
                            <div id="contenido">
                                <?php echo $mensaje; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-danger" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Rut ya existe</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>El Rut que intentas ingresar ya existe en el sistema. Si olvidaste tu contraseña recupérala <a href="olvido.php">haciendo click aquí.</a></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModal2Label" aria-hidden="true">
        <div class="modal-dialog modal-danger" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Correo ya existe</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>El Correo que intentas ingresar ya existe en el sistema. Si olvidaste tu contraseña recupérala <a href="olvido.php">haciendo click aquí.</a></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- CoreUI and necessary plugins-->
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="node_modules/pace-progress/pace.min.js"></script>
    <script src="node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
    <script src="node_modules/@coreui/coreui/dist/js/coreui.min.js"></script>

    <script language="JavaScript" src="js/jquery.blockUI.js"></script>
    <script src="js/validadores.js"></script>
    <script src="js/rut/jquery.rut.js"></script>
    <script src="js/validate-password/js/jquery.passwordRequirements.js"></script>

    <script>
        $(function () {
            $(".pr-password").passwordRequirements({
                numCharacters: 8,
                useLowercase: true,
                useUppercase: true,
                useNumbers: true,
                useSpecial: false
            });
        });

        function enviar() {
            var error = 0;
            var mensaje = "";
            var pass1 = $("#password").val();
            var pass2 = $("#password2").val();

            if ($("#nombre").val() == "") {
                error = 1;
                $("#nombre").addClass("is-invalid");
            } else {
                $("#nombre").removeClass("is-invalid");
            }

            if ($("#apellido").val() == "") {
                error = 1;
                $("#apellido").addClass("is-invalid");
            } else {
                $("#apellido").removeClass("is-invalid");
            }

            if ($("#rut").val() == "") {
                error = 1;
                $("#rut").addClass("is-invalid");
            } else {
                $("#rut").removeClass("is-invalid");
            }

            if ($("#email").val() == "") {
                error = 1;
                $("#email").addClass("is-invalid");
            } else {
                $("#email").removeClass("is-invalid");
            }

            if ($("#telefono").val() == "") {
                error = 1;
                $("#telefono").addClass("is-invalid");
            } else {
                $("#telefono").removeClass("is-invalid");
            }

            if ($("#password").val() == "") {
                error = 1;
                $("#password").addClass("is-invalid");
            } else {
                $("#password").removeClass("is-invalid");
            }

            if ($("#password2").val() == "") {
                error = 1;
                $("#password2").addClass("is-invalid");
            } else {
                $("#password2").removeClass("is-invalid");
            }

            if (!valida_mail(document.getElementById("email"))) {
                error = 1;
                $("#email").addClass("is-invalid");
                $(".error-email").html("Debe ingresar un email válido");
            } else {
                $("#email").removeClass("is-invalid");
                $(".error-email").html("");
            }

            if (pass1.length <= 7) {
                error = 1;
                mensaje = "La contraseña debe tener un largo mínimo de 8 caracteres";
            }

            if ((pass1 != pass2) && error == "0") {
                error = 1;
                mensaje = "La contraseñas no coinciden. Deben ser iguales.";
            }

            var minusc = new RegExp('[a-z]');
            var mayusc = new RegExp('[A-Z]');
            var numero = new RegExp('[0-9]');

            if (!(minusc.test(pass1)) && error == "0") {
                error = 1;
                mensaje = "La contraseñas debe contener a lo menos una letra minúscula.";
            }

            if (!(mayusc.test(pass1)) && error == "0") {
                error = 1;
                mensaje = "La contraseñas debe contener a lo menos una letra mayúscula.";
            }

            if (!(numero.test(pass1)) && error == "0") {
                error = 1;
                mensaje = "La contraseñas debe contener a lo menos un número.";
            }

            if (error == "1") {
                $(".error-pass").html(mensaje);
            } else {
                $(".error-pass").html("");
            }

            if (error == "0") {
                $("alerta-invalido").html();

                var data = $("#formulario").serialize();

                $("#contenido").html("<div style='width:100%; text-align: center;'><br><br><br><br>Enviando, un momento por favor...<br><img src='images/reload.gif' ></div>");

                $.ajax({
                    type: 'POST',
                    url: "envia.php",
                    data: data,
                    success: function (resp) {
                        var retorno = resp.split(',xxx');
                        var resultado = retorno[1];

                        $('#contenido').html("<div style='width:100%; text-align: center; padding-top:30px;padding-bottom:90px'><strong><br>Hemos enviado un email a tu correo para que verifiques tu cuenta antes de acceder. Sigue las instrucciones y terminarás tu registro.<br><br>Muchas Gracias.<br>Ramuch.</strong><br><br></div>");
                    }
                });

                window.parent.parent.scrollTo(0, 0);
            } else {
                $("#alerta-invalido").html("<div class='alert alert-danger' role='alert'>Debe completar correctamente todos los datos para registrarse.</div>");
            } //if(error=="0")
        } //function enviar

        $(document).ready(function () {
            $("#rut")
                .rut({ formatOn: 'blur', validateOn: 'blur' })
                .on('rutInvalido', function () {
                    $(this).parents(".control-group").addClass("errorClass");
                    $(this).css("border-color", "red");
                    $("#errorrut2").html("Rut inválido. Debe ingresar un Rut válido.");
                    $("#rut").addClass("rutnovalido");
                })
                .on('rutValido', function () {
                    $(this).parents(".control-group").removeClass("errorClass")
                    $(this).css("border-color", "#ccc");
                    $("#errorrut2").html("");
                    $("#rut").removeClass("rutnovalido");
                });
        });

        function valida_mail(e) {
            if (e.value != "") {
                if (e.value.indexOf("@") == -1 || e.value.indexOf(".") == -1) {
                    return false;
                } //fin if indexOf
            } //Fin if !=""
            return true;
        } //fin function valida_mail  

        function mailExiste(e) {
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

            var email = e.value;

            url = "mail_existe.php?email=" + email;

            $.ajax({
                url: url,
                type: "post",
                dataType: "html",
                data: "",
                cache: false,
                contentType: false,
                processData: false
            })
                .done(function (res) {
                    var retorno = res.split('|');
                    var existe = retorno[1];
                    if (existe == "1") {
                        $("#myModal2").modal('show');
                        e.value = "";
                    }
                });
        } //function mailExiste()

        function rutExiste(e) {
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

            var rut = e.value;

            url = "rut_existe.php?rut=" + rut;

            $.ajax({
                url: url,
                type: "post",
                dataType: "html",
                data: "",
                cache: false,
                contentType: false,
                processData: false
            })
                .done(function (res) {
                    var retorno = res.split('|');
                    var existe = retorno[1];
                    if (existe == "1") {
                        $("#myModal").modal('show');
                        e.value = "";
                    }
                });
        } //function rutExiste()
    </script>
</body>
</html>