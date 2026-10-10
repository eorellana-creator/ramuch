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
    <title>Registro Ramuch</title>

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

    <!-- Clave del sitio reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<script>

setTimeout(
			function(){
				document.getElementById( "formulario" ).reset();
			},
			5
			);


$(function(){
        $(".pr-password").passwordRequirements({
                  numCharacters: 8,
                  useLowercase: true,
                  useUppercase: true,
                  useNumbers: true,
                  useSpecial: false
                });
    });

$(document).ready(function() {
    $('#referencia').change(function() {
        if ($(this).val() === 'Otro') {
            $('#otro-referencia-container').show();
            $('#otro_referencia').prop('required', true);
        } else {
            $('#otro-referencia-container').hide();
            $('#otro_referencia').prop('required', false);
        }
    });
});

function enviar(){

  var error   = 0;
  var mensaje = "";
  var pass1   = $("#password").val();
  var pass2   = $("#password2").val();

  if( $("#nombre").val()==""  ){
      error=1;
      $("#nombre").addClass( "is-invalid" );
  }else{
      $("#nombre").removeClass( "is-invalid" );
  }

  if( $("#rut").val()==""  ){
      error=1;
      $("#rut").addClass( "is-invalid" );
  }else{
      $("#rut").removeClass( "is-invalid" );
  }

  if( $("#email").val()==""  ){
      error=1;
      $("#email").addClass( "is-invalid" );
  }else{
      $("#email").removeClass( "is-invalid" );
  }

  if( $("#telefono").val()==""  ){
      error=1;
      $("#telefono").addClass( "is-invalid" );
  }else{
      $("#telefono").removeClass( "is-invalid" );
  }

  if( $("#password").val()==""  ){
      error=1;
      $("#password").addClass( "is-invalid" );
  }else{
      $("#password").removeClass( "is-invalid" );
  }

  if( $("#password2").val()==""  ){
      error=1;
      $("#password2").addClass( "is-invalid" );
  }else{
      $("#password2").removeClass( "is-invalid" );
  }

  if( !valida_mail( document.getElementById("email") ) ){
      error=1;
      $("#email").addClass( "is-invalid" );
      $(".error-email").html("Debe ingresar un email válido");
  }else{
      $("#email").removeClass( "is-invalid" );
      $(".error-email").html("");
  }

  if( document.getElementById("email").value != document.getElementById("email2").value   ){
      error=1;
      $("#email2").addClass( "is-invalid" );
      $(".error-email2").html("Los email ingresados no coinciden. Deben ser iguales.");
  }else{
      $("#email").removeClass( "is-invalid" );
      $(".error-email2").html("");
  }

  if(pass1.length <=7 ){
    error    = 1;
    mensaje  = "La contraseña debe tener un largo mínimo de 8 caracteres";
  }

  if(   (pass1 != pass2) && error =="0"){
    error    = 1;
    mensaje  = "La contraseñas no coinciden. Deben ser iguales.";
  }

  var     minusc   		= new RegExp('[a-z]');
  var	    mayusc   		= new RegExp('[A-Z]');
  var 	  numero     	= new RegExp('[0-9]');

  if( ! (minusc.test(pass1))  && error =="0" ){
    error    = 1;
    mensaje  = "La contraseñas debe contener a lo menos una letra minúscula.";
  }

  if( ! (mayusc.test(pass1))  && error =="0" ){
    error    = 1;
    mensaje  = "La contraseñas debe contener a lo menos una letra mayúscula.";
  }

  if( ! (numero.test(pass1))  && error =="0" ){
    error    = 1;
    mensaje  = "La contraseñas debe contener a lo menos un número.";
  }

  if( error=="1" ){
    $(".error-pass").html(mensaje);
  }else{
    $(".error-pass").html("");
  }

  if( !($('#terminos').is(':checked') )  ) {
    $(".acepto-terminos").css("border","1px #ff0000 solid");
      error    = 1;
      mensaje  = "Debes aceptar el Reglamento y deberes marcando la casilla.";
  }else{
    $(".acepto-terminos").css("border","0px #ffffff solid");
  }

  if($("#referencia").val()==""){
        error=1;
        $("#referencia").addClass("is-invalid");
        mensaje = "Debes seleccionar dónde nos conociste";
  }else{
        $("#referencia").removeClass("is-invalid");
  }

  if($("#referencia").val()=="Otro" && $("#otro_referencia").val()==""){
        error=1;
        $("#otro_referencia").addClass("is-invalid");
        mensaje = "Debes especificar dónde nos conociste";
  }else if($("#referencia").val()=="Otro"){
        $("#otro_referencia").removeClass("is-invalid");
  }

  // Verificar si el captcha fue completado
  var recaptchaResponse = grecaptcha.getResponse();
  if (recaptchaResponse.length === 0) {
    // Si el captcha no está completado
    $('#alerta-invalido').html('<p style="color:red;">Por favor, completa el captcha.</p>');
    return false;
  }

  // Si el captcha esta con el ticket, enviar el formulario con AJAX para validarlo.

  //Kop REVISAR SI SE DEBE COLOCAR UN ELSE AL FINAL O NO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  // fueron eliminados los campos que sobraban en el envio de post
  $.ajax({
    url: 'verificar_captcha.php',
    type: 'POST',
    data: { 
        'g-recaptcha-response': recaptchaResponse
    },
    success: function(response) {
        // Mostrar el resultado en la página sin recargar
        $('#mensaje').html(response);
    }

    
  });  // Verificar si el captcha fue completado

  if(error=="0"){
    $("alerta-invalido").html();
    var data = $("#formulario").serialize();
    var formData = new FormData(document.getElementById("formulario"));
    $("#contenido").html("<div style='width:100%; text-align: center;'><br><br><br><br>Enviando, un momento por favor...<br><img src='images/reload.gif' ></div>");

    $.ajax({
      url: "envia.php",
                  type: "post",
                  dataType: "html",
                  data: formData,
                  cache: false,
                  contentType: false,
                processData: false,
      success: function(resp){

      var retorno = resp.split(',xxx');
      var resultado = retorno[1];

      $('#contenido').html("<div style='width:100%; text-align: center; padding-top:30px;padding-bottom:90px'><strong><br>Hemos enviado un email a tu correo para que verifiques tu cuenta antes de acceder. <br>Sigue las instrucciones y terminarás tu registro.<br><br>Muchas Gracias.<br>Ramuch.</strong><br><br></div>");

      }
    });	
    window.parent.parent.scrollTo(0,0);
  }else{
    $("#alerta-invalido").html("<div class='alert alert-danger' role='alert'>Debe completar correctamente todos los datos para registrarse. </div>");
  }//if(error=="0")

}//function enviar



