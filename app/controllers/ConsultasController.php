<?php
// app/controllers/ConsultasController.php
require_once APP_ROOT . '/models/Consulta.php';
require_once APP_ROOT . '/models/Mascota.php';

class ConsultasController extends Controller {
    private $consultaModel;
    private $mascotaModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->consultaModel = new Consulta($pdo);
        $this->mascotaModel = new Mascota($pdo);
    }

    public function index()
    {
        $veterinarioId = $_SESSION['user']['id']; // Asegúrate que el veterinario esté autenticado
        $consultas = $this->consultaModel->getByVeterinario($veterinarioId);

        $this->view('veterinario/consultas/index', [
            'consultas' => $consultas
        ], 'main_veterinario');
    }

    public function crear($mascota_id = null) {
        $mascota = null;
        if ($mascota_id) $mascota = $this->mascotaModel->getById($mascota_id);
        $this->view('veterinario/consultas/crear', ['mascota' => $mascota], 'main_veterinario');
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vetsmart/veterinario/consultas');
            exit;
        }
        $data = [
            'mascota_id' => $_POST['mascota_id'] ?? null,
            'empleado_id' => $_SESSION['user']['id'] ?? null,
            'motivo' => $_POST['motivo'] ?? null,
            'examen' => $_POST['examen'] ?? null,
            'diagnostico' => $_POST['diagnostico'] ?? null,
            'tratamiento' => $_POST['tratamiento'] ?? null,
            'recomendaciones' => $_POST['recomendaciones'] ?? null,
            'notas' => $_POST['notas'] ?? null,
            'creado_por' => $_SESSION['user']['id'] ?? null
        ];

        if (empty($data['mascota_id'])) {
            $_SESSION['flash_error'] = 'Debe seleccionar una mascota.';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/vetsmart/veterinario/consultas'));
            exit;
        }

        $id = $this->consultaModel->crear($data);
        header('Location: /vetsmart/veterinario/consultas/ver/' . $id);
        exit;
    }

    public function ver($id) {
        $consulta = $this->consultaModel->getById($id);
        if (!$consulta) {
            echo "Consulta no encontrada.";
            return;
        }
        $this->view('veterinario/consultas/ver', ['consulta' => $consulta], 'main_veterinario');
    }

    public function editar($id) {
        $consulta = $this->consultaModel->getById($id);
        if (!$consulta) {
            echo "Consulta no encontrada.";
            return;
        }
        $this->view('veterinario/consultas/editar', ['consulta' => $consulta], 'main_veterinario');
    }

    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vetsmart/veterinario/consultas');
            exit;
        }
        $data = [
            'motivo' => $_POST['motivo'] ?? null,
            'examen' => $_POST['examen'] ?? null,
            'diagnostico' => $_POST['diagnostico'] ?? null,
            'tratamiento' => $_POST['tratamiento'] ?? null,
            'recomendaciones' => $_POST['recomendaciones'] ?? null,
            'notas' => $_POST['notas'] ?? null
        ];
        $this->consultaModel->actualizar($id, $data);
        header('Location: /vetsmart/veterinario/consultas/ver/' . $id);
        exit;
    }

    public function eliminar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->consultaModel->eliminar($id);
            header('Location: /vetsmart/veterinario/consultas');
            exit;
        }
        header('Location: /vetsmart/veterinario/consultas');
        exit;
    }
}
