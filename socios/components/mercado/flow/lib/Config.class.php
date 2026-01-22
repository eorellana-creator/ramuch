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

class Config {
    // Configuración de Flow.cl
    private static $COMMERCE_CONFIG = array(
        "APIKEY" => "320F6E9B-F458-4190-85B3-4714L7297E0D", // API Key de Flow.cl
        "SECRETKEY" => "49b6132abe79b2aa70d876523c2dfb55328a683e", // Secret Key de Flow.cl
        "APIURL" => "https://sandbox.flow.cl/api", // Endpoint de la API de Flow.cl (sandbox)
        "BASEURL" => "https://ramuch.cl/pagar" // URL base de tu sitio
    );

    /**
     * Obtiene un valor de configuración.
     *
     * @param string $name Nombre de la configuración (APIKEY, SECRETKEY, APIURL, BASEURL).
     * @return mixed Valor de la configuración.
     * @throws Exception Si la configuración no existe.
     */
    public static function get($name) {
        if (!isset(self::$COMMERCE_CONFIG[$name])) {
            throw new Exception("The configuration element does not exist", 1);
        }
        return self::$COMMERCE_CONFIG[$name];
    }
}
?>