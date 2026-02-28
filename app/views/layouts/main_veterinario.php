<?php
// app/views/layouts/main_veterinario.php

// Define el título de la página
$title = 'Panel del Veterinario';

// Define el título del encabezado
$header_title = '👨‍⚕️ Veterinario VetSmart';

// Define los enlaces de navegación para el veterinario
$nav_links = [
    ['url' => BASE . '/veterinario/dashboard', 'icon' => 'fas fa-home', 'text' => 'Dashboard'],
    ['url' => BASE . '/veterinario/agenda', 'icon' => 'fas fa-calendar-alt', 'text' => 'Mi Agenda'],
    ['url' => BASE . '/veterinario/consultas', 'icon' => 'fas fa-file-medical', 'text' => 'Estado'],
    ['url' => BASE . '/veterinario/reportes', 'icon' => 'fas fa-chart-bar', 'text' => 'Reportes'],
];

// Incluye el layout unificado
require __DIR__ . '/unified_layout.php';

// Incluye el modal específico para esta vista
$modalPath = __DIR__ . '/../veterinario/pacientes/_modal.php';
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
