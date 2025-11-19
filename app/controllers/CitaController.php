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

        // Anti-duplicados: misma fecha+hora para peluquero o mascota
        $check = $this->pdo->prepare("SELECT COUNT(*) FROM cpeluq WHERE fecha=? AND hora=? AND (peluquero_id=? OR mascota_id=?)");
        $check->execute([$fecha, $hora, $peluquero_id, $mascota_id]);
        if ((int)$check->fetchColumn() > 0) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Conflicto: ya existe una cita en ese horario.'];
            header('Location: /vetsmart/recepcionista/citas-peluqueria/create');
            exit;
        }

        $ins = $this->pdo->prepare("INSERT INTO cpeluq (cliente_id, mascota_id, peluquero_id, servicio_id, fecha, hora, estado, observaciones)
                                     VALUES (?,?,?,?,?,?, 'pendiente', ?)");
        $ins->execute([$cliente_id, $mascota_id, $peluquero_id, $servicio_id, $fecha, $hora, $observaciones]);

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
            header('Location: /vetsmart/recepcionista/citas/create');
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

        // Validación de rol para servicios de peluquería
        if ($this->esServicioPeluqueria((int)$data['servicio_id']) && !$this->esPeluquero((int)$data['empleado_id'])) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Para servicios de peluquería, debes asignar un empleado con rol Peluquero.'];
            header('Location: /vetsmart/recepcionista/citas/create');
            exit;
        }

        // Anti-duplicados: misma fecha y hora para el mismo empleado o la misma mascota
        // Nota: con ATTR_EMULATE_PREPARES=false no se puede reutilizar el mismo placeholder dos veces
        $check = $this->pdo->prepare(
            "SELECT COUNT(*) FROM citas 
             WHERE DATE(fecha)=DATE(:f1) AND TIME(fecha)=TIME(:f2)
               AND (empleado_id = :empleado_id OR mascota_id = :mascota_id)"
        );
        $check->execute([
            ':f1' => $data['fecha'],
            ':f2' => $data['fecha'],
            ':empleado_id' => $data['empleado_id'],
            ':mascota_id' => $data['mascota_id'],
        ]);
        if ((int)$check->fetchColumn() > 0) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Ya existe una cita para esa fecha y hora (empleado o mascota).'];
            header('Location: /vetsmart/recepcionista/citas/create');
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

        header('Location: /vetsmart/recepcionista/agenda');
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
