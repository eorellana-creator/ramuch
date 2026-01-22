

$(document).ready(function() {
	
  
  var region = $('#hiddenregion').val();
  var ciudad = $("#hiddenciudad").val();
  var comuna = $("#hiddencomuna").val();
  
//alert( "accion="+"0"+"&valor="+""+"&valorregion="+region+"&valorciudad="+ciudad+"&valorcomuna="+comuna);
	
  $.ajax({
  type: 'POST',
  url: "includes/region_ciudad_comuna.php",
  data:  "accion="+"0"+"&valor="+""+"&valorregion="+region+"&valorciudad="+ciudad+"&valorcomuna="+comuna,
  success: function(resp){
		var retorno = resp.split('xxx,');
		var resultado1 = retorno[1];
		var resultado2 = retorno[2];
		var resultado3 = retorno[3];
		
		
		
	
		
try {
   $("#divregion").html(resultado1); 
   $("#divciudad").html(resultado2); 
   $("#divcomuna").html(resultado3); 
  
}
catch(err) {
     
}
		
	
   }
   }); 


 } );
 
 //********************************************************************************
 
function seteaProvinciaComuna(accion,valor){

$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);		
	
$.ajax({
type: 'POST',
url: "includes/region_ciudad_comuna.php",
data:  "accion="+accion+"&valor="+valor,
  success: function(resp){
		var retorno = resp.split('xxx,');
		var resultado = retorno[1];
		//alert(retorno);
		
try {
	if(accion=="1" && valor!=""){
   $("#divciudad").html(resp);
    $("#divcomuna").html(" <select id='comuna' name='comuna' style='color:#6d6c6c' required data-validation-required disabled  class='form-control'  ><option value=''>Antes seleccione la Provincia/Ciudad</option></select>");
	}
   
   if(accion=="2" && valor!="")
   $("#divcomuna").html(resp); 
   
   
   if ( accion=="1"  && valor==""){
   $("#divciudad").html(" <select id='ciudad' name='ciudad' style='color:#6d6c6c' required data-validation-required disabled  class='form-control'  ><option value=''>Antes seleccione la Región</option></select>");
    $("#divcomuna").html(" <select id='comuna' name='comuna' style='color:#6d6c6c' required data-validation-required disabled  class='form-control'  ><option value=''>Antes seleccione la Región</option></select>");
	}
   
   
    if ( accion=="2"  && valor==""){
    $("#divcomuna").html(" <select id='comuna' name='comuna' style='color:#6d6c6c' required data-validation-required disabled  class='form-control'  ><option value=''>Antes seleccione la Provincia/Ciudad</option></select>");
	}  
   
   
}
catch(err) {
     
}
		
	
   }
   }); 
	

}//function seteaProvinciaComuna




function resetaRegionCiudadComuna(){
	
	
	  var region = $('#hiddenregion').val();
  var ciudad = $("#hiddenciudad").val();
  var comuna = $("#hiddencomuna").val();
  
//alert( "accion="+"0"+"&valor="+""+"&valorregion="+region+"&valorciudad="+ciudad+"&valorcomuna="+comuna);
	
  $.ajax({
  type: 'POST',
  url: "includes/region_ciudad_comuna.php",
  data:  "accion="+"0"+"&valor="+""+"&valorregion="+region+"&valorciudad="+ciudad+"&valorcomuna="+comuna,
  success: function(resp){
		var retorno = resp.split('xxx,');
		var resultado1 = retorno[1];
		var resultado2 = retorno[2];
		var resultado3 = retorno[3];
		
		
		
	
		
try {
   $("#divregion").html(resultado1); 
   $("#divciudad").html(resultado2); 
   $("#divcomuna").html(resultado3); 
  
}
catch(err) {
     
}
		
	
   }
   }); 
	
	
	
	
}