function enviar(){
	
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
	
var formData = new FormData(document.getElementById("formulario"));

 if( valida_mail(document.getElementById("email")) ){
   
   $.ajax({
                url: "components/user/models/insert_update.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
	            processData: false
            })
                .done(function(res){
				//alert(res);
				var retorno = res.split(',xxx,');
		        var resultado = retorno[1];
				
			BootstrapDialog.show({
            message: "El Usuario se ha guardado.",
			type: BootstrapDialog.TYPE_PRIMARY,
			title: "Atención",
			buttons: [{
                label: 'Aceptar',
				cssClass: 'btn-primary',
             action: function(dialogItself){
                    dialogItself.close();
					document.location.href="index.php?component=user&view=user&token="+resultado;
                }
           
            }]
        });

				  
                });
      
 }else{
	 BootstrapDialog.alert("Debe ingresar un email corecto.");
	 $("#email").focus();
 }// if( valida_mail(e) )

}//function enviar


//Desde acá código para Datatable listado de usuarios
//*****************************************************************************************


$(document).ready(function() {


    $('#tabla').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[ 0, "asc" ]],
        "processing": true,
        "serverSide": true,
        "pageLength": 25,
        "columnDefs": [ { orderable: false, targets: [2, 3,4] } ],
        "ajax": {
            "url": "components/user/models/user_list_procesa.php",
            "type": "POST"
        }
 
   
    } );
    
   

} );




 
//************************************************************************
function deleteUser(token){
     
	BootstrapDialog.confirm('Realmente desea Eliminar al Usuario? No se puede deshacer.', function(result){
            if(result) {
               
  $.ajax({
  type: 'POST',
  url: "components/user/models/delete.php",
  data:  "token="+token,
  success: function(resp){
		var retorno = resp.split('xxx,');
		var resultado = retorno[1];
		//alert(retorno);
	
		 document.location.reload(); 
	
   }
   });  
			   
			   
            }else {
				//nada
            }
        });
  
}

//***********************************************************************
$(document).ready(function() { 

$('#clave').on('blur', function(){
    if(this.value.length < 6){ // checks the password value length
      BootstrapDialog.alert("La contraseña debe tener al menos 6 caracteres. Reingrésela.");
	
	   this.value = "";
       $(this).focus(); // focuses the current field.
	    
       return false; // stops the execution.
    }
	 
});

});


//************************************************************************
function seteaEmail(usuario){
           
  $.ajax({
  type: 'POST',
  url: "components/user/models/setea_email.php",
  data:  "usuario="+usuario,
  success: function(resp){
		var retorno = resp.split('xxx,');
		var resultado = retorno[1];
		//alert(retorno);
  document.getElementById("email").value=resultado;
 
	
   }
   });  
		   
 
}
	
