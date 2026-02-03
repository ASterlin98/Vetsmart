<?php
require_once APP_ROOT . '/models/Cliente.php';
require_once APP_ROOT . '/models/Mascota.php';

class ClientesController extends Controller {

    private $clienteModel;
    private $mascotaModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->clienteModel = new Cliente($pdo);
        $this->mascotaModel = new Mascota($pdo);
    }

    public function index() {
        // Paginación: mostrar 8 clientes por página
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 8;
        $total = $this->clienteModel->countAll();
        $totalPages = (int)ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $clientes = $this->clienteModel->getPaginated($offset, $perPage);

        $this->view('admin/clientes/index', [
            'clientes' => $clientes,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages
        ], 'main_admin');
    }

    

    public function ver($id) {
        $cliente = $this->clienteModel->getById($id);
        $mascotas = $this->mascotaModel->getByDueno($id);
        $this->view('admin/clientes/ver', ['cliente' => $cliente, 'mascotas' => $mascotas], 'main_admin');
    }

    public function crear() {
        $this->view('admin/clientes/crear', [], 'main_admin');
    }

public function guardar() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /vetsmart/admin/clientes');
        exit;
    }

    $email = $_POST['email'] ?? '';
    $docusu = $_POST['docusu'] ?? '';

    $errors = $this->clienteModel->checkDuplicados($email, $docusu);

    if (!empty($errors)) {
        $this->view('admin/clientes/crear', [
            'error' => implode(' ', $errors),
            'cliente' => $_POST // Repopulate form
        ], 'main_admin');
        return;
    }

    try {
        $newId = $this->clienteModel->crear($_POST);
        header('Location: /vetsmart/admin/clientes');
        exit;
    } catch (Exception $e) {
        error_log('ClientesController::guardar error: ' . $e->getMessage());
        $this->view('admin/clientes/crear', [
            'error' => 'Error al guardar el cliente. Por favor, intente de nuevo.',
            'cliente' => $_POST
        ], 'main_admin');
    }
}


    public function verMascota($clienteId, $mascotaId) {
        $mascota = $this->mascotaModel->getById($mascotaId);
        $this->view('admin/clientes/mascotas/ver', [
            'cliente_id' => $clienteId,
            'mascota' => $mascota
        ], 'main_admin');
    }

    public function editar($id) {
        $cliente = $this->clienteModel->getById($id);
        if (!$cliente) {
            http_response_code(404);
            echo "Cliente no encontrado.";
            return;
        }
        $this->view('admin/clientes/editar', ['cliente' => $cliente], 'main_admin');
    }

    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $docusu = $_POST['docusu'] ?? '';

            $errors = $this->clienteModel->checkDuplicados($email, $docusu, (int)$id);

            if (!empty($errors)) {
                // Si hay errores, repoblar el formulario con los datos enviados,
                // manteniendo el ID original.
                $clienteData = $_POST;
                $clienteData['id'] = $id;
                $this->view('admin/clientes/editar', [
                    'error' => implode(' ', $errors),
                    'cliente' => $clienteData
                ], 'main_admin');
                return;
            }

            try {
                $this->clienteModel->actualizar($id, $_POST);
                header("Location: /vetsmart/admin/clientes");
            } catch (Exception $e) {
                error_log('ClientesController::actualizar error: ' . $e->getMessage());
                $cliente = $this->clienteModel->getById($id);
                $this->view('admin/clientes/editar', [
                    'error' => 'Error al actualizar el cliente.',
                    'cliente' => $cliente
                ], 'main_admin');
            }
        }
    }

    public function eliminar($id) {
        $this->clienteModel->eliminar($id);
        header('Location: /vetsmart/admin/clientes');
    }

    // Mascotas
    public function crearMascota($idCliente) {
        $this->view('admin/clientes/mascotas/crear', ['cliente_id' => $idCliente], 'main_admin');
    }

    public function guardarMascota($idCliente) {
        $this->mascotaModel->crear($idCliente, $_POST);
        header("Location: /vetsmart/admin/clientes/$idCliente");
    }

    public function editarMascota($idCliente, $idMascota) {
        $mascota = $this->mascotaModel->getById($idMascota);
        $this->view('admin/clientes/mascotas/editar', ['cliente_id' => $idCliente, 'mascota' => $mascota], 'main_admin');
    }

    public function eliminarMascota($idCliente, $idMascota) {
        $this->mascotaModel->eliminar($idMascota);
        header("Location: /vetsmart/admin/clientes/$idCliente");
    }

    public function actualizarMascota($idCliente, $idMascota) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->mascotaModel->actualizar($idMascota, $_POST);
        header("Location: /vetsmart/admin/clientes");
        exit;
    } else {
        http_response_code(405);
        echo "Método no permitido.";
        exit;
    }
}
}
