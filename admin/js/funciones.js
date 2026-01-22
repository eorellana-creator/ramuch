  function toggleFlecha(capa){
	 $(".capaBloque"+capa).toggle(500);
	 
	if($(".chevron-flecha"+capa).is('.fa-chevron-down')){
		$(".chevron-flecha"+capa).removeClass( "fa-chevron-down" );
		$(".chevron-flecha"+capa).addClass( "fa-chevron-up" );
	}else{
		$(".chevron-flecha"+capa).removeClass( "fa-chevron-up" );
		$(".chevron-flecha"+capa).addClass( "fa-chevron-down" );
	}
	 
 }// function mostrarPregunta(capa)

