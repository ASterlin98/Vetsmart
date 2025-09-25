<?php
// public/test_phpmailer.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

echo "<h3>vendor/autoload.php: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'OK' : 'NO') . "</h3>";
echo "<p>PHPMailer class_exists: ";
var_export(class_exists(\PHPMailer\PHPMailer\PHPMailer::class));
echo "</p>";
