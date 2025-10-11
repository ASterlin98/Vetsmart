<?php
// public/index.php
declare(strict_types=1);
session_start();

define('APP_ROOT', dirname(__DIR__) . '/app');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/core/Database.php';

$pdo = Database::getInstance();

// modelos
require __DIR__ . '/../app/models/Usuario.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Servicio.php';
require_once APP_ROOT . '/models/Vacuna.php';
require_once APP_ROOT . '/models/Mascota.php';
require_once APP_ROOT . '/models/Cliente.php';

// core
require __DIR__ . '/../app/core/CSRF.php';
require __DIR__ . '/../app/core/Controller.php';
require __DIR__ . '/../app/core/Auth.php';
require __DIR__ . '/../app/core/Router.php';

// controladores
require __DIR__ . '/../app/controllers/AuthController.php';
require __DIR__ . '/../app/controllers/SuperAdminController.php';
require_once APP_ROOT . '/controllers/ClientesController.php';
require __DIR__ . '/../app/controllers/VeterinarioController.php';
require __DIR__ . '/../app/controllers/ServiciosController.php';
require_once APP_ROOT . '/controllers/ConsultasController.php';

// <-- AÑADIDO: AdminController (necesario para gestión empleados) -->
require_once APP_ROOT . '/controllers/AdminController.php';
require_once APP_ROOT . '/controllers/SoporteController.php';

// Detectar base path (subcarpeta donde vive la app)
$basePath = '/vetsmart';
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Eliminar el prefijo basePath de la URI (si aplica)
$path = $requestUri;
if (strpos((string)$path, $basePath) === 0) {
    $path = substr((string)$path, strlen($basePath));
}
if ($path === '' || $path === false) {
    $path = '/';
}

// Instancia del AuthController (tu AuthController actual probablemente no necesita $pdo)
$authController = new AuthController(); // si tu AuthController requiere $pdo, cámbialo a new AuthController($pdo)

// Helper para llamar al método correcto si existen varias variantes (ej. showLogin() o login())
$callPreferred = function ($obj, array $methods) {
    foreach ($methods as $m) {
        if (method_exists($obj, $m)) {
            return $obj->{$m}();
        }
    }
    // si ninguno existe, lanzar excepción leve para debug
    throw new RuntimeException('Método ninguno de los esperados existe en el controlador: ' . implode(',', $methods));
};

