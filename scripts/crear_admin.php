<?php
// scripts/crear_admin.php
require __DIR__ . '/../app/core/Database.php';
$pdo = Database::getInstance();

$docusu = '00000000';
$nombre = 'Admin';
$apellido = 'Prueba';
$email = 'admin@ejemplo.com';
$password = password_hash('Admin123!', PASSWORD_DEFAULT);
$role_id = 1;

$sql = "INSERT INTO usuarios (docusu,nombre,apellido,email,password,role_id)
        VALUES (:docusu,:nombre,:apellido,:email,:password,:role_id)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':docusu' => $docusu,
  ':nombre' => $nombre,
  ':apellido' => $apellido,
  ':email' => $email,
  ':password' => $password,
  ':role_id' => $role_id
]);
echo "Admin creado\n";
