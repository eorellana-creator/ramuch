 

$(document).ready(function() {


    $('#tabla').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "order": [[ 0, "asc" ]],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "pageLength": 25,
        "columnDefs": [ { orderable: false, targets: [1,2,3,4,5,6,7] } ],
        "ajax": {
            "url": "components/plan/models/plan_list_procesa.php",
            "type": "POST"
        }
 

    } );


    $('[data-toggle="tooltip"]').tooltip(); 



} );

 


 

 


     function enviar(){
   
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	

            var formData = new FormData(document.getElementById("formulario"));

            url	= "components/plan/models/insert_update.php";

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
	
                document.location.href="index.php?component=plan&view=plan&token="+token;
				  
				  
                });




     }//function enviar()

     //*************************************************************************************** */
     //*************************************************************************************** */


     function enviarPlanPago(){
   
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	

        var formData = new FormData(document.getElementById("formulario"));

        var tokenMatricula = $("#tokenMatricula").val();

        url	= "components/plan/models/insert_update_plan_pago.php";

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

            document.location.href="index.php?component=plan&view=plan_pago&token="+tokenMatricula+"&tokenPlanPago="+token;
              
              
            });




 }//function enviar()

 //*************************************************************************************** */
 //*************************************************************************************** */





        if( $("#htipo").val()!="" ){
        $('#tipo option[value="'+$("#htipo").val()+'"]').prop("selected", true);
        }

        if( $("#hdia").val()!="" ){
        $('#dia option[value="'+$("#hdia").val()+'"]').prop("selected", true);
        }

        if( $("#hpp").val()!="" ){
        $('#pp option[value="'+$("#hpp").val()+'"]').prop("selected", true);
        }


        if( $("#hperiodop").val()!="" ){
            $('#periodo option[value="'+$("#hperiodop").val()+'"]').prop("selected", true);
            }
    
            if( $("#hdiap").val()!="" ){
            $('#diap option[value="'+$("#hdiap").val()+'"]').prop("selected", true);
            }
    
            if( $("#hppp").val()!="" ){
            $('#ppp option[value="'+$("#hppp").val()+'"]').prop("selected", true);
            }