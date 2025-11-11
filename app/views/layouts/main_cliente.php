<?php
// app/views/layouts/main_cliente.php

// Define el título de la página
$title = 'Panel del Cliente';

// Define el título del encabezado
$header_title = '🐾 VetSmart Cliente';

// Define los enlaces de navegación para el cliente
$nav_links = [
    ['url' => '/vetsmart/cliente/dashboard', 'icon' => '<i class="fas fa-home"></i>', 'text' => 'Dashboard'],
    ['url' => '/vetsmart/cliente/citas', 'icon' => '<i class="fas fa-calendar-alt"></i>', 'text' => 'Mis Citas'],
    ['url' => '/vetsmart/cliente/mascotas', 'icon' => '<i class="fas fa-paw"></i>', 'text' => 'Mis Mascotas'],
    ['url' => '/vetsmart/cliente/historial', 'icon' => '<i class="fas fa-file-medical"></i>', 'text' => 'Historial Clínico'],
    ['url' => '/vetsmart/cliente/reportes', 'icon' => '<i class="fas fa-chart-bar"></i>', 'text' => 'Reportes'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
