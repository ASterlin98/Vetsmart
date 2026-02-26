<?php
declare(strict_types=1);

class CitaController
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
        if (empty($_SESSION['user'])) {
            header('Location: /vetsmart/login');
            exit;
        }
    }

    // 🔹 Lista de citas (reutilizable)
   public function index(): void
    {
        $this->verificarSesion();
        $stmt = $this->pdo->query("
            SELECT c.id, c.fecha, c.estado,
                uc.nombre AS cliente_nombre, uc.apellido AS cliente_apellido,
                ue.nombre AS empleado_nombre, ue.apellido AS empleado_apellido,
                s.nombre AS servicio
            FROM citas c
            LEFT JOIN usuarios uc ON c.cliente_id = uc.id
            LEFT JOIN usuarios ue ON c.empleado_id = ue.id
            LEFT JOIN servicios s ON c.servicio_id = s.id
            ORDER BY c.fecha DESC
        ");
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content = $this->renderView('citas/index', ['citas' => $citas]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }



    // 🔹 Formulario crear cita
    public function create(): void
    {
        $this->verificarSesion();

        // 🔹 Traer clientes, empleados, servicios y mascotas
        $clientes = $this->pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE role_id = 6")->fetchAll(PDO::FETCH_ASSOC); // clientes
        // Mostrar solo roles operativos (veterinario y peluquero) por nombre de rol para evitar IDs incorrectos
        $empleados = $this->pdo->query("SELECT u.id, u.nombre, u.apellido, r.nombre AS rol
                                        FROM usuarios u
                                        INNER JOIN roles r ON r.id = u.role_id
                                        WHERE r.nombre IN ('veterinario','peluquero')")->fetchAll(PDO::FETCH_ASSOC);
        $servicios = $this->pdo->query("SELECT id, nombre FROM servicios")->fetchAll(PDO::FETCH_ASSOC);
        $mascotas  = $this->pdo->query("SELECT id, nombre FROM mascotas")->fetchAll(PDO::FETCH_ASSOC); // 🐾 agregado

        // 🔹 Renderizar vista con todas las variables disponibles
        $content = $this->renderView('recepcionista/crear_cita_modal', [
            'clientes'  => $clientes,
            'empleados' => $empleados,
            'servicios' => $servicios,
            'mascotas'  => $mascotas,
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    // 🔹 Formulario crear cita de Peluquería
    public function createPeluqueria(): void
    {
        $this->verificarSesion();

        $clientes = $this->pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE role_id = 6")->fetchAll(PDO::FETCH_ASSOC);
        $peluqueros = $this->pdo->query("SELECT u.id, u.nombre, u.apellido
                                          FROM usuarios u
                                          INNER JOIN roles r ON r.id = u.role_id
                                          WHERE r.nombre = 'peluquero'")->fetchAll(PDO::FETCH_ASSOC);
        $servicios = $this->pdo->query("SELECT id, nombre, duracion_min, precio_base FROM servicios_peluqueria WHERE activo=1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
        $mascotas  = $this->pdo->query("SELECT id, nombre, dueno_id FROM mascotas ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

        $content = $this->renderView('recepcionista/crear_cita_peluqueria', [
            'clientes'   => $clientes,
            'peluqueros' => $peluqueros,
            'servicios'  => $servicios,
            'mascotas'   => $mascotas,
        ]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    // 🔹 Guardar cita de Peluquería (POST)
    public function guardarCitaPeluqueria(): void
    {
        $this->verificarSesion();

        $token = (string)($_POST['_csrf'] ?? '');
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Token inválido'];
            header('Location: /vetsmart/recepcionista/citas-peluqueria/create');
            exit;
        }

        $cliente_id   = (int)($_POST['cliente_id'] ?? 0);
        $mascota_id   = (int)($_POST['mascota_id'] ?? 0);
        $peluquero_id = (int)($_POST['peluquero_id'] ?? 0);
        $servicio_id  = (int)($_POST['servicio_id'] ?? 0);
        $fecha        = trim((string)($_POST['fecha'] ?? ''));
        $hora         = trim((string)($_POST['hora'] ?? ''));
        $observaciones= trim((string)($_POST['observaciones'] ?? ''));

        if (!$cliente_id || !$mascota_id || !$peluquero_id || !$servicio_id || $fecha === '' || $hora === '') {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Completa todos los campos obligatorios.'];
            header('Location: /vetsmart/recepcionista/citas-peluqueria/create');
            exit;
        }

        // Validar que la fecha+hora no estén en el pasado
        try {
            $dt = DateTime::createFromFormat('Y-m-d H:i', $fecha . ' ' . $hora);
            if ($dt === false) { throw new Exception('Fecha/hora inválida'); }
            $now = new DateTime('now');
            if ($dt < $now) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se permiten citas en fechas u horas pasadas.'];
                header('Location: /vetsmart/recepcionista/citas-peluqueria/create');
                exit;
            }
        } catch (Throwable $e) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Fecha u hora inválida.'];
            header('Location: /vetsmart/recepcionista/citas-peluqueria/create');
            exit;
        }

        // Anti-duplicados / validación de solapamiento teniendo en cuenta la duración del servicio
        try {
            // Obtener duración del servicio en minutos (fallback 30min si no existe)
            $durStmt = $this->pdo->prepare("SELECT duracion_min FROM servicios_peluqueria WHERE id = ? LIMIT 1");
            $durStmt->execute([$servicio_id]);
            $durMin = (int)$durStmt->fetchColumn();
            if ($durMin <= 0) $durMin = 30;

            $newStart = DateTime::createFromFormat('Y-m-d H:i', $fecha . ' ' . $hora);
            if ($newStart === false) {
                throw new Exception('Fecha/hora inválida');
            }
            $newEnd = (clone $newStart)->add(new DateInterval('PT' . $durMin . 'M'));

            // Traer citas existentes del mismo día para el peluquero (SOLO peluquero, sin importar mascota o cliente)
            $stmt = $this->pdo->prepare("SELECT id, fecha, hora, servicio_id, peluquero_id, mascota_id FROM cpeluq WHERE fecha = ? AND peluquero_id = ? AND estado != 'cancelada'");
            $stmt->execute([$fecha, $peluquero_id]);
            $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($existing as $ex) {
                // Calcular intervalo de la cita existente
                $exStart = DateTime::createFromFormat('Y-m-d H:i', $ex['fecha'] . ' ' . $ex['hora']);
                if ($exStart === false) {
                    error_log("[OVERLAP CHECK] Fecha/hora existente inválida: " . $ex['fecha'] . ' ' . $ex['hora']);
                    continue;
                }

                // Obtener duracion del servicio existente
                $dStmt = $this->pdo->prepare("SELECT duracion_min FROM servicios_peluqueria WHERE id = ? LIMIT 1");
                $dStmt->execute([(int)$ex['servicio_id']]);
                $exDur = (int)$dStmt->fetchColumn();
                if ($exDur <= 0) $exDur = 30;
                $exEnd = (clone $exStart)->add(new DateInterval('PT' . $exDur . 'M'));

                // Log de depuración: mostrar qué se está comparando
                error_log("[OVERLAP CHECK] Nueva: " . $newStart->format('Y-m-d H:i:s') . " - " . $newEnd->format('Y-m-d H:i:s'));
                error_log("[OVERLAP CHECK] Existente: " . $exStart->format('Y-m-d H:i:s') . " - " . $exEnd->format('Y-m-d H:i:s'));
                error_log("[OVERLAP CHECK] Condición 1 (newStart < exEnd): " . ($newStart < $exEnd ? 'true' : 'false'));
                error_log("[OVERLAP CHECK] Condición 2 (exStart < newEnd): " . ($exStart < $newEnd ? 'true' : 'false'));

                // Solapamiento: nuevaStart < exEnd AND exStart < newEnd
                if ($newStart < $exEnd && $exStart < $newEnd) {
                    error_log("[OVERLAP CHECK] ¡SOLAPAMIENTO DETECTADO!");
                    $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Conflicto: el peluquero ya tiene una cita que se solapa en ese horario (desde ' . $exStart->format('H:i') . ').'];
                    header('Location: /vetsmart/recepcionista/citas-peluqueria/create');
                    exit;
                }
            }
        } catch (Throwable $e) {
            error_log('Error comprobando solapamientos de cita peluqueria: ' . $e->getMessage());
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Error al validar disponibilidad. Intenta nuevamente.'];
            header('Location: /vetsmart/recepcionista/citas-peluqueria/create');
            exit;
        }

        $ins = $this->pdo->prepare("INSERT INTO cpeluq (cliente_id, mascota_id, peluquero_id, servicio_id, fecha, hora, estado, observaciones)
                                     VALUES (?,?,?,?,?,?, 'pendiente', ?)");
        $ins->execute([$cliente_id, $mascota_id, $peluquero_id, $servicio_id, $fecha, $hora, $observaciones]);

        // Obtener datos para enviar correo de confirmación
        try {
            $citaQuery = $this->pdo->prepare("
                SELECT 
                    CONCAT(:fecha, ' ', :hora) AS fecha,
                    :hora AS hora,
                    CONCAT(cli.nombre, ' ', cli.apellido) AS cliente_nombre,
                    cli.email AS cliente_email,
                    m.nombre AS mascota,
                    sp.nombre AS servicio,
                    CONCAT(p.nombre, ' ', p.apellido) AS empleado,
                    'Peluquero' AS tipo_empleado
                FROM usuarios cli
                LEFT JOIN mascotas m ON m.id = :mascota_id
                LEFT JOIN servicios_peluqueria sp ON sp.id = :servicio_id
                LEFT JOIN usuarios p ON p.id = :peluquero_id
                WHERE cli.id = :cliente_id
                LIMIT 1
            ");
            $citaQuery->execute([
                ':cliente_id' => $cliente_id,
                ':mascota_id' => $mascota_id,
                ':servicio_id' => $servicio_id,
                ':peluquero_id' => $peluquero_id,
                ':fecha' => $fecha,
                ':hora' => $hora
            ]);
            $citaInfo = $citaQuery->fetch(PDO::FETCH_ASSOC);

            error_log("📝 [CITA PELUQUERÍA] Buscando cita para cliente_id: $cliente_id");
            
            if ($citaInfo) {
                error_log("📝 [CITA PELUQUERÍA] Cita encontrada - Email cliente: " . ($citaInfo['cliente_email'] ?? 'SIN EMAIL'));
                
                if (!empty($citaInfo['cliente_email'])) {
                    require_once APP_ROOT . '/helpers/EmailHelper.php';
                    $mailSent = EmailHelper::enviarConfirmacionCita(
                        $citaInfo['cliente_email'],
                        $citaInfo['cliente_nombre'],
                        [
                            'fecha' => $citaInfo['fecha'],
                            'hora' => $citaInfo['hora'],
                            'mascota' => $citaInfo['mascota'],
                            'servicio' => $citaInfo['servicio'],
                            'empleado' => $citaInfo['empleado'],
                            'tipo_empleado' => $citaInfo['tipo_empleado']
                        ]
                    );
                } else {
                    error_log("⚠️  [CITA PELUQUERÍA] El cliente no tiene email registrado");
                }
            } else {
                error_log("❌ [CITA PELUQUERÍA] No se encontró la cita creada");
            }
        } catch (Throwable $e) {
            error_log("❌ [CITA PELUQUERÍA] Error al enviar correo: " . $e->getMessage());
            error_log("❌ [CITA PELUQUERÍA] Trace: " . $e->getTraceAsString());
        }

        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Cita de peluquería creada.'];
        header('Location: /vetsmart/recepcionista/agenda');
        exit;
    }

    // 🔹 Guardar cita (POST)
    public function store(): void
    {
        $this->verificarSesion();
        
        // CSRF
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
            header('Location: /vetsmart/recepcionista/citas');
            exit;
        }

        $data = [
            'fecha' => $_POST['fecha'] ?? null,
            'cliente_id' => $_POST['cliente_id'] ?? null,
            'mascota_id' => $_POST['mascota_id'] ?? null,
            'empleado_id' => $_POST['empleado_id'] ?? null,
            'servicio_id' => $_POST['servicio_id'] ?? null,
            'estado' => 'pendiente',
            'notas' => $_POST['notas'] ?? ''
        ];
        
        // Asegurar formato MySQL (YYYY-MM-DD HH:MM:SS)
        if (!empty($data['fecha'])) {
             $data['fecha'] = str_replace('T', ' ', $data['fecha']);
             if (strlen($data['fecha']) == 16) { $data['fecha'] .= ':00'; }
        }

        if (empty($data['fecha']) || empty($data['cliente_id']) || empty($data['empleado_id']) || empty($data['servicio_id'])) {
             $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Faltan campos obligatorios.'];
             header('Location: /vetsmart/recepcionista/citas');
             exit;
        }

        // Validación de rol para servicios de peluquería
        if ($this->esServicioPeluqueria((int)$data['servicio_id']) && !$this->esPeluquero((int)$data['empleado_id'])) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Para servicios de peluquería, debes asignar un empleado con rol Peluquero.'];
            header('Location: /vetsmart/recepcionista/citas');
            exit;
        }

        // Anti-duplicados / validación de solapamiento teniendo en cuenta la duración del servicio
        try {
            // Obtener duración del servicio en minutos (fallback 30min si no existe)
            $durStmt = $this->pdo->prepare("SELECT duracion_min FROM servicios WHERE id = ? LIMIT 1");
            $durStmt->execute([(int)$data['servicio_id']]);
            $durMin = (int)$durStmt->fetchColumn();
            if ($durMin <= 0) $durMin = 30;

            $newStart = new DateTime((string)$data['fecha']);
            if ($newStart === false) {
                throw new Exception('Fecha/hora inválida');
            }
            $newEnd = (clone $newStart)->add(new DateInterval('PT' . $durMin . 'M'));

            // Traer citas existentes del mismo día para el empleado (SOLO empleado, sin importar mascota o cliente)
            $stmt = $this->pdo->prepare("SELECT id, fecha, servicio_id, empleado_id, mascota_id FROM citas WHERE DATE(fecha) = DATE(:fecha) AND empleado_id = :empleado_id AND estado != 'cancelada'");
            $stmt->execute([
                ':fecha' => $data['fecha'],
                ':empleado_id' => $data['empleado_id']
            ]);
            $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($existing as $ex) {
                try {
                    $exStart = new DateTime($ex['fecha']);
                } catch (Throwable $ee) {
                    error_log("[OVERLAP CHECK STORE] Fecha existente inválida: " . $ex['fecha']);
                    continue;
                }

                // Duración del servicio existente
                $dStmt = $this->pdo->prepare("SELECT duracion_min, nombre FROM servicios WHERE id = ? LIMIT 1");
                $dStmt->execute([(int)$ex['servicio_id']]);
                $svcRow = $dStmt->fetch(PDO::FETCH_ASSOC);
                $exDur = isset($svcRow['duracion_min']) ? (int)$svcRow['duracion_min'] : 0;
                $svcName = $svcRow['nombre'] ?? 'Servicio';
                if ($exDur <= 0) $exDur = 30;
                $exEnd = (clone $exStart)->add(new DateInterval('PT' . $exDur . 'M'));

                // Log de depuración
                error_log("[OVERLAP CHECK STORE] Nueva: " . $newStart->format('Y-m-d H:i:s') . " - " . $newEnd->format('Y-m-d H:i:s'));
                error_log("[OVERLAP CHECK STORE] Existente: " . $exStart->format('Y-m-d H:i:s') . " - " . $exEnd->format('Y-m-d H:i:s'));
                error_log("[OVERLAP CHECK STORE] Condición 1 (newStart < exEnd): " . ($newStart < $exEnd ? 'true' : 'false'));
                error_log("[OVERLAP CHECK STORE] Condición 2 (exStart < newEnd): " . ($exStart < $newEnd ? 'true' : 'false'));

                // Solapamiento: newStart < exEnd AND exStart < newEnd
                if ($newStart < $exEnd && $exStart < $newEnd) {
                    $conflictTime = $exStart->format('H:i');
                    $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => "Conflicto: existe una cita que se solapa a las $conflictTime (servicio: $svcName). Por favor reprograme." ];
                    header('Location: /vetsmart/recepcionista/citas');
                    exit;
                }
            }
        } catch (Throwable $e) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Error al validar disponibilidad. Intenta nuevamente.'];
            header('Location: /vetsmart/recepcionista/citas');
            exit;
        }

        // Validar que la fecha completa no esté en el pasado
        try {
            $tz = new DateTimeZone('America/Bogota');
            $dt = new DateTime((string)$data['fecha'], $tz);
            $now = new DateTime('now', $tz);
            if ($dt < $now) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se permiten citas en fechas u horas pasadas.'];
                header('Location: /vetsmart/recepcionista/citas');
                exit;
            }
        } catch (Throwable $e) {
<<<<<<< HEAD
            // si la fecha es inválida, rechazamos
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Fecha inválida.'];
=======
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Fecha/Hora inválida.'];
>>>>>>> 551a971277bd5d0b94296071273044a14c300280
            header('Location: /vetsmart/recepcionista/citas');
            exit;
        }

        $sql = "INSERT INTO citas (fecha, cliente_id, mascota_id, empleado_id, servicio_id, estado, notas)
                VALUES (:fecha, :cliente_id, :mascota_id, :empleado_id, :servicio_id, :estado, :notas)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':fecha' => $data['fecha'],
            ':cliente_id' => $data['cliente_id'],
            ':mascota_id' => $data['mascota_id'],
            ':empleado_id' => $data['empleado_id'],
            ':servicio_id' => $data['servicio_id'],
            ':estado' => $data['estado'],
            ':notas' => $data['notas'],
        ]);

        // Obtener datos para enviar correo de confirmación
        try {
            $citaQuery = $this->pdo->prepare("
                SELECT 
                    c.fecha,
                    TIME(c.fecha) AS hora,
                    CONCAT(cli.nombre, ' ', cli.apellido) AS cliente_nombre,
                    cli.email AS cliente_email,
                    m.nombre AS mascota,
                    s.nombre AS servicio,
                    CONCAT(emp.nombre, ' ', emp.apellido) AS empleado,
                    CASE WHEN emp.role_id = 2 THEN 'Veterinario' WHEN emp.role_id = 4 THEN 'Peluquero' ELSE 'Otro' END AS tipo_empleado
                FROM citas c
                LEFT JOIN usuarios cli ON c.cliente_id = cli.id
                LEFT JOIN usuarios emp ON c.empleado_id = emp.id
                LEFT JOIN servicios s ON c.servicio_id = s.id
                LEFT JOIN mascotas m ON c.mascota_id = m.id
                WHERE c.cliente_id = :cliente_id AND c.fecha = :fecha
                ORDER BY c.id DESC LIMIT 1
            ");
            $citaQuery->execute([
                ':cliente_id' => $data['cliente_id'],
                ':fecha' => $data['fecha']
            ]);
            $citaInfo = $citaQuery->fetch(PDO::FETCH_ASSOC);

            error_log("📝 [CITA] Buscando cita para cliente_id: " . $data['cliente_id'] . ", fecha: " . $data['fecha']);
            
            if ($citaInfo) {
                error_log("📝 [CITA] Cita encontrada - Email cliente: " . ($citaInfo['cliente_email'] ?? 'SIN EMAIL'));
                error_log("📝 [CITA] Datos: " . json_encode($citaInfo));
                
                if (!empty($citaInfo['cliente_email'])) {
                    require_once APP_ROOT . '/helpers/EmailHelper.php';
                    $mailSent = EmailHelper::enviarConfirmacionCita(
                        $citaInfo['cliente_email'],
                        $citaInfo['cliente_nombre'],
                        [
                            'fecha' => $citaInfo['fecha'],
                            'hora' => $citaInfo['hora'],
                            'mascota' => $citaInfo['mascota'],
                            'servicio' => $citaInfo['servicio'],
                            'empleado' => $citaInfo['empleado'],
                            'tipo_empleado' => $citaInfo['tipo_empleado']
                        ]
                    );
                } else {
                    error_log("⚠️  [CITA] El cliente no tiene email registrado");
                }
            } else {
                error_log("❌ [CITA] No se encontró la cita creada");
            }
        } catch (Throwable $e) {
            error_log("❌ [CITA] Error al enviar correo de confirmación de cita: " . $e->getMessage());
            error_log("❌ [CITA] Trace: " . $e->getTraceAsString());
        }

        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Cita creada exitosamente.'];
        header('Location: /vetsmart/recepcionista/citas');
        exit;
    }

    private function esServicioPeluqueria(int $servicioId): bool
    {
        if ($servicioId <= 0) return false;
        try {
            // Preferir columna categoria si existe
            $q = $this->pdo->prepare("SELECT nombre, COALESCE(categoria,'') AS categoria FROM servicios WHERE id = ?");
            $q->execute([$servicioId]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
            if (!$row) return false;
            $cat = strtolower((string)($row['categoria'] ?? ''));
            if ($cat === 'peluqueria') return true;
            $nom = strtolower((string)($row['nombre'] ?? ''));
            return (str_contains($nom, 'peluquer') || str_contains($nom, 'bañ') || str_contains($nom, 'ban') || str_contains($nom, 'cort') || str_contains($nom, 'spa'));
        } catch (Throwable $e) {
            return false;
        }
    }

    private function esPeluquero(int $usuarioId): bool
    {
        if ($usuarioId <= 0) return false;
        $q = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios u INNER JOIN roles r ON r.id = u.role_id WHERE u.id = ? AND r.nombre = 'peluquero'");
        $q->execute([$usuarioId]);
        return ((int)$q->fetchColumn()) > 0;
    }

    private function renderView(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include APP_ROOT . '/views/' . $view . '.php';
        return ob_get_clean();
    }

    // Mostrar formulario de edición
    public function edit(int $id): void
    {
        $this->verificarSesion();

        // Obtener la cita actual
        $stmt = $this->pdo->prepare("
            SELECT * FROM citas WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cita) {
            die('Cita no encontrada');
        }

        // Traer datos para los selects
        $clientes = $this->pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE role_id = 6")->fetchAll(PDO::FETCH_ASSOC);
        $empleados = $this->pdo->query("SELECT u.id, u.nombre, u.apellido, r.nombre AS rol
                                        FROM usuarios u
                                        INNER JOIN roles r ON r.id = u.role_id
                                        WHERE r.nombre IN ('veterinario','peluquero')")->fetchAll(PDO::FETCH_ASSOC);
        $servicios = $this->pdo->query("SELECT id, nombre FROM servicios")->fetchAll(PDO::FETCH_ASSOC);
        $mascotas = $this->pdo->query("SELECT id, nombre FROM mascotas")->fetchAll(PDO::FETCH_ASSOC);

        // Renderizar el formulario
        $content = $this->renderView('recepcionista/editar_cita', [
            'cita' => $cita,
            'clientes' => $clientes,
            'empleados' => $empleados,
            'servicios' => $servicios,
            'mascotas' => $mascotas,
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    // Guardar actualización
    public function update(): void
    {
        $this->verificarSesion();
        // CSRF
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
            header('Location: /vetsmart/recepcionista/citas');
            exit;
        }

        $data = [
            'id' => $_POST['id'] ?? null,
            'fecha' => $_POST['fecha'] ?? null,
            'cliente_id' => $_POST['cliente_id'] ?? null,
            'mascota_id' => $_POST['mascota_id'] ?? null,
            'empleado_id' => $_POST['empleado_id'] ?? null,
            'servicio_id' => $_POST['servicio_id'] ?? null,
            'estado' => $_POST['estado'] ?? 'pendiente',
            'notas' => $_POST['notas'] ?? ''
        ];

        // Validación de rol para servicios de peluquería
        if ($this->esServicioPeluqueria((int)$data['servicio_id']) && !$this->esPeluquero((int)$data['empleado_id'])) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Para servicios de peluquería, debes asignar un empleado con rol Peluquero.'];
            header('Location: /vetsmart/recepcionista/citas');
            exit;
        }
        // Normalizar estado permitido
        $allowed = ['pendiente','completada'];
        if (!in_array(strtolower((string)$data['estado']), $allowed, true)) {
            $data['estado'] = 'pendiente';
        }

        // Anti-duplicados en actualización (excluye la misma cita)
        $check = $this->pdo->prepare(
            "SELECT COUNT(*) FROM citas 
             WHERE id <> :id 
               AND DATE(fecha)=DATE(:f1) AND TIME(fecha)=TIME(:f2)
               AND (empleado_id = :empleado_id OR mascota_id = :mascota_id)"
        );
        $check->execute([
            ':id' => $data['id'],
            ':f1' => $data['fecha'],
            ':f2' => $data['fecha'],
            ':empleado_id' => $data['empleado_id'],
            ':mascota_id' => $data['mascota_id'],
        ]);
        if ((int)$check->fetchColumn() > 0) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Conflicto: ya existe una cita con esa fecha y hora.'];
            header('Location: /vetsmart/citas/edit/' . (int)$data['id']);
            exit;
        }

        $sql = "UPDATE citas 
                SET fecha = :fecha,
                    cliente_id = :cliente_id,
                    mascota_id = :mascota_id,
                    empleado_id = :empleado_id,
                    servicio_id = :servicio_id,
                    estado = :estado,
                    notas = :notas
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $data['id'],
            ':fecha' => $data['fecha'],
            ':cliente_id' => $data['cliente_id'],
            ':mascota_id' => $data['mascota_id'],
            ':empleado_id' => $data['empleado_id'],
            ':servicio_id' => $data['servicio_id'],
            ':estado' => $data['estado'],
            ':notas' => $data['notas'],
        ]);

        header('Location: /vetsmart/recepcionista/citas');
        exit;
    }

    public function delete(int $id): void
    {
        $this->verificarSesion();

        $stmt = $this->pdo->prepare("DELETE FROM citas WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header('Location: /vetsmart/recepcionista/citas');
        exit;
    }
}
