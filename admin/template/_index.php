<?php
$nombre_user	= $_SESSION["usuario_nombre"];
$url_logout     = "login/logout.php";	
$url_changepass = "index.php?component=changepassword&view=dashboard";



?><!-- ok -->


<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <title><?php echo $config->sitename;?></title><!-- ok -->
    
    <link href="favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script><!-- ok -->
    
     <!-- Bootstrap -->
    <link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="js/bootstrap/css/bootstrap-dialog.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="js/datatable/media/css/jquery.dataTables.css">
    
   

    <!-- Custom CSS -->
    <link href="template/css/sb-admin.css" rel="stylesheet">

    <!-- Custom Fonts -->
   
	 <link href="template/font-awesome/css/all.css" rel="stylesheet"> 
    
    
    <link href="template/css/datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet"> 
	<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox.min.css">
    
     <link href="template/css/style.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <script src="js/jquery-1.12.1.min.js"></script>
    <script src="js/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/validadores.js"></script><!-- ok -->
	<script src="js/funciones.js"></script><!-- ok -->
	<script src="js/region_ciudad_comuna.js"></script><!-- ok -->
     
	<script src="js/fancybox/jquery.fancybox.min.js"></script>
    
    <script type="text/javascript" language="javascript" src="js/datatable/media/js/jquery.dataTables.js"></script>
	
	<script src="js/printthis/printThis.js"></script>
	
	
	
	
	
  
 

    
    <script language="JavaScript" src="js/jquery.blockUI.js"></script><!-- ok -->
    
   	<?php echo $incluir_js;?><!-- ok -->
    <?php echo $incluir_css;?><!-- ok -->
    
<script>
	$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
	
	
	
		
	
	});
	
 
 
	
	
	
	
</script>

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#" style="margin-top:-10px; margin-left:-16px;"><img src="images/logo-admin-int.jpg" width="144" height="40" style="margin-left: 20px;" alt=""/><?php //echo $config->sitename;?></a>
            </div>
            <!-- Top Menu Items --><!-- ok -->
    
			
            <ul class="nav navbar-right top-nav">
			
			
			
			 
			
 
	  
	  
	  
	  
			<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fas fa-cogs"></i> Configuraciones <b class="caret"></b></a>
					
					<?php 
					include("components/menu_config/menu_config.php"); 
					?>
                                           
						
					
					
			</li>
				

			
			
			
                
              
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $nombre_user;?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                       
						
                        <li>
                            <a href="<?php echo $url_changepass;?>"><i class="fas fa-key"></i> Cambio Clave</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo $url_logout;?>"><i class="fa fa-fw fa-power-off"></i> Cerrar Sesión</a>
                        </li>
                    </ul>
                </li>
            </ul>       
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <?php 
				 include("components/menu/menu_lateral.php"); 
				
			?>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">

            

     
                    <div class="col-lg-12">
                       
                       
                       
<?php 


include("controller/controller.php");

?>


     
                       
                    </div>
                

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
	
 
	
	
	
<script type="text/javascript" src="js/datetimepicker/moment.js"></script>
<script type="text/javascript" src="js/datetimepicker/es_moment.js"></script>
<script type="text/javascript" src="js/datetimepicker/bootstrap-datetimepicker.min.js"></script>
 

<script src="js/rut/jquery.rut.js"></script>


</body>



</html>
