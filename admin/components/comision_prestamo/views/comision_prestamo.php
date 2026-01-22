               
<div class="card">
                    <div class="card-header">
                        <i class="fa fa-user"></i> Comisión Préstamo <?php echo @$result->nombre;?>
                    </div>  

                <div class="card-body">
			   
                            <form name="formulario3" id="formulario3" method="post" action="javascript: enviar();">

                                <div class="row">

                                            <div class="col-md-3">

                                                <div class="form-group">
                                                <label >Agregar Usuario:</label>
                                                <?php echo   $option_usuarios;?> 
                                                </div>
                                                
                                            </div>

                                            <div class="col-md-3">
                                            <br>
                                            <button onClick="agregarIntegrante();" style="margin-top:5px;" type="button" class="btn btn-primary">Agregar a la comisión</button>
                                            </div>

                                                                            
                                           
                                    
                                </div>


                                <div class="row">

                                            <div class="col-lg-12 text-left">
                                            <strong>Integrantes de la comisión:</strong><br><br>
                                                
                                                <?php echo @$comision;?>
                                
                                    
                                            </div>


                                </div>
                                
                            </form>


                </div>
</div>