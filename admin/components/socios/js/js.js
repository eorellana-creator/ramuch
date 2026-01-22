 

$(document).ready(function() {

 var tipoSocio = "";
 var tipoCuota = "";

    if( $("#tipo").val()!="" ){
    $('#tipos option[value="'+$("#tipo").val()+'"]').prop("selected", true);
    tipoSocio = $("#tipo").val();
    }
    
    if( $("#cuota").val()!="" ){
    $('#cuotas option[value="'+$("#cuota").val()+'"]').prop("selected", true);
    tipoCuota = $("#cuota").val();
    }



    $('#tabla').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[ 1, "asc" ]],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "pageLength": 25,
        "columnDefs": [ { orderable: false, targets: [5,6,7,8] } ],
        "ajax": {
            "url": "components/socios/models/socios_list_procesa.php?tipo="+tipoSocio+"&cuota="+tipoCuota,
            "type": "POST"
        }
 

    } );


  



} );

 



 

//*********************************************************************** */

function goTabPass(){
    $(".tab-pass").click(); 
}

//*********************************************************************** */

function selCuotas(e){
var tipo = $("#tipo").val();

  document.location.href = "index.php?component=socios&view=socios_list&cuota="+e.value+"&tipo="+tipo;
}//function selCuotas(e)
    
//********************************************************************** */

function selTipo(e){
    var cuota = $("#cuota").val();
    document.location.href = "index.php?component=socios&view=socios_list&tipo="+e.value+"&cuota="+cuota;
}//function selTipo(e)

//********************************************************************** */

$(document).ready(function() {
    

    $('#tablapagos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[ 0, "desc" ]],
    });

    $('#tabladeudas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[ 0, "desc" ]],
    });


    $('#tablaprestamos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[ 0, "desc" ]],
    });

    
} );


function validaCertificado(e){
    var fileExtension = ['png','jpeg','jpg','docx','doc','pdf','xls','xlsx'];
    if ($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        BootstrapDialog.alert('El Archivo debe ser un PDF, imagen o word.');
        $(e).val("");
        return false;
    }else{
        return true;
        }
    
}

function validaImagen(e){
    var fileExtension = ['png','jpeg','jpg','gif'];
    if ($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        BootstrapDialog.alert('El Archivo debe ser una imagen.');
        $(e).val("");
        return false;
    }else{
        return true;
        }
    
}




function subirImagen(){
	
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        
    var datos = $("#formulario").serialize();	
    
    var formData = new FormData(document.getElementById("formulario"));
    
    if(validaImagen( $("#foto") )==true  )  {
          
    url	= "components/socios/models/subir_imagen.php";

       
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
                    //alert(res);
                    var retorno = res.split('|');
                    var subida = retorno[1]; 
                    $("#foto-perfil").attr("src","images/img_perfil/"+subida);
                    });
                    
          
      }//if(validaImagen( $("#imagen") )==true)  {
        
}//function subirImagen()



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


     function enviar(){
        var error = 0;
        var mensaje = "";

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

        if( $("#mail").val()==""  ){
            error=1;
            $("#mail").addClass( "is-invalid" );
        }else{
            $("#mail").removeClass( "is-invalid" );
        }
        

        if( $("#tipoInscripcion").val()==""  ){
            error=1;
            $("#tipoInscripcion").addClass( "is-invalid" );
        }else{
            $("#tipoInscripcion").removeClass( "is-invalid" );
        }


        


        if(error=="1"){
           $(".tab-datos").click(); 
        }else{
            
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
            var formData = new FormData(document.getElementById("formulario"));
            url	= "components/socios/models/insert_update.php";



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
				//alert(res);
				var retorno = res.split('|');
		        var token = retorno[1];
	
               document.location.href="index.php?component=socios&view=socios&token="+token;
				  
				  
                });


        }//else if(error=="1")

         



     }//function enviar()

     //*************************************************************************************** */
     //*************************************************************************************** */

     function rutExiste(e){
	
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
            
        var rut = e.value;	
        var token = $("#token").val();
            
        url	= "components/socios/models/rut_existe.php?rut="+rut+"&token="+token;

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



     //*************************************************************************************** */
     //*************************************************************************************** */

     function mailExiste(e){
	
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
            
        var mail = e.value;	
        var token = $("#token").val();
        
              
        url	= "components/socios/models/mail_existe.php?mail="+mail+"&token="+token;
    
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












    $(function(){
        $(".pr-password").passwordRequirements({
                  numCharacters: 8,
                  useLowercase: true,
                  useUppercase: true,
                  useNumbers: true,
                  useSpecial: false
                });
    });



    function savePassword(){

        var pass1    = $("#password").val();
        var pass2   = $("#password2").val();
        var error   = 0;
        var mensaje = "";

    

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
		var 	numero     		= new RegExp('[0-9]');

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
            actualizaPass();
        }

          


    }//function savePassword()

    //*************************************************************************** */

    function actualizaPass(){


        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
            
        var pass    = $("#password").val();
        var token   = $("#token").val();
        
           
        $.ajax({
            type: 'POST',
            url: "components/socios/models/actualiza_pass.php",
            data:  "&pass="+pass+"&token="+token,
            
            success: function(resp){
                var retorno = resp.split('|');
                var existe 	= retorno[0];

                document.location.reload();
                     
                 			
            }
        });	

    

    }// function actualizaPass()




    $(document).ready(function() {

    if( $("#hplan").val()!="" ){
        $('#tipoInscripcion option[value="'+$("#hplan").val()+'"]').prop("selected", true);
    }
 

    });





    function actualizaInscripcion(){



        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
            
        var tipo    = $("#tipoInscripcion").val();
        var token   = $("#token").val();
        
           
        $.ajax({
            type: 'POST',
            url: "components/socios/models/actualiza_inscripcion.php",
            data:  "&tipo="+tipo+"&token="+token,
            
            success: function(resp){
                var retorno = resp.split('|');
                var existe 	= retorno[0];
            
                document.location.reload();
                     
                 			
            }
        });	


   
    }//  function actualizaInscripcion()

    function eliminarFila(id_usuario) {
        var fila = document.getElementById(id_usuario);
        fila.parentNode.removeChild(fila);
        // Aquí puedes hacer una llamada AJAX para eliminar el usuario de la base de datos si es necesario
    }