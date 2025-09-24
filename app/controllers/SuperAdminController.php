<?php

require_once '../app/models/Rol.php';
require_once '../app/models/Permiso.php';

class SuperAdminController extends Controller {

    private $rolModel;
    private $permisoModel;

    public function __construct($pdo) {
        parent::__construct($pdo); // ✅ CORRECTO: llama al constructor padre con el PDO

        $this->rolModel = new Rol($pdo);
        $this->permisoModel = new Permiso($pdo);
    }

    public function permisos()
        {
            $roles = $this->rolModel->getAllRoles();
            $permisosPorModulo = $this->permisoModel->getAllGroupedByModulo();

            // ✅ Renderiza con layout
            $this->view('super_admin/permisos_content', [
                'roles' => $roles,
                'permisosPorModulo' => $permisosPorModulo
            ], 'main_superadmin');
        }


    public function actualizarPermisos() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);

            $roleId = $data['role_id'] ?? null;
            $permisos = $data['permisos'] ?? [];

            if ($roleId === null) {
                http_response_code(400);
                echo json_encode(['error' => 'Falta el ID del rol']);
                exit;
            }

            $this->permisoModel->asignarPermisosARol($roleId, $permisos, $_SESSION['usuario_id'] ?? null);
            echo json_encode(['success' => true]);
        }
    }

    public function getPermisosPorRol() {
        $role_id = $_GET['role_id'] ?? null;
        if (!$role_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta role_id']);
            exit;
        }

        $asignados = $this->permisoModel->getPermisosPorRol($role_id);
        echo json_encode(['permisosAsignados' => $asignados]);
    }
}
