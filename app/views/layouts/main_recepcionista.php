<?php
// app/views/layouts/main_recepcionista.php

if (session_status() === PHP_SESSION_NONE) session_start();
$roleName = $_SESSION['user']['role_name'] ?? '';
$roleAlt  = $_SESSION['user']['role'] ?? '';
if (empty($_SESSION['user']) || ($roleName !== 'recepcionista' && $roleAlt !== 'recepcionista')) {
  header('Location: /vetsmart/login');
  exit;
}

// Define el título de la página
$title = 'Panel del Recepcionista';

// Define el título del encabezado
$header_title = ' reception VetSmart';

// Define los enlaces de navegación para el recepcionista
$nav_links = [
    ['url' => '/vetsmart/recepcionista/dashboard', 'icon' => 'fas fa-home', 'text' => 'Dashboard'],
    ['url' => '/vetsmart/recepcionista/agenda', 'icon' => 'fas fa-calendar-alt', 'text' => 'Agenda'],
    ['url' => '/vetsmart/recepcionista/citas', 'icon' => 'fas fa-plus-circle', 'text' => 'Crear Cita'],
    ['url' => '/vetsmart/recepcionista/clientes', 'icon' => 'fas fa-users', 'text' => 'Gestión de Clientes'],
    ['url' => '/vetsmart/recepcionista/reportes', 'icon' => 'fas fa-chart-bar', 'text' => 'Reportes'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
