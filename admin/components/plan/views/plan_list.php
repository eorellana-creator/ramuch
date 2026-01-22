<script> </script>    

        <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-invoice"></i> Listado de Planes | <a href="index.php?component=plan&amp;view=plan"><i class="fas fa-plus" aria-hidden="true"></i> Agregar nuevo Plan</a>    
                       
                        <a href="javascript:document.location.reload();"><span class="badge badge-primary float-right" style='padding:6px;margin-bottom:6px;'><i class="fas fa-sync"></i> Recargar datos</span></a> 
                        
                        
                       
                    </div>  
                <div class="card-body">
    
                
                <table id="tabla" class=" responsive display nowrap" style="width:100%"  >
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo de Matrícula</th>
                                <th>Día de Pago</th>
                                <th>Valor Matrícula</th>
                                <th>Pública/Privada</th>
                                <th>Planes de Pago</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                    
                    </table>

                </div>
        </div>

<script>
function deletePlan(token) {
    if (confirm("¿Estás seguro de que deseas eliminar este plan? Esta acción no se puede deshacer.")) {
        // Realizar una solicitud AJAX para eliminar el plan
        fetch('components/plan/models/delete_plan.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'token=' + encodeURIComponent(token),
        })
        .then(response => response.text())
        .then(data => {
            // Recargar la página para reflejar los cambios
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

// Función para eliminar un plan de pago específico
function deletePlanPago(token) {
    if (confirm("¿Estás seguro de que deseas eliminar este plan de pago? Esta acción no se puede deshacer.")) {
        // Realizar una solicitud AJAX para eliminar el plan de pago
        fetch('components/plan/models/delete_plan_pago.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'token=' + encodeURIComponent(token),
        })
        .then(response => response.text())
        .then(data => {
            // Recargar la página para reflejar los cambios
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>