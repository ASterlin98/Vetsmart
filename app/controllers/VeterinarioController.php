<?php
// app/controllers/VeterinarioController.php

require_once APP_ROOT . '/models/Mascota.php';
require_once APP_ROOT . '/models/Cliente.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Servicio.php';
require_once APP_ROOT . '/models/Vacuna.php';
require_once APP_ROOT . '/models/NotaMascota.php';
use Dompdf\Dompdf;
class VeterinarioController extends Controller
{
    private $mascotaModel;
    private $clienteModel;
    private $citaModel;
    private $servicioModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->mascotaModel = new Mascota($pdo);
        $this->clienteModel = new Cliente($pdo);
        $this->citaModel = new Cita($pdo);
        $this->servicioModel = new Servicio($pdo);
        $this->notaMascotaModel = new NotaMascota($pdo);
    }

    // Mostrar todas las mascotas con su propietario
    public function pacientes()
    {
        // si quieres búsqueda por query string (opcional)
        $q = trim($_GET['q'] ?? '');

        if ($q !== '') {
            $mascotas = $this->mascotaModel->searchWithOwner($q);
        } else {
            $mascotas = $this->mascotaModel->getTodasConDueño();
        }

        // renderiza la vista; layout ya lo tienes main_veterinario
        $this->view('veterinario/pacientes/index', [
            'mascotas' => $mascotas,
            'q' => $q
        ], 'main_veterinario');
    }

    // Mostrar historial clínico de una mascota
public function verHistorial($idMascota) {
    $mascota = $this->mascotaModel->getByIdConDueno($idMascota);
    $citas = $this->citaModel->getPorMascota($idMascota);
    $notasRapidas = $this->notaMascotaModel->obtenerPorMascota($idMascota); // ✅

    $this->view('veterinario/mascotas/historial', [
        'mascota' => $mascota,
        'citas' => $citas,
        'notasRapidas' => $notasRapidas
    ], 'main_veterinario');
}


    // Mostrar formulario de agendar (preselección por mascota)
