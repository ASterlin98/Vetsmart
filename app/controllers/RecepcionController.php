<?php
class RecepcionController {

    public function dashboard() {
        // Renderizar el contenido del dashboard
        $content = $this->renderView('recepcionista/dashboard');
        
        // Incluir el layout con sidebar, header y footer
        include 'app/views/layouts/main_recepcionista.php';
    }

    private function renderView($view, $data = []) {
        extract($data); // Variables para la vista
        ob_start();
        include "app/views/$view.php"; // Cargar vista específica
        return ob_get_clean(); // Devolver el contenido como string
    }
}
