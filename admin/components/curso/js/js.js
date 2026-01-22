 

$(document).ready(function() {


    $('#tabla').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "responsive": false,
        "pageLength": 25,
        "initComplete": function(settings, json) {
            $('.sel2-basic-single').select2();
          },
        "columnDefs": [ { orderable: false, targets: [1,2,3,4] } ],
        "ajax": {
            "url": "components/curso/models/curso_list_procesa.php",
            "type": "POST"
        }
         
 

    } );



  
    $('[data-toggle="tooltip"]').tooltip(); 


    $(".usuarios-tags").select2({
        dropdownParent: $(".modal-body"),
        tags: true
      });

 


} );


//********************************************************************************** */

function enviar(){
    var error = 0;
    var mensaje = "";

    if( $("#nombre").val()==""  ){
        error=1;
        $("#nombre").addClass( "is-invalid" );
    }else{
        $("#nombre").removeClass( "is-invalid" );
    }

    if( $("#fechaInicio").val()==""  ){
        error=1;
        $("#fechaInicio").addClass( "is-invalid" );
    }else{
        $("#fechaInicio").removeClass( "is-invalid" );
    }

    if( $("#fechaFin").val()==""  ){
        error=1;
        $("#fechaFin").addClass( "is-invalid" );
    }else{
        $("#fechaFin").removeClass( "is-invalid" );
    }

    if( $("#tipo").val()==""  ){
        error=1;
        $("#tipo").addClass( "is-invalid" );
    }else{
        $("#tipo").removeClass( "is-invalid" );
    }

    if( $("#precio").val()==""  ){
        error=1;
        $("#precio").addClass( "is-invalid" );
    }else{
        $("#precio").removeClass( "is-invalid" );
    }


    if( $("#capacidad").val()==""  ){
        error=1;
        $("#capacidad").addClass( "is-invalid" );
    }else{
        $("#capacidad").removeClass( "is-invalid" );
    }




    if(error=="1"){
    
         
    }else{
        
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var formData = new FormData(document.getElementById("formulario"));
        url	= "components/curso/models/insert_update.php";



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

           document.location.href="index.php?component=curso&view=curso&token="+token;
              
              
            });


    }//else if(error=="1")

     



 }//function enviar()

 //*************************************************************************************** */
 //*************************************************************************************** */
 


 function eliminarCurso(token){

	BootstrapDialog.confirm('Confirma que la eliminación del Curso. No se puede deshacer.', function(result){
            if(result) {
               
			   
$.ajax({
  type: 'POST',
  url: "components/curso/models/delete_curso.php",
  data:  "token="+token,
  success: function(resp){
		var retorno = resp.split('|');
		var resultado = retorno[1];
		 
		if(resultado=="1"){

                    BootstrapDialog.show({
                    message: "El curso se ha eliminado.",
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
	
                        }else{
                            BootstrapDialog.alert("No se puede eliminar el curso del sistema. Existen participantes relacionado al curso.");
                        }
			
   
                        }
    }); 
			   
  
			   
            }else {
               // alert('no.');
            }
        });
	
}//function eliminarCurso(token)



//******************************************************************************************************* */
//******************************************************************************************************* */


function inscribir(token){
    var error = 0;
    var mensaje = "";



    if( $("#participanteNombre").val()==""  ){
        error=1;
        $("#participanteNombre").addClass( "is-invalid" );
    }else{
        $("#participanteNombre").removeClass( "is-invalid" );
    }

    if( $("#participantePrecio").val()==""  ){
        error=1;
        $("#participantePrecio").addClass( "is-invalid" );
    }else{
        $("#participantePrecio").removeClass( "is-invalid" );
    }



   



    if(error=="1"){
    
         
    }else{

        var nombre      = $("#participanteNombre").val();
        var precio      = $("#participantePrecio").val();
        var comentario  = $("#participanteComentario").val();
        
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var datos = "nombre="+nombre+"&precio="+precio+"&comentario="+comentario+"&token="+token;
        url	= "components/curso/models/inscribir.php";



        $.ajax({
            url: url,
            type: "get",
            dataType: "html",
            data: datos,
            cache: false,
            contentType: false,
            processData: false
        })
            .done(function(res){
            //alert(res);
            var retorno = res.split('|');
            var token = retorno[1];

          document.location.href="index.php?component=curso&view=curso&token="+token;
              
              
            });


    }//else if(error=="1")

     



 }//function inscribir()


 //******************************************************************************************************* */
//******************************************************************************************************* */


function pagar(campo, token){
    var error = 0;
    var mensaje = "";



    if( $("#pago"+campo).val()==""  ){
        error=1;
        $("#pago"+campo).addClass( "is-invalid" );
    }else{
        $("#pago"+campo).removeClass( "is-invalid" );
    }



    if(error=="1"){
    
         
    }else{

        var tipopago      = $("#pago"+campo).val();

        
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var datos = "tipopago="+tipopago+"&token="+token;
        url	= "components/curso/models/pagar.php";



        $.ajax({
            url: url,
            type: "get",
            dataType: "html",
            data: datos,
            cache: false,
            contentType: false,
            processData: false
        })
            .done(function(res){
            //alert(res);
            var retorno = res.split('|');
            var token = retorno[1];

          document.location.reload();
              
              
            });


    }//else if(error=="1")

     



 }//function pagar()



 //******************************************************************************************************* */
//******************************************************************************************************* */


function deshacerPago(token){
   

    BootstrapDialog.confirm('Confirma que desea deshacer el pago.', function(result){
        if(result) {
           
           
$.ajax({
type: 'POST',
url: "components/curso/models/deshacer_pago.php",
data:  "token="+token,
success: function(resp){
    var retorno = resp.split('|');
    var resultado = retorno[1];
     
    if(resultado=="1"){

                BootstrapDialog.show({
                message: "El pago se ha eliminado.",
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

                    }else{
                       BootstrapDialog.alert("No se puede eliminar el pago del sistema.");
                    }
        

                    }
}); 
           

           
        }else {
           // alert('no.');
        }
    });




 }//function deshaverPago()



 //******************************************************************************************************* */
//******************************************************************************************************* */


function eliminarParticipante(token){
   

    BootstrapDialog.confirm('Confirma que desea eliminar al participante.', function(result){
        if(result) {
           
           
$.ajax({
type: 'POST',
url: "components/curso/models/delete_participante.php",
data:  "token="+token,
success: function(resp){
    var retorno = resp.split('|');
    var resultado = retorno[1];
     
    if(resultado=="1"){

                BootstrapDialog.show({
                message: "El participante se ha eliminado.",
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

                    }else{
                       BootstrapDialog.alert("No se puede eliminar el participante del sistema.");
                    }
        

                    }
}); 
           

           
        }else {
           // alert('no.');
        }
    });




 }//function eliminarParticipante()
    