// app/controllers/VeterinarioController.php (dentro de la clase)
public function agendar($idMascota)
{
    $servicios = $this->servicioModel->getAllActivos();

    // intentar obtener la mascota y su dueño para pasar cliente_id al form
    $mascota = null;
    try {
        if (method_exists($this->mascotaModel, 'getById')) {
            $mascota = $this->mascotaModel->getById($idMascota);
        } else {
            // fallback: consulta directa si el modelo no existe con getById
            $stmt = $this->db->prepare("SELECT * FROM mascotas WHERE id = ?");
            $stmt->execute([(int)$idMascota]);
            $mascota = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        error_log("Error cargando mascota para agendar: " . $e->getMessage());
    }

    $this->view('veterinario/mascotas/agendar_cita', [
        'id_mascota' => $idMascota,
        'servicios'  => $servicios,
        'mascota'    => $mascota
    ], 'main_veterinario');
}


    // Guardar cita (desde modal o formulario)
 public function guardarCita()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($base === '' || $base === '.') $base = '';
        header('Location: ' . $base . '/veterinario/mis-citas');
        exit;
    }

    // detectar AJAX
    $isAjax = (
        (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
    );

    // prevenir que warnings rompan JSON
    ini_set('display_errors', 0);

    $cliente_id   = $_POST['cliente_id'] ?? null;
    $mascota_id   = $_POST['mascota_id'] ?? null;
    $servicio_id  = $_POST['servicio_id'] ?? null;
    $empleado_id  = $_POST['empleado_id'] ?? ($_SESSION['user']['id'] ?? null);
    $notas        = $_POST['notas'] ?? null;

    // Fecha: aceptar datetime-local o fecha+hora separados
    if (!empty($_POST['fecha'])) {
        $fechaHora = date('Y-m-d H:i:s', strtotime($_POST['fecha']));
    } elseif (!empty($_POST['fecha_date']) && !empty($_POST['hora_time'])) {
        $fechaHora = date('Y-m-d H:i:s', strtotime($_POST['fecha_date'] . ' ' . $_POST['hora_time']));
    } else {
        $fechaHora = null;
    }

    // Si no viene cliente_id, intentar obtenerlo desde la mascota
    if (empty($cliente_id) && !empty($mascota_id)) {
        try {
            $masc = null;
            if (method_exists($this->mascotaModel, 'getById')) {
                $masc = $this->mascotaModel->getById($mascota_id);
            } else {
                $stmt = $this->db->prepare("SELECT * FROM mascotas WHERE id = ?");
                $stmt->execute([(int)$mascota_id]);
                $masc = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            if ($masc) {
                // posibles nombres de columna en tu BD
                $cliente_id = $masc['cliente_id'] ?? $masc['dueño_id'] ?? $masc['dueno_id'] ?? $masc['owner_id'] ?? $cliente_id;
            }
        } catch (Exception $e) {
            error_log("guardarCita: no se pudo obtener mascota: " . $e->getMessage());
        }
    }

    // validación mínima
    if (empty($mascota_id) || empty($servicio_id) || empty($fechaHora)) {
        $msg = 'Faltan campos obligatorios (mascota, servicio o fecha).';
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8', true, 400);
            echo json_encode(['success' => false, 'message' => $msg]);
            exit;
        } else {
            $_SESSION['flash_error'] = $msg;
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            if ($base === '' || $base === '.') $base = '';
            header('Location: ' . $base . '/veterinario/mascotas/' . (int)$mascota_id . '/agendar');
            exit;
        }
    }

    // preparar data para el modelo
    $data = [
        'cliente_id'   => $cliente_id,
        'mascota_id'   => $mascota_id,
        'empleado_id'  => $empleado_id,
        'servicio_id'  => $servicio_id,
        'fecha'        => $fechaHora,
        'duracion_min' => $_POST['duracion_min'] ?? null, // opcional
        'estado'       => $_POST['estado'] ?? 'programada',
        'notas'        => $notas,
        'creado_por'   => $_SESSION['user']['id'] ?? null
    ];

    try {
        if (method_exists($this->citaModel, 'crear')) {
            $newId = $this->citaModel->crear($data);
            if (empty($newId) && isset($this->db) && $this->db instanceof PDO) {
                $newId = $this->db->lastInsertId();
            }
        } else {
            $stmt = $this->db->prepare("INSERT INTO citas (cliente_id, mascota_id, empleado_id, servicio_id, fecha, duracion_min, estado, notas, creado_por, creado_en) VALUES (:cliente_id, :mascota_id, :empleado_id, :servicio_id, :fecha, :duracion_min, :estado, :notas, :creado_por, NOW())");
            $stmt->execute([
                ':cliente_id'   => $data['cliente_id'],
                ':mascota_id'   => $data['mascota_id'],
                ':empleado_id'  => $data['empleado_id'],
                ':servicio_id'  => $data['servicio_id'],
                ':fecha'        => $data['fecha'],
                ':duracion_min' => $data['duracion_min'],
                ':estado'       => $data['estado'],
                ':notas'        => $data['notas'],
                ':creado_por'   => $data['creado_por']
            ]);
            $newId = $this->db->lastInsertId();
        }

        // RESPUESTA
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'id' => $newId]);
            exit;
        } else {
            $_SESSION['flash_success'] = 'Cita creada correctamente.';
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            if ($base === '' || $base === '.') $base = '';
            header('Location: ' . $base . '/veterinario/mis-citas');
            exit;
        }

    } catch (PDOException $e) {
        error_log("ERROR guardarCita: " . $e->getMessage() . " | SQLSTATE: " . $e->getCode());
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8', true, 500);
            echo json_encode(['success' => false, 'message' => 'Error interno al guardar la cita.']);
            exit;
        } else {
            $_SESSION['flash_error'] = 'Error guardando la cita (revise logs).';
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            if ($base === '' || $base === '.') $base = '';
            header('Location: ' . $base . '/veterinario/mascotas/' . (int)$mascota_id . '/agendar');
            exit;
        }
    }
}


    // Calendario / Mis citas del veterinario
