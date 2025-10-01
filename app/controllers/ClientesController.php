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
        $clientes = $this->clienteModel->getAll();
        $this->view('admin/clientes/index', ['clientes' => $clientes], 'main_admin');
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

    // Detectar AJAX
    $isAjax = (
        (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
    );

    try {
        // intentar crear y obtener ID del nuevo cliente
        $newId = null;
        if (method_exists($this->clienteModel, 'crear')) {
            // suponemos que crear() devuelve lastInsertId o al menos inserta
            $maybe = $this->clienteModel->crear($_POST);
            if ($maybe) $newId = $maybe;
        }

        // fallback a lastInsertId si no fue devuelto por el modelo
        if (empty($newId) && isset($this->db) && $this->db instanceof PDO) {
            $newId = $this->db->lastInsertId();
        }

        // obtener el registro creado para devolver al front (si es posible)
        $cliente = null;
        if ($newId) {
            try {
                $cliente = $this->clienteModel->getById($newId);
            } catch (Exception $e) {
                // ignore: devolvemos lo mínimo
                $cliente = null;
            }
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'id' => $newId,
                'cliente' => $cliente
            ]);
            exit;
        } else {
            header('Location: /vetsmart/admin/clientes');
            exit;
        }
    } catch (Exception $e) {
        // log para debugging
        error_log('ClientesController::guardar error: ' . $e->getMessage());
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8', true, 500);
            echo json_encode(['success' => false, 'message' => 'Error guardando cliente.']);
            exit;
        } else {
            $this->view('admin/clientes/crear', ['error' => $e->getMessage()], 'main_admin');
            exit;
        }
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
            $this->clienteModel->actualizar($id, $_POST);
            header("Location: /vetsmart/admin/clientes/$id");
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
}
