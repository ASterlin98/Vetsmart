<?php
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $m = new PHPMailer();
    echo "PHPMailer instanciado correctamente.\n";
} catch (Throwable $e) {
    echo "Error al instanciar PHPMailer: " . $e->getMessage() . "\n";
}
