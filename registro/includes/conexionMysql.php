<?php

class mysql {
    
    private  $server	= "localhost";
    private  $user		= "ramuchcl_ramuch";
    private  $password	= "ramuch2024#1105";
    private  $data_base	= "ramuchcl_admin";
    var $conexion;
    var $flag = false;
    var $error_conexion = "Error en la conexion a MYSQL";
	
	
	function __construct(){ 
      	
      	} 
	
	
    
    function connect(){
		
			$this->conexion = mysqli_connect($this->server , $this->user, $this->password, $this->data_base);
			$this->flag = true;
            
			mysqli_query($this->conexion,"SET NAMES utf8");
		   
			//mysqli_select_db($this->data_base,$this->conexion); Sólo necesaria si se cambia de Base de datos
			
            return $this->conexion;
    }//function connect()
    
    function close(){
        if($this->flag == true){
            @mysqli_close($this->conexion);
        }
    }//function close()
    
    function query($query){
        return @mysqli_query($this->conexion,$query);
    }//function query($query)
    
    function f_obj($query){
        return @mysqli_fetch_object($query);
    }//function f_obj($query)

    function f_array($query){
        return @mysqli_fetch_assoc($query);
    }//f_array($query)

    function f_num($query){
        return @mysqli_num_rows($query);
    }//f_num($query)
	
	function ultimo_id(){
        return @mysqli_insert_id($this->conexion);
    }//function ultimo_id()
    
    
    function select($db){
        $result = @mysqli_select_db($db,$this->conexion);
        if($result){
            $this->conexion = $db; 
            return true;
        }else{
            return false;
        }
    }//select($db)
    
    function free_sql($query){
        mysqli_free_result($query);
    }//function free_sql($query
	
}//class mysql
  

?>