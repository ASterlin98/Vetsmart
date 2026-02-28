<?php
// app/views/layouts/main_recepcionista.php

if (session_status() === PHP_SESSION_NONE) session_start();
$roleName = $_SESSION['user']['role_name'] ?? '';
$roleAlt  = $_SESSION['user']['role'] ?? '';
if (empty($_SESSION['user']) || ($roleName !== 'recepcionista' && $roleAlt !== 'recepcionista')) {
  header('Location: <?= BASE ?>/login');
  exit;
}

// Define el título de la página
$title = 'Panel del Recepcionista';

// Define el título del encabezado
$header_title = ' Recepcionista VetSmart';

// Define los enlaces de navegación para el recepcionista
$nav_links = [
    ['url' => '<?= BASE ?>/recepcionista/dashboard', 'icon' => 'fas fa-home', 'text' => 'Dashboard'],
    ['url' => '<?= BASE ?>/recepcionista/agenda', 'icon' => 'fas fa-calendar-alt', 'text' => 'Agenda'],
    ['url' => '<?= BASE ?>/recepcionista/citas', 'icon' => 'fas fa-plus-circle', 'text' => 'Crear Cita'],
    ['url' => '<?= BASE ?>/recepcionista/clientes', 'icon' => 'fas fa-users', 'text' => 'Gestión de Clientes'],
    ['url' => '<?= BASE ?>/recepcionista/mascotas', 'icon' => 'fas fa-paw', 'text' => 'Gestión de Mascotas'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
