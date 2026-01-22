<script> 

</script>    

<input id="subcuenta" name="subcuenta" type="hidden" value="<?php echo @$subcuenta;?>">

        <div class="card">
                    <div class="card-header">
                    <i class="fas fa-sign-in-alt"></i> Ingresos <?php echo @$subcuenta . $agregar_ingreso;?> 
                       
                        <a href="javascript:document.location.reload();"><span class="badge badge-primary float-right" style='padding:6px;margin-bottom:6px;'><i class="fas fa-sync"></i> Recargar datos</span></a> 
            
                    </div>  
                <div class="card-body">
    
                
                <table id="tabla" class="  table table-striped table-hover dt-responsive display   "  style="width:100%;"  >
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Glosa</th>
                                <th>Observación</th>
                                <th>Medio</th>
                                <th>Doc. Respaldo</th>
                                <th>Estado</th>
                                <th>Monto</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                    
                    </table>

                </div>
        </div>