<script> 

</script>    
<?php echo @$mensaje;?>

        <div class="card">
                    <div class="card-header">
                    <i class="fas fa-dollar-sign"></i> Deudas <a href='index.php?component=deudas&amp;view=deuda'><button type='button' class='btn btn-primary'><i class='fas fa-plus' aria-hidden='true'></i> Agregar Deuda</button></a>
                       
                        <a href="javascript:document.location.reload();"><span class="badge badge-primary float-right" style='padding:6px;margin-bottom:6px;'><i class="fas fa-sync"></i> Recargar datos</span></a> 
            
                    </div>  
                <div class="card-body">
    
                
                <table id="tabla" class="  table table-striped table-hover dt-responsive display   "  style="width:100%;"  >
                        <thead>
                            <tr>
                                <th>ID Deuda</th>
                                <th>Fecha</th>
                                <th>Deudor</th>
                                <th>Glosa</th>
                                <th>Observación</th>
                                <th>Doc. Respaldo</th>
                                <th>Estado</th>
                                <th>Monto</th>
                                <th>Editar</th>
                                <th>Condonar</th>
                                <th>Pagar</th>
                                <th>Eliminar</th>
                                <th>Desactivar</th>

                            </tr>
                        </thead>
                    
                    </table>

                </div>
        </div>
