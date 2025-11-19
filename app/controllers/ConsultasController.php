<?php
// app/controllers/ConsultasController.php
require_once APP_ROOT . '/models/Consulta.php';
require_once APP_ROOT . '/models/Mascota.php';
require_once APP_ROOT . '/models/Cita.php';

class ConsultasController extends Controller {
    private $consultaModel;
    private $mascotaModel;
    private $citaModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->consultaModel = new Consulta($pdo);
        $this->mascotaModel = new Mascota($pdo);
        $this->citaModel = new Cita($pdo);
    }

    public function index()
    {
        $veterinarioId = $_SESSION['user']['id']; // Asegúrate que el veterinario esté autenticado
        $consultas = $this->consultaModel->getByVeterinario($veterinarioId);
        // Obtener también las citas del veterinario para poder crear consultas desde una cita
        try {
            $citas = $this->citaModel->getPorVeterinario($veterinarioId);
        } catch (Exception $e) {
            error_log('Error obteniendo citas para consultas: ' . $e->getMessage());
            $citas = [];
        }
        // Obtener mascotas que ya tienen citas asociadas a este veterinario (cualquier estado)
        try {
            $sql = "SELECT m.*, u.nombre AS nombre_dueno, u.apellido AS apellido_dueno, COALESCE(cd.telefono, u.telefono) AS telefono_dueno, MAX(c.fecha) AS last_cita
                    FROM mascotas m
                    LEFT JOIN usuarios u ON m.dueno_id = u.id
                    LEFT JOIN cliente_detalles cd ON cd.idusu = u.id
                    JOIN citas c ON c.mascota_id = m.id
                    WHERE c.empleado_id = :vet
                    GROUP BY m.id
                    ORDER BY last_cita DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':vet' => $veterinarioId]);
            $mascotas_con_citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error obteniendo mascotas con citas para el veterinario: ' . $e->getMessage());
            $mascotas_con_citas = [];
        }

        // Para mantener compatibilidad con la vista, exponer como 'mascotas'
        $mascotas = $mascotas_con_citas ?? [];

        $this->view('veterinario/consultas/index', [
            'consultas' => $consultas,
            'citas' => $citas,
            'mascotas' => $mascotas
        ], 'main_veterinario');
    }

    public function crear($mascota_id = null) {
        $mascota = null;
        if ($mascota_id) $mascota = $this->mascotaModel->getById($mascota_id);
        // Si viene cita_id en query string, intentar precargar datos desde la cita
        $cita = null;
        $citaId = $_GET['cita_id'] ?? $_GET['cita'] ?? null;
        if ($citaId && method_exists($this->citaModel, 'getById')) {
            try {
                $cita = $this->citaModel->getById((int)$citaId);
                // intentar obtener nombre de servicio si existe
                if ($cita && !empty($cita['servicio_id'])) {
                    $stmt = $this->db->prepare('SELECT nombre FROM servicios WHERE id = :id LIMIT 1');
                    $stmt->execute([':id' => $cita['servicio_id']]);
                    $r = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($r) $cita['servicio_nombre'] = $r['nombre'];
                }
            } catch (Exception $e) {
                error_log('Error cargando cita para precarga de consulta: ' . $e->getMessage());
                $cita = null;
            }
        }

        $this->view('veterinario/consultas/crear', ['mascota' => $mascota, 'cita' => $cita], 'main_veterinario');
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
    $consultaModel = new Consulta($this->db);
    $consulta = $consultaModel->getById($id);

    if (!$consulta) {
        die('Consulta no encontrada.');
    }

    $this->view('veterinario/consultas/ver', ['consulta' => $consulta], 'main_veterinario');
}


public function editar($id) {
    $consultaModel = new Consulta($this->db);
    $consulta = $consultaModel->getById($id);

    if (!$consulta) {
        die('Consulta no encontrada.');
    }

    $this->view('veterinario/consultas/editar', ['consulta' => $consulta], 'main_veterinario');
}


public function actualizar($id)
{
    // Detectar si es AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if ($isAjax) {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        } else {
            $_SESSION['flash_error'] = 'Método no permitido.';
            header('Location: /vetsmart/veterinario/consultas');
        }
        exit;
    }

    $consultaModel = new Consulta($this->db);

    $data = [
        'motivo' => $_POST['motivo'] ?? null,
        'examen' => $_POST['examen'] ?? null,
        'diagnostico' => $_POST['diagnostico'] ?? null,
        'tratamiento' => $_POST['tratamiento'] ?? null,
        'recomendaciones' => $_POST['recomendaciones'] ?? null,
        'notas' => $_POST['notas'] ?? null,
    ];

    try {
        $consultaModel->actualizar($id, $data);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Consulta actualizada correctamente.']);
        } else {
            $_SESSION['flash_success'] = 'Consulta actualizada correctamente.';
            header('Location: /vetsmart/veterinario/consultas/ver/' . $id);
        }
    } catch (Exception $e) {
        error_log("Error al actualizar consulta: " . $e->getMessage());
        if ($isAjax) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la consulta.']);
        } else {
            $_SESSION['flash_error'] = 'Error actualizando la consulta.';
            header('Location: /vetsmart/veterinario/consultas/editar/' . $id);
        }
    }

    exit;
}


public function eliminar($id) {
    $consultaModel = new Consulta($this->db);
    $consultaModel->eliminar($id);
    
    $_SESSION['flash_success'] = 'Consulta eliminada correctamente.';
    header('Location: /vetsmart/veterinario/consultas');
    exit;
}

}
