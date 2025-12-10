<?php
/**
 * Script de prueba de envío a vetsmart70@gmail.com
 * Ejecutar: php tests/test_vetsmart_email.php
 */

define('APP_ROOT', dirname(__DIR__, 1));
require APP_ROOT . '/vendor/autoload.php';
require APP_ROOT . '/app/helpers/EmailHelper.php';

echo "\n";
echo "═════════════════════════════════════════════════════════════\n";
echo "📧 PRUEBA DE ENVÍO A vetsmart70@gmail.com\n";
echo "═════════════════════════════════════════════════════════════\n";

// Cargar configuración del .env
$dotenv = APP_ROOT . '/.env';
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

echo "\n1️⃣  VERIFICANDO CONFIGURACIÓN\n";
echo "───────────────────────────────────────────────────────────\n";
echo "MAIL_HOST: " . ($config['MAIL_HOST'] ?? 'NO CONFIGURADO') . "\n";
echo "MAIL_USERNAME: " . ($config['MAIL_USERNAME'] ?? 'NO CONFIGURADO') . "\n";
echo "MAIL_PORT: " . ($config['MAIL_PORT'] ?? 'NO CONFIGURADO') . "\n";
echo "MAIL_SECURE: " . ($config['MAIL_SECURE'] ?? 'NO CONFIGURADO') . "\n";
echo "MAIL_FROM: " . ($config['MAIL_FROM'] ?? 'NO CONFIGURADO') . "\n";

if (empty($config['MAIL_USERNAME']) || empty($config['MAIL_PASSWORD'])) {
    echo "\n❌ ERROR: Credenciales SMTP no configuradas\n";
    exit(1);
}

echo "\n2️⃣  PREPARANDO CORREO DE PRUEBA\n";
echo "───────────────────────────────────────────────────────────\n";

$destino = 'vetsmart70@gmail.com';
$nombre_cliente = 'Cliente Prueba VetSmart';

$datos_cita = [
    'fecha' => date('Y-m-d', strtotime('+3 days')) . ' 10:00',
    'hora' => '10:00',
    'mascota' => 'Luna (Prueba)',
    'servicio' => 'Consulta General + Vacunación',
    'empleado' => 'Dr. Juan García',
    'tipo_empleado' => 'Veterinario'
];

echo "De: " . ($config['MAIL_FROM'] ?? 'noreply@vetsmart.com') . "\n";
echo "Para: $destino\n";
echo "Asunto: Confirmación de tu cita - VetSmart\n";
echo "Fecha de cita: " . $datos_cita['fecha'] . "\n";
echo "Mascota: " . $datos_cita['mascota'] . "\n";

echo "\n3️⃣  ENVIANDO CORREO...\n";
echo "───────────────────────────────────────────────────────────\n";

try {
    $resultado = EmailHelper::enviarConfirmacionCita(
        $destino,
        $nombre_cliente,
        $datos_cita
    );

    if ($resultado) {
        echo "\n✅ ¡ÉXITO!\n";
        echo "───────────────────────────────────────────────────────────\n";
        echo "El correo ha sido enviado exitosamente a: $destino\n";
        echo "\n📝 Detalles del correo:\n";
        echo "  • Asunto: Confirmación de tu cita - VetSmart\n";
        echo "  • Fecha de cita: " . $datos_cita['fecha'] . "\n";
        echo "  • Mascota: " . $datos_cita['mascota'] . "\n";
        echo "  • Servicio: " . $datos_cita['servicio'] . "\n";
        echo "  • Profesional: " . $datos_cita['empleado'] . "\n";
        echo "\n⏱️  El correo debería llegar en 1-2 minutos.\n";
        echo "💡 Si no lo ves, verifica la carpeta de SPAM.\n";
        echo "\n═════════════════════════════════════════════════════════════\n\n";
    } else {
        echo "\n❌ ERROR: No se pudo enviar el correo\n";
        echo "───────────────────────────────────────────────────────────\n";
        echo "\n📋 Verifica los logs:\n";
        echo "  • Ruta: C:\\xampp\\apache\\logs\\error.log\n";
        echo "  • Comando: Get-Content C:\\xampp\\apache\\logs\\error.log -Tail 30\n";
        echo "\n═════════════════════════════════════════════════════════════\n\n";
    }
} catch (Exception $e) {
    echo "\n❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
    echo "───────────────────────────────────────────────────────────\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    echo "\n═════════════════════════════════════════════════════════════\n\n";
}
