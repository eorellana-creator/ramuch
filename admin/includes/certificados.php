<?php
/**
 * Entrega protegida de certificados de alumno regular.
 *
 * Los archivos viven en admin/components/socios/archivos, carpeta bloqueada
 * para acceso web directo mediante .htaccess. Solo pueden descargarse a
 * través de los endpoints autenticados del admin y del portal de socios, que
 * validan la sesión antes de llamar a esta función.
 */

if (!function_exists('ramuchEnviarCertificado')) {
    function ramuchDirectorioCertificados()
    {
        return realpath(__DIR__ . '/../components/socios/archivos');
    }

    function ramuchEnviarCertificado($nombreArchivo)
    {
        $nombreArchivo = basename((string)$nombreArchivo);
        $directorio = ramuchDirectorioCertificados();
        $ruta = ($directorio && $nombreArchivo !== '') ? realpath($directorio . DIRECTORY_SEPARATOR . $nombreArchivo) : false;

        // Impide salir de la carpeta de certificados y servir archivos ocultos.
        if ($ruta === false || strpos($ruta, $directorio . DIRECTORY_SEPARATOR) !== 0 || $nombreArchivo[0] === '.' || !is_file($ruta)) {
            http_response_code(404);
            exit('Certificado no disponible.');
        }

        $tiposPermitidos = array(
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        );
        $tipo = function_exists('mime_content_type') ? mime_content_type($ruta) : '';
        if (!isset($tiposPermitidos[$tipo])) {
            http_response_code(415);
            exit('Tipo de archivo no permitido.');
        }

        header('Content-Type: ' . $tipo);
        header('Content-Length: ' . filesize($ruta));
        header('Content-Disposition: inline; filename="certificado.' . $tiposPermitidos[$tipo] . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');
        readfile($ruta);
        exit;
    }
}
