<?php
$nombre_user	= $_SESSION["usuario_nombre"];
$url_logout     = "login/logout.php";	
$url_changepass = "index.php?component=changepassword&view=dashboard";

?>


<html lang="es">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Sistema desarrollado por Emasmas">
    <meta name="author" content="Emasmas">
    <meta name="keyword" content="">
    <title><?php echo $config->sitename;?></title>

    <link href="favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
      <link rel="stylesheet" type="text/css" href="js/datatable/datatables.css">
    <link href="template/node_modules/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
    <link href="template/node_modules/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
    <link href="template/node_modules/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="template/node_modules/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="js/validate-password/css/jquery.passwordRequirements.css" />


    <link href="template/font-awesome/css/all.css" rel="stylesheet"> 
  
    <link href="template/css/style.css" rel="stylesheet">
    <link href="template/vendors/pace-progress/css/pace.min.css" rel="stylesheet">


    <link href="js/bootstrap4-dialog/scss/bootstrap-dialog.scss" rel="stylesheet">

    <link href="js/select2/css/select2.css" rel="stylesheet" />

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
 

  <script src="js/validadores.js"></script>
	<script src="js/funciones.js"></script>
 




  <?php echo $incluir_css;?>

 
     
 
  </head>
  <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
    <header class="app-header navbar">
      <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="index.php?component=dashboard&view=dashboard">
        <img class="navbar-brand-full" src="images/logo-admin-int.png" onClick="document.location.href='index.php?component=dashboard&view=dashboard';" width="150" height="30" alt="<?php echo $config->sitename;?>">
        <img class="navbar-brand-minimized" src="images/icono-30.png"  onClick="document.location.href='index.php?component=dashboard&view=dashboard';" width="30" height="30" alt="<?php echo $config->sitename;?>">
      </a>
      <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
      </button>
  
      <ul class="nav navbar-nav ml-auto">


      <!--
        <li class="nav-item d-md-down-none">
          <a class="nav-link" href="#">
            <i class="icon-bell"></i>
            <span class="badge badge-pill badge-danger">5</span>
          </a>
        </li>

-->
     
     
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <img class="img-avatar" src="<?php echo @$_SESSION["img_perfil"];?>" alt="">
          </a>
          <div class="dropdown-menu dropdown-menu-right">
      
            <div class="dropdown-header text-center">
              <strong>Perfil</strong>
            </div>
            <a class="dropdown-item" href="index.php?component=socios&view=socios&token=<?php echo $_SESSION["usuario_token"];?>">
              <i class="fa fa-user"></i>Mi Perfil
            </a>

            <!--
            <a class="dropdown-item" href="index.php?component=socios&view=socios&token=<?php echo $_SESSION["usuario_token"];?>&tab=pass">
              <i class="fa fa-key"></i>Cambiar Contraseña
            </a>
-->
             

            <?php 
					  include("components/menu_config/menu_config.php"); 
					  ?>

             
            
            <div class="dropdown-divider"></div>
             
            <a class="dropdown-item" href="login/logout.php">
              <i class="fa fa-lock"></i> Cerrar Sesión</a>
          </div>
        </li>
      </ul>


   
    </header>
    <div class="app-body">
      <div class="sidebar">
        <nav class="sidebar-nav">
          <ul class="nav">
             
            <li class="nav-title">Menú</li>


        <?php 
				 include("components/menu/menu_lateral.php"); 
        ?>
 
           
          </ul>
        </nav>
        <button class="sidebar-minimizer brand-minimizer" type="button"></button>
      </div>



      <main class="main">
         
 
 


        <div class="container-fluid">
          <div class="animated fadeIn">




<div class="row" >
 
  <div class="col-lg-12 contenido">

          <?php 
              include("controller/controller.php");
          ?>
 
  </div>
</div>


          </div>
        </div>
      </main>
   
    </div>
    <footer class="app-footer">
      <div>
        <span><?php echo $config->sitename;?></span>
      </div>
      <div class="ml-auto">
        <span>Desarrollado por </span>
        <a href="https://emasmas.cl">Emasmas</a>
      </div>
    </footer>
    <!-- CoreUI and necessary plugins-->
    <script src="template/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="template/node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="template/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="template/node_modules/pace-progress/pace.min.js"></script>
    <script src="template/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
    <script src="template/node_modules/@coreui/coreui/dist/js/coreui.min.js"></script>
    <script type="text/javascript" charset="utf8" src="js/datatable/datatables.js"></script>

    <script src="js/bootstrap4-dialog/js/bootstrap-dialog.js"></script>

    <script src="js/select2/js/select2.min.js"></script>

    <?php echo $incluir_js;?>


    <!-- Plugins and scripts required by this view-->
    
    <script src="template/node_modules/@coreui/coreui-plugin-chartjs-custom-tooltips/dist/js/custom-tooltips.min.js"></script>
 
    <script language="JavaScript" src="js/jquery.blockUI.js"></script>
    <script src="js/rut/jquery.rut.js"></script>
    <script src="js/validate-password/js/jquery.passwordRequirements.js"></script>
 
    <?php 
    echo @$go_tab_pass;
    echo $_SESSION["script_final"];
    $_SESSION["script_final"] = "";
    ?>


  </body>
</html>
