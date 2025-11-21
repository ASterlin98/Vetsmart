<?php
/**
 * Script simple para probar envío de correo
 * Ejecutar: php tests/test_envio_simple.php
 */

define('APP_ROOT', dirname(__DIR__, 1));
require APP_ROOT . '/vendor/autoload.php';
require APP_ROOT . '/app/helpers/EmailHelper.php';

echo "\n🧪 PRUEBA SIMPLE DE ENVÍO DE CORREO\n";
echo "════════════════════════════════════════\n\n";

// Solicitar email
echo "Ingresa el email para la prueba: ";
$email = trim(fgets(STDIN));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Email inválido\n";
    exit(1);
}

echo "\nEnviando correo de prueba a: $email\n";
echo "Espera...\n\n";

$testData = [
    'fecha' => date('Y-m-d', strtotime('+2 days')) . ' 14:30',
    'hora' => '14:30',
    'mascota' => 'Max (Prueba)',
    'servicio' => 'Consulta General Veterinaria',
    'empleado' => 'Dr. Juan García',
    'tipo_empleado' => 'Veterinario'
];

$result = EmailHelper::enviarConfirmacionCita(
    $email,
    'Cliente de Prueba',
    $testData
);

if ($result) {
    echo "✅ ÉXITO: Correo enviado correctamente\n";
    echo "📧 Destinatario: $email\n";
    echo "📅 Fecha de cita: " . $testData['fecha'] . "\n";
    echo "🐾 Mascota: " . $testData['mascota'] . "\n";
    echo "\n⏱️  El correo debería llegar en los próximos 1-2 minutos.\n";
    echo "💡 Si no lo ves, revisa la carpeta de SPAM.\n";
} else {
    echo "❌ ERROR: No se pudo enviar el correo\n";
    echo "📋 Revisa los logs en: C:\\xampp\\apache\\logs\\error.log\n";
}

echo "\n";
