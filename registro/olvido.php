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

  <!-- Icons -->
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
  <link href="node_modules/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
  <link href="node_modules/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
  <link href="node_modules/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="node_modules/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">

  <!-- Main styles for this application -->
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
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card mx-4">
            <div class="card-body p-4">
              <h1><img src="images/tf.png" alt="Logo Ramuch"> &nbsp; Recuperación</h1>
              <p class="text-muted">Recuperación de contraseña</p>

              <div id="contenido">
                <p>
                  Para recuperar tu contraseña ingresa tu correo electrónico y enviaremos las instrucciones a tu correo para que recuperes la contraseña.<br>
                  Si no sabes con qué correo estás registrado escríbenos a directiva@ramuch.cl
                </p>
              </div>

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fa fa-id-card-o" style="color:#9ea1a2;"></i>
                  </span>
                </div>
                <input id="dato" name="dato" class="form-control" type="text" placeholder="Ingresa tu email" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);">
              </div>

              <br>
              <div id="alerta-invalido"></div>
              <br>

              <button class="btn btn-block btn-success" onClick="enviar();" type="button">Recuperar</button>
            </div>

            <div class="card-footer p-4">
              <div class="row">
                <div class="col-6">
                  <a href="https://www.ramuch.cl/">
                    <button class="btn btn-block btn-secondary color-bl" type="button">
                      <span>volver a Ramuch</span>
                    </button>
                  </a>
                </div>
                <div class="col-6">
                  <a href="index.php">
                    <button class="btn btn-block btn-info color-bl" type="button">
                      <span>Volver al Registro</span>
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

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-danger" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Atención</h4>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <p>El Email o Rut que intentas ingresar no existe en nuestro sistema.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-danger" type="button" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- CoreUI and necessary plugins -->
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
    function enviar() {
      var error = 0;
      var mensaje = "";

      if ($("#dato").val() == "") {
        error = 1;
        $("#dato").addClass("is-invalid");
      } else {
        $("#dato").removeClass("is-invalid");
      }

      if (error == "0") {
        $("alerta-invalido").html();

        var data = $("#formulario").serialize();

        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

        $.ajax({
          type: 'POST',
          url: "olvido_recupera.php",
          data: data,
          success: function(resp) {
            var retorno = resp.split('|');
            var resultado = retorno[1];

            if (resultado == "1")
              $('#contenido').html("<div style='width:100%; text-align: center; padding-top:30px;padding-bottom:90px'><strong><br>Hemos enviado un email a tu correo para que verifiques tu cuenta antes de acceder. Sigue las instrucciones y terminarás tu registro.<br><br>Muchas Gracias.<br>Ramuch.</strong><br><br></div>");

            if (resultado == "0")
              $("#myModal").modal('show');
          }
        });

        window.parent.parent.scrollTo(0, 0);
      } else {
        $("#alerta-invalido").html("<div class='alert alert-danger' role='alert'>Debes completar el email o rut.</div>");
      }
    }
  </script>
</body>
</html>