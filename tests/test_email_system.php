<?php
/**
 * Script de prueba para el sistema de envío de correos
 * Ejecutar en terminal: php tests/test_email_system.php
 */

// Cargar configuración
define('APP_ROOT', dirname(__DIR__, 1));
require APP_ROOT . '/vendor/autoload.php';

// Cargar .env
$dotenvPath = APP_ROOT . '/.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value, '\'"');
        }
    }
}

// Cargar helper
require APP_ROOT . '/app/helpers/EmailHelper.php';

echo "=== PRUEBA DEL SISTEMA DE ENVÍO DE CORREOS ===\n\n";

// Verificar configuración
echo "1. Verificando configuración de .env...\n";
$required = ['MAIL_HOST', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_PORT', 'MAIL_SECURE'];
$config = [];
foreach ($required as $key) {
    $dotenv = APP_ROOT . '/.env';
    if (file_exists($dotenv)) {
        $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, $key . '=') === 0) {
                $value = substr($line, strlen($key . '='));
                $config[$key] = trim($value, '\'"');
            }
        }
    }
}

if (!empty($config['MAIL_HOST'])) {
    echo "   ✓ MAIL_HOST: " . $config['MAIL_HOST'] . "\n";
    echo "   ✓ MAIL_USERNAME: " . substr($config['MAIL_USERNAME'] ?? '', 0, 5) . "***\n";
    echo "   ✓ MAIL_PORT: " . ($config['MAIL_PORT'] ?? 'N/A') . "\n";
    echo "   ✓ MAIL_SECURE: " . ($config['MAIL_SECURE'] ?? 'N/A') . "\n";
} else {
    echo "   ✗ No se encontró configuración SMTP\n";
    echo "   → Asegúrate de completar el archivo .env\n";
    exit(1);
}

echo "\n2. Preparando datos de prueba...\n";

$testEmail = 'cliente@ejemplo.com';
$testData = [
    'fecha' => date('Y-m-d H:i', strtotime('+1 day 10:30')),
    'hora' => date('H:i', strtotime('+1 day 10:30')),
    'mascota' => 'Max (Pastor Alemán)',
    'servicio' => 'Consulta General + Vacunación',
    'empleado' => 'Dr. Juan García',
    'tipo_empleado' => 'Veterinario'
];

echo "   Email destino: " . $testEmail . "\n";
echo "   Fecha: " . $testData['fecha'] . "\n";
echo "   Mascota: " . $testData['mascota'] . "\n";
echo "   Servicio: " . $testData['servicio'] . "\n";
echo "   Empleado: " . $testData['empleado'] . "\n";

echo "\n3. Enviando correo de prueba...\n";

try {
    $result = EmailHelper::enviarConfirmacionCita(
        $testEmail,
        'Juan Pérez García',
        $testData
    );

    if ($result) {
        echo "   ✓ Correo enviado exitosamente!\n\n";
        echo "   Verifica tu bandeja de entrada en: " . $testEmail . "\n";
        echo "   Nota: Puede tardarse 1-2 minutos en llegar.\n";
    } else {
        echo "   ✗ Error al enviar el correo.\n";
        echo "   → Revisa los logs de PHP para más detalles\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ✗ Excepción capturada: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== FIN DE LA PRUEBA ===\n";
echo "\n📧 Tips:\n";
echo "   • Si usas Gmail, verifica que la contraseña sea la de aplicación\n";
echo "   • Si usas Outlook, asegúrate que 2FA esté activado\n";
echo "   • Revisa la carpeta de SPAM si no ves el correo en Bandeja de Entrada\n";
echo "   • Para envíos reales, usa el email del cliente en la base de datos\n";
