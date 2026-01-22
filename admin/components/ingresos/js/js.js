 

$(document).ready(function() {

var subcuenta = $("#subcuenta").val();

    $('#tabla').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[ 0, "desc" ]],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "pageLength": 25,
        "columnDefs": [ { orderable: false, targets: [2] } ],
        "ajax": {
            "url": "components/ingresos/models/ingresos_list_procesa.php?subcuenta="+subcuenta,
            "type": "POST"
        }
 

    } );


} );






//********************************************************************************** */

function enviar(){
    var error = 0;
    var mensaje = "";



    if( $("#glosa").val()==""  ){
        error=1;
        $("#glosa").addClass( "is-invalid" );
    }else{
        $("#glosa").removeClass( "is-invalid" );
    }


    if( $("#fecha").val()==""  ){
        error=1;
        $("#fecha").addClass( "is-invalid" );
    }else{
        $("#fecha").removeClass( "is-invalid" );
    }

    if( $("#medio").val()==""  ){
        error=1;
        $("#medio").addClass( "is-invalid" );
    }else{
        $("#medio").removeClass( "is-invalid" );
    }

    if( $("#monto").val()==""  ){
        error=1;
        $("#monto").addClass( "is-invalid" );
    }else{
        $("#monto").removeClass( "is-invalid" );
    }




    if(error=="1"){
    
         
    }else{
        
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var formData = new FormData(document.getElementById("formulario"));
        url	= "components/ingresos/models/insert_update.php";



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

           document.location.href="index.php?component=ingresos&view=ingreso&token="+token;
              
              
            });


    }//else if(error=="1")

     



 }//function enviar()

 //*************************************************************************************** */
 //*************************************************************************************** */
 
 
 function deleteItem(token){

	BootstrapDialog.confirm('Confirma la eliminación del Egreso. No se puede deshacer.', function(result){
            if(result) {
               
			   
$.ajax({
  type: 'POST',
  url: "components/ingresos/models/delete_ingreso.php",
  data:  "token="+token,
  success: function(resp){
		var retorno = resp.split('|');
		var resultado = retorno[1];
		 
		if(resultado=="1"){

                    BootstrapDialog.show({
                    message: "El ingreso se ha eliminado.",
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
                            BootstrapDialog.alert("No se puede eliminar el ingreso del sistema. Existen datos relacionado al item.");
                        }
			
   
                        }
    }); 
			   
  
			   
            }else {
               // alert('no.');
            }
        });
	
}//function deleteItem(token)


function validaDocumento(e){
    var fileExtension = ['png','jpeg','jpg','gif','docx','doc','pdf','xls','xlsx'];
    if ($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        BootstrapDialog.alert('El Archivo debe ser un PDF, imagen o word.');
        $(e).val("");
        return false;
    }else{
        return true;
        }
    
}



//************************************************************************************** */
//************************************************************************************** */

function borrarDocumento(token){

	BootstrapDialog.confirm('Confirma la eliminación del Documento. No se puede deshacer.', function(result){
            if(result) {
               
			   
$.ajax({
  type: 'POST',
  url: "components/ingresos/models/delete_documento.php",
  data:  "token="+token,
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
	
                        }else{
                            BootstrapDialog.alert("No se puede eliminar el documento del sistema. Existen datos relacionado al documento.");
                        }
			
   
                        }
    }); 
			   
  
			   
            }else {
               // alert('no.');
            }
        });
	
}//function borrarArchivo(token)