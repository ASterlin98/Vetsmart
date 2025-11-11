<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // ajusta la ruta si usas composer

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'andres.rojast98@gmail.com'; 
    $mail->Password = 'lzkzjjmrmbjssflb'; // clave de aplicación
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
    $mail->Port = 465;

    // Opciones SSL (temporalmente desactivamos la verificación)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('andres.rojast98@gmail.com', 'Prueba');
    $mail->addAddress('andres.rojast98@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Prueba SMTP';
    $mail->Body    = 'Esto es un correo de prueba con PHPMailer.';

    $mail->send();
    echo '✅ Correo enviado correctamente';
} catch (Exception $e) {
    echo "❌ Error al enviar: {$mail->ErrorInfo}";
}
