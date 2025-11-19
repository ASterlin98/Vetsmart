<?php
// app/views/layouts/main_superadmin.php

// Define el título de la página
$title = 'Panel del Super Admin';

// Define el título del encabezado
$header_title = '🛠️ VetSmart SuperAdmin';

// Define los enlaces de navegación para el superadmin
$nav_links = [
    ['url' => '/vetsmart/super_admin/dashboard', 'icon' => '🏠', 'text' => 'Dashboard'],
    ['url' => '/vetsmart/super_admin/permisos', 'icon' => '🔐', 'text' => 'Gestión de Permisos'],
    ['url' => '/vetsmart/super_admin/configuracion', 'icon' => '⚙️', 'text' => 'Configuración Global'],
    ['url' => '/vetsmart/super_admin/reportes', 'icon' => '📊', 'text' => 'Reportes'],
    ['url' => '/vetsmart/soporte', 'icon' => '🆘', 'text' => 'Centro de Soporte', 'badge' => $unseen_tickets ?? 0],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
