<?php
/**
 * Script de diagnóstico para el sistema de envío de correos
 * Ejecutar en terminal: php tests/diagnostico_email.php
 */

// Cargar configuración
define('APP_ROOT', dirname(__DIR__, 1));
require APP_ROOT . '/vendor/autoload.php';

echo "\n";
echo "═════════════════════════════════════════════════════════════\n";
echo "🔍 DIAGNÓSTICO DEL SISTEMA DE ENVÍO DE CORREOS VetSmart\n";
echo "═════════════════════════════════════════════════════════════\n";

// 1. Verificar archivo .env
echo "\n1️⃣  VERIFICANDO ARCHIVO .env\n";
echo "───────────────────────────────────────────────────────────\n";

$envPath = APP_ROOT . '/.env';
if (!file_exists($envPath)) {
    echo "❌ No se encontró archivo .env\n";
    exit(1);
}

echo "✓ Archivo .env encontrado\n\n";

// Leer .env
$config = [];
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
        list($key, $value) = explode('=', $line, 2);
        $config[trim($key)] = trim($value, '\'"');
    }
}

$requiredKeys = ['MAIL_HOST', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_PORT', 'MAIL_SECURE', 'MAIL_FROM'];
foreach ($requiredKeys as $key) {
    if (isset($config[$key])) {
        $value = $config[$key];
        // Ocultar contraseña
        if ($key === 'MAIL_PASSWORD') {
            $value = substr($value, 0, 3) . '****' . substr($value, -2);
        }
        echo "  ✓ $key = $value\n";
    } else {
        echo "  ❌ $key NO CONFIGURADO\n";
    }
}

// 2. Verificar configuración de PHP
echo "\n2️⃣  CONFIGURACIÓN DE PHP\n";
echo "───────────────────────────────────────────────────────────\n";

echo "  • Versión PHP: " . phpversion() . "\n";
echo "  • Extensión OpenSSL: " . (extension_loaded('openssl') ? '✓ Habilitada' : '❌ Deshabilitada') . "\n";
echo "  • Extensión CURL: " . (extension_loaded('curl') ? '✓ Habilitada' : '❌ Deshabilitada') . "\n";
echo "  • Función mail(): " . (function_exists('mail') ? '✓ Disponible' : '❌ No disponible') . "\n";

// 3. Verificar PHPMailer
echo "\n3️⃣  VERIFICANDO PHPMailer\n";
echo "───────────────────────────────────────────────────────────\n";

try {
    require APP_ROOT . '/vendor/autoload.php';
    $testMail = new PHPMailer\PHPMailer\PHPMailer();
    echo "✓ PHPMailer cargado correctamente\n";
    echo "  Versión: " . PHPMailer\PHPMailer\PHPMailer::VERSION . "\n";
} catch (Exception $e) {
    echo "❌ Error al cargar PHPMailer: " . $e->getMessage() . "\n";
}

// 4. Verificar EmailHelper
echo "\n4️⃣  VERIFICANDO EmailHelper\n";
echo "───────────────────────────────────────────────────────────\n";

$helperPath = APP_ROOT . '/app/helpers/EmailHelper.php';
if (file_exists($helperPath)) {
    echo "✓ Archivo EmailHelper.php encontrado\n";
} else {
    echo "❌ Archivo EmailHelper.php NO ENCONTRADO\n";
}

// 5. Verificar conexión SMTP
echo "\n5️⃣  PRUEBA DE CONEXIÓN SMTP\n";
echo "───────────────────────────────────────────────────────────\n";

$smtpHost = $config['MAIL_HOST'] ?? 'smtp.gmail.com';
$smtpPort = (int)($config['MAIL_PORT'] ?? 465);

echo "Intentando conectar a: $smtpHost:$smtpPort\n";

$fp = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 5);
if ($fp) {
    echo "✓ Conexión establecida\n";
    fclose($fp);
} else {
    echo "❌ No se pudo conectar: $errstr (Error: $errno)\n";
    echo "   Consejo: Verifica que el firewall no esté bloqueando la conexión\n";
}

// 6. Revisar logs
echo "\n6️⃣  ÚLTIMOS REGISTROS DE ERROR\n";
echo "───────────────────────────────────────────────────────────\n";

