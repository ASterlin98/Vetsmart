<?php
declare(strict_types=1);

class RecepcionistaController extends Controller
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function verificarSesion(): void
    {
        if (empty($_SESSION['user']) || ( ($_SESSION['user']['role_name'] ?? '') !== 'recepcionista' && ($_SESSION['user']['role'] ?? '') !== 'recepcionista')) {
            header('Location: ' . BASE . '/login');
            exit;
        }
    }

    public function dashboard(): void{

        $this->verificarSesion();

        try {
            // Totales
            $totalClientes = (int)$this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE role_id = 6")->fetchColumn();
            $totalMascotas = (int)$this->pdo->query("SELECT COUNT(*) FROM mascotas")->fetchColumn();
            $totalCitas    = (int)$this->pdo->query("SELECT COUNT(*) FROM citas")->fetchColumn();

            // Citas del dia
            $hoy = date('Y-m-d');
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM citas WHERE DATE(fecha) = :hoy");
            $stmt->execute([':hoy' => $hoy]);
            $citasHoy = (int)$stmt->fetchColumn();

            // Citas pendientes
            $pendientes = (int)$this->pdo->query("SELECT COUNT(*) FROM citas WHERE estado = 'pendiente'")->fetchColumn();

            // Estados de citas de hoy (para grafico)
            $estadosHoy = [];
            $stmt = $this->pdo->prepare("SELECT estado, COUNT(*) total FROM citas WHERE DATE(fecha)=:hoy GROUP BY estado");
            $stmt->execute([':hoy' => $hoy]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $estadosHoy[$row['estado']] = (int)$row['total'];
            }

            // proximas citas de todos (veterinarios y peluqueros) - TOP 10
            $stmt = $this->pdo->prepare("\n                
            SELECT TIME(c.fecha) AS hora, DATE(c.fecha) AS fecha, c.id,
                       CONCAT(cli.nombre,' ',cli.apellido) AS cliente,
                       m.nombre AS mascota,
                       s.nombre AS servicio,
                       CONCAT(emp.nombre,' ',emp.apellido) AS empleado,
                       CASE WHEN emp.role_id = 2 THEN 'Veterinario' WHEN emp.role_id = 4 THEN 'Peluquero' ELSE 'Otro' END AS tipo_empleado,
                       c.estado
                FROM citas c
                LEFT JOIN usuarios cli ON c.cliente_id = cli.id
                LEFT JOIN usuarios emp ON c.empleado_id = emp.id
                LEFT JOIN servicios s ON c.servicio_id = s.id
                LEFT JOIN mascotas m ON c.mascota_id = m.id
                WHERE c.fecha >= NOW()
                ORDER BY c.fecha ASC
                LIMIT 10
            ");
            $stmt->execute();
            $proximas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Top servicios utimos 30 dias (para grafico)
            $stmt = $this->pdo->prepare("\n                
            SELECT s.nombre AS servicio, COUNT(*) AS total
                FROM citas c
                JOIN servicios s ON c.servicio_id = s.id
                WHERE c.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY s.id, s.nombre
                ORDER BY total DESC
                LIMIT 5
            ");
            $stmt->execute();
            $topServicios = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Ingresos/Egresos de hoy (si existe la tabla movimientos)
            $ingresosHoy = 0.0; $egresosHoy = 0.0;
            try {
                $st = $this->pdo->prepare("SELECT tipo, SUM(monto) AS monto FROM movimientos WHERE fecha = :hoy GROUP BY tipo");
                $st->execute([':hoy' => $hoy]);
                foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                    if (($r['tipo'] ?? '') === 'ingreso') { $ingresosHoy = (float)$r['monto']; }
                    if (($r['tipo'] ?? '') === 'egreso') { $egresosHoy  = (float)$r['monto']; }
                }
            } catch (Throwable $e) {
                // si no existe la tabla o falla, mantener 0
            }

        } catch (PDOException $e) {
            // En caso de error DB, evita cortar la vista: poner ceros y registrar el error
            error_log("Dashboard error: " . $e->getMessage());
            $totalClientes = $totalMascotas = $totalCitas = $citasHoy = $pendientes = 0;
            $estadosHoy = [];
            $proximas = [];
            $topServicios = [];
            $ingresosHoy = 0.0; $egresosHoy = 0.0;
        }

        // Pasar variables a la vista
        $content = $this->renderView('recepcionista/dashboard', [
            'totalClientes' => $totalClientes,
            'totalMascotas' => $totalMascotas,
            'totalCitas'    => $totalCitas,
            'citasHoy'      => $citasHoy,
            'pendientes'    => $pendientes,
            'estadosHoy'    => $estadosHoy,
            'proximas'      => $proximas,
            'topServicios'  => $topServicios,
            'ingresosHoy'   => $ingresosHoy,
            'egresosHoy'    => $egresosHoy,
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function agenda(): void
    {
        $this->verificarSesion();

        // Paginación
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Filtros: fecha (YYYY-mm-dd) opcional y estado
        $fecha = isset($_GET['fecha']) ? trim((string)$_GET['fecha']) : '';
        if ($fecha !== '') {
            $dt = DateTime::createFromFormat('Y-m-d', $fecha);
            if (!$dt || $dt->format('Y-m-d') !== $fecha) { $fecha = ''; }
        }

        $estado = isset($_GET['estado']) ? trim((string)$_GET['estado']) : '';
        $estadoValido = in_array($estado, ['pendiente', 'completada'], true);

        // Construir WHERE dinámico y parámetros
        $where = ' WHERE 1=1 ';
        $params = [];
        if ($fecha !== '') {
            $where .= ' AND DATE(c.fecha) = :fecha ';
            $params[':fecha'] = $fecha;
        }
        if ($estadoValido) {
            $where .= ' AND c.estado = :estado ';
            $params[':estado'] = $estado;
        }

        // Contar total
        try {
            $countSql = "SELECT COUNT(*) FROM citas c" . $where;
            $stmtCount = $this->pdo->prepare($countSql);
            $stmtCount->execute($params);
            $total = (int)$stmtCount->fetchColumn();
        } catch (PDOException $e) {
            $total = 0;
        }

        // Consulta principal con joins y paginación
        $sql = "
            SELECT 
                c.id,
                DATE(c.fecha) AS fecha,
                TIME(c.fecha) AS hora,
                c.duracion_min,
                c.estado,
                c.notas AS notas,
                c.fecha_actualizacion,
                c.actualizado_por,
                u_c.id AS cliente_id,
                u_c.nombre AS cliente_nombre,
                u_c.apellido AS cliente_apellido,
                u_e.id AS empleado_id,
                u_e.nombre AS empleado_nombre,
                u_e.apellido AS empleado_apellido,
                s.nombre AS servicio,
                m.nombre AS mascota_nombre,
                u_up.nombre AS updater_nombre,
                u_up.apellido AS updater_apellido
            FROM citas c
            LEFT JOIN usuarios u_c ON c.cliente_id = u_c.id
            LEFT JOIN usuarios u_e ON c.empleado_id = u_e.id
            LEFT JOIN servicios s ON c.servicio_id = s.id
            LEFT JOIN mascotas m ON c.mascota_id = m.id
            LEFT JOIN usuarios u_up ON c.actualizado_por = u_up.id
        " . $where . " ORDER BY c.fecha ASC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        $content = $this->renderView('recepcionista/agenda_diaria', [
            'fecha' => $fecha,
            'estado' => $estadoValido ? $estado : '',
            'citas' => $citas,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }
    

    public function citas(): void
    {
        $this->verificarSesion();

        $estado = isset($_GET['estado']) ? trim((string)$_GET['estado']) : '';
        $validEstados = ['pendiente','completada'];

        $sql = "
            SELECT 
                c.id, 
                DATE(c.fecha) AS fecha,
                TIME(c.fecha) AS hora,
                c.estado,
                c.duracion_min,
                uc.nombre AS cliente_nombre, uc.apellido AS cliente_apellido,
                ue.nombre AS empleado_nombre, ue.apellido AS empleado_apellido,
                s.nombre AS servicio,
                m.nombre AS mascota_nombre
            FROM citas c
            LEFT JOIN usuarios uc ON c.cliente_id = uc.id
            LEFT JOIN usuarios ue ON c.empleado_id = ue.id
            LEFT JOIN servicios s ON c.servicio_id = s.id
            LEFT JOIN mascotas m ON c.mascota_id = m.id
            WHERE 1=1
        ";
        $params = [];
        if ($estado !== '' && in_array(strtolower($estado), $validEstados, true)) {
            $sql .= " AND c.estado = :estado ";
            $params[':estado'] = strtolower($estado);
        }
        $sql .= " ORDER BY c.fecha DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $content = $this->renderView('recepcionista/citas_index', [
            'citas' => $citas,
            'estado' => ($estado !== '' && in_array(strtolower($estado), $validEstados, true)) ? strtolower($estado) : ''
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }


    public function clientes(): void
    {
        $this->verificarSesion();

        try {
            // Paginación: 6 clientes por página
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 6;
            $offset = ($page - 1) * $perPage;

            // Contar total de clientes con role_id = 6
            $total = 0;
            $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE role_id = 6");
            $stmtCount->execute();
            $total = (int)$stmtCount->fetchColumn();

            $clientes = [];
            if ($total > 0) {
                $sql = "
                SELECT 
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.email,
                    u.telefono,
                    u.direccion,
                    u.foto AS foto,
                    u.created_at
                FROM usuarios u
                WHERE u.role_id = 6
                ORDER BY u.nombre ASC
                LIMIT " . (int)$perPage . " OFFSET " . (int)$offset . "
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } else {
                // Fallback: incluir usuarios que figuran como dueños de mascotas (por si role_id no es 6)
                try {
                    $ownerCol = $this->resolveMascotaOwnerColumn();
                    $stmtCount2 = $this->pdo->prepare("SELECT COUNT(DISTINCT u.id) FROM usuarios u WHERE EXISTS (SELECT 1 FROM mascotas m WHERE m.`{$ownerCol}` = u.id)");
                    $stmtCount2->execute();
                    $total = (int)$stmtCount2->fetchColumn();

                    if ($total > 0) {
                        $sql2 = "
                        SELECT DISTINCT
                            u.id, u.nombre, u.apellido, u.email, u.telefono, u.direccion, u.foto AS foto, u.created_at
                        FROM usuarios u
                        WHERE EXISTS (SELECT 1 FROM mascotas m WHERE m.`{$ownerCol}` = u.id)
                        ORDER BY u.nombre ASC
                        LIMIT " . (int)$perPage . " OFFSET " . (int)$offset . "
                        ";
                        $stmt2 = $this->pdo->prepare($sql2);
                        $stmt2->execute();
                        $clientes = $stmt2->fetchAll(PDO::FETCH_ASSOC) ?: [];
                    }
                } catch (Throwable $e2) { /* ignore */ }
            }

            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        } catch (PDOException $e) {
            // En caso de error en la consulta
            $clientes = [];
            $total = 0;
            $page = 1;
            $perPage = 6;
            $totalPages = 1;
            $_SESSION['mensaje'] = [
                'tipo' => 'danger',
                'texto' => 'Error al cargar los clientes: ' . $e->getMessage()
            ];
        }

        // Recuperar mensaje de <sesio></sesio>n si existe
        $mensaje = $_SESSION['mensaje'] ?? null;
        if (isset($_SESSION['mensaje'])) {
            unset($_SESSION['mensaje']);
        }

        // Renderizar vista
        $content = $this->renderView('recepcionista/clientes_index', [
            'clientes' => $clientes,
            'mensaje' => $mensaje,
            'page' => $page ?? 1,
            'perPage' => $perPage ?? 6,
            'total' => $total ?? count($clientes),
            'totalPages' => $totalPages ?? 1
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    // Mostrar formulario de creacion de cliente
    public function createCliente(): void
    {
        $this->verificarSesion();

        $mensajeError = $_SESSION['error_cliente'] ?? null;
        unset($_SESSION['error_cliente']);

        $content = $this->renderView('recepcionista/cliente_create', [
            'mensajeError' => $mensajeError
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    // Guardar cliente <nuevo></nuevo>
  public function storeCliente(): void
    {
        $this->verificarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE . '/recepcionista/clientes/create');
            exit;
        }
        // CSRF
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['error_cliente'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
            header('Location: ' . BASE . '/recepcionista/clientes/create');
            exit;
        }

        // campos cliente
        $nombre    = trim($_POST['nombre'] ?? '');
        $apellido  = trim($_POST['apellido'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $docusu    = trim($_POST['documento'] ?? ''); //ahora se guarda en docusu
        $password  = $_POST['password'] ?? '';

        // mascotas
        $pets = $_POST['pets'] ?? [];
        // Asegurar columna dueño correcta para futuras inserciones de mascotas
        $dueno_col = $this->resolveMascotaOwnerColumn();

        // Subida de foto del cliente (opcional) -> guardar solo nombre en BD
        $fotoClienteNombre = null;
        if (!empty($_FILES['foto']['name'] ?? '')) {
            $uploadDir = dirname(APP_ROOT) . '/public/assets/uploads/clientes';
            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png'])) {
                $fn = 'client_' . time() . '_' . mt_rand(100000, 999999) . '.' . $ext;
                $dest = $uploadDir . '/' . $fn;
                if (@move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                    $fotoClienteNombre = $fn;
                }
            }
        }

        try {
            // validar duplicados (email o documento)
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = :email OR docusu = :docusu");
            $stmt->execute([':email' => $email, ':docusu' => $docusu]);
            if ($stmt->fetch()) {
                $_SESSION['error_cliente'] = [
                    'tipo' => 'danger',
                    'texto' => 'Ya existe un cliente con ese correo o documento.'
                ];
                header('Location: ' . BASE . '/recepcionista/clientes/create');
                exit;
            }

            // iniciar transaccion
            $this->pdo->beginTransaction();

            // insertar cliente
            $pwdHash = password_hash($password ?: bin2hex(random_bytes(4)), PASSWORD_BCRYPT);
            $insertClient = $this->pdo->prepare("
                INSERT INTO usuarios (nombre, apellido, docusu, email, telefono, direccion, password, role_id, created_at)
                VALUES (:nombre, :apellido, :docusu, :email, :telefono, :direccion, :password, 6, NOW())
            ");
            $insertClient->execute([
                ':nombre'    => $nombre,
                ':apellido'  => $apellido,
                ':docusu'    => $docusu,
                ':email'     => $email,
                ':telefono'  => $telefono,
                ':direccion' => $direccion,
                ':password'  => $pwdHash
            ]);

            $clienteId = (int)$this->pdo->lastInsertId();

            // Asegurar que se guarda solo el nombre de la foto si se subio en este alta
            if ($clienteId > 0 && !empty($fotoClienteNombre) && $this->hasColumn('usuarios','foto')) {
                try {
                    $this->pdo->prepare('UPDATE usuarios SET foto = :f WHERE id = :id')
                        ->execute([':f' => $fotoClienteNombre, ':id' => $clienteId]);
                } catch (Throwable $e) { /* ignore */ }
            }

            // insertar mascotas
            if (!empty($pets) && is_array($pets)) {
                $insertPet = $this->pdo->prepare("
                    INSERT INTO mascotas (nombre, especie, raza, edad, sexo, {$dueno_col}, created_at)
                    VALUES (:nombre, :especie, :raza, :edad, :sexo, :dueno, NOW())
                ");
                foreach ($pets as $p) {
                    $pnombre  = trim($p['nombre'] ?? '');
                    $pespecie = trim($p['especie'] ?? '');
                    $praza    = trim($p['raza'] ?? '');
                    $pedad    = trim($p['edad'] ?? null);
                    $psexo    = trim($p['sexo'] ?? null);

                    if ($pnombre === '') continue;

                    $insertPet->execute([
                        ':nombre'  => $pnombre,
                        ':especie' => $pespecie,
                        ':raza'    => $praza,
                        ':edad'    => $pedad !== '' ? $pedad : null,
                        ':sexo'    => $psexo,
                        ':dueno'   => $clienteId
                    ]);
                }
            }

            // confirmar
            $this->pdo->commit();

            $_SESSION['mensaje'] = [
                'tipo' => 'success',
                'texto' => "Cliente y mascotas registradas correctamente."
            ];

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $_SESSION['error_cliente'] = [
                    'tipo' => 'danger',
                    'texto' => 'El correo o documento ya estan registrados.'
                ];
                header('Location: ' . BASE . '/recepcionista/clientes/create');
                exit;
            }

            $_SESSION['error_cliente'] = [
                'tipo' => 'danger',
                'texto' => 'Error interno: ' . $e->getMessage()
            ];
            header('Location: ' . BASE . '/recepcionista/clientes/create');
            exit;
        }

        header('Location: ' . BASE . '/recepcionista/clientes');
        exit;
    }

    //  Mostrar formulario de edicion de cliente
    public function editCliente(int $id): void
    {
        $this->verificarSesion();

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = :id AND role_id = 6");
            $stmt->execute([':id' => $id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cliente) {
                $_SESSION['mensaje'] = [
                    'tipo' => 'danger',
                    'texto' => 'Cliente no encontrado.'
                ];
                header('Location: ' . BASE . '/recepcionista/clientes');
                exit;
            }

            $content = $this->renderView('recepcionista/cliente_edit', [
                'cliente' => $cliente
            ]);

            require APP_ROOT . '/views/layouts/main_recepcionista.php';
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = [
                'tipo' => 'danger',
                'texto' => 'Error interno: ' . $e->getMessage()
            ];
            header('Location: ' . BASE . '/recepcionista/clientes');
            exit;
        }
    }


    //  Guardar cambios del cliente
    public function updateCliente(): void
    {
        $this->verificarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
            header('Location: ' . BASE . '/recepcionista/clientes');
            exit;
        }
        // CSRF
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
            header('Location: ' . BASE . '/recepcionista/clientes');
            exit;
        }

        $id        = (int) $_POST['id'];
        $nombre    = trim($_POST['nombre'] ?? '');
        $apellido  = trim($_POST['apellido'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $docusu    = trim($_POST['documento'] ?? '');

        try {
            // Validar duplicados (email/documento) en otro cliente
            $dup = $this->pdo->prepare("SELECT id FROM usuarios WHERE (email = :email OR docusu = :docusu) AND id <> :id");
            $dup->execute([':email' => $email, ':docusu' => $docusu, ':id' => $id]);
            if ($dup->fetch()) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Correo o documento ya registrado por otro cliente.'];
                header('Location: ' . BASE . '/recepcionista/clientes');
                exit;
            }
            $stmt = $this->pdo->prepare("
                UPDATE usuarios
                SET nombre = :nombre,
                    apellido = :apellido,
                    email = :email,
                    telefono = :telefono,
                    direccion = :direccion,
                    docusu = :docusu
                WHERE id = :id AND role_id = 6
            ");
            $stmt->execute([
                ':nombre'    => $nombre,
                ':apellido'  => $apellido,
                ':email'     => $email,
                ':telefono'  => $telefono,
                ':direccion' => $direccion,
                ':docusu'    => $docusu,
                ':id'        => $id
            ]);

            // Actualizar foto si se sube una nueva (solo nombre de archivo)
            if (!empty($_FILES['foto']['name'] ?? '')) {
                try {
                    $q = $this->pdo->prepare('SELECT foto FROM usuarios WHERE id = :id');
                    $q->execute([':id' => $id]);
                    $prev = $q->fetchColumn();

                    $uploadDir = dirname(APP_ROOT) . '/public/assets/uploads/clientes';
                    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
                    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png'])) {
                        $fn = 'client_' . time() . '_' . mt_rand(100000, 999999) . '.' . $ext;
                        $dest = $uploadDir . '/' . $fn;
                        if (@move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                            $this->pdo->prepare('UPDATE usuarios SET foto = :f WHERE id = :id')->execute([':f' => $fn, ':id' => $id]);
                            if (!empty($prev)) {
                                $prevPath = dirname(APP_ROOT) . '/public/assets/uploads/clientes/' . basename((string)$prev);
                                if (@is_file($prevPath)) { @unlink($prevPath); }
                            }
                        }
                    }
                } catch (Throwable $e) { /* ignore */ }
            }

            $_SESSION['mensaje'] = [
                'tipo' => 'success',
                'texto' => 'Cliente actualizado correctamente.'
            ];
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = [
                'tipo' => 'danger',
                'texto' => 'Error al actualizar: ' . $e->getMessage()
            ];
        }

        header('Location: ' . BASE . '/recepcionista/clientes');
        exit;
    }


    //  Eliminar cliente
    public function deleteCliente(int $id): void
    {
        $this->verificarSesion();
        try {
            $this->pdo->beginTransaction();

            // Detectar columna de dueño (dueno_id o dueno_id)
            $ownerCol = 'dueno_id';
            try {
                $q = $this->pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mascotas' AND COLUMN_NAME IN ('dueno_id','dueno_id') ORDER BY FIELD(COLUMN_NAME,'dueno_id','dueno_id') LIMIT 1");
                $col = $q->fetchColumn();
                if ($col) { $ownerCol = $col; }
            } catch (Throwable $e) { /* fallback dueno_id */ }

            // Obtener mascotas del cliente
            $stmt = $this->pdo->prepare("SELECT id FROM mascotas WHERE `{$ownerCol}` = :cid");
            $stmt->execute([':cid' => $id]);
            $mascotasIds = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

            // Eliminar dependencias por mascota
            if (!empty($mascotasIds)) {
                $delNotas = $this->pdo->prepare("DELETE FROM notas_mascotas WHERE mascota_id = :mid");
                $delConsultas = $this->pdo->prepare("DELETE FROM consultas WHERE mascota_id = :mid");
                $delCitasPorMascota = $this->pdo->prepare("DELETE FROM citas WHERE mascota_id = :mid");
                $delVacunas = $this->pdo->prepare("DELETE FROM vacunas WHERE mascota_id = :mid");
                foreach ($mascotasIds as $mid) {
                    $delNotas->execute([':mid' => $mid]);
                    $delConsultas->execute([':mid' => $mid]);
                    $delCitasPorMascota->execute([':mid' => $mid]);
                    $delVacunas->execute([':mid' => $mid]);
                }

                // Eliminar mascotas del cliente
                $delMascotas = $this->pdo->prepare("DELETE FROM mascotas WHERE `{$ownerCol}` = :cid");
                $delMascotas->execute([':cid' => $id]);
            }

            // Eliminar citas del cliente (por si hay alguna sin mascota)
            $this->pdo->prepare("DELETE FROM citas WHERE cliente_id = :cid")->execute([':cid' => $id]);

            // Eliminar cliente
            $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id AND role_id = 6")->execute([':id' => $id]);

            $this->pdo->commit();

            $_SESSION['mensaje'] = [
                'tipo' => 'success',
                'texto' => 'Cliente eliminado correctamente.'
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
            $_SESSION['mensaje'] = [
                'tipo' => 'danger',
                'texto' => 'Error al eliminar cliente: ' . $e->getMessage()
            ];
        }

        header('Location: ' . BASE . '/recepcionista/clientes');
        exit;
    }

    // Listar mascotas con paginación (6 por página)
    public function mascotas(): void
    {
        $this->verificarSesion();

        try {
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 6;
            $offset = ($page - 1) * $perPage;

            // Contar total de mascotas
            $total = 0;
            $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM mascotas");
            $stmtCount->execute();
            $total = (int)$stmtCount->fetchColumn();

            $mascotas = [];
            if ($total > 0) {
                $ownerCol = $this->resolveMascotaOwnerColumn();
                $sql = "
                    SELECT m.*, u.nombre AS dueno_nombre, u.apellido AS dueno_apellido
                    FROM mascotas m
                    LEFT JOIN usuarios u ON m.`{$ownerCol}` = u.id
                    ORDER BY m.nombre ASC
                    LIMIT " . (int)$perPage . " OFFSET " . (int)$offset . "
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }

            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
        } catch (PDOException $e) {
            $mascotas = [];
            $total = 0;
            $page = 1;
            $perPage = 6;
            $totalPages = 1;
            $_SESSION['mensaje'] = [
                'tipo' => 'danger',
                'texto' => 'Error al cargar las mascotas: ' . $e->getMessage()
            ];
        }

        $mensaje = $_SESSION['mensaje'] ?? null;
        if (isset($_SESSION['mensaje'])) { unset($_SESSION['mensaje']); }

        $content = $this->renderView('recepcionista/mascotas_index', [
            'mascotas' => $mascotas,
            'mensaje' => $mensaje,
            'page' => $page ?? 1,
            'perPage' => $perPage ?? 6,
            'total' => $total ?? count($mascotas),
            'totalPages' => $totalPages ?? 1
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function crearMascota(): void
    {
        $this->verificarSesion();

        // Obtener todos los clientes para asociar la mascota a un dueño
        $stmt = $this->pdo->prepare("SELECT id, nombre, apellido FROM usuarios WHERE role_id = 6 ORDER BY nombre ASC");
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content = $this->renderView('recepcionista/mascotas_create', [
            'clientes' => $clientes
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }
    public function guardarMascota(): void
    {
        $this->verificarSesion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF
            $token = $_POST['_csrf'] ?? '';
            if (!CSRF::validate($token)) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
                header('Location: ' . BASE . '/recepcionista/mascotas');
                exit;
            }
            // Ruta robusta: actualizar usando la columna de dueño detectada y evitar tildes en placeholders
            try {
                $idR     = (int)($_POST['id'] ?? 0);
                $nombreR = trim($_POST['nombre'] ?? '');
                $duenoIdR = $this->readOwnerFromPost($_POST);
                if ($idR > 0 && $nombreR !== '' && $duenoIdR > 0) {
                    $especieR = trim($_POST['especie'] ?? '');
                    $razaR    = trim($_POST['raza'] ?? '');
                    $edadR    = trim($_POST['edad'] ?? '');
                    $sexoR    = trim($_POST['sexo'] ?? '');

                    $ownerCol = $this->resolveMascotaOwnerColumn();
                    $sqlR = "UPDATE mascotas SET nombre=:nombre, especie=:especie, raza=:raza, edad=:edad, sexo=:sexo, `{$ownerCol}`=:dueno_id WHERE id=:id";
                    $upR = $this->pdo->prepare($sqlR);
                    $upR->execute([
                        ':id' => $idR,
                        ':nombre' => $nombreR,
                        ':especie' => $especieR,
                        ':raza' => $razaR,
                        ':edad' => $edadR,
                        ':sexo' => $sexoR,
                        ':dueno_id' => $duenoIdR,
                    ]);

                    // Foto (opcional)
                    if (!empty($_FILES['foto']['name'] ?? '')) {
                        try {
                            // obtener foto anterior
                            $prev = $this->pdo->prepare('SELECT foto FROM mascotas WHERE id = :id');
                            $prev->execute([':id' => $idR]);
                            $prevFile = $prev->fetchColumn();

                            $uploadDir = dirname(APP_ROOT) . '/public/assets/uploads/mascotas';
                            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
                            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg','jpeg','png'])) {
                                $fn = 'pet_' . time() . '_' . mt_rand(100000, 999999) . '.' . $ext;
                                $dest = $uploadDir . '/' . $fn;
                                if (@move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                                    $u = $this->pdo->prepare('UPDATE mascotas SET foto = :foto WHERE id = :id');
                                    $u->execute([':foto' => $fn, ':id' => $idR]);
                                    if (!empty($prevFile)) {
                                    $prevPath = (strpos((string)$prevFile, '/') === false)
                                            ? (dirname(APP_ROOT) . '/public/assets/uploads/mascotas/' . $prevFile)
                                            : str_replace('/vetsmart', dirname(APP_ROOT), (string)$prevFile);
                                        if (@is_file($prevPath)) { @unlink($prevPath); }
                                    }
                                }
                            }
                        } catch (Throwable $eUp) { /* ignore */ }
                    }

                    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Mascota actualizada correctamente.'];
                    header('Location: ' . BASE . '/recepcionista/mascotas');
                    exit;
                }
            } catch (Throwable $eRobustUpdate) { /* continuar con ruta legada */ }
            // Ruta robusta: insertar usando columna de dueño detectada (evita problemas de ñ/encoding)
            try {
                $nombreR  = trim($_POST['nombre'] ?? '');
                $duenoIdR = $this->readOwnerFromPost($_POST);
                if ($nombreR !== '' && $duenoIdR > 0) {
                    $especieR = trim($_POST['especie'] ?? '');
                    $razaR    = trim($_POST['raza'] ?? '');
                    $edadR    = trim($_POST['edad'] ?? '');
                    $sexoR    = trim($_POST['sexo'] ?? '');

                    $ownerCol = $this->resolveMascotaOwnerColumn();
                    $ins = $this->pdo->prepare(
                        "INSERT INTO mascotas (nombre, especie, raza, edad, sexo, `{$ownerCol}`)
                         VALUES (:nombre, :especie, :raza, :edad, :sexo, :dueno_id)"
                    );
                    $ins->execute([
                        ':nombre'   => $nombreR,
                        ':especie'  => $especieR,
                        ':raza'     => $razaR,
                        ':edad'     => $edadR,
                        ':sexo'     => $sexoR,
                        ':dueno_id' => $duenoIdR,
                    ]);

                    $newId = (int)$this->pdo->lastInsertId();
                    // Foto opcional (guardar solo nombre)
                    if (!empty($_FILES['foto']['name'] ?? '')) {
                        try {
                            $uploadDir = dirname(APP_ROOT) . '/public/assets/uploads/mascotas';
                            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
                            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg','jpeg','png'])) {
                                $fn = 'pet_' . time() . '_' . mt_rand(100000, 999999) . '.' . $ext;
                                $dest = $uploadDir . '/' . $fn;
                                if (@move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                                    if ($newId > 0) {
                                        $u = $this->pdo->prepare('UPDATE mascotas SET foto = :foto WHERE id = :id');
                                        $u->execute([':foto' => $fn, ':id' => $newId]);
                                    }
                                }
                            }
                        } catch (Throwable $eUp) { /* ignore upload error */ }
                    }

                    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Mascota registrada correctamente.'];
                    header('Location: ' . BASE . '/recepcionista/mascotas');
                    exit;
                }
            } catch (Throwable $eRobustCreate) { /* continuar con ruta legada */ }
            $nombre = trim($_POST['nombre'] ?? '');
            $especie = trim($_POST['especie'] ?? '');
            $raza = trim($_POST['raza'] ?? '');
            $edad = trim($_POST['edad'] ?? '');
            $sexo = trim($_POST['sexo'] ?? '');
            $dueno_id = (int)($_POST['dueno_id'] ?? 0);

            if (empty($nombre) || empty($dueno_id)) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'El nombre y el dueño son obligatorios.'];
                header('Location: ' . BASE . '/recepcionista/mascotas/create');
                exit;
            }

            // Subida de foto (opcional) -> guardar solo nombre de archivo
            $fotoNombre = null;
            if (!empty($_FILES['foto']['name'] ?? '')) {
                $uploadDir = dirname(APP_ROOT) . '/public/assets/uploads/mascotas';
                if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png'])) {
                    $fn = 'pet_' . time() . '_' . mt_rand(100000, 999999) . '.' . $ext;
                    $dest = $uploadDir . '/' . $fn;
                    if (@move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                        $fotoNombre = $fn;
                    }
                }
            }

            $stmt = $this->pdo->prepare(" 
                INSERT INTO mascotas (nombre, especie, raza, edad, sexo, dueno_id)
                VALUES (:nombre, :especie, :raza, :edad, :sexo, :dueno_id)
            ");

            $stmt->execute([
                ':nombre' => $nombre,
                ':especie' => $especie,
                ':raza' => $raza,
                ':edad' => $edad,
                ':sexo' => $sexo,
                ':dueno_id' => $dueno_id
            ]);

            // Si subimos foto, actualizarla en el registro recien creado
            if (!empty($fotoNombre)) {
                $newId = (int)$this->pdo->lastInsertId();
                if ($newId > 0) {
                    $u = $this->pdo->prepare("UPDATE mascotas SET foto = :foto WHERE id = :id");
                    $u->execute([':foto' => $fotoNombre, ':id' => $newId]);
                }
            }

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Mascota registrada correctamente.'];
            header('Location: ' . BASE . '/recepcionista/mascotas');
            exit;
        }
    }
    public function editarMascota(int $id): void
    {
        $this->verificarSesion();

        // Obtener datos de la mascota
        $stmt = $this->pdo->prepare("SELECT * FROM mascotas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $mascota = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$mascota) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Mascota no encontrada.'];
            header('Location: ' . BASE . '/recepcionista/mascotas');
            exit;
        }

        // Obtener lista de dueños (clientes)
        $stmt = $this->pdo->prepare("SELECT id, nombre, apellido FROM usuarios WHERE role_id = 6 ORDER BY nombre ASC");
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content = $this->renderView('recepcionista/mascotas_edit', [
            'mascota' => $mascota,
            'clientes' => $clientes
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function actualizarMascota(): void
    {
        $this->verificarSesion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF
            $token = $_POST['_csrf'] ?? '';
            if (!CSRF::validate($token)) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
                header('Location: ' . BASE . '/recepcionista/mascotas');
                exit;
            }
            $id = (int)($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $especie = trim($_POST['especie'] ?? '');
            $raza = trim($_POST['raza'] ?? '');
            $edad = trim($_POST['edad'] ?? '');
            $sexo = trim($_POST['sexo'] ?? '');
            $dueno_id = (int)($_POST['dueno_id'] ?? 0);

            if (empty($nombre) || empty($dueno_id)) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'El nombre y el dueño son obligatorios.'];
                header("Location: " . BASE . "/recepcionista/mascotas/edit/{$id}");
                exit;
            }

            $stmt = $this->pdo->prepare("
                UPDATE mascotas 
                SET nombre = :nombre, especie = :especie, raza = :raza, edad = :edad, sexo = :sexo, dueno_id = :dueno_id
                WHERE id = :id
            ");

            $stmt->execute([
                ':id' => $id,
                ':nombre' => $nombre,
                ':especie' => $especie,
                ':raza' => $raza,
                ':edad' => $edad,
                ':sexo' => $sexo,
                ':dueno_id' => $dueno_id
            ]);

            // Actualizar foto si se sube una nueva (opcional)
            if (!empty($_FILES['foto']['name'] ?? '')) {
                try {
                    // obtener foto anterior para eliminarla luego
                    $prevStmt = $this->pdo->prepare('SELECT foto FROM mascotas WHERE id = :id');
                    $prevStmt->execute([':id' => $id]);
                    $prev = $prevStmt->fetchColumn();

                    $uploadDir = dirname(APP_ROOT) . '/public/assets/uploads/mascotas';
                    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
                    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png'])) {
                        $fn = 'pet_' . time() . '_' . mt_rand(100000, 999999) . '.' . $ext;
                        $dest = $uploadDir . '/' . $fn;
                        if (@move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                            // guardamos solo el nombre de archivo
                            $up = $this->pdo->prepare('UPDATE mascotas SET foto = :foto WHERE id = :id');
                            $up->execute([':foto' => $fn, ':id' => $id]);
                            // eliminar anterior si era un nombre de archivo
                            if (!empty($prev)) {
                                $prevPath = $prev;
                                if (strpos((string)$prev, '/') === false) {
                                    $prevPath = dirname(APP_ROOT) . '/public/assets/uploads/mascotas/' . $prev;
                                } else {
                                    // si venia como ruta absoluta en BD, convertirla al path del FS si apunta a /public/uploads
                                    $prevPath = str_replace('/vetsmart', dirname(APP_ROOT), (string)$prev);
                                }
                                if (@is_file($prevPath)) { @unlink($prevPath); }
                            }
                        }
                    }
                } catch (Throwable $e) {
                    error_log('Upload foto mascota (update) error: ' . $e->getMessage());
                }
            }

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Mascota actualizada correctamente.'];
            header('Location: ' . BASE . '/recepcionista/mascotas');
            exit;
        }
    }

    public function eliminarMascota(int $id): void
    {
        $this->verificarSesion();
        try {
            $this->pdo->beginTransaction();

            // Eliminar dependencias primero
            $this->pdo->prepare("DELETE FROM notas_mascotas WHERE mascota_id = :id")->execute([':id' => $id]);
            $this->pdo->prepare("DELETE FROM consultas WHERE mascota_id = :id")->execute([':id' => $id]);
            $this->pdo->prepare("DELETE FROM citas WHERE mascota_id = :id")->execute([':id' => $id]);
            $this->pdo->prepare("DELETE FROM vacunas WHERE mascota_id = :id")->execute([':id' => $id]);

            // Ahora sí, eliminar mascota
            $this->pdo->prepare("DELETE FROM mascotas WHERE id = :id")->execute([':id' => $id]);

            $this->pdo->commit();

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Mascota eliminada correctamente.'];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo eliminar la mascota: ' . $e->getMessage()];
        }
        header('Location: ' . BASE . '/recepcionista/mascotas');
        exit;
    }


    // Historial  (solo lectura) de una mascota
    public function historialMascota(int $id): void
    {
        $this->verificarSesion();

        // Instanciar modelos necesarios
        $mascotaModel = new Mascota($this->pdo);
        $citaModel = new Cita($this->pdo);
        $consultaModel = new Consulta($this->pdo);
        $notaModel = new NotaMascota($this->pdo);

        // Datos principales de la mascota
        $mascota = $mascotaModel->getByIdConDueno($id);
        if (!$mascota) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Mascota no encontrada.'];
            header('Location: ' . BASE . '/recepcionista/mascotas');
            exit;
        }

        // Historial de citas, consultas y notas
        $citas = $citaModel->getPorMascota($id);
        $consultas = $consultaModel->getByMascota($id);
        $notas = $notaModel->obtenerPorMascota($id);

        // Listar archivos adjuntos (reportes)
        $archivos = [];
        $uploadDir = dirname(APP_ROOT) . '/public/assets/uploads/reportes/' . $id;
        
        // Limpiar caché de estado de archivos para asegurar que no salgan eliminados
        clearstatcache();

        if (is_dir($uploadDir)) {
            $files = scandir($uploadDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($uploadDir . '/' . $file)) {
                    $archivos[] = $file;
                }
            }
        }

        $content = $this->renderView('recepcionista/mascota_historial', [
            'mascota' => $mascota,
            'citas' => $citas,
            'consultas' => $consultas,
            'notas' => $notas,
            'archivos' => $archivos,
        ]);

        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function eliminarArchivoReporte(): void
    {
        $this->verificarSesion();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE . '/recepcionista/mascotas');
            exit;
        }

        // CSRF opcional pero recomendado
        $token = $_POST['_csrf'] ?? '';
        if (class_exists('CSRF') && !CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada.'];
            // Redirigir al historial si es posible, sino a mascotas
            if (!empty($_POST['mascota_id'])) {
                header('Location: ' . BASE . '/recepcionista/mascotas/' . (int)$_POST['mascota_id'] . '/historial');
            } else {
                header('Location: ' . BASE . '/recepcionista/mascotas');
            }
            exit;
        }

        $mascotaId = (int)($_POST['mascota_id'] ?? 0);
        $filename = basename($_POST['filename'] ?? '');

        if ($mascotaId > 0 && !empty($filename)) {
            $filePath = dirname(APP_ROOT) . '/public/assets/uploads/reportes/' . $mascotaId . '/' . $filename;
            if (file_exists($filePath) && is_file($filePath)) {
                if (unlink($filePath)) {
                    clearstatcache();
                    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Archivo eliminado correctamente.'];
                } else {
                    $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo eliminar el archivo.'];
                }
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Archivo no encontrado.'];
            }
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Datos inválidos.'];
        }

        header('Location: ' . BASE . '/recepcionista/mascotas/' . $mascotaId . '/historial');
        exit;
    }






    public function ingresos(): void
    {
        $this->verificarSesion();
        $mov = new Movimiento($this->pdo);
        $tipo = isset($_GET['tipo']) ? trim((string)$_GET['tipo']) : '';
        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;
        $items = [];
        $errorMov = null;
        try {
            $items = $mov->listar($desde, $hasta, $tipo ?: null);
        } catch (Throwable $e) {
            $errorMov = $e->getMessage();
        }
        $content = $this->renderView('recepcionista/ingresos', [
            'items' => $items,
            'tipo' => $tipo,
            'desde' => $desde,
            'hasta' => $hasta,
            'errorMov' => $errorMov,
        ]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function inventario(): void
    {
        $this->verificarSesion();
        $producto = new Producto($this->pdo);
        $q = trim($_GET['q'] ?? '');
        $items = $producto->listar($q);
        $content = $this->renderView('recepcionista/inventario_index', [
            'items' => $items,
            'q' => $q,
        ]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function inventarioCreate(): void
    {
        $this->verificarSesion();
        $content = $this->renderView('recepcionista/inventario_form', [
            'accion' => 'crear',
            'item' => null,
        ]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function inventarioStore(): void
    {
        $this->verificarSesion();
        $producto = new Producto($this->pdo);
        // CSRF
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
            header('Location: ' . BASE . '/recepcionista/inventario/create');
            exit;
        }
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
            'unidad' => trim($_POST['unidad'] ?? 'unidad'),
            'stock' => (float)($_POST['stock'] ?? 0),
            'costo' => (float)($_POST['costo'] ?? 0),
            'precio' => (float)($_POST['precio'] ?? 0),
            'sku' => trim($_POST['sku'] ?? ''),
            'notas' => trim($_POST['notas'] ?? ''),
        ];
        if ($data['nombre'] === '') {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'El nombre es obligatorio.'];
            header('Location: ' . BASE . '/recepcionista/inventario/create');
            exit;
        }
        $producto->crear($data);
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Producto creado.'];
        header('Location: ' . BASE . '/recepcionista/inventario');
        exit;
    }

    public function inventarioEdit(int $id): void
    {
        $this->verificarSesion();
        $producto = new Producto($this->pdo);
        $item = $producto->obtener($id);
        if (!$item) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Producto no encontrado.'];
            header('Location: ' . BASE . '/recepcionista/inventario');
            exit;
        }
        $content = $this->renderView('recepcionista/inventario_form', [
            'accion' => 'editar',
            'item' => $item,
        ]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function inventarioUpdate(int $id): void
    {
        $this->verificarSesion();
        $producto = new Producto($this->pdo);
        // CSRF
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
            header('Location: ' . BASE . '/recepcionista/inventario/' . $id . '/edit');
            exit;
        }
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
            'unidad' => trim($_POST['unidad'] ?? 'unidad'),
            'stock' => (float)($_POST['stock'] ?? 0),
            'costo' => (float)($_POST['costo'] ?? 0),
            'precio' => (float)($_POST['precio'] ?? 0),
            'sku' => trim($_POST['sku'] ?? ''),
            'notas' => trim($_POST['notas'] ?? ''),
        ];
        if ($data['nombre'] === '') {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'El nombre es obligatorio.'];
            header('Location: ' . BASE . '/recepcionista/inventario/' . $id . '/edit');
            exit;
        }
        $producto->actualizar($id, $data);
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Producto actualizado.'];
        header('Location: ' . BASE . '/recepcionista/inventario');
        exit;
    }

    public function inventarioDelete(int $id): void
    {
        $this->verificarSesion();
        $producto = new Producto($this->pdo);
        $producto->eliminar($id);
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Producto eliminado.'];
        header('Location: ' . BASE . '/recepcionista/inventario');
        exit;
    }

    public function inventarioAjustar(int $id): void
    {
        $this->verificarSesion();
        // CSRF
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
            header('Location: ' . BASE . '/recepcionista/inventario');
            exit;
        }
        $tipo = $_POST['tipo'] ?? '';
        $cantidad = (float)($_POST['cantidad'] ?? 0);
        if (!in_array($tipo, ['entrada','salida'], true) || $cantidad <= 0) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Datos de ajuste invalidos.'];
            header('Location: ' . BASE . '/recepcionista/inventario');
            exit;
        }

        // Crear tabla historial si no existe
        $sqlCreate = "CREATE TABLE IF NOT EXISTS inventario_movimientos (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            producto_id INT UNSIGNED NOT NULL,
            tipo ENUM('entrada','salida','ajuste') NOT NULL,
            cantidad DECIMAL(12,2) NOT NULL,
            motivo VARCHAR(255) DEFAULT NULL,
            creado_por INT NULL,
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_inv_mov_producto (producto_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        try { $this->pdo->exec($sqlCreate); } catch (Throwable $e) { /* ignore */ }

        // Obtener producto
        $stmt = $this->pdo->prepare("SELECT stock FROM productos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$prod) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Producto no encontrado.'];
            header('Location: ' . BASE . '/recepcionista/inventario');
            exit;
        }

        $stock = (float)($prod['stock'] ?? 0);
        $nuevo = $tipo === 'entrada' ? $stock + $cantidad : max(0, $stock - $cantidad);

        // Actualizar stock
        $u = $this->pdo->prepare("UPDATE productos SET stock = :s, actualizado_en = NOW() WHERE id = :id");
        $u->execute([':s' => $nuevo, ':id' => $id]);

        // Registrar movimiento
        $m = $this->pdo->prepare("INSERT INTO inventario_movimientos (producto_id, tipo, cantidad, motivo, creado_por) VALUES (:pid, :t, :c, :motivo, :uid)");
        $m->execute([
            ':pid' => $id,
            ':t' => $tipo,
            ':c' => $cantidad,
            ':motivo' => $_POST['motivo'] ?? null,
            ':uid' => $_SESSION['user']['id'] ?? null,
        ]);

        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Stock actualizado.'];
        header('Location: ' . BASE . '/recepcionista/inventario');
        exit;
    }

    // CRUD Ingresos/Egresos
    public function ingresosCreate(): void
    {
        $this->verificarSesion();
        $mov = new Movimiento($this->pdo);
        $conceptosIngreso = $mov->conceptos('ingreso');
        $conceptosEgreso  = $mov->conceptos('egreso');
        // Productos para concepto Medicamento
        $producto = new Producto($this->pdo);
        $productos = $producto->listar('');
        $content = $this->renderView('recepcionista/ingresos_form', [
            'accion' => 'crear',
            'item' => null,
            'conceptosIngreso' => $conceptosIngreso,
            'conceptosEgreso'  => $conceptosEgreso,
            'productos' => $productos,
        ]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function ingresosStore(): void
    {
        $this->verificarSesion();
        $mov = new Movimiento($this->pdo);
        // CSRF
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
            header('Location: ' . BASE . '/recepcionista/ingresos/create');
            exit;
        }
        $data = [
            'tipo' => $_POST['tipo'] ?? 'ingreso',
            'concepto' => trim($_POST['concepto'] ?? ''),
            'monto' => (float)($_POST['monto'] ?? 0),
            'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
            'notas' => $_POST['notas'] ?? null,
            'creado_por' => $_SESSION['user']['id'] ?? null,
        ];
        // Si se selecciona un concepto predefinido, se impone su monto
        $conceptoSel = trim($_POST['concepto_predef'] ?? '');
        if ($conceptoSel !== '' && $conceptoSel !== 'otro') {
            $c = $mov->conceptoPorNombre($data['tipo'], $conceptoSel);
            if ($c) {
                $data['concepto'] = $c['concepto'];
                $data['monto'] = (float)$c['monto'];
            }
        } elseif ($conceptoSel === 'otro') {
            if ($data['concepto'] === '' || empty($data['notas'])) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Para "Otro" indique concepto y notas.'];
                header('Location: ' . BASE . '/recepcionista/ingresos/create');
                exit;
            }
        }
        // Si el concepto es Medicamento y se selecciona un producto, usar el precio del producto
        if (strcasecmp($conceptoSel, 'Medicamento') === 0) {
            $productoId = (int)($_POST['producto_id'] ?? 0);
            if ($productoId > 0) {
                try {
                    $producto = new Producto($this->pdo);
                    $p = $producto->obtener($productoId);
                    if ($p && isset($p['precio'])) {
                        $data['monto'] = (float)$p['precio'];
                        $data['producto_id'] = $productoId;
                        // Opcional: anclar nombre de producto en notas si no hay
                        if (empty($data['notas'])) {
                            $data['notas'] = 'Producto: ' . (string)($p['nombre'] ?? ('ID ' . $productoId));
                        }
                    }
                } catch (Throwable $e) { /* ignore */ }
            }
        }
        if ($data['concepto'] === '' || $data['monto'] <= 0) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Concepto y monto son obligatorios.'];
            header('Location: ' . BASE . '/recepcionista/ingresos/create');
            exit;
        }
        $mov->crear($data);
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Movimiento registrado.'];
        header('Location: ' . BASE . '/recepcionista/ingresos');
        exit;
    }

    public function ingresosEdit(int $id): void
    {
        $this->verificarSesion();
        $mov = new Movimiento($this->pdo);
        $item = $mov->obtener($id);
        if (!$item) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Movimiento no encontrado.'];
            header('Location: ' . BASE . '/recepcionista/ingresos');
            exit;
        }
        $conceptosIngreso = $mov->conceptos('ingreso');
        $conceptosEgreso  = $mov->conceptos('egreso');
        $producto = new Producto($this->pdo);
        $productos = $producto->listar('');
        $content = $this->renderView('recepcionista/ingresos_form', [
            'accion' => 'editar',
            'item' => $item,
            'conceptosIngreso' => $conceptosIngreso,
            'conceptosEgreso'  => $conceptosEgreso,
            'productos' => $productos,
        ]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function ingresosUpdate(int $id): void
    {
        $this->verificarSesion();
        $mov = new Movimiento($this->pdo);
        // CSRF
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesion expirada. Intenta nuevamente.'];
            header('Location: ' . BASE . '/recepcionista/ingresos/' . $id . '/edit');
            exit;
        }
        $data = [
            'tipo' => $_POST['tipo'] ?? 'ingreso',
            'concepto' => trim($_POST['concepto'] ?? ''),
            'monto' => (float)($_POST['monto'] ?? 0),
            'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
            'notas' => $_POST['notas'] ?? null,
        ];
        $conceptoSel = trim($_POST['concepto_predef'] ?? '');
        if ($conceptoSel !== '' && $conceptoSel !== 'otro') {
            $c = $mov->conceptoPorNombre($data['tipo'], $conceptoSel);
            if ($c) {
                $data['concepto'] = $c['concepto'];
                $data['monto'] = (float)$c['monto'];
            }
        } elseif ($conceptoSel === 'otro') {
            if ($data['concepto'] === '' || empty($data['notas'])) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Para "Otro" indique concepto y notas.'];
                header('Location: ' . BASE . '/recepcionista/ingresos/' . $id . '/edit');
                exit;
            }
        }
        if (strcasecmp($conceptoSel, 'Medicamento') === 0) {
            $productoId = (int)($_POST['producto_id'] ?? 0);
            if ($productoId > 0) {
                try {
                    $producto = new Producto($this->pdo);
                    $p = $producto->obtener($productoId);
                    if ($p && isset($p['precio'])) {
                        $data['monto'] = (float)$p['precio'];
                        $data['producto_id'] = $productoId;
                        if (empty($data['notas'])) {
                            $data['notas'] = 'Producto: ' . (string)($p['nombre'] ?? ('ID ' . $productoId));
                        }
                    }
                } catch (Throwable $e) { /* ignore */ }
            }
        }
        if ($data['concepto'] === '' || $data['monto'] <= 0) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Concepto y monto son obligatorios.'];
            header('Location: ' . BASE . '/recepcionista/ingresos/' . $id . '/edit');
            exit;
        }
        $mov->actualizar($id, $data);
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Movimiento actualizado.'];
        header('Location: ' . BASE . '/recepcionista/ingresos');
        exit;
    }

    public function ingresosDelete(int $id): void
    {
        $this->verificarSesion();
        $mov = new Movimiento($this->pdo);
        $mov->eliminar($id);
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Movimiento eliminado.'];
        header('Location: ' . BASE . '/recepcionista/ingresos');
        exit;
    }

    // Crear tabla de movimientos si no existe (utilidad rapida)
    public function ingresosSetup(): void
    {
        $this->verificarSesion();
        $sql = "
            CREATE TABLE IF NOT EXISTS movimientos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tipo ENUM('ingreso','egreso') NOT NULL,
                concepto VARCHAR(255) NOT NULL,
                monto DECIMAL(12,2) NOT NULL,
                fecha DATE NOT NULL,
                notas TEXT NULL,
                creado_por INT NULL,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_fecha (fecha),
                KEY idx_tipo (tipo)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        try {
            $this->pdo->exec($sql);
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS mov_conceptos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tipo ENUM('ingreso','egreso') NOT NULL,
                concepto VARCHAR(120) NOT NULL,
                monto DECIMAL(12,2) NOT NULL,
                activo TINYINT(1) NOT NULL DEFAULT 1,
                UNIQUE KEY unq_tipo_concepto (tipo, concepto)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            // Agregar columna producto_id si no existe (para asociar movimientos con inventario)
            try { $this->pdo->exec("ALTER TABLE movimientos ADD COLUMN producto_id INT NULL"); } catch (Throwable $e2) { /* ignore if exists */ }
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Tabla de movimientos creada/validada.'];
        } catch (Throwable $e) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Error creando tabla: ' . $e->getMessage()];
        }
        header('Location: ' . BASE . '/recepcionista/ingresos');
        exit;
    }

    private function renderView(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        $viewFile = APP_ROOT . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {  
            // fallback: muestra un mensaje  para debugging
            echo "<p>Vista no encontrada: {$viewFile}</p>";
            return ob_get_clean();
        }
        include $viewFile;
        return ob_get_clean();
    }

    public function eliminarCliente(int $id): void
    {
        $this->verificarSesion();

        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id AND role_id = 6");
            $stmt->execute([':id' => $id]);

            $_SESSION['success'] = "Cliente eliminado correctamente.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "No se pudo eliminar el cliente: " . $e->getMessage();
        }

        header('Location: ' . BASE . '/recepcionista/clientes');
        exit;
    }

    // Helpers de columna dueño y utilidades
    private function resolveMascotaOwnerColumn(): string
    {
        $owner = 'dueno_id';
        try {
            $q = $this->pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mascotas'");
            $q->execute();
            $cols = $q->fetchAll(PDO::FETCH_COLUMN) ?: [];
            foreach ($cols as $c) {
                $lc = strtolower((string)$c);
                $norm = strtr($lc, ["ñ"=>'n', "Ñ"=>'n']);
                if ($norm === 'dueno_id') {
                    $owner = (string)$c; // usar nombre exacto de la DB
                    break;
                }
            }
        } catch (Throwable $e) {
            // fallback dueno_id
        }
        return $owner;
    }

    private function readOwnerFromPost(array $post): int
    {
        if (isset($post['dueno_id'])) return (int)$post['dueno_id'];
        foreach ($post as $k => $v) {
            $lk = strtolower((string)$k);
            $nk = strtr($lk, ["ñ"=>'n', "Ñ"=>'n']);
            if ($nk === 'dueno_id') {
                return (int)$v;
            }
        }
        return 0;
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c LIMIT 1");
            $stmt->execute([':t' => $table, ':c' => $column]);
            return (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            return false;
        }
    }

    // Vistas de perfil/carnet
    public function verMascota(int $id): void
    {
        $this->verificarSesion();
        $mascotaModel = new Mascota($this->pdo);
        $mascota = $mascotaModel->getById($id);
        if (!$mascota) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Mascota no encontrada.'];
            header('Location: ' . BASE . '/recepcionista/mascotas');
            exit;
        }
        $content = $this->renderView('recepcionista/mascota_show', [ 'mascota' => $mascota ]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    public function verCliente(int $id): void
    {
        $this->verificarSesion();
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = :id AND role_id = 6');
        $stmt->execute([':id' => $id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cliente) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Cliente no encontrado.'];
            header('Location: ' . BASE . '/recepcionista/clientes');
            exit;
        }
        $content = $this->renderView('recepcionista/cliente_show', [ 'cliente' => $cliente ]);
        require APP_ROOT . '/views/layouts/main_recepcionista.php';
    }

    // ==================== Reportes (Recepcionista) ====================
    public function reportes(): void
    {
        $this->verificarSesion();

        // Mascotas básicas para selects
        $mascotas = [];
        try {
            $stmt = $this->pdo->query("SELECT id, nombre FROM mascotas ORDER BY nombre ASC");
            $mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) { /* ignore */ }

        // Listado simple de archivos por mascota (por carpeta)
        $uploads = [];
        $baseDir = APP_ROOT . '/public/assets/uploads/reportes';
        if (@is_dir($baseDir)) {
            foreach ((array)@scandir($baseDir) as $dir) {
                if ($dir === '.' || $dir === '..') continue;
                if (!ctype_digit((string)$dir)) continue;
                $full = $baseDir . '/' . $dir;
                if (@is_dir($full)) {
                    $files = array_values(array_filter((array)@scandir($full), function($f){ return !in_array($f, ['.','..']); }));
                    if ($files) {
                        $uploads[(int)$dir] = $files;
                    }
                }
            }
        }

        $this->view('recepcionista/reportes', [
            'mascotas' => $mascotas,
            'uploads'  => $uploads,
        ], 'main_recepcionista');
    }

    public function reportesUpload(): void
    {
        $this->verificarSesion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE . '/recepcionista/reportes');
            exit;
        }
        // CSRF opcional
        $token = $_POST['_csrf'] ?? '';
        if (class_exists('CSRF') && !CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Sesión expirada. Intenta nuevamente.'];
            header('Location: ' . BASE . '/recepcionista/reportes');
            exit;
        }

        $mascotaId = (int)($_POST['mascota_id'] ?? 0);
        if ($mascotaId <= 0 || empty($_FILES['archivo'])) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Selecciona mascota y archivo.'];
            header('Location: ' . BASE . '/recepcionista/reportes');
            exit;
        }

        $file = $_FILES['archivo'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Error al subir el archivo.'];
            header('Location: ' . BASE . '/recepcionista/reportes');
            exit;
        }

        // Validaciones básicas
        $maxBytes = 5 * 1024 * 1024; // 5MB
        if (($file['size'] ?? 0) > $maxBytes) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Archivo demasiado grande (máx 5MB).'];
            header('Location: ' . BASE . '/recepcionista/reportes');
            exit;
        }

        // Permitir PDF, imágenes y texto
        $allowed = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'text/plain' => 'txt'
        ];
        $mime = @mime_content_type($file['tmp_name']) ?: ($file['type'] ?? '');
        $ext  = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($ext, array_values($allowed), true)) {
            $ext = $allowed[$mime] ?? $ext;
        }
        if ($ext === '' || (!in_array($ext, array_values($allowed), true) && !array_key_exists($mime, $allowed))) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Tipo de archivo no permitido.'];
            header('Location: ' . BASE . '/recepcionista/reportes');
            exit;
        }

        $safeBase = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', (string)($file['name'] ?? 'archivo'));
        $filename = 'rep_mascota_' . $mascotaId . '_' . time() . '_' . $safeBase;
        $dir = dirname(APP_ROOT) . '/public/assets/uploads/reportes/' . $mascotaId;
        if (!@is_dir($dir)) { @mkdir($dir, 0777, true); }
        $dest = $dir . '/' . $filename;
        if (!@move_uploaded_file($file['tmp_name'], $dest)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo guardar el archivo.'];
            header('Location: ' . BASE . '/recepcionista/reportes');
            exit;
        }

        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Archivo cargado correctamente.'];
        header('Location: ' . BASE . '/recepcionista/reportes');
        exit;
    }

}
