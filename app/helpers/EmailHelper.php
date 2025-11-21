<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{
    /**
     * Envía un correo de confirmación de cita al cliente
     *
     * @param string $clienteEmail Email del cliente
     * @param string $clienteNombre Nombre del cliente
     * @param array $citaData Datos de la cita (fecha, hora, mascota, servicio, empleado)
     * @return bool true si se envió correctamente, false en caso contrario
     */
    public static function enviarConfirmacionCita(string $clienteEmail, string $clienteNombre, array $citaData): bool
    {
        try {
            $mail = new PHPMailer(true);
            
            // Cargar configuración desde .env
            $dotenv = dirname(__DIR__, 2) . '/.env';
            $config = [];
            if (file_exists($dotenv)) {
                $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                        list($key, $value) = explode('=', $line, 2);
                        $config[trim($key)] = trim($value, '\'"');
                    }
                }
            }
            
            $smtpHost = $config['MAIL_HOST'] ?? $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $smtpUser = $config['MAIL_USERNAME'] ?? $_ENV['SMTP_USER'] ?? '';
            $smtpPass = $config['MAIL_PASSWORD'] ?? $_ENV['SMTP_PASS'] ?? '';
            $smtpPort = (int)($config['MAIL_PORT'] ?? $_ENV['SMTP_PORT'] ?? 465);
            $smtpSecure = $config['MAIL_SECURE'] ?? $_ENV['SMTP_SECURE'] ?? 'smtps';
            $mailFrom = $config['MAIL_FROM'] ?? $_ENV['MAIL_FROM'] ?? 'noreply@vetsmart.com';
            $mailFromName = $config['MAIL_FROM_NAME'] ?? $_ENV['MAIL_FROM_NAME'] ?? 'VetSmart';
            
            // Si no hay credenciales SMTP, usar mail() del sistema
            if (empty($smtpUser) || empty($smtpPass)) {
                return self::enviarConMailNativo($clienteEmail, $clienteNombre, $citaData);
            }

            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = $smtpSecure;
            $mail->Port = $smtpPort;

            // Remitente
            $mail->setFrom($mailFrom, $mailFromName);
            $mail->addAddress($clienteEmail, $clienteNombre);

            // Asunto
            $mail->Subject = 'Confirmación de tu cita - VetSmart';

            // Cuerpo del correo en HTML
            $html = self::generarHTMLConfirmacion($clienteNombre, $citaData);
            $mail->isHTML(true);
            $mail->Body = $html;
            
            // Texto plano alternativo
            $mail->AltBody = self::generarTextoPlanoConfirmacion($clienteNombre, $citaData);

            // Enviar
            return $mail->send();

        } catch (Exception $e) {
            error_log("Error al enviar correo de confirmación de cita: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía correo usando mail() del sistema (fallback)
     */
    private static function enviarConMailNativo(string $clienteEmail, string $clienteNombre, array $citaData): bool
    {
        try {
            $asunto = 'Confirmación de tu cita - VetSmart';
            $headers = [
                'MIME-Version' => '1.0',
                'Content-type' => 'text/html; charset=UTF-8',
                'From' => 'noreply@vetsmart.com',
                'Reply-To' => 'soporte@vetsmart.com'
            ];
            
            $headerString = '';
            foreach ($headers as $key => $value) {
                $headerString .= $key . ': ' . $value . "\r\n";
            }

            $html = self::generarHTMLConfirmacion($clienteNombre, $citaData);
            
            return mail($clienteEmail, $asunto, $html, $headerString);
        } catch (Throwable $e) {
            error_log("Error al enviar correo con mail(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Genera el HTML del correo de confirmación
     */
    private static function generarHTMLConfirmacion(string $clienteNombre, array $citaData): string
    {
        $fecha = date('d/m/Y', strtotime($citaData['fecha']));
        $hora = $citaData['hora'] ?? date('H:i', strtotime($citaData['fecha']));
        $mascota = htmlspecialchars($citaData['mascota'] ?? 'Tu mascota');
        $servicio = htmlspecialchars($citaData['servicio'] ?? 'Servicio');
        $empleado = htmlspecialchars($citaData['empleado'] ?? 'Profesional');
        $tipoEmpleado = htmlspecialchars($citaData['tipo_empleado'] ?? 'Profesional');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Cita - VetSmart</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #20c997 0%, #198754 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .details {
            background-color: #f9f9f9;
            border-left: 4px solid #20c997;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
            width: 40%;
        }
        .detail-value {
            color: #333;
            text-align: right;
            width: 60%;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .cta-button {
            display: inline-block;
            background-color: #20c997;
            color: white;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            margin: 20px 0;
            font-weight: 600;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Cita Confirmada</h1>
            <p>Tu cita ha sido agendada exitosamente</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hola <strong>$clienteNombre</strong>,<br><br>
                Nos complace confirmar que tu cita ha sido agendada correctamente en VetSmart.
            </div>

            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">📅 Fecha:</span>
                    <span class="detail-value"><strong>$fecha</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">⏰ Hora:</span>
                    <span class="detail-value"><strong>$hora</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">🐾 Mascota:</span>
                    <span class="detail-value"><strong>$mascota</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">💊 Servicio:</span>
                    <span class="detail-value"><strong>$servicio</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">👨‍⚕️ Profesional:</span>
                    <span class="detail-value"><strong>$empleado</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">🏷️ Tipo:</span>
                    <span class="detail-value"><strong>$tipoEmpleado</strong></span>
                </div>
            </div>

            <div class="warning">
                <strong>⚠️ Recuerda:</strong> Por favor, llega 10 minutos antes de tu cita. Si necesitas cancelar o reprogramar, avísanos con anticipación.
            </div>

            <p>
                Si tienes alguna pregunta o necesitas cambiar tu cita, no dudes en contactarnos:
            </p>
            <ul>
                <li>📧 Email: soporte@vetsmart.com</li>
                <li>📞 Teléfono: +1-234-567-8900</li>
            </ul>

            <center>
                <a href="https://vetsmart.com/mis-citas" class="cta-button">Ver mis citas</a>
            </center>
        </div>

        <div class="footer">
            <p>© 2025 VetSmart - Clínica Veterinaria. Todos los derechos reservados.</p>
            <p>Este es un correo automático. Por favor, no respondas directamente.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Genera el texto plano del correo de confirmación
     */
    private static function generarTextoPlanoConfirmacion(string $clienteNombre, array $citaData): string
    {
        $fecha = date('d/m/Y', strtotime($citaData['fecha']));
        $hora = $citaData['hora'] ?? date('H:i', strtotime($citaData['fecha']));

        return <<<TEXT
¡Hola $clienteNombre!

Tu cita ha sido confirmada en VetSmart.

DETALLES DE TU CITA:
- Fecha: $fecha
- Hora: $hora
- Mascota: {$citaData['mascota']}
- Servicio: {$citaData['servicio']}
- Profesional: {$citaData['empleado']}
- Tipo: {$citaData['tipo_empleado']}

Recuerda llegar 10 minutos antes de tu cita.

Si necesitas cambiar o cancelar tu cita, contacta con nosotros:
- Email: soporte@vetsmart.com
- Teléfono: +1-234-567-8900

© 2025 VetSmart - Clínica Veterinaria
TEXT;
    }
}
