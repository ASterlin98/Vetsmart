<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    define('APP_ROOT', dirname(__DIR__) . '/app');

    // Cargar variables de entorno
    $envFile = dirname(__DIR__) . '/.env';
    if (file_exists($envFile) && is_readable($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $value = trim($value, "\"'");
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }

    require __DIR__ . '/../app/core/Database.php';
    require_once APP_ROOT . '/models/Mascota.php';
    require_once APP_ROOT . '/models/Cita.php';
    require_once APP_ROOT . '/models/NotaMascota.php';

    $pdo = Database::getInstance();
    
    echo "DB Connection success.<br>\n";

    $mascotaId = 61; // URL param

    $mascotaModel = new Mascota($pdo);
    $citaModel = new Cita($pdo);
    $notaMascotaModel = new NotaMascota($pdo);
    
    echo "Fetching mascota...<br>\n";
    $mascota = $mascotaModel->getByIdConDueno($mascotaId);
    echo "Mascota fetch success.<br>\n";

    echo "Fetching citas...<br>\n";
    $citas = $citaModel->getPorMascota($mascotaId);
    echo "Citas fetch success.<br>\n";

    echo "Fetching notas...<br>\n";
    $notasRapidas = $notaMascotaModel->obtenerPorMascota($mascotaId);
    echo "Notas fetch success.<br>\n";

    echo "<b>All queries passed successfully.</b><br>\n";

} catch (Throwable $e) {
    echo "<h3>EXCEPTION CAUGHT:</h3> <b>" . $e->getMessage() . "</b><br>\n";
    echo "<pre>TRACE:\n" . $e->getTraceAsString() . "</pre>\n";
}
