<?php

class archivos{
	
	public $listado = array();

	function __construct(){ 
      	
   	} //function__construct
	
	
function listar($directorio){
  if(is_dir("$directorio")){
  $directorio = opendir("$directorio") or die('Error');
	while($archivo = @readdir($directorio)) {
		if( $archivo != '.' && $archivo != '..'   &&  substr($archivo, 0, 1)!="." ) {
                $this->listado[] = $archivo;        
		}//if
	
	}//while
  closedir($directorio);
  }//if(is_dir("$directorio"))
  return $this->listado;
}

function limpiar(){
 unset($this->listado);
}


}//class archivos




?>