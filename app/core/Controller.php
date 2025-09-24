<?php
// app/core/Controller.php


class Controller {
    protected $db; // ✅ conexión PDO disponible en controladores hijos

    public function __construct($pdo = null)
    {
        $this->db = $pdo;
    }

    public function view(string $view, array $data = [], string $layout = 'main')
    {
        $viewFile = __DIR__ . "/../views/{$view}.php";

        ob_start();
        if (file_exists($viewFile)) {
            extract($data);
            require $viewFile;
        } else {
            echo "Vista {$view} no encontrada.";
        }
        $content = ob_get_clean();

        // Si el layout es null o vacío, mostrar solo el contenido
        if (empty($layout)) {
            echo $content;
            return;
        }

        $layoutFile = __DIR__ . "/../views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            extract($data);
            require $layoutFile;
        } else {
            echo "Layout {$layout} no encontrado.";
        }
    }
}