$( document ).ready(function() {

$("#rut")
.rut({formatOn: 'blur', validateOn: 'blur'})
.on('rutInvalido', function(){ 
$(this).parents(".control-group").addClass("errorClass");
$(this).css("border-color","red");
$("#errorrut2").html("Rut inválido. Debe ingresar un Rut válido.");
$( "#rut" ).addClass( "rutnovalido" );

})
.on('rutValido', function(){ 
$(this).parents(".control-group").removeClass("errorClass")
$(this).css("border-color","#ccc");
$("#errorrut2").html("");
$( "#rut" ).removeClass( "rutnovalido" );

});

});


$(document).ready(function() {
            $('#formulario').on('submit', function(e) {
                e.preventDefault(); // Evitar recarga de la página
                var formData = $(this).serialize(); // Serializar los datos del formulario
                
                $.ajax({
                    url: 'verificar_captcha.php', // Archivo PHP donde se valida el captcha
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#result').html(response); // Mostrar el mensaje de validación
                    },
                    error: function() {
                        $('#result').html('Error al enviar el formulario.');
                    }
                });
            });
        });


function valida_mail(e){  
if(e.value!=""){
if(e.value.indexOf("@")==-1 || e.value.indexOf(".")==-1){ 
return false; 
} //fin if indexOf
} //Fin if !=""
return true;
} //fin function valida_mail  



function mailExiste(e){
 
  $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
      
  var email = e.value;
       
  url	= "mail_existe.php?email="+email;

     $.ajax({
                  url: url,
                  type: "post",
                  dataType: "html",
                  data: "",
                  cache: false,
                  contentType: false,
                  processData: false
              })
                  .done(function(res){
                  //alert(res);
                  var retorno = res.split('|');
                  var existe = retorno[1]; 
                   if(existe=="1"){
                       $("#myModal2").modal('show');
                       e.value="";
                   }


                  });

}//function mailExiste()




