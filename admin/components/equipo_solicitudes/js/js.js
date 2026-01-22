 

$(document).ready(function() {


    $('#tabla').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "ordering": true,
        "processing": true,
        "serverSide": true,
        "responsive": false,
        "order": [[0, 'desc']],
        "pageLength": 25,
        "initComplete": function(settings, json) {
            $('.sel2-basic-single').select2();
          },
        "columnDefs": [ { orderable: false, targets: [1,2] } ],
        "ajax": {
            "url": "components/equipo_solicitudes/models/equipo_solicitudes_list_procesa.php",
            "type": "POST"
        }
         
 

    } );

    var table = $('#tabla').DataTable();

 


   

    


} );





//********************************************************************************** */

function enviar(){
    var error = 0;
    var mensaje = "";

    if( $("#nombre").val()==""  ){
        error=1;
        $("#nombre").addClass( "is-invalid" );
    }else{
        $("#nombre").removeClass( "is-invalid" );
    }





    if(error=="1"){
    
         
    }else{
        
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
        var formData = new FormData(document.getElementById("formulario"));
        url	= "components/equipo/models/insert_update.php";



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

            document.location.href="index.php?component=equipo&view=equipo&token="+token;
              
              
            });


    }//else if(error=="1")

     



 }//function enviar()

 //*************************************************************************************** */
 //*************************************************************************************** */
 


 function eliminarEquipo(token){

	BootstrapDialog.confirm('Confirma que la eliminación del Equipo. No se puede deshacer.', function(result){
            if(result) {
               
			   
$.ajax({
  type: 'POST',
  url: "components/equipo/models/borrar_equipo.php",
  data:  "token="+token,
  success: function(resp){
		var retorno = resp.split('|');
		var resultado = retorno[1];
		 
		if(resultado=="1"){

                    BootstrapDialog.show({
                    message: "El equipo se ha eliminado.",
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
                            BootstrapDialog.alert("No se puede eliminar el equipo del sistema. Existen datos (préstamos, historial) relacionado al equipo.");
                        }
			
   
                        }
    }); 
			   
  
			   
            }else {
               // alert('no.');
            }
        });
	
}//function borrarArchivo(token)



 
   
function fechaPrestamo(e, token, capa, fecha){

    if(e.value!=""){
       // $(".capasN").css("display","none");
        $("#"+capa).css("display","block");
    }else{
        $("#"+capa).css("display","none");
    }

}//function fechaPrestamo(token, capa, fecha)



function prestar(f, token, u){

    var fecha   = document.getElementById(f).value;
    var user    = document.getElementById(u).value;


    if(fecha!="" && user!="" ){

    var datos = "&fecha="+fecha+"&user="+user+"&token="+token;




        $.ajax({
            url: "components/equipo/models/prestar_equipo.php",
            type: "get",
            dataType: "html",
            data: datos,
            cache: false,
            contentType: false,
            processData: false
        })
            .done(function(res){
            //alert(res);
            var retorno = res.split('|');
            var token = retorno[1];

           document.location.reload();
              
              
            });







    }else{
        BootstrapDialog.alert("Se deben seleccionar todos los datos para realizar el préstamo.");
    }//if(fecha!="" && user!="" )



}//function prestar()



function seteaTokenPrestamo(tokenPrestamo){

    $("#tokenPrestamo").val(tokenPrestamo);
    document.getElementById('observacion').value="";

}//function seteaTokenEquipo(seteaTokenPrestamo)


function rechaza(token){

    var observacion = document.getElementById('observacion').value;

    if(observacion!=""){
        aceptaRechaza(0,document.getElementById('tokenPrestamo').value);
    }else{
        BootstrapDialog.alert("Debes ingresar el motivo del rechazo.");
    }

}//function rechaza



function aceptaRechaza(tipo, token){

var mensaje = "";
var observacion = document.getElementById('observacion').value;

    if( tipo=="0" )
    mensaje = "¿Confirmas el rechazo?"; 

    if( tipo=="1" )
    mensaje = "¿Confirmas el préstamo del equipo?"; 

        BootstrapDialog.confirm(mensaje, function(result){
                if(result) {
                    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
                    $('#primaryModal').modal('hide');
                                var datos = "&token="+token+"&observacion="+observacion+"&tipo="+tipo;
                                $.ajax({
                                    url: "components/equipo_solicitudes/models/acepta_rechaza_prestamo.php",
                                    type: "get",
                                    dataType: "html",
                                    data: datos,
                                    cache: false,
                                    contentType: false,
                                    processData: false
                                })
                                    .done(function(res){
                                    //alert(res);
                                    var retorno = res.split('|');
                                    var token = retorno[1];

                                });
                                document.location.reload();

                }else {
                    // alert('no.');
                    }
                });
                

}//function aceptaRechaza()

function prueba(tipo, token){
}



 

 
           