try {
    if ($path === '/' || ($path === '/login' && $_SERVER['REQUEST_METHOD'] === 'GET')) {
        // Mostrar login
        $callPreferred($authController, ['showLogin', 'login']);
        exit;
    }

    // también permitir /auth/login (GET)
    if ($path === '/auth/login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $callPreferred($authController, ['showLogin', 'login']);
        exit;
    }

    // POST /login o POST /auth/login -> procesa login
    if (($path === '/login' || $path === '/auth/login') && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (method_exists($authController, 'login')) {
            $authController->login();
            exit;
        }
        throw new RuntimeException('AuthController no implementa login()');
    }

    // GET /logout or /auth/logout -> cerrar sesión
    if ($path === '/logout' || $path === '/auth/logout') {
        if (method_exists($authController, 'logout')) {
            $authController->logout();
            exit;
        }
        // fallback básico
        session_unset();
        session_destroy();
        header('Location: ' . $basePath . '/login');
        exit;
    }

    // GET /auth/register -> muestra formulario de registro (si existe)
    if ($path === '/auth/register' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        if (method_exists($authController, 'showRegister')) {
            $authController->showRegister();
            exit;
        }
    }

    // POST /auth/register -> procesa el registro (si existe)
    if ($path === '/auth/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (method_exists($authController, 'register')) {
            $authController->register();
            exit;
        }
    }

    // GET /auth/forgot -> formulario "olvidé mi clave"
    if ($path === '/auth/forgot' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        if (method_exists($authController, 'forgot')) {
            $authController->forgot();
            exit;
        }
        // Si no existe el método, intentar cargar vista directametne (por compatibilidad)
        require APP_ROOT . '/views/auth/forgot_password.php';
        exit;
    }

    if ($path === '/auth/storeClient' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (method_exists($authController, 'storeClient')) {
            $authController->storeClient();
            exit;
        }
    }

    // POST /auth/sendResetLink -> procesa envío de correo (form forgot)
    if ($path === '/auth/sendResetLink' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (method_exists($authController, 'sendResetLink')) {
            $authController->sendResetLink();
            exit;
        }
    }

    // GET /auth/reset?token=xxx -> formulario para nueva contraseña
    if ($path === '/auth/reset' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        if (method_exists($authController, 'reset')) {
            $authController->reset();
            exit;
        }
    }

    // POST /auth/updatePassword -> guarda la nueva contraseña
    if ($path === '/auth/updatePassword' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (method_exists($authController, 'updatePassword')) {
            $authController->updatePassword();
            exit;
        }
    }

    /* ---------------------------
       DASHBOARD según rol
       --------------------------- */
    if ($path === '/dashboard') {
        if (empty($_SESSION['user'])) {
            header('Location: ' . $basePath . '/login');
            exit;
        }

        $role = $_SESSION['user']['role_name'] ?? null;

        if ($role === 'veterinario') {
            $controller = new VeterinarioController($pdo);
            $controller->dashboard();
            exit;
        }

        // fallback para otros roles (puedes ampliar)
        $controller = new Controller($pdo);
        switch ($role) {
            case 'recepcionista':
                $controller->view("recepcionista/dashboard", [], "main_recepcionista");
                break;
            case 'cliente':
                $controller->view("cliente/dashboard", [], "main_cliente");
                break;
            case 'peluquero':
                $controller->view("peluquero/dashboard", [], "main_peluquero");
                break;
            case 'admin':
                $controller->view("admin/dashboard", [], "main_admin");
                break;
            case 'super_admin':
                $controller = new SuperAdminController($pdo);
                $controller->dashboard();
                break;
            default:
                echo "Rol no reconocido.";
                break;
        }
        exit;
    }

    $rolesPaths = [
        '/cliente/dashboard',
        '/veterinario/dashboard',
        '/recepcionista/dashboard',
        '/peluquero/dashboard',
        '/admin/dashboard',
        '/super_admin/dashboard'
    ];

    if (in_array($path, $rolesPaths)) {
        if (empty($_SESSION['user'])) {
            header('Location: ' . $basePath . '/login');
            exit;
        }
        header('Location: ' . $basePath . '/dashboard');
        exit;
    }

    // === Gestión de Permisos por Rol (solo superadmin) ===
    if ($path === '/superadmin/permisos' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new SuperAdminController($pdo);
        $controller->permisos();
        exit;
    }


    // ==================== CLIENTES ====================
    if ($path === '/admin/clientes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClientesController($pdo);
        $controller->index();
        exit;
    }

    if ($path === '/admin/clientes/crear' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClientesController($pdo);
        $controller->crear();
        exit;
    }

    if ($path === '/admin/clientes/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClientesController($pdo);
        $controller->guardar();
        exit;
    }

    if (preg_match('#^/admin/clientes/(\d+)$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClientesController($pdo);
        $controller->ver($matches[1]);
        exit;
    }

    if (preg_match('#^/admin/clientes/(\d+)/editar$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClientesController($pdo);
        $controller->editar($matches[1]);
        exit;
    }

    if (preg_match('#^/admin/clientes/(\d+)/actualizar$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClientesController($pdo);
        $controller->actualizar($matches[1]);
        exit;
    }

    if (preg_match('#^/admin/clientes/(\d+)/eliminar$#', $path, $matches)) {
        $controller = new ClientesController($pdo);
        $controller->eliminar($matches[1]);
        exit;
    }

    // ==================== MASCOTAS ====================
    if (preg_match('#^/admin/clientes/(\d+)/mascotas/crear$#', $path, $matches)) {
        $controller = new ClientesController($pdo);
        $controller->crearMascota($matches[1]);
        exit;
    }

    if (preg_match('#^/admin/clientes/(\d+)/mascotas/guardar$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClientesController($pdo);
        $controller->guardarMascota($matches[1]);
        exit;
    }

    if (preg_match('#^/admin/clientes/(\d+)/mascotas/(\d+)/actualizar$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClientesController($pdo);
        $controller->actualizarMascota((int)$matches[1], (int)$matches[2]);
        exit;
    }

    if (preg_match('#^/admin/clientes/(\d+)/mascotas/(\d+)/editar$#', $path, $matches)) {
        $controller = new ClientesController($pdo);
        $controller->editarMascota($matches[1], $matches[2]);
        exit;
    }

    if (preg_match('#^/admin/clientes/(\d+)/mascotas/(\d+)/eliminar$#', $path, $matches)) {
        $controller = new ClientesController($pdo);
        $controller->eliminarMascota($matches[1], $matches[2]);
        exit;
    }

    if (preg_match('#^/admin/clientes/(\d+)/mascotas/(\d+)$#', $path, $matches)) {
        $controller = new ClientesController($pdo);
        $controller->verMascota($matches[1], $matches[2]);
        exit;
    }

    // RUTAS VETERINARIO (pacientes, reportes, mis-citas, etc.)
    if ($path === '/veterinario/pacientes') {
        $controller = new VeterinarioController($pdo);
        $controller->pacientes();
        exit;
    }

    if ($path === '/veterinario/reportes') {
        $controller = new VeterinarioController($pdo);
        $controller->reportes();
        exit;
    }

    if ($path === '/veterinario/reportes/exportar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->exportar();
        exit;
    }

    // Mostrar formulario de agendamiento
    if (preg_match('#^/veterinario/mascotas/(\d+)/agendar$#', $path, $matches)) {
        $controller = new VeterinarioController($pdo);
        $controller->agendar($matches[1]);
        exit;
    }

    // Mostrar historial clínico de una mascota
    if (preg_match('#^/veterinario/mascotas/(\d+)/historial$#', $path, $matches)) {
        $controller = new VeterinarioController($pdo);
        $controller->verHistorial($matches[1]);
        exit;
    }

    if (preg_match('#^/veterinario/mascotas/(\d+)/notas/guardar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->guardarNotaRapida($m[1]);
        exit;
    }

    if (preg_match('#^/veterinario/mascotas/(\d+)/notas/(\d+)/editar$#', $path, $m)) {
        $controller = new VeterinarioController($pdo);
        $controller->editarNotaRapida($m[1], $m[2]);
        exit;
    }

    if (preg_match('#^/veterinario/mascotas/(\d+)/notas/(\d+)/actualizar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->actualizarNotaRapida($m[1], $m[2]);
        exit;
    }

    if (preg_match('#^/veterinario/mascotas/(\d+)/notas/(\d+)/eliminar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->eliminarNotaRapida($m[1], $m[2]);
        exit;
    }

    if (preg_match('#^/veterinario/mascotas/(\d+)/notas/(\d+)/guardar_edicion$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->guardarEdicionNotaRapida($m[1], $m[2]);
        exit;
    }

    // Subir / actualizar foto
    if (preg_match('#^/veterinario/mascotas/(\d+)/actualizar-foto$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->actualizarFoto($m[1]);
        exit;
    }

    // Eliminar foto
    if (preg_match('#^/veterinario/mascotas/(\d+)/eliminar-foto$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->eliminarFoto($m[1]);
        exit;
    }

    // Consultas veterinario
    if ($path === '/veterinario/consultas') {
        $controller = new ConsultasController($pdo);
        $controller->index();
        exit;
    }

    if ($path === '/veterinario/consultas/crear') {
        $controller = new ConsultasController($pdo);
        $controller->crear();
        exit;
    }

    // crear con mascota preseleccionada: /veterinario/consultas/crear/12
    if (preg_match('#^/veterinario/consultas/crear/(\d+)$#', $path, $m)) {
        $controller = new ConsultasController($pdo);
        $controller->crear($m[1]);
        exit;
    }

    if ($path === '/veterinario/consultas/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ConsultasController($pdo);
        $controller->guardar();
        exit;
    }

    if (preg_match('#^/veterinario/consultas/ver/(\d+)$#', $path, $m)) {
        $controller = new ConsultasController($pdo);
        $controller->ver($m[1]);
        exit;
    }

    if (preg_match('#^/veterinario/consultas/editar/(\d+)$#', $path, $m)) {
        $controller = new ConsultasController($pdo);
        $controller->editar($m[1]);
        exit;
    }

    if (preg_match('#^/veterinario/consultas/actualizar/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ConsultasController($pdo);
        $controller->actualizar($m[1]);
        exit;
    }

    // Ver / Editar consulta (desde modal - usan VeterinarioController en tu proyecto)
    if (preg_match('#^/veterinario/consultas/(\d+)/ver$#', $path, $m)) {
        $controller = new VeterinarioController($pdo);
        $controller->verConsulta($m[1]);
        exit;
    }

    if (preg_match('#^/veterinario/consultas/(\d+)/editar$#', $path, $m)) {
        $controller = new VeterinarioController($pdo);
        $controller->editarConsulta($m[1]);
        exit;
    }

    // Actualizar consulta (POST) (tu proyecto usa ConsultasController para esto)
    if (preg_match('#^/veterinario/consultas/actualizar/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ConsultasController($pdo);
        $controller->actualizar($m[1]);
        exit;
    }

    // Eliminar consulta
    if (preg_match('#^/veterinario/consultas/(\d+)/eliminar$#', $path, $m)) {
        $controller = new ConsultasController($pdo);
        $controller->eliminar($m[1]);
        exit;
    }

    // Servicios (admin)
    if ($path === '/admin/servicios') {
        $controller = new ServiciosController($pdo);
        $controller->index();
        exit;
    }

    if ($path === '/admin/servicios/crear') {
        $controller = new ServiciosController($pdo);
        $controller->crear();
        exit;
    }

    if ($path === '/admin/servicios/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ServiciosController($pdo);
        $controller->guardar();
        exit;
    }

    if (preg_match('#^/admin/servicios/(\d+)/editar$#', $path, $matches)) {
        $controller = new ServiciosController($pdo);
        $controller->editar($matches[1]);
        exit;
    }

    if (preg_match('#^/admin/servicios/(\d+)/actualizar$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ServiciosController($pdo);
        $controller->actualizar($matches[1]);
        exit;
    }

    if (preg_match('#^/admin/servicios/(\d+)/eliminar$#', $path, $matches)) {
        $controller = new ServiciosController($pdo);
        $controller->eliminar($matches[1]);
        exit;
    }

    // mostrar calendario veterinario (mis-citas)
    if ($path === '/veterinario/mis-citas') {
        $controller = new VeterinarioController($pdo);
        $controller->misCitas();
        exit;
    }
    if ($path === '/veterinario/citas/listar' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new VeterinarioController($pdo);
        $controller->listarCitasJson();
        exit;
    }
    // guardar cita (POST)
    if ($path === '/veterinario/citas/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->guardarCita();
        exit;
    }

    if ($path === '/veterinario/citas/actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->actualizarCita();
        exit;
    }

    if (preg_match('#^/veterinario/citas/(\d+)/eliminar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->eliminarCitaAjax($m[1]);
        exit;
    }

    // en public/index.php, donde están las rutas veterinario ...
    if ($path === '/veterinario/dashboard') {
        $controller = new VeterinarioController($pdo);
        $controller->dashboard();
        exit;
    }

    if (preg_match('#^/veterinario/mascotas/(\d+)/vacunas$#', $path, $matches)) {
        $controller = new VeterinarioController($pdo);
        $controller->vacunas($matches[1]);
        exit;
    }

    if ($path === '/veterinario/vacunas/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->guardarVacuna();
        exit;
    }

    if (preg_match('#^/veterinario/vacunas/(\d+)/mascota/(\d+)/eliminar$#', $path, $m)) {
        $controller = new VeterinarioController($pdo);
        $controller->eliminarVacuna($m[1], $m[2]);
        exit;
    }

    if (preg_match('#^/veterinario/mascotas/(\d+)/vacunas/(\d+)/editar$#', $path, $m)) {
        $controller = new VeterinarioController($pdo);
        $controller->editarVacuna($m[1], $m[2]);
        exit;
    }

    if (preg_match('#^/veterinario/mascotas/(\d+)/vacunas/(\d+)/actualizar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new VeterinarioController($pdo);
        $controller->actualizarVacuna($m[1], $m[2]);
        exit;
    }

    // APIs auxiliares
    if ($path === '/api/clientes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $clienteModel = new Cliente($pdo);
        header('Content-Type: application/json');
        echo json_encode($clienteModel->getAll());
        exit;
    }

    if (preg_match('#^/api/clientes/(\d+)/mascotas$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $clienteId = (int)$matches[1];
        try {
            $mascotaModel = new Mascota($pdo);
            if (method_exists($mascotaModel, 'getByDueno')) {
                $data = $mascotaModel->getByDueno($clienteId);
            } else {
                $stmt = $pdo->prepare("
                    SELECT id, nombre
                    FROM mascotas
                    WHERE cliente_id = :cid
                       OR dueno_id = :cid
                       OR dueño_id = :cid
                       OR owner_id = :cid
                ");
                $stmt->execute([':cid' => $clienteId]);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8', true, 500);
            echo json_encode(['error' => 'Error DB', 'msg' => $e->getMessage()]);
        }
        exit;
    }

    if (preg_match('#^/api/mascota/(\d+)$#', $path, $matches)) {
        $mid = $matches[1];
        $mascotaModel = new Mascota($pdo);
        header('Content-Type: application/json');
        echo json_encode($mascotaModel->getById($mid));
        exit;
    }

    if (preg_match('#^/api/citas/(\d+)$#', $path, $matches)) {
        $id = $matches[1];
        $citaModel = new Cita($pdo);
        header('Content-Type: application/json');
        echo json_encode($citaModel->getById($id));
        exit;
    }

    // Admin - Agenda
if ($path === '/admin/agenda' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->agenda();
    exit;
}
if ($path === '/admin/agenda/listar' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->listarCitasJson();
    exit;
}
if ($path === '/admin/agenda/estadisticas' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->estadisticasAgendaJson();
    exit;
}
if ($path === '/admin/agenda/exportarExcel' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->exportarExcel();   // 👈 este método debe existir en tu AdminController
    exit;
}

// ==================== FINANZAS ====================
if ($path === '/admin/finanzas' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->finanzasIndex();
    exit;
}

if ($path === '/admin/finanzas/exportarExcel' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->exportarFinanzasExcel(); // 👈 crea este método en AdminController
    exit;
}
// ==================== REPORTES ====================
if ($path === '/admin/reportes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->reportesIndex();
    exit;
}

if ($path === '/admin/reportes/exportarExcel' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->exportarReportesExcel(); // 👈 crea este método en AdminController
    exit;
}

// ==================== EMPLEADOS ====================
if ($path === '/admin/empleados' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu método se llama empleadosIndex()
    $controller->empleadosIndex();
    exit;
}

if ($path === '/admin/empleados/crear' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu método se llama crearEmpleado()
    $controller->crearEmpleado();
    exit;
}

if ($path === '/admin/empleados/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu método se llama guardarEmpleado()
    $controller->guardarEmpleado();
    exit;
}

if (preg_match('#^/admin/empleados/(\d+)/editar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu método se llama editarEmpleado($id)
    $controller->editarEmpleado($m[1]);
    exit;
}

if (preg_match('#^/admin/empleados/(\d+)/actualizar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu método se llama actualizarEmpleado($id)
    $controller->actualizarEmpleado($m[1]);
    exit;
}

if (preg_match('#^/admin/empleados/(\d+)/eliminar$#', $path, $m)) {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu método se llama eliminarEmpleado($id)
    $controller->eliminarEmpleado($m[1]);
    exit;
}

// ==================== HORARIOS ====================
if ($path === '/admin/horarios' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new AdminController($pdo);
    $controller->horariosIndex();
    exit;
}

/* ------- SEMANALES ------- */
// Guardar
if ($path === '/admin/horarios/guardar-semana' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AdminController($pdo);
    $controller->guardarHorarioSemana();
    exit;
}
// Editar
if (preg_match('#^/admin/horarios/(\d+)/editar-semana$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new AdminController($pdo);
    $controller->editarSemana($m[1]);
    exit;
}
// Actualizar
if (preg_match('#^/admin/horarios/(\d+)/actualizarSemana$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AdminController($pdo);
    $controller->actualizarSemana((int)$m[1]);
    exit;
}
// Eliminar
if (preg_match('#^/admin/horarios/(\d+)/eliminarSemana$#', $path, $m)) {
    $controller = new AdminController($pdo);
    $controller->eliminarSemana((int)$m[1]);
    exit;
}

/* ------- TURNOS EXTRA ------- */
// Guardar
if ($path === '/admin/horarios/guardar-turno' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AdminController($pdo);
    $controller->guardarTurno();
    exit;
}
// Editar
if (preg_match('#^/admin/horarios/(\d+)/editar-turno$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new AdminController($pdo);
    $controller->editarTurno($m[1]);
    exit;
}
// Actualizar
if (preg_match('#^/admin/horarios/(\d+)/actualizarTurno$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AdminController($pdo);
    $controller->actualizarTurno((int)$m[1]);
    exit;
}
// Eliminar
if (preg_match('#^/admin/horarios/(\d+)/eliminarTurno$#', $path, $m)) {
    $controller = new AdminController($pdo);
    $controller->eliminarTurno((int)$m[1]);
    exit;
}

/* ------- SOLICITUDES ------- */
// Guardar
if ($path === '/admin/horarios/guardar-solicitud' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AdminController($pdo);
    $controller->guardarSolicitud();
    exit;
}
// Editar
if (preg_match('#^/admin/horarios/(\d+)/editar-solicitud$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new AdminController($pdo);
    $controller->editarSolicitud($m[1]);
    exit;
}
// Actualizar
if (preg_match('#^/admin/horarios/(\d+)/actualizarSolicitud$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AdminController($pdo);
    $controller->actualizarSolicitud((int)$m[1]);
    exit;
}
// Eliminar
if (preg_match('#^/admin/horarios/(\d+)/eliminarSolicitud$#', $path, $m)) {
    $controller = new AdminController($pdo);
    $controller->eliminarSolicitud((int)$m[1]);
    exit;
}

if ($path === '/api/disponibilidad-veterinario' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new VeterinarioController($pdo);
    $controller->disponibilidadVeterinario();
    exit;
}

if ($path === '/super_admin/dashboard' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once APP_ROOT . '/controllers/SuperAdminController.php';
    $controller = new SuperAdminController($pdo);
    $controller->dashboard();
    exit;
}

// ==================== SUPER ADMIN: GESTIÓN DE ROLES (ahora manejado por API) ====================

// ==================== SUPER ADMIN: CONFIGURACIÓN GLOBAL ====================
if ($path === '/super_admin/configuracion' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new SuperAdminController($pdo);
    $controller->configuracion();
    exit;
}

if ($path === '/super_admin/actualizarConfiguracion' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new SuperAdminController($pdo);
    $controller->actualizarConfiguracion();
    exit;
}

// ==================== SUPER ADMIN: REPORTES ====================
if ($path === '/super_admin/reportes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new SuperAdminController($pdo);
    $controller->reportes();
    exit;
}

if ($path === '/super_admin/exportarReportes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new SuperAdminController($pdo);
    $controller->exportarReportes();
    exit;
}

// ==================== SUPER ADMIN: GESTIÓN DE PERMISOS (CRUD) ====================
if ($path === '/super_admin/permisos' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new SuperAdminController($pdo);
    $controller->permisos();
    exit;
}
if ($path === '/super_admin/guardarPermiso' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new SuperAdminController($pdo);
    $controller->guardarPermiso();
    exit;
}
if ($path === '/super_admin/actualizarPermiso' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new SuperAdminController($pdo);
    $controller->actualizarPermiso();
    exit;
}
if (preg_match('#^/super_admin/eliminarPermiso/(\d+)$#', $path, $matches)) {
    $controller = new SuperAdminController($pdo);
    $controller->eliminarPermiso($matches[1]);
    exit;
}

// ==================== CENTRO DE SOPORTE ====================
if ($path === '/soporte' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new SoporteController($pdo);
    $controller->index();
    exit;
}
if ($path === '/soporte/crear' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new SoporteController($pdo);
    $controller->crear();
    exit;
}
if ($path === '/soporte/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new SoporteController($pdo);
    $controller->guardar();
    exit;
}
if (preg_match('#^/soporte/ver/(\d+)$#', $path, $matches)) {
    $controller = new SoporteController($pdo);
    $controller->ver($matches[1]);
    exit;
}
if ($path === '/soporte/responder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new SoporteController($pdo);
    $controller->responder();
    exit;
}
if ($path === '/soporte/actualizarMeta' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new SoporteController($pdo);
    $controller->actualizarMeta();
    exit;
}


// ==================== API: DISPONIBILIDAD ====================
if ($path === '/api/disponibilidad' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $empleadoId = $_GET['empleado_id'] ?? null;
        $fecha      = $_GET['fecha'] ?? null;
        $hora       = $_GET['hora'] ?? null;

        if (!$empleadoId || !$fecha || !$hora) {
            echo json_encode(['disponible' => false, 'msg' => 'Parámetros incompletos']);
            exit;
        }

        // Lógica básica: verificar si el empleado tiene horario ese día y hora
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM horarios_semana
            WHERE empleado_id = :eid
              AND dia = DAYOFWEEK(:fecha) - 1
              AND :hora BETWEEN hora_inicio AND hora_fin
        ");
        $stmt->execute([
            ':eid'   => $empleadoId,
            ':fecha' => $fecha,
            ':hora'  => $hora
        ]);
        $enHorario = $stmt->fetchColumn() > 0;

        // Verificar si tiene turno que bloquee
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM turnos_empleado
            WHERE empleado_id = :eid
              AND :fechaHora BETWEEN inicio AND fin
        ");
        $stmt->execute([
            ':eid'       => $empleadoId,
            ':fechaHora' => $fecha . ' ' . $hora
        ]);
        $enTurno = $stmt->fetchColumn() > 0;

        // Verificar si hay solicitud (permiso, vacaciones, incapacidad)
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM solicitudes
            WHERE usuario_id = :eid
              AND :fecha BETWEEN fecha_inicio AND fecha_fin
        ");
        $stmt->execute([
            ':eid'   => $empleadoId,
            ':fecha' => $fecha
        ]);
        $enSolicitud = $stmt->fetchColumn() > 0;

        $disponible = $enHorario && !$enTurno && !$enSolicitud;

        echo json_encode([
            'disponible' => $disponible,
            'enHorario'  => $enHorario,
            'enTurno'    => $enTurno,
            'enSolicitud'=> $enSolicitud
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

    // Si nada coincide -> 404
    http_response_code(404);
    echo "Página no encontrada. <a href='{$basePath}/auth/login'>Ir a login</a>";
} catch (Throwable $ex) {
    // Falla segura: registrar y mostrar mensaje amigable en dev
    error_log("Router error: " . $ex->getMessage());
    http_response_code(500);
    if (ini_get('display_errors')) {
        echo "<h2>Error interno</h2><pre>" . htmlspecialchars($ex->getMessage()) . "</pre>";
    } else {
        echo "Error interno. Revisa logs.";
    }
    exit;
}
