<?php
// public/test_send.php
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors',1);

// CONFIG — reemplaza temporalmente con tus datos si no usas .env
$mailHost = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
$mailUser = getenv('MAIL_USERNAME') ?: 'andres.rojast98@gmail.com';       // <-- tu usuario SMTP
$mailPass = getenv('MAIL_PASSWORD') ?: 'ocuebaapqlfqwbvv';         // <-- app password o contraseña SMTP
$mailPort = getenv('MAIL_PORT') ? (int)getenv('MAIL_PORT') : 465;
$mailSecure = getenv('MAIL_SECURE') ?: 'smtps';
$mailFrom = getenv('MAIL_FROM') ?: $mailUser;
$mailFromName = getenv('MAIL_FROM_NAME') ?: 'VetSmart';

// Destino de prueba (por defecto mismo usuario)
$dest = 'andres.rojast98@gmail.com'; // <- cámbialo para enviar a otra cuenta de prueba

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $mailHost;
    $mail->SMTPAuth = true;
    $mail->Username = $mailUser;
    $mail->Password = $mailPass;

    // DEBUG visible en navegador (nivel 2 = verbose)
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level){
        echo nl2br(htmlspecialchars("[$level] $str\n"));
    };

    $mail->SMTPSecure = (strtolower($mailSecure) === 'starttls' || strtolower($mailSecure) === 'tls')
                        ? PHPMailer::ENCRYPTION_STARTTLS
                        : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $mailPort;

    // Opcional en dev: evitar verificación SSL (NO en prod)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ]
    ];

    $mail->setFrom($mailFrom, $mailFromName);
    $mail->addAddress($dest);
    $mail->isHTML(true);
    $mail->Subject = 'Prueba SMTP VetSmart - ' . date('Y-m-d H:i:s');
    $mail->Body    = '<p>Mensaje de prueba desde <b>VetSmart</b></p><p>Hora: ' . date('Y-m-d H:i:s') . '</p>';

    $mail->send();
    echo "<p><strong>Enviado OK</strong></p>";
} catch (Exception $e) {
    echo "<p><strong>Error (PHPMailer):</strong> " . htmlspecialchars($mail->ErrorInfo ?: $e->getMessage()) . "</p>";
}