$phpErrorLog = ini_get('error_log');
if ($phpErrorLog && file_exists($phpErrorLog)) {
    $lines = array_slice(file($phpErrorLog), -10);
    foreach ($lines as $line) {
        if (strpos($line, '[EMAIL]') !== false || strpos($line, '[CITA]') !== false) {
            echo trim($line) . "\n";
        }
    }
} else {
    echo "No se encontraron logs o error_log no está configurado\n";
}

// 7. Verificar base de datos
echo "\n7️⃣  VERIFICACIÓN DE BASE DE DATOS\n";
echo "───────────────────────────────────────────────────────────\n";

try {
    require APP_ROOT . '/app/core/Database.php';
    $db = Database::getInstance();
    
    // Verificar tabla usuarios
    $stmt = $db->query("SELECT COUNT(*) as count FROM usuarios WHERE email IS NOT NULL AND email != ''");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Usuarios con email: " . ($result['count'] ?? 0) . "\n";
    
    // Verificar tabla citas
    $stmt = $db->query("SELECT COUNT(*) as count FROM citas");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Total de citas: " . ($result['count'] ?? 0) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error al conectar BD: " . $e->getMessage() . "\n";
}

// 8. Resumen y recomendaciones
echo "\n8️⃣  CHECKLIST DE CONFIGURACIÓN\n";
echo "───────────────────────────────────────────────────────────\n";

$checks = [];
$checks['MAIL_HOST'] = isset($config['MAIL_HOST']) && !empty($config['MAIL_HOST']);
$checks['MAIL_USERNAME'] = isset($config['MAIL_USERNAME']) && !empty($config['MAIL_USERNAME']);
$checks['MAIL_PASSWORD'] = isset($config['MAIL_PASSWORD']) && !empty($config['MAIL_PASSWORD']);
$checks['MAIL_PORT'] = isset($config['MAIL_PORT']) && !empty($config['MAIL_PORT']);
$checks['MAIL_SECURE'] = isset($config['MAIL_SECURE']) && !empty($config['MAIL_SECURE']);
$checks['MAIL_FROM'] = isset($config['MAIL_FROM']) && !empty($config['MAIL_FROM']) && $config['MAIL_FROM'] !== 'tu.email@gmail.com';
$checks['OpenSSL'] = extension_loaded('openssl');
$checks['PHPMailer'] = file_exists(APP_ROOT . '/vendor/phpmailer/phpmailer/src/PHPMailer.php');

$allGood = true;
foreach ($checks as $check => $status) {
    echo ($status ? "✓" : "❌") . " $check\n";
    if (!$status) $allGood = false;
}

echo "\n═════════════════════════════════════════════════════════════\n";
if ($allGood) {
    echo "✅ TODO ESTÁ CONFIGURADO CORRECTAMENTE\n";
    echo "   Los correos deberían enviarse sin problemas.\n";
} else {
    echo "⚠️  HAY PROBLEMAS QUE NECESITAN ATENCIÓN\n";
    echo "   Revisa los ítems marcados con ❌ arriba.\n";
}
echo "═════════════════════════════════════════════════════════════\n\n";

// 9. Ofrecer prueba
echo "¿Deseas enviar un correo de prueba? (S/N): ";
$input = trim(fgets(STDIN));

if (strtoupper($input) === 'S') {
    echo "\nIngresa el email de destino: ";
    $testEmail = trim(fgets(STDIN));
    
    if (filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        require_once APP_ROOT . '/app/helpers/EmailHelper.php';
        
        echo "\nEnviando correo de prueba a $testEmail...\n";
        
        $result = EmailHelper::enviarConfirmacionCita(
            $testEmail,
            'Usuario de Prueba',
            [
                'fecha' => date('Y-m-d', strtotime('+1 day')) . ' 10:00',
                'hora' => '10:00',
                'mascota' => 'Max (Prueba)',
                'servicio' => 'Consulta General',
                'empleado' => 'Dr. Test',
                'tipo_empleado' => 'Veterinario'
            ]
        );
        
        if ($result) {
            echo "✅ Correo enviado exitosamente\n";
            echo "   Verifica tu bandeja de entrada en: $testEmail\n";
        } else {
            echo "❌ Error al enviar el correo\n";
            echo "   Revisa los logs para más detalles\n";
        }
    } else {
        echo "Email inválido\n";
    }
}

echo "\n";
