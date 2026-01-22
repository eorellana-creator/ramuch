function enviar(){
	
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
	
var datos = $("#formulario").serialize();	

var formData = new FormData(document.getElementById("formulario"));

	window.scrollTo(0, 0);
   $.ajax({
                url: "components/contacto/models/envia.php",
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
		        var subida = retorno[1];
			
document.location.reload();
				  
                });
				
	  
 
	
}//function enviar()