public function misCitas()
{
    $veterinarioId = $_SESSION['user']['id'] ?? null;
    $citas = $this->citaModel->getPorVeterinario($veterinarioId);
    $clientes = $this->clienteModel->getAll();
    $servicios = $this->servicioModel->getAllActivos();
    $this->view('veterinario/citas/index', [
        'citas' => $citas,
        'clientes' => $clientes,
        'servicios' => $servicios
    ], 'main_veterinario');
}

    // (Opcionales) actualizar / eliminar métodos mínimos
    public function actualizarCita()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /veterinario/mis-citas');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID requerido']);
            exit;
        }

        // leer campos similares a guardar
        $cliente_id = $_POST['cliente_id'] ?? null;
        $mascota_id = $_POST['mascota_id'] ?? null;
        $servicio_id = $_POST['servicio_id'] ?? null;
        $empleado_id = $_POST['empleado_id'] ?? ($_SESSION['user']['id'] ?? null);
        $notas = $_POST['notas'] ?? null;

        if (!empty($_POST['fecha'])) {
            $fechaHora = date('Y-m-d H:i:s', strtotime($_POST['fecha']));
        } elseif (!empty($_POST['fecha_date']) && !empty($_POST['hora_time'])) {
            $fechaHora = date('Y-m-d H:i:s', strtotime($_POST['fecha_date'] . ' ' . $_POST['hora_time']));
        } else {
            $fechaHora = null;
        }

        $data = [];
        if ($cliente_id !== null) $data['cliente_id'] = $cliente_id;
        if ($mascota_id !== null) $data['mascota_id'] = $mascota_id;
        if ($servicio_id !== null) $data['servicio_id'] = $servicio_id;
        if ($empleado_id !== null) $data['empleado_id'] = $empleado_id;
        if ($fechaHora !== null) $data['fecha'] = $fechaHora;
        if ($notas !== null) $data['notas'] = $notas;

        if (empty($data)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Nada para actualizar']);
            exit;
        }

        try {
            if (method_exists($this->citaModel, 'actualizar')) {
                $this->citaModel->actualizar($id, $data);
            } else {
                // construir SET dinámico
                $sets = [];
                $params = [':id' => $id];
                foreach ($data as $k => $v) {
                    $sets[] = "{$k} = :{$k}";
                    $params[":{$k}"] = $v;
                }
                $sql = "UPDATE citas SET " . implode(', ', $sets) . " WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            error_log("ERROR actualizarCita: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error DB']);
        }
        exit;
    }

    // Eliminar cita por id (acepta GET o POST)
    public function eliminarCitaAjax($id = null)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id === null) {
        $id = $_POST['id'] ?? null;
    }

    if (!$id) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID requerido']);
        exit;
    }

    try {
        if (method_exists($this->citaModel, 'eliminar')) {
            $this->citaModel->eliminar($id);
        } else {
            $stmt = $this->db->prepare("DELETE FROM citas WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }

        // AJAX detection
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        } else {
            header('Location: /veterinario/mis-citas');
            exit;
        }

    } catch (PDOException $e) {
        error_log("ERROR eliminarCitaAjax: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error DB']);
        exit;
    }
}


    public function listarCitasJson()
    {
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        try {
            $sql = "
                SELECT ci.*,
                    m.nombre AS nombre_mascota,
                    u_cliente.nombre AS cliente_nombre, u_cliente.apellido AS cliente_apellido,
                    s.nombre AS nombre_servicio
                FROM citas ci
                LEFT JOIN mascotas m ON ci.mascota_id = m.id
                LEFT JOIN usuarios u_cliente ON ci.cliente_id = u_cliente.id
                LEFT JOIN servicios s ON ci.servicio_id = s.id
            ";

            if ($start && $end) {
                $sql .= " WHERE DATE(ci.fecha) BETWEEN :start AND :end ";
                $sql .= " ORDER BY ci.fecha ASC ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':start' => $start, ':end' => $end]);
            } else {
                $sql .= " ORDER BY ci.fecha ASC ";
                $stmt = $this->db->query($sql);
            }

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Map a formato FullCalendar (start = fecha)
            $out = array_map(function($r){
                return [
                    'id' => $r['id'],
                    'title' => ($r['nombre_mascota'] ?? 'Cita') . (isset($r['nombre_servicio']) ? " — {$r['nombre_servicio']}" : ''),
                    'start' => $r['fecha'],
                    'allDay' => false,
                    // incluir propiedades extendidas para el modal
                    'nombre_mascota' => $r['nombre_mascota'] ?? null,
                    'mascota_id' => $r['mascota_id'] ?? null,
                    'cliente_id' => $r['cliente_id'] ?? null,
                    'servicio_id' => $r['servicio_id'] ?? null,
                    'notas' => $r['notas'] ?? null,
                    'nombre_servicio' => $r['nombre_servicio'] ?? null,
                    'estado' => $r['estado'] ?? 'programada',
                ];
            }, $rows);

            header('Content-Type: application/json');
            echo json_encode($out);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error DB', 'msg' => $e->getMessage()]);
        }
        exit;
    }

    public function dashboard()
    {
        $vetId = $_SESSION['user']['id'] ?? null;
        if (!$vetId) {
            header('Location: /vetsmart/login');
            exit;
        }

        // métricas generales
        $totalMascotas = $this->mascotaModel->countAll();
        $totalClientes = $this->clienteModel->countAll();

        // Citas relacionadas con este veterinario
        $upcomingCount = $this->citaModel->countUpcomingByVeterinario($vetId);
        $todayAppointments = $this->citaModel->getTodayByVeterinario($vetId);
        $upcomingList = $this->citaModel->getUpcomingByVeterinario($vetId, 8);

        // Mascotas asignadas a este veterinario (según existencia de citas)
        $assignedMascotas = $this->mascotaModel->getAssignedToVeterinario($vetId, 8);

        $this->view('veterinario/dashboard', [
            'totalMascotas'    => $totalMascotas,
            'totalClientes'    => $totalClientes,
            'upcomingCount'    => $upcomingCount,
            'todayAppointments'=> $todayAppointments,
            'upcomingList'     => $upcomingList,
            'assignedMascotas' => $assignedMascotas
        ], 'main_veterinario');
    }

    public function vacunas($idMascota) {
        $vacunaModel = new Vacuna($this->db);
        $vacunas = $vacunaModel->getPorMascota($idMascota);
        $mascota = $this->mascotaModel->getById($idMascota);

        $this->view('veterinario/mascotas/vacunas/index', [
            'vacunas' => $vacunas,
            'mascota' => $mascota
        ], 'main_veterinario');
    }

    public function guardarVacuna() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vacunaModel = new Vacuna($this->db);
            $vacunaModel->crear($_POST);
            header("Location: /vetsmart/veterinario/mascotas/{$_POST['mascota_id']}/vacunas");
            exit;
        }
    }

    public function eliminarVacuna($id, $mascota_id) {
        $vacunaModel = new Vacuna($this->db);
        $vacunaModel->eliminar($id);
        header("Location: /vetsmart/veterinario/mascotas/{$mascota_id}/vacunas");
        exit;
    }

    public function editarVacuna($idMascota, $idVacuna)
    {
        $vacunaModel = new Vacuna($this->db);
        $vacuna = $vacunaModel->getById($idVacuna);
        $mascota = $this->mascotaModel->getById($idMascota);

        if (!$vacuna || !$mascota) {
            die("Vacuna o mascota no encontrada.");
        }

        $this->view('veterinario/mascotas/vacunas/editar', [
            'vacuna' => $vacuna,
            'mascota' => $mascota
        ], 'main_veterinario');
    }

    public function actualizarVacuna($idMascota, $idVacuna)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vacunaModel = new Vacuna($this->db);
            $vacunaModel->actualizar($idVacuna, [
                'nombre' => $_POST['nombre'],
                'fecha_aplicacion' => $_POST['fecha'],
                'proxima_dosis' => $_POST['proxima'] ?? null,
                'descripcion' => $_POST['descripcion']
            ]);
            header("Location: /vetsmart/veterinario/mascotas/$idMascota/vacunas");
            exit;
        }
    }

    public function reportes() {

        $idVet = $_SESSION['user']['id'];

        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;

        $consultaModel = new Consulta($this->db);
        $citaModel = new Cita($this->db);

        $consultas = $consultaModel->getByVeterinario($idVet, $desde, $hasta);
        $citas = $citaModel->getHistorialPorVeterinario($idVet, $desde, $hasta);

        $this->view('veterinario/reportes', [
            'consultas' => $consultas,
            'citas' => $citas
        ], 'main_veterinario');
    }

    public function exportar()
{
    // Cargar autoload si Dompdf no está disponible
    if (!class_exists(\Dompdf\Dompdf::class)) {
        require_once APP_ROOT . '/../vendor/autoload.php';
    }

    // Capturar POST
    $desde = $_POST['desde'] ?? '';
    $hasta = $_POST['hasta'] ?? '';

    if (empty($desde) || empty($hasta)) {
        header('Location: /vetsmart/veterinario/reportes?error=seleccione_fechas');
        exit;
    }

    $desde_dt = date('Y-m-d', strtotime($desde));
    $hasta_dt = date('Y-m-d', strtotime($hasta));

    // Obtener PDO (adapta si tu Controller guarda la conexión en otra propiedad)
    $pdo = $this->db ?? $GLOBALS['pdo'] ?? null;
    if (!$pdo instanceof PDO) {
        error_log('ERROR: No hay conexión PDO disponible en VeterinarioController::exportar()');
        die('Error interno: no hay conexión a la base de datos.');
    }

    try {
        // CONSULTAS: tabla 'consultas' usa mascota_id y empleado_id y columna fecha 'creado_en'
        $sqlConsultas = "
            SELECT c.*, m.nombre AS nombre_mascota, u.nombre AS nombre_veterinario, u.apellido AS apellido_veterinario
            FROM consultas c
            LEFT JOIN mascotas m ON c.mascota_id = m.id
            LEFT JOIN usuarios u ON c.empleado_id = u.id
            WHERE DATE(c.creado_en) BETWEEN :desde AND :hasta
            ORDER BY c.creado_en ASC
        ";
        $stmt = $pdo->prepare($sqlConsultas);
        $stmt->execute([':desde' => $desde_dt, ':hasta' => $hasta_dt]);
        $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // CITAS: tabla 'citas' usa cliente_id y mascota_id y fecha 'fecha'
        // JOIN con 'usuarios' para obtener nombre/apellido del cliente
        $sqlCitas = "
            SELECT ci.*, m.nombre AS nombre_mascota,
                   u_cliente.nombre AS cliente_nombre, u_cliente.apellido AS cliente_apellido,
                   s.nombre AS nombre_servicio
            FROM citas ci
            LEFT JOIN mascotas m ON ci.mascota_id = m.id
            LEFT JOIN usuarios u_cliente ON ci.cliente_id = u_cliente.id
            LEFT JOIN servicios s ON ci.servicio_id = s.id
            WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
            ORDER BY ci.fecha ASC
        ";
        $stmt2 = $pdo->prepare($sqlCitas);
        $stmt2->execute([':desde' => $desde_dt, ':hasta' => $hasta_dt]);
        $citas = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Log detallado para debugging
        error_log('ERROR SQL en exportar(): ' . $e->getMessage());
        error_log('SQLSTATE: ' . $e->getCode());
        die('Error en la consulta a la base de datos. Revisa logs.');
    }

    // Preparar plantilla
    $data = [
        'desde' => $desde_dt,
        'hasta' => $hasta_dt,
        'consultas' => $consultas,
        'citas' => $citas
    ];

    ob_start();
    extract($data);
    include APP_ROOT . '/views/veterinario/plantilla_pdf.php';
    $html = ob_get_clean();

    // Generar PDF
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = "reporte_veterinario_{$desde_dt}_a_{$hasta_dt}.pdf";
    $dompdf->stream($filename, ["Attachment" => false]); // mostrar en navegador
    exit;
}
public function guardarNotaRapida($mascotaId) {
    $notaTexto = trim($_POST['nota'] ?? '');
    $veterinarioId = $_SESSION['user']['id'] ?? null;

    if (!$notaTexto || !$veterinarioId) {
        $_SESSION['flash_error'] = 'Nota vacía o usuario no identificado.';
        header("Location: /vetsmart/veterinario/mascotas/$mascotaId/historial");
        exit;
    }

    try {
        // usar la instancia creada en el constructor
        $insertId = $this->notaMascotaModel->crear($mascotaId, $veterinarioId, $notaTexto);
        $_SESSION['flash_success'] = 'Nota agregada.';
    } catch (Exception $e) {
        error_log('guardarNotaRapida error: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Error guardando la nota (revisa logs).';
    }

    header("Location: /vetsmart/veterinario/mascotas/$mascotaId/historial");
    exit;
}

public function editarNotaRapida($mascotaId, $notaId) {
    try {
        $nota = $this->notaMascotaModel->obtenerPorId($notaId);
    } catch (Exception $e) {
        error_log('editarNotaRapida error: ' . $e->getMessage());
        $nota = null;
    }

    if (!$nota) {
        $_SESSION['flash_error'] = 'Nota no encontrada.';
        header("Location: /vetsmart/veterinario/mascotas/{$mascotaId}/historial");
        exit;
    }

    $this->view('veterinario/mascotas/editar_nota', [
        'mascota_id' => $mascotaId,
        'nota' => $nota
    ], 'main_veterinario');
}

public function actualizarNotaRapida($mascotaId, $notaId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /vetsmart/veterinario/mascotas/{$mascotaId}/historial");
        exit;
    }

    $notaTexto = trim($_POST['nota'] ?? '');

    if ($notaTexto === '') {
        $_SESSION['flash_error'] = 'La nota no puede quedar vacía.';
        header("Location: /vetsmart/veterinario/mascotas/{$mascotaId}/historial");
        exit;
    }

    try {
        $this->notaMascotaModel->actualizar((int)$notaId, $notaTexto);
        $_SESSION['flash_success'] = 'Nota actualizada correctamente.';
    } catch (Exception $e) {
        error_log('actualizarNotaRapida error: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Error actualizando la nota (revisa logs).';
    }

    header("Location: /vetsmart/veterinario/mascotas/{$mascotaId}/historial");
    exit;
}

public function eliminarNotaRapida($idMascota, $idNota)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
        exit;
    }

    try {
        // preferir $this->db (si tu Controller lo define), sino usar $this->pdo o $GLOBALS['pdo']
        $pdo = $this->db ?? $this->pdo ?? ($GLOBALS['pdo'] ?? null);
        if (!$pdo instanceof PDO) {
            throw new Exception('No hay conexión PDO disponible para eliminar nota.');
        }

        $notaModel = new NotaMascota($pdo);
        $notaModel->eliminar($idNota);

        $_SESSION['flash_success'] = 'Nota eliminada correctamente.';
    } catch (Exception $e) {
        error_log('eliminarNotaRapida error: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Error al eliminar la nota (revisa logs).';
    }

    header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
    exit;
}


