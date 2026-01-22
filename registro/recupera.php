<?php
include("includes/sql_inyection.php");
include("includes/conexionMysql.php");
include("includes/funciones.php");

$token = @$_GET["token"];

$mysql = new mysql;
$mysql->connect();

$existe = 0;
$mensaje = "<br> <br><br> <br>El enlace al que intentas acceder no existe o ha caducado.<br> <br><br>  <a href='http://www.ramuch.cl/'><button class='btn btn-block btn-primary' type='button'>Ir al Inicio</button></a><br><br> <br> <br> <br>";

$sql = $mysql->query("SELECT id_usuario FROM usuario WHERE token='$token' AND token!=''  ; ");
$existe = $mysql->f_num($sql);

if ($existe > 0) {
    $mensaje = "";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="by Kop">
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
        <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data">
            <input id="token" name="token" type="hidden" value="<?php echo @$_GET['token']; ?>">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card mx-4">
                        <div class="card-body p-4">
                            <h1><img src="images/tf.png" alt="Logo Ramuch"> &nbsp; Recuperar</h1>
                            <p class="text-muted">Recuperación de contraseña</p>
                            <div id="contenido">
                                <?php
                                if ($existe > 0) {
                                    ?>
                                    <p class="">Ingresa tu nueva contraseña.<br><strong>Debe tener al menos</strong> 8 caracteres, una letra mayúscula, una letra minúscula y un número.</p>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="icon-lock"></i>
                                            </span>
                                        </div>
                                        <input id="password" name="password" class="form-control pr-password" type="password" placeholder="Contraseña Nueva" onBlur="elimina_blancos_inicio_fin(this);">
                                        <div class="error-pass"></div>
                                    </div>
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="icon-lock"></i>
                                            </span>
                                        </div>
                                        <input id="password2" name="password2" class="form-control" type="password" placeholder="Repite contraseña" onBlur="elimina_blancos_inicio_fin(this);">
                                    </div>
                                    <div id="alerta-invalido"></div>
                                    <button class="btn btn-block btn-success" onClick="enviar();" type="button">Restablecer contraseña</button>
                                    <br> <br>
                                    <?php
                                } else {
                                    echo @$mensaje;
                                }
                                ?>
                            </div>
                        </div>
                        <div class="card-footer p-4">
                            <div class="row">
                                <div class="col-6">
                                    <a href="https://ramuch.cl/">
                                        <button class="btn btn-block btn-secondary color-bl" type="button">
                                            <span>volver al inicio</span>
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
        $(function() {
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
                    url: "recupera_setea.php",
                    data: data,
                    success: function(resp) {
                        var retorno = resp.split(',xxx');
                        var resultado = retorno[1];
                        $('#contenido').html("<div style='width:100%; text-align: center; padding-top:30px;padding-bottom:90px'><strong><br>Tu contraseña se ha restablecido exitosamente para que accedas a nuestro sistema de socios.<br><br>Muchas Gracias.<br>Ramuch.</strong><br><br></div>");
                    }
                });

                window.parent.parent.scrollTo(0, 0);
            } else {
                $("#alerta-invalido").html("<div class='alert alert-danger' role='alert'>Debe completar correctamente todos los datos.</div>");
            }
        }
    </script>
</body>
</html>