function rutExiste(e){
	
  $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
      
  var rut = e.value;	

      
  url	= "rut_existe.php?rut="+rut;

     $.ajax({
                  url: url,
                  type: "post",
                  dataType: "html",
                  data: "",
                  cache: false,
                  contentType: false,
                  processData: false
              })
                  .done(function(res){
                  //alert(res);
                  var retorno = res.split('|');
                  var existe = retorno[1]; 
                   if(existe=="1"){
                       $("#myModal").modal('show');
                       e.value="";
                   }


                  });

}//function rutExiste()


function validaCertificado(e){
        var fileExtension = ['pdf','jpg','jpeg','jpg','png'];
        if ($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
          $("#myModal3").modal('show');
			$(e).val("");
			return false;
        }else{
			return true;
			}
}
 </script>

  </head>

  <style>
      .app, app-dashboard, app-root{
        min-height: 0px !important;
      }
  </style>


  <body class="app flex-row align-items-center" >
  
      <div class="container" style="margin-top:40px;">
            <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data">

                  <div class="row justify-content-center">
                    <div class="col-md-8">
                      <div class="card mx-4">
                        <div class="card-body p-4">
                          <h1><img src="images/tf.png" alt="Logo Ramuch" > &nbsp; Registrarse</h1>
                          <p class="text-muted">Crea tu cuenta en Ramuch</p>
                          <div  id="contenido">
                          <label for="nombres">Nombres *</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="icon-user"></i>
                              </span>
                            </div>
                            <input id="nombres" name="nombres" class="form-control" type="text" placeholder="Nombres" maxlength="100" required>
                          </div>

                          <label for="apellido_paterno">Apellido paterno *</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="icon-user"></i></span></div>
                            <input id="apellido_paterno" name="apellido_paterno" class="form-control" type="text" placeholder="Apellido paterno" maxlength="60" required>
                          </div>

                          <label for="apellido_materno">Apellido materno *</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="icon-user"></i></span></div>
                            <input id="apellido_materno" name="apellido_materno" class="form-control" type="text" placeholder="Apellido materno" maxlength="60" required>
                          </div>

                          <label for="sexo_genero">Sexo/Género (opcional)</label>
                          <div class="input-group mb-3">
                            <select id="sexo_genero" name="sexo_genero" class="form-control">
                              <option value="">Selecciona una opción</option>
                              <option value="Femenino">Femenino</option>
                              <option value="Masculino">Masculino</option>
                              <option value="Otro">Otro</option>
                            </select>
                          </div>

                          <label for="rut">RUT *</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                              <i class="fa fa-id-card-o" style="color:#9ea1a2;"></i>
                              </span>
                            </div>
                            <input id="rut" name="rut" class="form-control" type="text" placeholder="Ej: 14.231.123-K" maxlength="12" autocomplete="off" onBlur="rutExiste(this);" required>
                            <div id="errorrut2" class="errorcampo" style="width:100%;"></div>
                          </div>

                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">@</span>
                            </div>
                            <input id="email" name="email" class="form-control" type="email" placeholder="Tu correo electrónico" maxlength="150" autocomplete="email" onBlur="mailExiste(this);" required>
                            <div id="error-email" class="error-email" style="width:100%;"></div>
                          </div>

                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">@</span>
                            </div>
                            <input id="email2" name="email2" class="form-control" type="email" placeholder="Confirma tu correo electrónico" maxlength="150" autocomplete="email" required>
                            <div id="error-email2" class="error-email2" style="width:100%;"></div>
                          </div>

                          <label>Fecha de nacimiento *</label>
                          <div class="form-row mb-3">
                            <div class="col">
                              <select id="nacimiento_dia" name="nacimiento_dia" class="form-control" required>
                                <option value="">Día</option>
                                <?php for ($dia = 1; $dia <= 31; $dia++) { ?><option value="<?php echo $dia; ?>"><?php echo $dia; ?></option><?php } ?>
                              </select>
                            </div>
                            <div class="col">
                              <select id="nacimiento_mes" name="nacimiento_mes" class="form-control" required>
                                <option value="">Mes</option>
                                <?php foreach (array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre') as $numeroMes => $nombreMes) { ?><option value="<?php echo $numeroMes; ?>"><?php echo $nombreMes; ?></option><?php } ?>
                              </select>
                            </div>
                            <div class="col">
                              <select id="nacimiento_anio" name="nacimiento_anio" class="form-control" required>
                                <option value="">Año</option>
                                <?php for ($anio = (int)date('Y'); $anio >= (int)date('Y') - 100; $anio--) { ?><option value="<?php echo $anio; ?>"><?php echo $anio; ?></option><?php } ?>
                              </select>
                            </div>
                          </div>

                          <label for="telefono">Teléfono *</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <select id="telefono_pais" name="telefono_pais" class="form-control" aria-label="Código de país" required>
                                <option value="+56" selected>Chile +56</option>
                                <option value="+54">Argentina +54</option>
                                <option value="+51">Perú +51</option>
                                <option value="+591">Bolivia +591</option>
                                <option value="+57">Colombia +57</option>
                                <option value="+593">Ecuador +593</option>
                                <option value="+1">EE.UU./Canadá +1</option>
                                <option value="+34">España +34</option>
                              </select>
                            </div>
                            <input id="telefono" name="telefono" class="form-control" type="tel" inputmode="numeric" placeholder="912345678" minlength="7" maxlength="15" autocomplete="tel-national" required>
                          </div>

                          <label for="tipo_socio">Tipo de inscripción *</label>
                          <div class="input-group mb-3">
                            <select id="tipo_socio" name="tipo_socio" class="form-control" required>
                              <option value="">Selecciona una opción</option>
                              <option value="profesional">Profesional</option>
                              <option value="estudiante">Estudiante</option>
                            </select>
                          </div>

                          <div id="certificado-container" style="display:none;">
                          <label class="form-col-form-label" for="archivo">Certificado de Alumno Regular (PDF o imagen) *</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="icons cui-paperclip"></i>
                              </span>
                            </div>
                            <input id="archivo" type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" onChange="validaCertificado(this);">
                          </div>
                          </div>

                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="icon-lock"></i>
                              </span>
                            </div>
                            <div><input id="password" name="password" class="form-control pr-password" type="password" placeholder="Contraseña" minlength="8" autocomplete="new-password" required></div>
                            <div class="error-pass"></div>
                          </div>
                          <div class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="icon-lock"></i>
                              </span>
                            </div>
                            <input id="password2" name="password2" class="form-control" type="password" placeholder="Repite contraseña" minlength="8" autocomplete="new-password" required>
                          </div>

                          <fieldset class="form-group">
                            <legend class="h6">¿Has sido parte de otro club de montañismo?</legend>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="otro_club_montanismo" id="otro_club_si" value="1">
                              <label class="form-check-label" for="otro_club_si">Sí</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="otro_club_montanismo" id="otro_club_no" value="0">
                              <label class="form-check-label" for="otro_club_no">No</label>
                            </div>
                          </fieldset>

                          <div id="nombre-club-container" class="input-group mb-3" style="display:none;">
                            <input id="nombre_otro_club" name="nombre_otro_club" class="form-control" type="text" maxlength="50" placeholder="Nombre del club (máximo 50 caracteres)">
                          </div>

                          <fieldset class="form-group motivaciones">
                            <legend class="h6">¿Cuáles son tus principales motivos para ingresar a RAMUCH? (selecciona hasta 3)</legend>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="motivaciones[]" id="motivacion_vinculos" value="vinculos"><label class="form-check-label" for="motivacion_vinculos">Conocer personas, formar cordada y generar vínculos con otros montañistas.</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="motivaciones[]" id="motivacion_formacion" value="formacion"><label class="form-check-label" for="motivacion_formacion">Tomar cursos, capacitaciones y desarrollar mis habilidades y experiencia en montaña.</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="motivaciones[]" id="motivacion_beneficios" value="beneficios"><label class="form-check-label" for="motivacion_beneficios">Aprovechar los beneficios que entrega el club.</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="motivaciones[]" id="motivacion_actividades" value="actividades"><label class="form-check-label" for="motivacion_actividades">Participar en salidas y actividades de montaña.</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="motivaciones[]" id="motivacion_historia" value="historia"><label class="form-check-label" for="motivacion_historia">Conocer y participar de la historia y tradición de RAMUCH.</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="motivaciones[]" id="motivacion_otro_check" value="otro"><label class="form-check-label" for="motivacion_otro_check">Otro.</label></div>
                          </fieldset>

                          <div id="motivacion-otro-container" class="input-group mb-3" style="display:none;">
                            <input id="motivacion_otro" name="motivacion_otro" class="form-control" type="text" maxlength="255" placeholder="Indica otro motivo">
                          </div>

                          <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                  <span class="input-group-text">
                                      <i class="icon-info"></i>
                                  </span>
                              </div>
                              <select id="referencia" name="referencia" class="form-control" required>
                                  <option value="">¿Dónde nos conociste?</option>
                                  <option value="Instagram">Instagram</option>
                                  <option value="Facebook">Facebook</option>
                                  <option value="Twitter/X">Twitter/X</option>
                                  <option value="LinkedIn">LinkedIn</option>
                                  <option value="Correo electrónico">Correo electrónico</option>
                                  <option value="Universidad">Universidad</option>
                                  <option value="Trabajo">Trabajo</option>
                                  <option value="Amigos/Familia">Amigos/Familia</option>
                                  <option value="Evento">Evento</option>
                                  <option value="Otro">Otro</option>
                              </select>
                          </div>

                          <div id="otro-referencia-container" style="display:none;" class="input-group mb-3">
                              <div class="input-group-prepend">
                                  <span class="input-group-text">
                                      <i class="icon-pencil"></i>
                                  </span>
                              </div>
                              <input id="otro_referencia" name="otro_referencia" class="form-control" type="text" placeholder="Especifica dónde nos conociste" maxlength="50">
                          </div>


                          <div class="acepto-terminos mb-3">
                            <label>
                              <input type="checkbox" id="terminos" name="terminos" value="1" required>
                              Acepto el <a href="https://www.ramuch.cl/admin/documentos/6ac2b38517f48.pdf" target="_blank" rel="noopener">Protocolo de Cuotas</a>,
                              <a href="https://ramuch.cl/wp-content/uploads/2023/03/ESTATUTOS-RAMUCH.pdf" target="_blank" rel="noopener">las obligaciones y derechos de los socios</a>
                              (art. 8 y 9 de los Estatutos), además de autorizar a la Rama de Montaña U. de Chile (RAMUCH) para comunicar mi nombre, RUT y correo electrónico únicamente a las entidades con las que el Club mantenga convenios de beneficios para socios, con el exclusivo objeto de permitir la aplicación de dichos beneficios. Esta autorización podrá ser revocada a solicitud.
                            </label>
                          </div>

                          <div id="alerta-invalido"></div>

                          
                           <!-- Kop Agrega el CAPTCHA justo antes del botón de enviar 
                                Aquí se inserta el captcha de Google -->
                          <div class="g-recaptcha" id="rct" data-sitekey="6LfEwTkqAAAAAES8d1xsGnu9cQ52GunART1qltZM"></div><br>

                          <button id="btn-crear-cuenta" class="btn btn-block btn-success" type="submit">Crear cuenta</button>
                        </div>
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
                            <a href="olvido.php">
                              <button class="btn btn-block btn-info color-bl" type="button">
                                <span>Olvidé mi contraseña</span>
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
                  <h4 class="modal-title">Rut ya existe</h4>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>El Rut que intentas ingresar ya existe en el sistema. Si olvidaste tu contraseña recupérala <a href="olvido.php">haciendo click aquí.</a><br><br>Si el problema persiste, envía un email a montana.uchile@gmail.com indicando tus datos.</p>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-danger" type="button" data-dismiss="modal">Cerrar</button>
      
                </div>
              </div>
              <!-- /.modal-content-->
            </div>
            <!-- /.modal-dialog-->
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
              <!-- /.modal-content-->
            </div>
            <!-- /.modal-dialog-->
          </div>

          <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModal3Label" aria-hidden="true">
            <div class="modal-dialog modal-danger" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Certificado no válido</h4>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>El Certificado debe ser un archivo PDF o una imagen. No se permite otro tipo de documento.</p>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-danger" type="button" data-dismiss="modal">Cerrar</button>
      
                </div>
              </div>
              <!-- /.modal-content-->
            </div>
            <!-- /.modal-dialog-->
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
    <script src="js/formulario-inscripcion.js"></script>

</body>
</html>
