<?php

function fecha_mysql_a_normal($fecha){
$fecha_retornada = "";
$fecha_retornada = $fecha_retornada . @$fecha[8];
$fecha_retornada = $fecha_retornada . @$fecha[9];
$fecha_retornada = $fecha_retornada . @$fecha[7];
$fecha_retornada = $fecha_retornada . @$fecha[5];
$fecha_retornada = $fecha_retornada . @$fecha[6];
$fecha_retornada = $fecha_retornada . @$fecha[4];
$fecha_retornada = $fecha_retornada . @$fecha[0];
$fecha_retornada = $fecha_retornada . @$fecha[1];
$fecha_retornada = $fecha_retornada . @$fecha[2];
$fecha_retornada = $fecha_retornada . @$fecha[3];
return($fecha_retornada);
}

function fecha_normal_mysql($fecha){
$fecha_retornada = "";
$fecha_retornada = $fecha_retornada . @$fecha[6];
$fecha_retornada = $fecha_retornada . @$fecha[7];
$fecha_retornada = $fecha_retornada . @$fecha[8];
$fecha_retornada = $fecha_retornada . @$fecha[9];
$fecha_retornada = $fecha_retornada . @$fecha[5];
$fecha_retornada = $fecha_retornada . @$fecha[3];
$fecha_retornada = $fecha_retornada . @$fecha[4];
$fecha_retornada = $fecha_retornada . @$fecha[2];
$fecha_retornada = $fecha_retornada . @$fecha[0];
$fecha_retornada = $fecha_retornada . @$fecha[1];
return($fecha_retornada);
}

function dia_ingles($dia){
	if($dia=="1")
	return("monday");
	if($dia=="2")
	return("tuesday");
	if($dia=="3")
	return("wednesday");
	if($dia=="4")
	return("thursday");
	if($dia=="5")
	return("friday");
	if($dia=="6")
	return("saturday");
	if($dia=="7")
	return("sunday");
}

function compararFechas($primera, $segunda)   
 {   
  $valoresPrimera = explode ("-", $primera);      
  $valoresSegunda = explode ("-", $segunda);    
  $diaPrimera    = $valoresPrimera[0];     
  $mesPrimera  = $valoresPrimera[1];     
  $anyoPrimera   = $valoresPrimera[2];    
  $diaSegunda   = $valoresSegunda[0];     
  $mesSegunda = $valoresSegunda[1];     
  $anyoSegunda  = $valoresSegunda[2];   
  $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);     
  $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);        
  if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){   
    // "La fecha ".$primera." no es válida";   
    return 0;   
  }elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){   
    // "La fecha ".$segunda." no es válida";   
    return 0;   
  }else{   
    return  $diasSegundaJuliano - $diasPrimeraJuliano;   
  }    
}



function getRealIP()
{
 
$ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;

    return $ip;
 
}

function formatea_rut($rut){
	
$rut 	= str_replace("k","K",$rut);
$rut 	= str_replace(".","",$rut);
$rut 	= str_replace("-","",$rut);
$dv 	= substr($rut, -1);
$rut 	= substr($rut, 0, -1);
$rut 	=  number_format( $rut, 0, "", ".");
$rut 	= $rut."-".$dv;

return $rut;

}

?>