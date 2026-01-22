function enviar(){
	
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
	
var formData = new FormData(document.getElementById("formulario5"));

  if(validaImagen( $("#imagen") )==true || $("#imagen").val()=="" )  {
   
   $.ajax({
                url: "components/company/models/insert_update.php",
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
				
				//BootstrapDialog.alert('La Empresa se ha actualizado.');
   			    //setTimeout(function(){ document.location.href="index.php?component=company&view=company"; }, 2500);
				BootstrapDialog.show({
            message: "Los datos del Local se han actualizado.",
			type: BootstrapDialog.TYPE_PRIMARY,
			title: "Atención",
			buttons: [{
                label: 'Aceptar',
				cssClass: 'btn-primary',
             action: function(dialogItself){
                    dialogItself.close();
					document.location.href="index.php?component=company&view=company";
                }
           
            }]
        });
		

				  
				  
                });
      
  }//if(validaImagen( $("#imagen") )==true)  {

}//function enviar  


function validaImagen(e){
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
        if ($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
			$("#errorarchivo").html("El Archivo debe ser una imagen.");
			return false;
        }else{
			$("#errorarchivo").html("");
			return true;
			}
		
}




$( document ).ready(function() {

$("#rut")
  .rut({formatOn: 'blur', validateOn: 'blur'})
  .on('rutInvalido', function(){ 
    $(this).parents(".control-group").addClass("errorClass");
	$(this).css("border-color","red");
	$("#errorrut").html("Rut inválido. Debe ingresar un Rut válido.");
	$( "#rut" ).addClass( "rutnovalido" );
	$(this).val("");
	
  })
  .on('rutValido', function(){ 
    $(this).parents(".control-group").removeClass("errorClass")
	$(this).css("border-color","#ccc");
	$("#errorrut").html("");
	$( "#rut" ).removeClass( "rutnovalido" );
	
  });

 
	

});  //$( document ).ready(function() {