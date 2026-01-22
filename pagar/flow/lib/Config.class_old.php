<?php
/**
 * Clase para Configurar el cliente
 * @Filename: Config.class.php
 * @version: 2.0
 * @Author: flow.cl
 * @Email: csepulveda@tuxpan.com
 * @Date: 28-04-2017 11:32
 * @Last Modified by: Carlos Sepulveda
 * @Last Modified time: 28-04-2017 11:32
 */
 
/*
 $COMMERCE_CONFIG = array(
 	"APIKEY" => "7AB28AEF-7900-4D30-A361-2CLC28E84635", // Registre aquí su apiKey
 	"SECRETKEY" => "022c9f928367b6b084f07b1790ab7b3ae3ab8663", // Registre aquí su secretKey
 	"APIURL" => "https://www.flow.cl/api", // Producción EndPoint o Sandbox EndPoint
 	"BASEURL" => "https://www.micomercio.cl/apiFlow" //Registre aquí la URL base en su página donde instalará el cliente
 );
 */
/*
 $COMMERCE_CONFIG = array(
	"APIKEY" => "320F6E9B-F458-4190-85B3-4714L7297E0D", // Registre aquí su apiKey
	"SECRETKEY" => "49b6132abe79b2aa70d876523c2dfb55328a683e", // Registre aquí su secretKey
	"APIURL" => "https://sandbox.flow.cl/api", // Producción EndPoint o Sandbox EndPoint
	"BASEURL" => "https://localhost/montanauchile/pagar" //Registre aquí la URL base en su página donde instalará el cliente
);
*/
/*
$COMMERCE_CONFIG = array(
	"APIKEY" => "320F6E9B-F458-4190-85B3-4714L7297E0D", // Registre aquí su apiKey
	"SECRETKEY" => "49b6132abe79b2aa70d876523c2dfb55328a683e", // Registre aquí su secretKey
	"APIURL" => "https://sandbox.flow.cl/api", // Producción EndPoint o Sandbox EndPoint
	"BASEURL" => "https://ramuch.cl/pagar" //Registre aquí la URL base en su página donde instalará el cliente
);
*/


//Kop
$COMMERCE_CONFIG = array(
	"APIKEY" =>   "7AB28AEF-7900-4D30-A361-2CLC28E84635", // Registre aquí su apiKey           "1F0418DF-7619-4A34-B32F-5E497LAAEDA3",        ///
	"SECRETKEY" => "022c9f928367b6b084f07b1790ab7b3ae3ab8663", // Registre aquí su secretKey   "0d231f85c13a2e8a43444444572ba16e150b6435",  ///
	"APIURL" => "https://www.flow.cl/api",                      /// Producción EndPoint o Sandbox EndPoint    "https://sandbox.flow.cl/api",   //  "https://www.flow.cl/api"          
	"BASEURL" => "https://ramuch.cl/pagar"                     ///"https://ramuch.cl/pagar"                    //Registre aquí la URL base en su página donde instalará el cliente
);
//Kop




 
 class Config {
 	
	static function get($name) {
		global $COMMERCE_CONFIG;
		if(!isset($COMMERCE_CONFIG[$name])) {
			throw new Exception("The configuration element thas not exist", 1);
		}
		return $COMMERCE_CONFIG[$name];
	}
 }
