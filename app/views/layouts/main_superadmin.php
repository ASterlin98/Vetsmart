<?php
// app/views/layouts/main_superadmin.php

// Define el título de la página
$title = 'Panel del Super Admin';

// Define el título del encabezado
$header_title = '🛠️ SuperAdmin VetSmart';

// Define los enlaces de navegación para el superadmin
$nav_links = [
    ['url' => '<?= BASE ?>/super_admin/dashboard', 'icon' => 'fas fa-home', 'text' => 'Dashboard'],
    ['url' => '<?= BASE ?>/super_admin/configuracion', 'icon' => 'fas fa-cog', 'text' => 'Configuración Global'],
    ['url' => '<?= BASE ?>/super_admin/reportes', 'icon' => 'fas fa-chart-bar', 'text' => 'Reportes'],
    ['url' => '<?= BASE ?>/soporte', 'icon' => 'fas fa-headset', 'text' => 'Centro de Soporte', 'badge' => $unseen_tickets ?? 0],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';
