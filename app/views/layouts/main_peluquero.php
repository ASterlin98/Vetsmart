<?php
// app/views/layouts/main_peluquero.php

// Define el título de la página
$title = 'Panel del Peluquero';

// Define el título del encabezado
$header_title = '✂️ VetSmart Peluquero';

// Define los enlaces de navegación para el peluquero
$nav_links = [
    ['url' => BASE . '/peluquero/dashboard', 'icon' => 'fas fa-home', 'text' => 'Dashboard'],
    ['url' => BASE . '/peluquero/agenda', 'icon' => 'fas fa-calendar-alt', 'text' => 'Mi Agenda'],
    ['url' => BASE . '/peluquero/citas', 'icon' => 'fas fa-cut', 'text' => 'Citas de Peluquería'],
    ['url' => BASE . '/peluquero/reportes', 'icon' => 'fas fa-chart-bar', 'text' => 'Reportes'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
