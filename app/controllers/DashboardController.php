<?php
require_once __DIR__ . '/../core/Controller.php';

class DashboardController extends Controller {
    public function index() {
        if (empty($_SESSION['user'])) {
            header("Location: /vetsmart/login");
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
                // Redirigir al dashboard de admin, que ahora tendrá su propia lógica
                header("Location: /vetsmart/admin/dashboard");
                exit;
            case 'super_admin':
                // Redirigir al dashboard de superadmin
                header("Location: /vetsmart/superadmin/dashboard");
                exit;
            default:
                echo "Rol no reconocido.";
        }
    }
}
