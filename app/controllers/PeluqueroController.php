<?php
declare(strict_types=1);

class PeluqueroController extends Controller
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function verificarSesion(): void
    {
        $u = $_SESSION['user'] ?? [];
        // Aceptar varias formas de identificar el rol: role_name (string) o role/role_id (numérico)
        $roleName = strtolower((string)($u['role_name'] ?? $u['role'] ?? ''));
        $roleId = (int)($u['role_id'] ?? $u['role'] ?? 0);
        // Role id 5 corresponde a peluquero en tu esquema de roles
        if ($roleName !== 'peluquero' && $roleId !== 5) {
            header('Location: /vetsmart/login');
            exit;
        }
    }

    private function renderView(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include APP_ROOT . '/views/' . $view . '.php';
        return (string)ob_get_clean();
    }

    private function groomingServiceIds(): array
    {
        $ids = [];
        try {
            // Preferir columna de clasificación si existe
            $q1 = $this->pdo->query("SELECT id FROM servicios WHERE activo=1 AND categoria='peluqueria'");
            if ($q1) {
                $rows = $q1->fetchAll(PDO::FETCH_ASSOC) ?: [];
                foreach ($rows as $r) { $ids[] = (int)$r['id']; }
            }
        } catch (Throwable $e) {
            // ignorar y usar heurística por nombre
        }
        if (!$ids) {
            $q = $this->pdo->query("SELECT id FROM servicios WHERE activo=1 AND (
                LOWER(nombre) LIKE '%peluquer%' OR LOWER(nombre) LIKE '%bañ%' OR LOWER(nombre) LIKE '%cort%' OR LOWER(nombre) LIKE '%spa%'
            )");
            $rows = $q ? $q->fetchAll(PDO::FETCH_ASSOC) : [];
            foreach ($rows as $r) { $ids[] = (int)$r['id']; }
        }
        if (!$ids) { $ids = [-1]; }
        return $ids;
    }

    // Expresión SQL robusta para detectar servicios de peluquería sin depender de ids previos
    private function groomingWhereExpr(): string
    {
        // Determina en PHP si la columna 'categoria' existe en la tabla 'servicios'
        // para evitar que el motor SQL intente resolver una columna inexistente
        // (lo que provoca SQLSTATE[42S22] incluso si se usa dentro de CASE).
        try {
            $st = $this->pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='servicios' AND COLUMN_NAME='categoria'");
            $st->execute();
            $hasCategoria = ((int)$st->fetchColumn()) > 0;
        } catch (Throwable $e) {
            $hasCategoria = false;
        }

        $nameConditions = "LOWER(s.nombre) LIKE '%peluquer%' OR LOWER(s.nombre) LIKE '%bañ%' OR LOWER(s.nombre) LIKE '%ban%' OR LOWER(s.nombre) LIKE '%cort%' OR LOWER(s.nombre) LIKE '%spa%'";

        if ($hasCategoria) {
            return "(s.categoria = 'peluqueria' OR ($nameConditions))";
        }

        return "($nameConditions)";
    }

    public function dashboard(): void
    {
        $this->verificarSesion();

        $empId = (int)($_SESSION['user']['id'] ?? 0);
        $hoy = date('Y-m-d');
        $whereG = $this->groomingWhereExpr(); // no se usa en dashboard para evitar falsos negativos
        // Normalizar estados por posibles variantes en BD
        $estadosPend = ['pendiente','agendada','programada'];
        $estadosProc = ['confirmada','en_proceso','atendiendo'];
        $estadosComp = ['completada','finalizada','cerrada'];
        // Contadores: sumar citas + cpeluq (si existe)
        $st = $this->pdo->prepare("SELECT COUNT(*)
                                    FROM citas c
                                    WHERE c.empleado_id=? AND DATE(c.fecha)=?");
        $st->execute([$empId, $hoy]);
        $citasHoyCitas = (int)$st->fetchColumn();
        $citasHoy = $citasHoyCitas;
        try {
            $q = $this->pdo->prepare("SELECT COUNT(*) FROM cpeluq WHERE peluquero_id=? AND DATE(fecha)=?");
            $q->execute([$empId, $hoy]);
            $citasHoyCpeluq = (int)$q->fetchColumn();
            $citasHoy += $citasHoyCpeluq;
        } catch (Throwable $e) { /* cpeluq puede no existir */ }

        $inProc = implode(",", array_fill(0, count($estadosProc), '?'));
        $st1 = $this->pdo->prepare("SELECT COUNT(*)
                                     FROM citas c
                                     WHERE c.empleado_id=? AND c.estado IN ($inProc)");
        $st1->execute(array_merge([$empId], $estadosProc));
        $enProcesoCitas = (int)$st1->fetchColumn();
        $enProceso = $enProcesoCitas;
        try {
            $in = implode(",", array_fill(0, count($estadosProc), '?'));
            $q = $this->pdo->prepare("SELECT COUNT(*) FROM cpeluq WHERE peluquero_id=? AND estado IN ($in)");
            $q->execute(array_merge([$empId], $estadosProc));
            $enProcesoCpeluq = (int)$q->fetchColumn();
            $enProceso += $enProcesoCpeluq;
        } catch (Throwable $e) { }

        $inPend = implode(",", array_fill(0, count($estadosPend), '?'));
        $st2 = $this->pdo->prepare("SELECT COUNT(*)
                                     FROM citas c
                                     WHERE c.empleado_id=? AND c.estado IN ($inPend) AND DATE(c.fecha)>=?");
        $st2->execute(array_merge([$empId], $estadosPend, [$hoy]));
        $pendientesCitas = (int)$st2->fetchColumn();
        $pendientes = $pendientesCitas;
        try {
            $in = implode(",", array_fill(0, count($estadosPend), '?'));
            $q = $this->pdo->prepare("SELECT COUNT(*) FROM cpeluq WHERE peluquero_id=? AND estado IN ($in) AND DATE(fecha)>=?");
            $q->execute(array_merge([$empId], $estadosPend, [$hoy]));
            $pendientesCpeluq = (int)$q->fetchColumn();
            $pendientes += $pendientesCpeluq;
        } catch (Throwable $e) { }

        $inComp = implode(",", array_fill(0, count($estadosComp), '?'));
        $st3 = $this->pdo->prepare("SELECT COUNT(*)
                                     FROM citas c
                                     WHERE c.empleado_id=? AND c.estado IN ($inComp) AND DATE(c.fecha)=?");
        $st3->execute(array_merge([$empId], $estadosComp, [$hoy]));
        $completadasHoyCitas = (int)$st3->fetchColumn();
        $completadasHoy = $completadasHoyCitas;
        try {
            $in = implode(",", array_fill(0, count($estadosComp), '?'));
            $q = $this->pdo->prepare("SELECT COUNT(*) FROM cpeluq WHERE peluquero_id=? AND estado IN ($in) AND DATE(fecha)=?");
            $q->execute(array_merge([$empId], $estadosComp, [$hoy]));
            $completadasHoyCpeluq = (int)$q->fetchColumn();
            $completadasHoy += $completadasHoyCpeluq;
        } catch (Throwable $e) { }

        $st4 = $this->pdo->prepare(
            "SELECT c.id, DATE(c.fecha) AS fecha, TIME(c.fecha) AS hora, c.estado, s.nombre AS servicio, m.nombre AS mascota,
                    CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS cliente
             FROM citas c
             JOIN servicios s ON s.id=c.servicio_id
             JOIN mascotas m ON m.id=c.mascota_id
             JOIN usuarios u ON u.id=c.cliente_id
             WHERE c.empleado_id=?
               AND c.estado IN (" . implode(',', array_fill(0, count(array_merge($estadosPend, $estadosProc)), '?')) . ")
             ORDER BY c.fecha ASC, c.id ASC
             LIMIT 20"
        );
        $st4->execute(array_merge([$empId], array_merge($estadosPend, $estadosProc)));
        $proximasCitas = $st4->fetchAll(PDO::FETCH_ASSOC) ?: [];
        
        // Filtrar solo las próximas (desde hoy en adelante)
        $proximasCitas = array_filter($proximasCitas, function($cita) {
            return strtotime($cita['fecha']) >= strtotime(date('Y-m-d'));
        });
        $proximasCp = [];
        try {
            $qP = $this->pdo->prepare(
                "SELECT c.id,
                        DATE(c.fecha) AS fecha,
                        TIME(c.hora) AS hora,
                        c.estado,
                        s.nombre AS servicio,
                        m.nombre AS mascota,
                        CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS cliente
                 FROM cpeluq c
                 JOIN servicios s ON s.id = c.servicio_id
                 JOIN mascotas m ON m.id = c.mascota_id
                 JOIN usuarios u ON u.id = c.cliente_id
                 WHERE c.peluquero_id = ?
                   AND c.estado IN (" . implode(',', array_fill(0, count(array_merge($estadosPend, $estadosProc)), '?')) . ")
                   AND DATE(c.fecha) >= ?
                 ORDER BY c.fecha ASC, c.hora ASC
                 LIMIT 10"
            );
            $qP->execute(array_merge([$empId], array_merge($estadosPend, $estadosProc), [$hoy]));
            $proximasCp = $qP->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) { /* ignorar si no existe */ }

        // Unir, ordenar y limitar
        $proxCitasCount = count($proximasCitas);
        $proxCpeluqCount = count($proximasCp);
        $proximas = array_merge($proximasCitas, $proximasCp);
        usort($proximas, function ($a, $b) {
            $ta = strtotime(($a['fecha'] ?? '') . ' ' . ($a['hora'] ?? '00:00:00'));
            $tb = strtotime(($b['fecha'] ?? '') . ' ' . ($b['hora'] ?? '00:00:00'));
            return $ta <=> $tb;
        });
        $proximas = array_slice($proximas, 0, 10);


        $content = $this->renderView('peluquero/dashboard', compact('citasHoy','enProceso','pendientes','completadasHoy','proximas'));
        require APP_ROOT . '/views/layouts/main_peluquero.php';
    }

    public function citasIndex(): void
    {
        $this->verificarSesion();
        $empId = (int)($_SESSION['user']['id'] ?? 0);
        $ids = $this->groomingServiceIds();

        $estado = isset($_GET['estado']) ? trim((string)$_GET['estado']) : '';
        $desde  = isset($_GET['desde']) ? trim((string)$_GET['desde']) : '';
        $hasta  = isset($_GET['hasta']) ? trim((string)$_GET['hasta']) : '';

        $sql = "SELECT c.id, DATE(c.fecha) AS fecha, TIME(c.fecha) AS hora, c.estado,
                       s.nombre AS servicio, s.precio AS precio, m.nombre AS mascota,
                       CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS cliente
                FROM citas c
                JOIN servicios s ON s.id=c.servicio_id
                JOIN mascotas m ON m.id=c.mascota_id
                JOIN usuarios u ON u.id=c.cliente_id
                WHERE c.empleado_id=? AND " . $this->groomingWhereExpr() . "";
        $params = [$empId];
        if ($estado !== '') {
            $sql .= " AND c.estado = ?";
            $params[] = $estado;
        }
        if ($desde !== '') {
            $sql .= " AND DATE(c.fecha) >= ?";
            $params[] = $desde;
        }
        if ($hasta !== '') {
            $sql .= " AND DATE(c.fecha) <= ?";
            $params[] = $hasta;
        }
        $sql .= " ORDER BY c.fecha DESC LIMIT 50";

        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        $citas = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Fallback si la clínica usa cpeluq en lugar de citas
        if (!$citas) {
            $sql2 = "SELECT c.id,
                            DATE(c.fecha) AS fecha,
                            TIME(c.hora) AS hora,
                            c.estado,
                            s.nombre AS servicio,
                            s.precio AS precio,
                            m.nombre AS mascota,
                            CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS cliente
                     FROM cpeluq c
                     JOIN servicios s ON s.id = c.servicio_id
                     JOIN mascotas m ON m.id = c.mascota_id
                     JOIN usuarios u ON u.id = c.cliente_id
                     WHERE c.peluquero_id = ? AND c.servicio_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")";
            $params2 = array_merge([$empId], $ids);
            if ($estado !== '') { $sql2 .= " AND c.estado = ?"; $params2[] = $estado; }
            if ($desde !== '')  { $sql2 .= " AND DATE(c.fecha) >= ?"; $params2[] = $desde; }
            if ($hasta !== '')  { $sql2 .= " AND DATE(c.fecha) <= ?"; $params2[] = $hasta; }
            $sql2 .= " ORDER BY c.fecha DESC, c.hora DESC LIMIT 50";
            try {
                $st2 = $this->pdo->prepare($sql2);
                $st2->execute($params2);
                $citas = $st2->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Throwable $e) {
                // Ignorar si no existe cpeluq
            }
        }

        $content = $this->renderView('peluquero/citas', compact('citas','estado','desde','hasta'));
        require APP_ROOT . '/views/layouts/main_peluquero.php';
    }

    public function atenderCita(): void
    {
        $this->verificarSesion();
        $token = (string)($_POST['_csrf'] ?? '');
        if (!CSRF::validate($token)) {
            http_response_code(400);
            exit('Token inválido');
        }
        $citaId = (int)($_POST['cita_id'] ?? 0);

        $this->pdo->prepare("UPDATE citas SET estado='confirmada' WHERE id=? AND estado='pendiente'")
            ->execute([$citaId]);

        $this->pdo->prepare("INSERT INTO atenciones_peluqueria (cita_id, inicio_at) VALUES (?, NOW())")
            ->execute([$citaId]);

        header('Location: /vetsmart/peluquero/citas');
        exit;
    }

    public function finalizarServicio(): void
    {
        $this->verificarSesion();
        $token = (string)($_POST['_csrf'] ?? '');
        if (!CSRF::validate($token)) {
            http_response_code(400);
            exit('Token inválido');
        }
        $citaId = (int)($_POST['cita_id'] ?? 0);
        $notas  = trim((string)($_POST['notas'] ?? ''));

        // precio fijo desde el servicio asociado
        $qPrecio = $this->pdo->prepare("SELECT s.precio
                                        FROM citas c INNER JOIN servicios s ON s.id = c.servicio_id
                                        WHERE c.id = ?");
        $qPrecio->execute([$citaId]);
        $precio = (float)($qPrecio->fetchColumn() ?: 0);

        $this->pdo->prepare("UPDATE citas SET estado='completada' WHERE id=?")
            ->execute([$citaId]);

        // Asegurar registro de atención (si no se presionó Atender antes)
        $st = $this->pdo->prepare("UPDATE atenciones_peluqueria SET fin_at=NOW(), precio_final=?, notas=? WHERE cita_id=?");
        $st->execute([$precio, $notas, $citaId]);
        if ($st->rowCount() === 0) {
            $ins = $this->pdo->prepare("INSERT INTO atenciones_peluqueria (cita_id, inicio_at, fin_at, precio_final, notas) VALUES (?, NOW(), NOW(), ?, ?)");
            $ins->execute([$citaId, $precio, $notas]);
        }

        header('Location: /vetsmart/peluquero/citas');
        exit;
    }

    public function serviciosIndex(): void
    {
        $this->verificarSesion();
        $whereG = $this->groomingWhereExpr();
        $st = $this->pdo->prepare("SELECT id, nombre, duracion_min, precio, descripcion FROM servicios s WHERE activo=1 AND $whereG ORDER BY nombre ASC");
        $st->execute();
        $servicios = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $content = $this->renderView('peluquero/servicios', compact('servicios'));
        require APP_ROOT . '/views/layouts/main_peluquero.php';
    }

    public function agendaIndex(): void
    {
        $this->verificarSesion();
        $empId = (int)($_SESSION['user']['id'] ?? 0);

        // Calcular semana (lunes a domingo) basada en parámetro ?desde=YYYY-MM-DD o semana actual
        $hoy = new DateTime('today');
        $desdeParam = isset($_GET['desde']) ? trim((string)$_GET['desde']) : '';
        $desde = $desdeParam !== '' ? DateTime::createFromFormat('Y-m-d', $desdeParam) ?: clone $hoy : clone $hoy;
        // Ajustar a lunes de la semana
        $dow = (int)$desde->format('N'); // 1=lunes..7=domingo
        if ($dow > 1) { $desde->modify('-' . ($dow - 1) . ' days'); }
        $hasta = (clone $desde)->modify('+6 days');

        $whereG = $this->groomingWhereExpr();
        $sql = "SELECT DATE(c.fecha) AS dia, TIME(c.fecha) AS hora, c.estado,
                       s.nombre AS servicio, m.nombre AS mascota,
                       CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS cliente
                FROM citas c
                JOIN servicios s ON s.id=c.servicio_id
                JOIN mascotas m ON m.id=c.mascota_id
                JOIN usuarios u ON u.id=c.cliente_id
                WHERE c.empleado_id=? AND $whereG AND DATE(c.fecha) BETWEEN ? AND ?
                ORDER BY c.fecha ASC";
        $st = $this->pdo->prepare($sql);
        $st->execute([$empId, $desde->format('Y-m-d'), $hasta->format('Y-m-d')]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Agrupar por día
        $agenda = [];
        $cursor = clone $desde;
        while ($cursor <= $hasta) {
            $agenda[$cursor->format('Y-m-d')] = [];
            $cursor->modify('+1 day');
        }
        foreach ($rows as $r) {
            $d = (string)($r['dia'] ?? '');
            if (!isset($agenda[$d])) { $agenda[$d] = []; }
            $agenda[$d][] = $r;
        }

        $desdeStr = $desde->format('Y-m-d');
        $hastaStr = $hasta->format('Y-m-d');
        $content = $this->renderView('peluquero/calendario', [
            'agenda' => $agenda,
            'desde' => $desdeStr,
            'hasta' => $hastaStr,
        ]);
        require APP_ROOT . '/views/layouts/main_peluquero.php';
    }

    public function clientesIndex(): void
    {
        $this->verificarSesion();
        $empId = (int)($_SESSION['user']['id'] ?? 0);
        $ids = $this->groomingServiceIds();

        $sql = "SELECT 
                    u.id AS cliente_id,
                    CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS cliente,
                    u.email,
                    u.telefono,
                    GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.nombre SEPARATOR ', ') AS mascotas,
                    COUNT(*) AS total_citas,
                    SUM(CASE WHEN c.estado='pendiente' THEN 1 ELSE 0 END) AS pendientes,
                    SUM(CASE WHEN c.estado='confirmada' THEN 1 ELSE 0 END) AS en_proceso,
                    SUM(CASE WHEN c.estado='completada' THEN 1 ELSE 0 END) AS completadas
                FROM citas c
                INNER JOIN usuarios u ON u.id = c.cliente_id
                INNER JOIN mascotas m ON m.id = c.mascota_id
                WHERE c.empleado_id = ? AND c.servicio_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")
                GROUP BY u.id, u.nombre, u.apellido, u.email, u.telefono
                ORDER BY cliente ASC";
        $st = $this->pdo->prepare($sql);
        $st->execute(array_merge([$empId], $ids));
        $clientes = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $content = $this->renderView('peluquero/clientes', compact('clientes'));
        require APP_ROOT . '/views/layouts/main_peluquero.php';
    }

    /**
     * Mostrar formulario para agendar cita
     */
    public function agendarCita(): void
    {
        $this->verificarSesion();
        $empId = (int)($_SESSION['user']['id'] ?? 0);

        // Obtener servicios de peluquería (filtrando por nombre)
        $sql = "SELECT id, nombre, precio, duracion_min FROM servicios 
                WHERE activo=1 AND (LOWER(nombre) LIKE '%peluquer%' OR LOWER(nombre) LIKE '%bañ%' OR LOWER(nombre) LIKE '%cort%')
                ORDER BY nombre ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Obtener clientes
        $sql = "SELECT DISTINCT u.id, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, u.email
                FROM usuarios u
                WHERE u.role_id = 6 AND u.estado = 1
                ORDER BY u.nombre ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $content = $this->renderView('peluquero/agendar_cita', [
            'servicios' => $servicios,
            'clientes' => $clientes,
            'empleado_id' => $empId
        ]);
        require APP_ROOT . '/views/layouts/main_peluquero.php';
    }

    /**
     * Guardar nueva cita de peluquería
     */
    public function guardarCitaPeluqueria(): void
    {
        $this->verificarSesion();
        $empId = (int)($_SESSION['user']['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vetsmart/peluquero/agenda');
            exit;
        }

        try {
            $cliente_id = (int)($_POST['cliente_id'] ?? 0);
            $mascota_id = (int)($_POST['mascota_id'] ?? 0);
            $servicio_id = (int)($_POST['servicio_id'] ?? 0);
            $fecha = trim((string)($_POST['fecha'] ?? ''));
            $hora = trim((string)($_POST['hora'] ?? ''));
            $notas = trim((string)($_POST['notas'] ?? ''));

            if (!$cliente_id || !$mascota_id || !$servicio_id || !$fecha || !$hora) {
                $_SESSION['flash_error'] = 'Por favor completa todos los campos obligatorios.';
                header('Location: /vetsmart/peluquero/agenda/agendar');
                exit;
            }

            // Crear datetime para la cita
            $fecha_hora = $fecha . ' ' . $hora . ':00';

            // Obtener duración del servicio
            $stmt = $this->pdo->prepare("SELECT duracion_min FROM servicios WHERE id = ?");
            $stmt->execute([$servicio_id]);
            $duracion = (int)($stmt->fetchColumn() ?? 60);

            // Insertar cita
            $sql = "INSERT INTO citas (cliente_id, mascota_id, empleado_id, servicio_id, fecha, duracion_min, estado, notas)
                    VALUES (?, ?, ?, ?, ?, ?, 'confirmada', ?)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$cliente_id, $mascota_id, $empId, $servicio_id, $fecha_hora, $duracion, $notas]);

            if ($result) {
                $_SESSION['flash_success'] = 'Cita agendada correctamente.';
                header('Location: /vetsmart/peluquero/dashboard');
            } else {
                $_SESSION['flash_error'] = 'Error al guardar la cita. Intenta de nuevo.';
                header('Location: /vetsmart/peluquero/agenda/agendar');
            }
            exit;
        } catch (Throwable $e) {
            error_log("Error al guardar cita: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error al guardar la cita. Intenta de nuevo.';
            header('Location: /vetsmart/peluquero/agenda/agendar');
            exit;
        }
    }

    /**
     * Mostrar reportes de peluquería
     */
    public function reportesIndex(): void
    {
        $this->verificarSesion();
        $empId = (int)($_SESSION['user']['id'] ?? 0);

        // Obtener estadísticas del mes
        $mesActual = date('Y-m');
        
        // Total de citas en el mes
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM citas 
            WHERE empleado_id = ? AND DATE_FORMAT(fecha, '%Y-%m') = ?
        ");
        $stmt->execute([$empId, $mesActual]);
        $totalCitasMes = (int)$stmt->fetchColumn();

        // Citas completadas en el mes
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM citas 
            WHERE empleado_id = ? AND DATE_FORMAT(fecha, '%Y-%m') = ? AND estado = 'completada'
        ");
        $stmt->execute([$empId, $mesActual]);
        $citasCompletadasMes = (int)$stmt->fetchColumn();

        // Ingresos del mes (suma de precios de servicios realizados)
        $stmt = $this->pdo->prepare("
            SELECT SUM(s.precio) FROM citas c
            JOIN servicios s ON s.id = c.servicio_id
            WHERE c.empleado_id = ? AND DATE_FORMAT(c.fecha, '%Y-%m') = ? AND c.estado = 'completada'
        ");
        $stmt->execute([$empId, $mesActual]);
        $ingresosMes = (float)($stmt->fetchColumn() ?? 0);

        // Clientes atendidos en el mes
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT cliente_id) FROM citas 
            WHERE empleado_id = ? AND DATE_FORMAT(fecha, '%Y-%m') = ? AND estado = 'completada'
        ");
        $stmt->execute([$empId, $mesActual]);
        $clientesAtendidos = (int)$stmt->fetchColumn();

        // Servicios más solicitados
        $stmt = $this->pdo->prepare("
            SELECT s.nombre, COUNT(c.id) as cantidad
            FROM citas c
            JOIN servicios s ON s.id = c.servicio_id
            WHERE c.empleado_id = ? AND DATE_FORMAT(c.fecha, '%Y-%m') = ?
            GROUP BY s.id, s.nombre
            ORDER BY cantidad DESC
            LIMIT 5
        ");
        $stmt->execute([$empId, $mesActual]);
        $serviciosMasUsados = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Últimas 10 citas realizadas
        $stmt = $this->pdo->prepare("
            SELECT c.id, DATE(c.fecha) AS fecha, TIME(c.fecha) AS hora, 
                   s.nombre AS servicio, m.nombre AS mascota,
                   CONCAT(u.nombre, ' ', u.apellido) AS cliente, c.estado,
                   s.precio
            FROM citas c
            JOIN servicios s ON s.id = c.servicio_id
            JOIN mascotas m ON m.id = c.mascota_id
            JOIN usuarios u ON u.id = c.cliente_id
            WHERE c.empleado_id = ? AND DATE_FORMAT(c.fecha, '%Y-%m') = ?
            ORDER BY c.fecha DESC
            LIMIT 10
        ");
        $stmt->execute([$empId, $mesActual]);
        $ultimasCitas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $content = $this->renderView('peluquero/reportes', compact(
            'totalCitasMes',
            'citasCompletadasMes',
            'ingresosMes',
            'clientesAtendidos',
            'serviciosMasUsados',
            'ultimasCitas',
            'mesActual'
        ));
        require APP_ROOT . '/views/layouts/main_peluquero.php';
    }

}

