function enviar(){
	
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
	
var datos = $("#formulario3").serialize();

  $.ajax({
  type: 'POST',
  url: "components/rol/models/insert_update.php",
  data:  datos,
  success: function(resp){
		var retorno = resp.split(',xxx');
		var resultado = retorno[1];
		//alert(retorno);
		//alert("Registro insertado");

		    BootstrapDialog.show({
            message: "El Rol se ha guardado exitosamente.",
			type: BootstrapDialog.TYPE_PRIMARY,
			title: "Atención",
			buttons: [{
                label: 'Aceptar',
				cssClass: 'btn-primary',
             action: function(dialogItself){
                    dialogItself.close();
					document.location.href="index.php?component=rol&view=rol&token="+resultado;
                }
           
            }]
        });
		
		
	
   }
   });  
} 

//Desde acá código para Datatable listado de roles
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
        "columnDefs": [ { orderable: false, targets: [1,2] } ],
        "ajax": {
            "url": "components/rol/models/rol_list_procesa.php",
            "type": "POST"
        }
 


   
    } );
    
   

} );


 

//************************************************************************
function deleteRol(token){
	BootstrapDialog.confirm('Realmente desea eliminar al rol? No se puede deshacer.', function(result){
            if(result) {
               
  $.ajax({
  type: 'POST',
  url: "components/rol/models/delete.php",
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