<?php
require_once __DIR__ . '/../core/Controller.php';

class DashboardController extends Controller {
    public function index() {
        if (empty($_SESSION['user'])) {
            header("Location: ' . BASE . '/login");
            exit;
        }

        $role = $_SESSION['user']['role_name'] ?? null;

        switch ($role) {
            case 'recepcionista':
                $this->view("recepcionista/dashboard", [], "main_recepcionista");
                break;
            case 'veterinario':
                $this->view("veterinario/dashboard", [], "main_veterinario");
                break;
            case 'cliente':
                $this->view("cliente/dashboard", [], "main_cliente");
                break;
            case 'peluquero':
                $this->view("peluquero/dashboard", [], "main_peluquero");
                break;
            case 'admin':
                $this->view("admin/dashboard", [], "main_admin");
                break;
            case 'super_admin':
                $this->view("super_admin/index", [], "main_superadmin");
                break;
            default:
                echo "Rol no reconocido.";
        }
    }
}
