<?php
// app/views/layouts/main_admin.php

// Define el título de la página
$title = 'Panel del Administrador';

// Define el título del encabezado
$header_title = '💼 VetSmart Administrador';

// Define los enlaces de navegación para el administrador
$nav_links = [
    ['url' => BASE . '/admin/dashboard', 'icon' => 'fas fa-home', 'text' => 'Dashboard'],
    ['url' => BASE . '/admin/empleados', 'icon' => 'fas fa-users', 'text' => 'Gestión de Empleados'],
    ['url' => BASE . '/admin/agenda', 'icon' => 'fas fa-calendar-alt', 'text' => 'Agenda General'],
    ['url' => BASE . '/admin/horarios', 'icon' => 'fas fa-clock', 'text' => 'Gestión de Horarios'],
    ['url' => BASE . '/admin/clientes', 'icon' => 'fas fa-paw', 'text' => 'Gestión de Clientes'],
    ['url' => BASE . '/admin/servicios', 'icon' => 'fas fa-tools', 'text' => 'Gestión de Servicios'],
    ['url' => BASE . '/admin/finanzas', 'icon' => 'fas fa-money-bill-wave', 'text' => 'Finanzas'],
    ['url' => BASE . '/admin/reportes', 'icon' => 'fas fa-chart-bar', 'text' => 'Reportes'],
    ['url' => BASE . '/admin/soporte', 'icon' => 'fas fa-headset', 'text' => 'Soporte', 'badge' => $unseen_tickets_admin ?? 0],
    ['url' => BASE . '/admin/locked_users', 'icon' => 'fas fa-lock', 'text' => 'Usuarios Bloqueados'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
