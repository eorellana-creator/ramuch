function largoPass(e){
	if(e.value.length<6){
		$("#password").addClass('input-error');
	}else{
		$("#password").removeClass('input-error');
	}

}//function largoPass()



//***************************************************************************
$(function () {
    $('#formlogin').submit(function (e) {
		
	e.preventDefault(); //para que no se envíe el formulario
	
	 var url 		= "login/models/login.php";
	 
		
		 
		$.ajax({
			   type: "POST",
			   url: url,
			   data: $("#formlogin").serialize(), 
			   success: function(resp)
			   {
				 
		   // alert(resp);
			var retorno = resp.split('|');
			var resultado = retorno[1];
			
					
			if(resultado=="0"){
			alert('Datos de acceso incorrectos.');
			$("#password").val("");
			}
			
			if(resultado=="x"){
			alert('Formulario Inválido');
			
			}
			
			if(resultado=="ok")
			document.location.href="index.php?component=dashboard&view=dashboard";
				 //****************************************
			   }
			 });
			  

    });
});