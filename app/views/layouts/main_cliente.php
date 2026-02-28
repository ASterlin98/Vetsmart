<?php
// app/views/layouts/main_cliente.php

// Define el título de la página
$title = 'Panel del Cliente';

// Define el título del encabezado
$header_title = '🐾 VetSmart Cliente';

// Define los enlaces de navegación para el cliente
$nav_links = [
    ['url' => '<?= BASE ?>/cliente/dashboard', 'icon' => 'fas fa-home', 'text' => 'Dashboard'],
    ['url' => '<?= BASE ?>/cliente/citas', 'icon' => 'fas fa-calendar-alt', 'text' => 'Mis Citas'],
    ['url' => '<?= BASE ?>/cliente/mascotas', 'icon' => 'fas fa-paw', 'text' => 'Mis Mascotas'],
    ['url' => '<?= BASE ?>/cliente/historial', 'icon' => 'fas fa-file-medical', 'text' => 'Historial Clínico'],
    ['url' => '<?= BASE ?>/cliente/reportes', 'icon' => 'fas fa-chart-bar', 'text' => 'Reportes'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
