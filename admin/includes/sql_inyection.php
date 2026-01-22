<?php 
// Modificamos las variables pasadas por URL 
foreach( $_GET as $variable => $valor ){ 
$_GET [ $variable ] = str_replace ( "'" , " " , $_GET [ $variable ]); 
} 
// Modificamos las variables de formularios 
foreach( $_POST as $variable => $valor ){ 
$_POST [ $variable ] = str_replace ( "'" , " " , $_POST [ $variable ]); 
} 

// Modificamos las variables pasadas por URL 
foreach( $_GET as $variable => $valor ){ 
$_GET [ $variable ] = str_replace ( '"' , " " , $_GET [ $variable ]); 
} 
// Modificamos las variables de formularios 
foreach( $_POST as $variable => $valor ){ 
$_POST [ $variable ] = str_replace ( '"' , " " , $_POST [ $variable ]); 
} 

 


// Modificamos las variables pasadas por URL 
foreach( $_GET as $variable => $valor ){ 
$_GET [ $variable ] = str_replace ( "\\" , " " , $_GET [ $variable ]); 
} 
// Modificamos las variables de formularios 
foreach( $_POST as $variable => $valor ){ 
$_POST [ $variable ] = str_replace ( "\\" , " " , $_POST [ $variable ]); 
} 

// Modificamos las variables pasadas por URL 
foreach( $_GET as $variable => $valor ){ 
$_GET [ $variable ] = str_replace ( "<" , " " , $_GET [ $variable ]); 
} 
// Modificamos las variables de formularios 
foreach( $_POST as $variable => $valor ){ 
$_POST [ $variable ] = str_replace ( "<" , " " , $_POST [ $variable ]); 
} 

// Modificamos las variables pasadas por URL 
foreach( $_GET as $variable => $valor ){ 
$_GET [ $variable ] = str_replace ( ">" , " " , $_GET [ $variable ]); 
} 
// Modificamos las variables de formularios 
foreach( $_POST as $variable => $valor ){ 
$_POST [ $variable ] = str_replace ( ">" , " " , $_POST [ $variable ]); 
} 


// Modificamos las variables pasadas por URL 
foreach( $_GET as $variable => $valor ){ 
$_GET [ $variable ] = str_replace ( "\n" , " " , $_GET [ $variable ]); 
} 
// Modificamos las variables de formularios 
foreach( $_POST as $variable => $valor ){ 
$_POST [ $variable ] = str_replace ( "\n" , " " , $_POST [ $variable ]); 
} 

// Modificamos las variables pasadas por URL 
foreach( $_GET as $variable => $valor ){ 
$_GET [ $variable ] = str_replace ( "\r" , " " , $_GET [ $variable ]); 
} 
// Modificamos las variables de formularios 
foreach( $_POST as $variable => $valor ){ 
$_POST [ $variable ] = str_replace ( "\r" , " " , $_POST [ $variable ]); 
} 

// Modificamos las variables pasadas por URL 
foreach( $_GET as $variable => $valor ){ 
$_GET [ $variable ] = str_replace ( "NULL" , " " , $_GET [ $variable ]); 
} 
// Modificamos las variables de formularios 
foreach( $_POST as $variable => $valor ){ 
$_POST [ $variable ] = str_replace ( "NULL" , " " , $_POST [ $variable ]); 
} 


?>