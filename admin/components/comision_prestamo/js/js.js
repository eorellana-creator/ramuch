function agregarIntegrante(){
	
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
	
var datos = "&usuario=" + document.getElementById("agregar").value;

  $.ajax({
  type: 'POST',
  url: "components/comision_prestamo/models/insert.php",
  data:  datos,
  success: function(resp){
		var retorno = resp.split('|');
		var resultado = retorno[1];
 

		    BootstrapDialog.show({
            message: "El integrante se ha agregado.",
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
		
		
	
   }
   });  
} 

//Desde acá código para Datatable listado de roles
//*****************************************************************************************

$(document).ready(function() {


    $('.sel2-basic-single').select2();
    
   

} );


 

//************************************************************************
function sacar(token){
	BootstrapDialog.confirm('Realmente desea eliminar al integrante de la comisión? No se puede deshacer.', function(result){
            if(result) {
               
  $.ajax({
  type: 'POST',
  url: "components/comision_prestamo/models/sacar.php",
  data:  "token="+token,
  success: function(resp){
		var retorno = resp.split('|');
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