<?php
// app/views/layouts/main_peluquero.php

// Define el título de la página
$title = 'Panel del Peluquero';

// Define el título del encabezado
$header_title = '✂️ VetSmart Peluquero';

// Define los enlaces de navegación para el peluquero
$nav_links = [
    ['url' => '/vetsmart/peluquero/dashboard', 'icon' => '<i class="fas fa-home"></i>', 'text' => 'Dashboard'],
    ['url' => '/vetsmart/peluquero/agenda', 'icon' => '<i class="fas fa-calendar-alt"></i>', 'text' => 'Mi Agenda'],
    ['url' => '/vetsmart/peluquero/citas', 'icon' => '<i class="fas fa-cut"></i>', 'text' => 'Citas de Peluquería'],
    ['url' => '/vetsmart/peluquero/reportes', 'icon' => '<i class="fas fa-chart-bar"></i>', 'text' => 'Reportes'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
