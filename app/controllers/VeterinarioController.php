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

// Asegúrate en el constructor: $this->db = $pdo;

public function guardarCita()
{
    // Esta versión acepta solicitudes AJAX (XMLHttpRequest / fetch) y devuelve JSON,
    // y también sigue funcionando para formularios normales con redirect.
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
             strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    try {
        $db = $this->db ?? $this->pdo ?? ($GLOBALS['pdo'] ?? null);
        if (!$db instanceof PDO) throw new Exception("No hay conexión PDO disponible.");

        // aceptar distintos nombres de campo (frontend envía fecha_date + hora_time)
        $cliente_id  = $_POST['cliente_id'] ?? null;
        $mascota_id  = $_POST['mascota_id'] ?? null;
        $empleado_id = $_POST['empleado_id'] ?? ($_SESSION['user']['id'] ?? null);
        $servicio_id = $_POST['servicio_id'] ?? null;
        $notas       = $_POST['notas'] ?? null;
        $estado      = $_POST['estado'] ?? 'programada';

        // Fecha/hora: aceptar fecha+hora por separado o campo combinado
        if (!empty($_POST['fecha_date']) && !empty($_POST['hora_time'])) {
            $fecha_date = $_POST['fecha_date'];
            $hora_time  = $_POST['hora_time'];
        } elseif (!empty($_POST['fecha']) && !empty($_POST['hora'])) {
            $fecha_date = $_POST['fecha'];
            $hora_time  = $_POST['hora'];
        } else {
            throw new Exception("Faltan fecha/hora.");
        }

        // validaciones básicas
        if (!$mascota_id || !$servicio_id || !$fecha_date || !$hora_time) {
            throw new Exception("Faltan datos obligatorios (mascota, servicio, fecha o hora).");
        }

<<<<<<< HEAD
=======
        $fechaCita = new DateTime($fecha_date);
        $hoy = new DateTime('today');

        if ($fechaCita < $hoy) {
            throw new Exception("No se puede agendar una cita en una fecha pasada.");
        }

>>>>>>> 1e60e97fddd47cf967f8dfd80aadd640df3ebe5a
        // obtener duración del servicio (si existe) - fallback 30 min
        $duracion = 30;
        $stmt = $db->prepare("SELECT duracion_min FROM servicios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $servicio_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['duracion_min'])) $duracion = (int)$row['duracion_min'];

        // Normalizar hora (acepta "09:00", "9:00", "09:00:00")
        $hora_time = trim($hora_time);
        if (strlen($hora_time) === 5) $hora_time .= ':00';

        $dtInicio = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_date . ' ' . $hora_time);
        if (!$dtInicio) {
            // fallback a strtotime
            $ts = strtotime($fecha_date . ' ' . $hora_time);
            if ($ts === false) throw new Exception("Fecha/hora inválida.");
            $dtInicio = new DateTime();
            $dtInicio->setTimestamp($ts);
        }
        $dtFin = (clone $dtInicio)->modify("+{$duracion} minutes");

        // --- 1) validar horario semanal del empleado ---
        $stmt = $db->prepare("SELECT * FROM horarios_semana WHERE empleado_id = :id");
        $stmt->execute([':id' => $empleado_id]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // normalizador simple: quitar acentos y minúsculas
        $normalize = function($s) {
            $s = mb_strtolower(trim($s));
            $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
            $s = preg_replace('/[^a-z0-9\s]/', '', $s);
            return trim($s);
        };

        $mapDias = [1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo'];
        $diaNum = (int)$dtInicio->format('N');
        $diaNombre = $mapDias[$diaNum] ?? $dtInicio->format('l');
        $diaNorm = $normalize($diaNombre);

        $inicioTime = $dtInicio->format('H:i:s');
        $finTime = $dtFin->format('H:i:s');

        $enHorario = false;
        foreach ($horarios as $h) {
            if (!isset($h['dia']) || !isset($h['hora_inicio']) || !isset($h['hora_fin'])) continue;
            if ($normalize($h['dia']) !== $diaNorm) continue;
            if ($h['hora_inicio'] <= $inicioTime && $h['hora_fin'] >= $finTime) { $enHorario = true; break; }
        }
        if (!$enHorario) throw new Exception("El veterinario no trabaja en ese día/hora.");

        // --- 2) validar solapamiento con otras citas ---
        $inicioStr = $dtInicio->format('Y-m-d H:i:s');
        $finStr = $dtFin->format('Y-m-d H:i:s');

        $stmt = $db->prepare("
            SELECT COUNT(*) AS cnt FROM citas
            WHERE empleado_id = :id
              AND NOT (
                (fecha + INTERVAL duracion_min MINUTE) <= :inicioNew
                OR fecha >= :finNew
              )
        ");
        $stmt->execute([':id' => $empleado_id, ':inicioNew' => $inicioStr, ':finNew' => $finStr]);
        $cnt = (int)$stmt->fetchColumn();
        if ($cnt > 0) throw new Exception("El veterinario ya tiene otra cita en ese rango horario.");

        // --- 3) insertar ---
        $stmt = $db->prepare("INSERT INTO citas (cliente_id, mascota_id, empleado_id, servicio_id, fecha, duracion_min, estado, notas, creado_por, creado_en)
                              VALUES (:cliente_id, :mascota_id, :empleado_id, :servicio_id, :fecha, :duracion_min, :estado, :notas, :creado_por, NOW())");
        $stmt->execute([
            ':cliente_id'   => $cliente_id,
            ':mascota_id'   => $mascota_id,
            ':empleado_id'  => $empleado_id,
            ':servicio_id'  => $servicio_id,
            ':fecha'        => $inicioStr,
            ':duracion_min' => $duracion,
            ':estado'       => $estado,
            ':notas'        => $notas,
            ':creado_por'   => $_SESSION['user']['id'] ?? null
        ]);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Cita creada']);
            exit;
        } else {
            $_SESSION['flash_success'] = "Cita creada correctamente.";
            header("Location: /vetsmart/veterinario/mis-citas");
            exit;
        }

    } catch (Exception $e) {
        error_log("guardarCita error: " . $e->getMessage());
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        } else {
            $_SESSION['flash_error'] = "Error: " . $e->getMessage();
            header("Location: /vetsmart/veterinario/mis-citas");
            exit;
        }
    }
}

/**
 * Endpoint JSON para comprobar disponibilidad sin crear la cita.
 * RUTA SUGERIDA: /api/disponibilidad-veterinario
 * Parámetros GET: veterinario_id, fecha (YYYY-mm-dd), hora (HH:MM), servicio_id (opcional)
 */
// Sustituir o actualizar la función existencia en app/controllers/VeterinarioController.php
public function disponibilidadVeterinario()
{
    header('Content-Type: application/json; charset=utf-8');
    try {
        $db = $this->db ?? $this->pdo ?? ($GLOBALS['pdo'] ?? null);
        if (!$db instanceof PDO) throw new Exception("No hay conexión PDO.");

        // permitir vet id desde GET o desde sesión (si el usuario es el veterinario)
        $vetId = $_GET['veterinario_id'] ?? ($_SESSION['user']['id'] ?? null);

        // aceptar distintos nombres de campo (compatibilidad con tu modal)
        $fecha  = $_GET['fecha'] ?? $_GET['fecha_date'] ?? null;
        $hora   = $_GET['hora'] ?? $_GET['hora_time'] ?? null;
        $servicio_id = $_GET['servicio_id'] ?? null;

        if (!$vetId || !$fecha || !$hora) {
            throw new Exception("Faltan parámetros requeridos: veterinario_id, fecha o hora.");
        }

        // duración por servicio (fallback 30)
        $duracion = 30;
        if ($servicio_id) {
            $stmt = $db->prepare("SELECT duracion_min FROM servicios WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $servicio_id]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r && !empty($r['duracion_min'])) $duracion = (int)$r['duracion_min'];
        }

        if (strlen($hora) === 5) $hora .= ':00';
        $dtInicio = DateTime::createFromFormat('Y-m-d H:i:s', $fecha . ' ' . $hora);
        if (!$dtInicio) {
            $ts = strtotime($fecha . ' ' . $hora);
            if ($ts === false) throw new Exception("Fecha/hora inválida.");
            $dtInicio = new DateTime(); $dtInicio->setTimestamp($ts);
        }
        $dtFin = (clone $dtInicio)->modify("+{$duracion} minutes");

        // obtener horarios_semana del vet
        $stmt = $db->prepare("SELECT * FROM horarios_semana WHERE empleado_id = :id");
        $stmt->execute([':id' => $vetId]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($horarios)) {
            // Si como política deben configurarse en admin, devolver no disponible
            echo json_encode(['disponible' => false, 'reason' => 'Sin horarios configurados para este veterinario.']);
            exit;
        }

        // normalizador simple (mismo enfoque que usabas)
        $normalize = function($s) {
            $s = mb_strtolower(trim($s));
            $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
            $s = preg_replace('/[^a-z0-9\s]/', '', $s);
            return trim($s);
        };

        $mapDias = [1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo'];
        $diaNum = (int)$dtInicio->format('N');
        $diaNombre = $mapDias[$diaNum] ?? $dtInicio->format('l');
        $diaNorm = $normalize($diaNombre);

        $inicioTime = $dtInicio->format('H:i:s');
        $finTime = $dtFin->format('H:i:s');

        $enHorario = false;
        foreach ($horarios as $h) {
            if (!isset($h['dia']) || !isset($h['hora_inicio']) || !isset($h['hora_fin'])) continue;
            if ($normalize($h['dia']) !== $diaNorm) continue;
            if ($h['hora_inicio'] <= $inicioTime && $h['hora_fin'] >= $finTime) { $enHorario = true; break; }
        }
        if (!$enHorario) {
            echo json_encode(['disponible' => false, 'reason' => 'Fuera de horario laboral']);
            exit;
        }

        // comprobar solapamiento con otras citas
        $stmt = $db->prepare("
            SELECT COUNT(*) AS cnt FROM citas
            WHERE empleado_id = :id
              AND NOT (
                    (fecha + INTERVAL duracion_min MINUTE) <= :inicioNew
                    OR fecha >= :finNew
              )
        ");
        $stmt->execute([':id' => $vetId, ':inicioNew' => $dtInicio->format('Y-m-d H:i:s'), ':finNew' => $dtFin->format('Y-m-d H:i:s')]);
        $cnt = (int)$stmt->fetchColumn();
        if ($cnt > 0) {
            echo json_encode(['disponible' => false, 'reason' => 'Solapamiento con otra cita']);
            exit;
        }

        echo json_encode(['disponible' => true]);
        exit;

    } catch (Exception $e) {
        error_log("disponibilidadVeterinario error: " . $e->getMessage());
        echo json_encode(['disponible' => false, 'reason' => $e->getMessage()]);
        exit;
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
public function actualizarCita() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        return;
    }

    $id         = $_POST['id'] ?? null;
    $cliente_id = $_POST['cliente_id'] ?? null;
    $mascota_id = $_POST['mascota_id'] ?? null;
    $servicio_id= $_POST['servicio_id'] ?? null;
    $empleado_id= $_SESSION['user']['id'] ?? null;
    $notas      = $_POST['notas'] ?? null;
    $estado     = $_POST['estado'] ?? null;

    // reconstruir fecha solo si viene
    $fechaHora = null;
    if (!empty($_POST['fecha_date']) && !empty($_POST['hora_time'])) {
        $fechaHora = date('Y-m-d H:i:s', strtotime($_POST['fecha_date'].' '.$_POST['hora_time']));
    } elseif (!empty($_POST['fecha'])) {
        $fechaHora = date('Y-m-d H:i:s', strtotime($_POST['fecha']));
    }

    // validar disponibilidad SOLO si cambió la fecha
    if ($fechaHora) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM citas 
            WHERE fecha = :fecha 
              AND empleado_id = :empleado_id 
              AND id != :id
        ");
        $stmt->execute([
            ':fecha' => $fechaHora,
            ':empleado_id' => $empleado_id,
            ':id' => $id
        ]);
        $exists = $stmt->fetchColumn();
        if ($exists > 0) {
            echo json_encode(['success' => false, 'message' => 'Ya existe otra cita en esa fecha y hora']);
            return;
        }
    }

    // armar datos para update dinámico
    $data = [];
    if ($cliente_id) $data['cliente_id'] = $cliente_id;
    if ($mascota_id) $data['mascota_id'] = $mascota_id;
    if ($servicio_id) $data['servicio_id'] = $servicio_id;
    if ($empleado_id) $data['empleado_id'] = $empleado_id;
    if ($fechaHora)   $data['fecha'] = $fechaHora;
    if ($notas !== null) $data['notas'] = $notas;
    if ($estado) $data['estado'] = $estado;

    if (empty($data)) {
        echo json_encode(['success' => false, 'message' => 'No hay datos para actualizar']);
        return;
    }

    // generar SET dinámico
    $setPart = implode(', ', array_map(fn($k)=>"$k = :$k", array_keys($data)));
    $data['id'] = $id;

    $sql = "UPDATE citas SET $setPart WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $ok = $stmt->execute($data);

    echo json_encode(['success' => $ok, 'message' => $ok ? 'Cita actualizada' : 'Error al actualizar']);
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
    $vetId = $_SESSION['user']['id'] ?? null; // 🔑 ID del veterinario logueado

    if (!$vetId) {
        http_response_code(403);
        echo json_encode(['error' => 'Usuario no autenticado']);
        exit;
    }

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
            WHERE ci.empleado_id = :vetId
        ";

        $params = [':vetId' => $vetId];

        if ($start && $end) {
            $sql .= " AND DATE(ci.fecha) BETWEEN :start AND :end ";
            $params[':start'] = $start;
            $params[':end'] = $end;
        }

        $sql .= " ORDER BY ci.fecha ASC ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = array_map(function($r){
            return [
                'id' => $r['id'],
                'title' => ($r['nombre_mascota'] ?? 'Cita') 
                         . (isset($r['nombre_servicio']) ? " — {$r['nombre_servicio']}" : ''),
                'start' => $r['fecha'],
                'allDay' => false,
                'mascota_id' => $r['mascota_id'] ?? null,
                'cliente_id' => $r['cliente_id'] ?? null,
                'servicio_id' => $r['servicio_id'] ?? null,
                'notas' => $r['notas'] ?? null,
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

// dentro de la clase VeterinarioController (o MascotaController)
public function actualizarFoto($idMascota)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
        exit;
    }

    if (empty($_FILES['foto'])) {
        $_SESSION['flash_error'] = 'No se detectó archivo.';
        header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
        exit;
    }

    $file = $_FILES['foto'];

    // Errores PHP de upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $msg = 'Error al subir archivo. Código: ' . $file['error'];
        // Mensaje legible
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE: $msg = 'El archivo excede upload_max_filesize en php.ini.'; break;
            case UPLOAD_ERR_FORM_SIZE: $msg = 'El archivo excede el tamaño permitido por el formulario.'; break;
            case UPLOAD_ERR_PARTIAL: $msg = 'Subida incompleta.'; break;
            case UPLOAD_ERR_NO_FILE: $msg = 'No se seleccionó archivo.'; break;
        }
        $_SESSION['flash_error'] = $msg;
        header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
        exit;
    }

    // Validaciones básicas
    $maxBytes = 2 * 1024 * 1024; // 2MB - ajusta si quieres más
    if ($file['size'] > $maxBytes) {
        $_SESSION['flash_error'] = 'Imagen demasiado grande (máx 2MB).';
        header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];
    if (!array_key_exists($mime, $allowed)) {
        $_SESSION['flash_error'] = 'Tipo de archivo no permitido. Usa JPG, PNG o WEBP.';
        header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
        exit;
    }

    // Directorio donde guardamos (ruta absoluta)
    $uploadsDir = rtrim(APP_ROOT, '/\\') . '/public/uploads/mascotas';
    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0755, true)) {
            $_SESSION['flash_error'] = 'No se pudo crear la carpeta de uploads.';
            header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
            exit;
        }
    }

    // Nombre seguro
    $ext = $allowed[$mime];
    $filename = 'mascota_' . (int)$idMascota . '_' . time() . '.' . $ext;
    $dest = $uploadsDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        $_SESSION['flash_error'] = 'No se pudo mover el archivo subido.';
        // logging para debugging
        error_log('move_uploaded_file falló. tmp: ' . $file['tmp_name'] . ' dest: ' . $dest);
        header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
        exit;
    }

    // Borra foto anterior si existe (opcional)
    try {
        $stmt = $this->db->prepare('SELECT foto FROM mascotas WHERE id = ?');
        $stmt->execute([(int)$idMascota]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['foto'])) {
            $oldRel = ltrim($row['foto'], '/');
            $oldFs = rtrim(APP_ROOT, '/\\') . '/public/' . $oldRel;
            if (is_file($oldFs) && strpos(realpath($oldFs), realpath(rtrim(APP_ROOT, '/\\') . '/public')) === 0) {
                @unlink($oldFs);
            }
        }
    } catch (Exception $e) {
        error_log('Error borrando foto antigua: ' . $e->getMessage());
    }

    // Guardar en DB la ruta relativa dentro de public: uploads/mascotas/archivo.jpg
    $publicPath = 'uploads/mascotas/' . $filename;
    $upd = $this->db->prepare('UPDATE mascotas SET foto = :foto WHERE id = :id');
    $upd->execute([':foto' => $publicPath, ':id' => (int)$idMascota]);

    $_SESSION['flash_success'] = 'Foto actualizada correctamente.';
    header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
    exit;
}


public function eliminarFoto($idMascota)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
        exit;
    }

    $stmt = $this->db->prepare('SELECT foto FROM mascotas WHERE id = ?');
    $stmt->execute([(int)$idMascota]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['foto'])) {
        $old = $row['foto'];
        $possible = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($old, '/');
        $possible2 = realpath(__DIR__ . '/../../public/' . ltrim($old, '/'));
        if ($possible && is_file($possible) && strpos(realpath($possible), realpath(__DIR__ . '/../../public')) === 0) {
            @unlink($possible);
        } elseif ($possible2 && is_file($possible2)) {
            @unlink($possible2);
        }
    }

    $upd = $this->db->prepare('UPDATE mascotas SET foto = NULL WHERE id = ?');
    $upd->execute([(int)$idMascota]);

    $_SESSION['flash_success'] = 'Foto eliminada.';
    header('Location: /vetsmart/veterinario/mascotas/' . (int)$idMascota . '/historial');
    exit;
}

}
