<?php
class Config {

	public $url_web 	= 'https://ramuch.cl/socios/index.php?component=dashboard&view=dashboard';
    public $zona_horaria = "America/Santiago";
	public $offline = '0';
	public $sitename = 'Ramuch';
	public $urlbase = 'intranet';
	public $list_limit = '20';
	public $access = '1';
	public $debug = '0';
	public $debug_lang = '0';
	public $dbtype = 'mysqli';
	public $baseprint = "/ramuch/socios/";

	public $error_reporting = 'default';
	
 
	 
	
	public $horas_semanales = '45';
	public $horas_dia = '9';
	public $iva = '19';
	
 
	public $smtpauth = '0';
 
 
 
	public $smtpsecure = 'none';
	public $smtpport = '25';
	public $log_path = '';
	public $lifetime = '180';
	public $offline_message = 'Sistema cerrado por tareas de mantenimiento.<br />Por favor, inténtelo nuevamente más tarde.';
		
    }//class Config
?>