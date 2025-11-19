<?php
// app/core/Controller.php

class Controller {
    protected $db;

    public function __construct($pdo = null)
    {
        $this->db = $pdo;
    }

    /**
     * Render a view.
     * If $layout is null or empty string, it will render the view only (no layout).
     *
     * @param string $view    path relative to app/views without .php
     * @param array  $data    variables to extract in view
     * @param string|null $layout layout name in app/views/layouts (or null/'' for no layout)
     */
    public function view(string $view, array $data = [], ?string $layout = 'main')
    {
        if ($this->db) {
            // Fetch unseen ticket count for superadmin
            if (isset($_SESSION['user']) && $_SESSION['user']['role_name'] === 'super_admin') {
                require_once APP_ROOT . '/models/Ticket.php';
                $ticketModel = new Ticket($this->db);
                $data['unseen_tickets'] = $ticketModel->countUnseen();
            }

            // Fetch unseen ticket count for admin
            if (isset($_SESSION['user']) && $_SESSION['user']['role_name'] === 'admin') {
                require_once APP_ROOT . '/models/Ticket.php';
                $ticketModel = new Ticket($this->db);
                $data['unseen_tickets_admin'] = $ticketModel->countUnseenForAdmin($_SESSION['user']['id']);
            }
        }

        $viewFile = __DIR__ . "/../views/{$view}.php";

        ob_start();
        if (file_exists($viewFile)) {
            extract($data);
            require $viewFile;
        } else {
            echo "Vista {$view} no encontrada.";
        }
        $content = ob_get_clean();

        // Si layout es null o vacío -> render parcial (solo la vista)
        if ($layout === null || $layout === '') {
            echo $content;
            return;
        }

        $layoutFile = __DIR__ . "/../views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            // el layout puede usar la variable $content
            extract($data);
            require $layoutFile;
        } else {
            echo "Layout {$layout} no encontrado.";
        }
    }
}

