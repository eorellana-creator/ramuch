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
                    <div class="col-md-6">
                      <div class="card mx-4">
                        <div class="card-body p-4">
                          <h1><img src="images/tf.png" alt="Logo Ramuch" > &nbsp; Registrarse</h1>

                          
                          <p class="text-muted">Crea tu cuenta en Ramuch</p>




<div  id="contenido">


                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="icon-user"></i>
                              </span>
                            </div>
                            <input id="nombre" name="nombre" class="form-control" type="text" placeholder="Tu nombre completo" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" >
                          </div>



                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                              <i class="fa fa-id-card-o" style="color:#9ea1a2;"></i>
                              </span>
                            </div>
                            <input id="rut" name="rut" class="form-control" type="text" placeholder="Tu Rut. Ej: 14.231.123-k" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);rutExiste(this);" >
                            <div id="errorrut2" class="errorcampo" style="width:100%;"></div>
                          </div>

                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">@</span>
                            </div>
                            <input id="email" name="email" class="form-control" type="email" placeholder="Tu Email" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);mailExiste(this);">
                            <div id="error-email" class="error-email" style="width:100%;"></div>
                          </div>


                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">@</span>
                            </div>
                            <input id="email2" name="email2" class="form-control" type="email" placeholder="Confirma tu Email" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);mailExiste(this);">
                            <div id="error-email2" class="error-email2" style="width:100%;"></div>
                          </div>




                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="icon-screen-smartphone"></i>
                              </span>
                            </div>
                            <input id="telefono" name="telefono" class="form-control" type="text" placeholder="Tu teléfono." onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" >
                          </div>


                          <label class="form-col-form-label" for="inputSuccess1">Certificado Alumno Regular si corresponde (PDF o Imagen)</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="icons   cui-paperclip"></i>
                              </span>
                            </div>
                            <input id="archivo" type="file" name="archivo" class="form-control"  onChange="validaCertificado(this);"  >
                          </div>




                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="icon-lock"></i>
                              </span>
                            </div>
                            <div><input id="password" name="password" class="form-control pr-password" type="password" placeholder="Contraseña" onBlur="elimina_blancos_inicio_fin(this);" ></div>
                            <div class="error-pass"></div>
                          </div>
                          <div class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="icon-lock"></i>
                              </span>
                            </div>
                            <input id="password2" name="password2" class="form-control" type="password" placeholder="Repite contraseña" onBlur="elimina_blancos_inicio_fin(this);" >
                          </div>

                          <div class="acepto-terminos"  ><input type="checkbox" id="terminos" name="terminos" value=""  > Acepto el <a href="reglamento-ramuch-2022.pdf" target="_blank">Reglamento de Cuotas</a> y los <a href="#">deberes del Club Ramuch</a></div>

                          <div id="alerta-invalido"></div>



                          

                          <button class="btn btn-block btn-success" onClick="enviar();" type="button">Crear cuenta</button>
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

  if(  !($('#terminos').is(':checked') )  ) {
  $(".acepto-terminos").css("border","1px #ff0000 solid");
      error    = 1;
      mensaje  = "Debes aceptar el Reglamento y deberes marcando la casilla.";
  }else{
    $(".acepto-terminos").css("border","0px #ffffff solid");
  }



if(error=="0"){
  $("alerta-invalido").html();

var data = $("#formulario").serialize();

var formData = new FormData(document.getElementById("formulario"));


//alert(data);
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
//alert(resp);
var retorno = resp.split(',xxx');
var resultado = retorno[1];

$('#contenido').html("<div style='width:100%; text-align: center; padding-top:30px;padding-bottom:90px'><strong><br>Hemos enviado un email a tu correo para que verifiques tu cuenta antes de acceder. <br>Sigue las instrucciones y terminarás tu registro.<br><br>Muchas Gracias.<br>Ramuch.</strong><br><br></div>");

}

});	


window.parent.parent.scrollTo(0,0);

}else{
  $("#alerta-invalido").html("<div class='alert alert-danger' role='alert'>Debe completar correctamente todos los datos para registrarse.</div>");
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



  </body>
</html>
