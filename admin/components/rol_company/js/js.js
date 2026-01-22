function enviar(){
	
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	
	
var datos = $("#formulario4").serialize();

  $.ajax({
  type: 'POST',
  url: "components/rol_company/models/insert_update.php",
  data:  datos,
  success: function(resp){
		var retorno = resp.split('xxx,');
		var resultado = retorno[1];
		if(resp=="insert"){
		BootstrapDialog.alert('El rol de empresa ha sido creado.');
		}
		if(resp=="update"){
		BootstrapDialog.alert('El rol de empresa ha sido actualizado.');
		}
	    setTimeout(function(){ document.location.reload(); }, 2500);
	
   }
   });
     
}   

//Desde acá código para Datatable listado de usuarios
//*****************************************************************************************
function loadRolesCompanies(){
	
$(document).ready(function() { 

  $('#tabla').DataTable( {
	   "order": [[ 0, "asc" ]],
        "processing": true,
        "serverSide": true,
		"columnDefs": [ { orderable: false, targets: [0,1] } ],
        "ajax": {
			"url":"components/rol_company/models/rol_company_list_procesa.php",
		"type": "POST"},
		"language": {
            "lengthMenu": "Display _MENU_ registros por página",
            "zeroRecords": "No encontrado",
            "info": "Mostrando página _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total de registros)",
			"loadingRecords": "Cargando...",
            "processing":     "Procesando...",
			"paginate": {
        "first":      "Primero",
        "last":       "Último",
        "next":       "siguiente",
        "previous":   "anterior"
    },
        }
		
    } );
	
$("div.dataTables_filter input").unbind(); // se desactiva la busqueda al presionar una tecla


$("<div id='divbotonbuscar' ><span id='buscar' class='glyphicon glyphicon-search' ></span></div>").insertBefore('.dataTables_filter input');


//Para realizar la búsqueda al hacer click en el botón
$('#buscar').click(function(e){
	    var table = $('#tabla').DataTable();
	    table.search( $("div.dataTables_filter input").val()).draw();
		//mostrar u ocultar botón para resetear las búsquedas y orden
		
		
    });//$('#buscar').click(function(e){

}); //$(document).ready(function() 

} //function loadUsers()

//************************************************************************
function deleteRolCompany(token){
	BootstrapDialog.confirm('Realmente desea Eliminar al rol de empresa? No se puede deshacer.', function(result){
            if(result) {
               
  $.ajax({
  type: 'POST',
  url: "components/rol_company/models/delete.php",
  data:  "token="+token,
  success: function(resp){
		var retorno = resp.split('xxx,');
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