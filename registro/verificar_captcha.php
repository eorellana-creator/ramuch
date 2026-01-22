<?php
// verificar_captcha.php

// Tu clave secreta de reCAPTCHA
$secret_key = "6LfEwTkqAAAAAGbm8VFwAxJXBGWFQ5bj3Al_aqUl";

// Comprobar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $captcha_response = $_POST['g-recaptcha-response'];

    // Si no se marcó el captcha
    if (!$captcha_response) {
        echo "Por favor completa el captcha.";
        exit;
    }

    // Validar la respuesta del captcha con la API de Google
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$captcha_response");
    $response_data = json_decode($response);

    // Verificar si el captcha fue exitoso
    if ($response_data->success) {
        echo "Captcha verificado correctamente. Puedes continuar con el procesamiento del formulario.";
    } else {
        echo "Error al verificar el captcha. Inténtalo nuevamente.";
    }
}
