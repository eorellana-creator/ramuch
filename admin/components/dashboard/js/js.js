function enviar(){
	

	
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
	
var datos = $("#formulario5").serialize();

  $.ajax({
  type: 'POST',
  url: "components/especialidad/models/insert_update.php",
  data:  datos,
  success: function(resp){
		var retorno = resp.split('xxx,');
		var resultado = retorno[1];
		//alert(retorno);
		//alert("Registro insertado");
		if(resp=="insert")
		BootstrapDialog.alert('La Especialidad ha sido creada.');
		if(resp=="update")
		BootstrapDialog.alert('La Especialidad ha sido actualizada.');
	    setTimeout(function(){ document.location.href="index.php?component=especialidad&view=especialidad_list"; }, 2500);
	
   }
   });  
   
   
}  