public function guardarEdicionNotaRapida($mascotaId, $notaId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /vetsmart/veterinario/mascotas/{$mascotaId}/historial");
        exit;
    }

    $notaTexto = trim($_POST['nota'] ?? '');
    if ($notaTexto === '') {
        $_SESSION['flash_error'] = 'La nota no puede quedar vacía.';
        header("Location: /vetsmart/veterinario/mascotas/{$mascotaId}/historial");
        exit;
    }

    try {
        $this->notaMascotaModel->actualizar((int)$notaId, $notaTexto);
        $_SESSION['flash_success'] = 'Nota actualizada correctamente.';
    } catch (Exception $e) {
        error_log('guardarEdicionNotaRapida error: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Error al guardar la edición (revisa logs).';
    }

    header("Location: /vetsmart/veterinario/mascotas/{$mascotaId}/historial");
    exit;
}

public function verConsulta($id)
{
    $consultaModel = new Consulta($this->db);
    $consulta = $consultaModel->getById($id);

    // Detectar si la solicitud viene por AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $this->view('veterinario/consultas/ver', [
        'consulta' => $consulta
    ], $isAjax ? null : 'main_veterinario');
}


public function editarConsulta($id)
{
    $consultaModel = new Consulta($this->db);
    $consulta = $consultaModel->getById($id);

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $this->view('veterinario/consultas/editar', [
        'consulta' => $consulta
    ], $isAjax ? null : 'main_veterinario');
}

}
