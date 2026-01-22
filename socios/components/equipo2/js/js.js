 

$(document).ready(function() {


    $('#tabla').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "responsive": false,
        "pageLength": 25,
        "initComplete": function(settings, json) {
            $('.sel2-basic-single').select2();
          },
        "columnDefs": [ { orderable: false, targets: [1,2,3,4] } ],
        "ajax": {
            "url": "components/equipo/models/equipo_list_procesa.php",
            "type": "POST"
        }
         
 

    } );

    var table = $('#tabla').DataTable();

table.on( 'draw', function () {
    $('.sel2-basic-single').select2();


    var dateToday = new Date();
    var month = dateToday.getMonth() + 1;
    var day = dateToday.getDate();
    var year = dateToday.getFullYear();
  
    if (month < 10)
      month = '0' + month.toString();
    if (day < 10)
      day = '0' + day.toString();
  
    var maxDate = year + '-' + month + '-' + day;
  
    $('.campofecha').attr('min', maxDate);



});


  
    $('[data-toggle="tooltip"]').tooltip(); 

    $("i.fa").popover({'trigger':'hover'});

    


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
 

 





function pedirEquipo(f1, f2, token){

    var fecha1   = document.getElementById(f1).value;
    var fecha2   = document.getElementById(f2).value;
 
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	

    if(fecha1!="" && fecha2!="" ){

    var datos = "&fecha1="+fecha1+"&fecha2="+fecha2+"&token="+token;




        $.ajax({
            url: "components/equipo/models/solicitar_equipo.php",
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
            window.scrollTo(0, 0);

         document.location.reload();
              
              
            });







    }else{
        BootstrapDialog.alert("Se deben seleccionar las fechas desde y hasta, para solicitar el préstamo.");
    }//if(fecha!="" && user!="" )



}//function prestar()

function mensaje(m1, m2){

    var mensaje1   = document.getElementById(m1).value;
    var mensaje2   = document.getElementById(m2).value;
    BootstrapDialog.alert($mensaje1);
    
}//function prestar()
 