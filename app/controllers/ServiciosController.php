<?php
require_once APP_ROOT . '/models/Servicio.php';

class ServiciosController extends Controller {
    private $servicioModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->servicioModel = new Servicio($pdo);
    }

    public function index() {
        $servicios = $this->servicioModel->getAll();
        $this->view('admin/servicios/index', ['servicios' => $servicios], 'main_admin');
    }

    public function crear() {
        $this->view('admin/servicios/crear', [], 'main_admin');
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->servicioModel->crear($_POST);
            header('Location: /vetsmart/admin/servicios');
        }
    }

    public function editar($id) {
        $servicio = $this->servicioModel->getById($id);
        $this->view('admin/servicios/editar', ['servicio' => $servicio], 'main_admin');
    }

    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->servicioModel->actualizar($id, $_POST);
            header('Location: /vetsmart/admin/servicios');
        }
    }

    public function eliminar($id) {
        $this->servicioModel->eliminar($id);
        header('Location: /vetsmart/admin/servicios');
    }
}
