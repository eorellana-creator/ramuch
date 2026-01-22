 

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
        "columnDefs": [ { orderable: false, targets: [0,1,2,3,4,5,6,7] } ],
        "ajax": {
            "url": "components/maestra/models/maestra_list_procesa.php",
            "type": "POST"
        }
 

    } );


  



} );

 



 