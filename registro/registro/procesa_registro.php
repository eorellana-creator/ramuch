<?php
require_once "recaptchalib.php";
/*
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $rut = $_POST['rut'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email = $_POST['email'];
    $captcha = $_POST['g-recaptcha-response'];
    
    $secretKey = "6Ldsrc0pAAAAALAI9AhbXFrnFT3xHwlMu5KAWqIO";
    $responseKey = $captcha;
    $userIP = $_SERVER['REMOTE_ADDR'];
    
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";
    $response = file_get_contents($url);
    $response = json_decode($response);
    
    if ($response->success) {
        // Aquí puedes agregar el código para insertar los datos en la base de datos
        echo "Registro exitoso!";
    } else {
        echo "Verificación de reCAPTCHA fallida. Por favor, inténtalo de nuevo.";
    }
} else {
    echo "Método no permitido.";
}
*/
/*
$secret = "6Ldsrc0pAAAAALAI9AhbXFrnFT3xHwlMu5KAWqIO";
$response = null; 
// Verificamos la clave secreta 
$reCaptcha = new ReCaptcha($secret);
if ($_POST["g-recaptcha-response"]) { 
    $response = $reCaptcha->verifyResponse( $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"] );
} 
if ($response != null && $response->success) {
    echo "Registro exitoso!";
} else { 
    echo "Verificación de reCAPTCHA fallida. Por favor, inténtalo de nuevo.";
}
*/

$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify'; $recaptcha_secret = '6Ldsrc0pAAAAALAI9AhbXFrnFT3xHwlMu5KAWqIO';
$recaptcha_response = $_POST['recaptcha_response']; 
$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
$recaptcha = json_decode($recaptcha); 
if($recaptcha->score >= 0.7){ 
    echo "Registro exitoso!";
} else { 
    echo "Verificación de reCAPTCHA fallida. Por favor, inténtalo de nuevo.";
}
?>


