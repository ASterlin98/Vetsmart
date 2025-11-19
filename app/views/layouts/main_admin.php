<?php
// app/views/layouts/main_admin.php

// Define el título de la página
$title = 'Panel del Administrador';

// Define el título del encabezado
$header_title = '💼 VetSmart Administrador';

// Define los enlaces de navegación para el administrador
$nav_links = [
    ['url' => '/vetsmart/admin/dashboard', 'icon' => '🏠', 'text' => 'Dashboard'],
    ['url' => '/vetsmart/admin/empleados', 'icon' => '👥', 'text' => 'Gestión de Empleados'],
    ['url' => '/vetsmart/admin/agenda', 'icon' => '📅', 'text' => 'Agenda General'],
    ['url' => '/vetsmart/admin/horarios', 'icon' => '⏰', 'text' => 'Gestión de Horarios'],
    ['url' => '/vetsmart/admin/clientes', 'icon' => '🐶', 'text' => 'Gestión de Clientes'],
    ['url' => '/vetsmart/admin/servicios', 'icon' => '🛠️', 'text' => 'Gestión de Servicios'],
    ['url' => '/vetsmart/admin/finanzas', 'icon' => '💰', 'text' => 'Finanzas'],
    ['url' => '/vetsmart/admin/reportes', 'icon' => '📊', 'text' => 'Reportes'],
    ['url' => '/vetsmart/admin/soporte', 'icon' => '🆘', 'text' => 'Soporte', 'badge' => $unseen_tickets_admin ?? 0],
    ['url' => '/vetsmart/admin/locked_users', 'icon' => '🔒', 'text' => 'Usuarios Bloqueados'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
