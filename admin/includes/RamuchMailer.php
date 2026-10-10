<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class RamuchMailer extends \PHPMailer\PHPMailer\PHPMailer
{
    public function send()
    {
        $hostActual = strtolower(preg_replace('/:\\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));
        if ($hostActual === 'staging.ramuch.cl') {
            $this->clearAllRecipients();
            $this->clearCCs();
            $this->clearBCCs();
            $this->clearReplyTos();
            $this->addAddress('eorellana@gmail.com', 'Pruebas staging');
            $this->addReplyTo('eorellana@gmail.com', 'Pruebas staging');
            if (strpos($this->Subject, '[STAGING]') !== 0) {
                $this->Subject = '[STAGING] ' . $this->Subject;
            }
            error_log('STAGING: destinatarios de correo redirigidos al buzón de pruebas');
        }
        return parent::send();
    }
}

function crearMailerRamuch($exceptions = true)
{
    $mail = new RamuchMailer($exceptions);
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'mail.ramuch.cl';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Timeout = 15;
    $mail->Username = 'no-responder@ramuch.cl';
    $mail->Password = '1941ramuch2024';
    $mail->CharSet = 'UTF-8';
    $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
    return $mail;
}
