<?php
// app/core/Controller.php

class Controller {
    protected $db; // ✅ conexión PDO disponible en controladores hijos

    public function __construct($pdo = null)
    {
        $this->db = $pdo;
    }

    // Renderiza una vista dentro de un layout
    public function view(string $view, array $data = [], string $layout = 'main')
    {
        $viewFile = __DIR__ . "/../views/{$view}.php";

        // Captura el contenido de la vista
        ob_start();
        if (file_exists($viewFile)) {
            extract($data); // variables disponibles dentro de la vista
            require $viewFile;
        } else {
            echo "Vista {$view} no encontrada.";
        }
        $content = ob_get_clean();

        // Extrae de nuevo para el layout
        extract($data);

        $layoutFile = __DIR__ . "/../views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            require $layoutFile; // aquí ya tendrás $content y $user disponibles
        } else {
            echo "Layout {$layout} no encontrado.";
        }
    }
}
