<?php
// app/views/layouts/main_veterinario.php

// Define el título de la página
$title = 'Panel del Veterinario';

// Define el título del encabezado
$header_title = '👨‍⚕️ VetSmart Veterinario';

// Define los enlaces de navegación para el veterinario
$nav_links = [
    ['url' => '/vetsmart/veterinario/dashboard', 'icon' => 'fas fa-home', 'text' => 'Dashboard'],
    ['url' => '/vetsmart/veterinario/agenda', 'icon' => 'fas fa-calendar-alt', 'text' => 'Mi Agenda'],
    ['url' => '/vetsmart/veterinario/citas', 'icon' => 'fas fa-stethoscope', 'text' => 'Consultas'],
    ['url' => '/vetsmart/veterinario/historial', 'icon' => 'fas fa-file-medical', 'text' => 'Historial Clínico'],
    ['url' => '/vetsmart/veterinario/reportes', 'icon' => 'fas fa-chart-bar', 'text' => 'Reportes'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';

// Incluye el modal específico para esta vista
$modalPath = __DIR__ . '/../veterinario/citas/_modal.php';
if (file_exists($modalPath)) {
    require $modalPath;
}
?>

<script>
    // Fix modal z-index
    (function(){
      const modal = document.getElementById('citaModal');
      if (modal) modal.classList.add('modal-root-high-z');
    })();
</script>
