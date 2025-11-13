<?php declare(strict_types=1);
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
require_once APP_ROOT . '/models/Producto.php';
require_once APP_ROOT . '/models/Movimiento.php';

// core
require __DIR__ . '/../app/core/CSRF.php';
require __DIR__ . '/../app/core/Controller.php';
require __DIR__ . '/../app/core/Auth.php';
require __DIR__ . '/../app/core/Router.php';

// controladores
require __DIR__ . '/../app/controllers/AuthController.php';
require __DIR__ . '/../app/controllers/SuperAdminController.php';
require __DIR__ . '/../app/controllers/ApiController.php';
require_once APP_ROOT . '/controllers/ClientesController.php';
require __DIR__ . '/../app/controllers/VeterinarioController.php';
require __DIR__ . '/../app/controllers/ServiciosController.php';
require_once APP_ROOT . '/controllers/ConsultasController.php';
require_once APP_ROOT . '/controllers/RecepcionistaController.php';
require_once APP_ROOT . '/controllers/CitaController.php';
require_once APP_ROOT . '/controllers/ClienteController.php';
require_once APP_ROOT . '/controllers/ReportesController.php';
require_once APP_ROOT . '/controllers/PeluqueroController.php';


// <-- : AdminController (necesario para gestion empleados) -->
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
$authController = new AuthController(); // si tu AuthController requiere $pdo,  a new AuthController($pdo)

// Helper para llamar al  correcto si existen varias variantes (ej. showLogin() o login())
$callPreferred = function ($obj, array $methods) {
    foreach ($methods as $m) {
        if (method_exists($obj, $m)) {
            return $obj->{$m}();
        }
    }
    // si ninguno existe, lanzar excepcion leve para debug
    throw new RuntimeException('©todo ninguno de los esperados existe en el controlador: ' . implode(',', $methods));
};

try {
    if ($path === '/' || ($path === '/login' && $_SERVER['REQUEST_METHOD'] === 'GET')) {
        // Mostrar login
        $callPreferred($authController, ['showLogin', 'login']);
        exit;
    }

    // tambien permitir /auth/login (GET)
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

    // GET /logout or /auth/logout -> cerrar sesion
    if ($path === '/logout' || $path === '/auth/logout') {
        if (method_exists($authController, 'logout')) {
            $authController->logout();
            exit;
        }
        // fallback basico
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

    // GET /auth/forgot -> formulario "olvido mi clave"
    if ($path === '/auth/forgot' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        if (method_exists($authController, 'forgot')) {
            $authController->forgot();
            exit;
        }
        // Si no existe el , intentar cargar vista directametne (por compatibilidad)
        require APP_ROOT . '/views/auth/forgot_password.php';
        exit;
    }

    if ($path === '/auth/storeClient' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (method_exists($authController, 'storeClient')) {
            $authController->storeClient();
            exit;
        }
    }

    // POST /auth/sendResetLink -> procesa  de correo (form forgot)
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
       DASHBOARD segun rol
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
                // Usar el controlador para cargar  correctamente
                (new RecepcionistaController($pdo))->dashboard();
                break;
            case 'cliente':
                // Usar el controlador de Cliente para cargar datos reales en el dashboard
                (new ClienteController($pdo))->dashboard();
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

    // ================== PELUQUERO ==================
    if ($path === '/peluquero' || $path === '/peluquero/dashboard') {
        (new PeluqueroController($pdo))->dashboard();
        exit;
    }
    if ($path === '/peluquero/citas' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        (new PeluqueroController($pdo))->citasIndex();
        exit;
    }
    if ($path === '/peluquero/agenda' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        (new PeluqueroController($pdo))->agendaIndex();
        exit;
    }
    if ($path === '/peluquero/citas/atender' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        (new PeluqueroController($pdo))->atenderCita();
        exit;
    }
    if ($path === '/peluquero/citas/finalizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        (new PeluqueroController($pdo))->finalizarServicio();
        exit;
    }
    if ($path === '/peluquero/servicios' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        (new PeluqueroController($pdo))->serviciosIndex();
        exit;
    }
    if ($path === '/peluquero/clientes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        (new PeluqueroController($pdo))->clientesIndex();
        exit;
    }
    
    // Reportes Peluquero
    if ($path === '/peluquero/reportes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        (new PeluqueroController($pdo))->reportesIndex();
        exit;
    }
    
    // Agendar cita de peluquería (formulario)
    if ($path === '/peluquero/agenda/agendar' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        (new PeluqueroController($pdo))->agendarCita();
        exit;
    }
    
    // Guardar cita de peluquería
    if ($path === '/peluquero/guardar-cita-peluqueria' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        (new PeluqueroController($pdo))->guardarCitaPeluqueria();
        exit;
    }

    // Reportes Peluquero (PDF)
    if (preg_match('#^/reportes/peluquero/(\d+)/pdf$#', (string)$path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ReportesController($pdo);
        $controller->peluqueroPdf(['id' => (int)$m[1]]);
        exit;
    }
    if (preg_match('#^/reportes/peluquero/(\d+)/pdf/preview$#', (string)$path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ReportesController($pdo);
        $controller->peluqueroPdfPreview(['id' => (int)$m[1]]);
        exit;
    }

    // Dashboard directo para cliente
    if ($path === '/cliente/dashboard') {
        if (empty($_SESSION['user'])) {
            header('Location: ' . $basePath . '/login');
            exit;
        }
        (new ClienteController($pdo))->dashboard();
        exit;
    }

    // ==================== CLIENTE (self-service) ====================
    if ($path === '/cliente/perfil' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClienteController($pdo);
        $controller->perfil();
        exit;
    }
    if ($path === '/cliente/perfil/actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClienteController($pdo);
        $controller->actualizarPerfil();
        exit;
    }
    if ($path === '/cliente/mascotas' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClienteController($pdo);
        $controller->mascotas();
        exit;
    }
    if ($path === '/cliente/mascotas/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClienteController($pdo);
        $controller->guardarMascota();
        exit;
    }
    if ($path === '/cliente/mascotas/editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClienteController($pdo);
        $controller->editarMascota();
        exit;
    }
    if ($path === '/cliente/mascotas/eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClienteController($pdo);
        $controller->eliminarMascota();
        exit;
    }
    if ($path === '/cliente/citas') {
        $controller = new ClienteController($pdo);
        $controller->citas();
        exit;
    }
    if ($path === '/cliente/citas/agendar' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClienteController($pdo);
        $controller->agendar();
        exit;
    }
    if ($path === '/cliente/citas/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClienteController($pdo);
        $controller->guardarCita();
        exit;
    }
    if ($path === '/cliente/citas/reagendar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ClienteController($pdo);
        $controller->reagendar();
        exit;
    }
    if ($path === '/cliente/historial' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClienteController($pdo);
        $controller->historial();
        exit;
    }
    if ($path === '/cliente/historial/exportar' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClienteController($pdo);
        $controller->historialExportar();
        exit;
    }
    if ($path === '/cliente/pagos' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClienteController($pdo);
        $controller->pagos();
        exit;
    }
    if ($path === '/cliente/reportes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClienteController($pdo);
        $controller->reportes();
        exit;
    }
    if ($path === '/cliente/reportes/pdf' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ClienteController($pdo);
        $controller->reportesPdf();
        exit;
    }

    // Reportes PDF profesional (dompdf)
    if (preg_match('#^/reportes/cliente/(\d+)/pdf$#', (string)$path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ReportesController($pdo);
        $controller->clientePdf(['id' => (int)$m[1]]);
        exit;
    }
    if (preg_match('#^/reportes/cliente/(\d+)/pdf/preview$#', (string)$path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ReportesController($pdo);
        $controller->clientePdfPreview(['id' => (int)$m[1]]);
        exit;
    }

    // === gestion de Permisos por Rol (solo superadmin) ===
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

    // Mostrar historial clinico de una mascota
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

    // Consultas veterinario (aliases: consultas, historial-clinico, historial)
    if ($path === '/veterinario/consultas' || $path === '/veterinario/historial-clinico' || $path === '/veterinario/historial') {
        $controller = new ConsultasController($pdo);
        $controller->index();
        exit;
    }

    if ($path === '/veterinario/consultas/crear' || $path === '/veterinario/historial-clinico/crear' || $path === '/veterinario/historial/crear') {
        $controller = new ConsultasController($pdo);
        $controller->crear();
        exit;
    }

    // crear con mascota preseleccionada: /veterinario/consultas/crear/12, /veterinario/historial-clinico/crear/12, o /veterinario/historial/crear/12
    if (preg_match('#^/veterinario/(consultas|historial-clinico|historial)/crear/(\d+)$#', $path, $m)) {
        $controller = new ConsultasController($pdo);
        $controller->crear($m[2]);
        exit;
    }

    if (($path === '/veterinario/consultas/guardar' || $path === '/veterinario/historial-clinico/guardar' || $path === '/veterinario/historial/guardar') && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ConsultasController($pdo);
        $controller->guardar();
        exit;
    }

    if (preg_match('#^/veterinario/(consultas|historial-clinico|historial)/ver/(\d+)$#', $path, $m)) {
        $controller = new ConsultasController($pdo);
        $controller->ver($m[2]);
        exit;
    }

    if (preg_match('#^/veterinario/(consultas|historial-clinico|historial)/editar/(\d+)$#', $path, $m)) {
        $controller = new ConsultasController($pdo);
        $controller->editar($m[2]);
        exit;
    }

    if (preg_match('#^/veterinario/(consultas|historial-clinico|historial)/actualizar/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ConsultasController($pdo);
        $controller->actualizar($m[2]);
        exit;
    }

    // Ver / Editar consulta (desde modal - usan VeterinarioController en tu proyecto)
    if (preg_match('#^/veterinario/(consultas|historial-clinico|historial)/(\d+)/ver$#', $path, $m)) {
        $controller = new VeterinarioController($pdo);
        $controller->verConsulta($m[2]);
        exit;
    }

    if (preg_match('#^/veterinario/(consultas|historial-clinico|historial)/(\d+)/editar$#', $path, $m)) {
        $controller = new VeterinarioController($pdo);
        $controller->editarConsulta($m[2]);
        exit;
    }

    // Actualizar consulta (POST) (tu proyecto usa ConsultasController para esto)
    if (preg_match('#^/veterinario/(consultas|historial-clinico|historial)/actualizar/(\d+)$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ConsultasController($pdo);
        $controller->actualizar($m[2]);
        exit;
    }

    // Eliminar consulta
    if (preg_match('#^/veterinario/(consultas|historial-clinico|historial)/(\d+)/eliminar$#', $path, $m)) {
        $controller = new ConsultasController($pdo);
        $controller->eliminar($m[2]);
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
    if ($path === '/veterinario/mis-citas' || $path === '/veterinario/agenda') {
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

if ($path === '/admin/soporte' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->soporte();
    exit;
}

if (preg_match('#^/admin/soporte/(\d+)$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->verTicket($matches[1]);
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

    // en public/index.php, donde estan las rutas veterinario ...
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
                       OR dueno_id = :cid
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
    $ctrl->listarAgenda();
    exit;
}
if ($path === '/admin/agenda/estadisticas' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->estadisticasAgendaJson();
    exit;
}
if ($path === '/admin/agenda/exportarExcel' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->exportarExcel();   //  este  debe existir en tu AdminController
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
    $ctrl->exportarFinanzasExcel(); //  crea este  en AdminController
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
    $ctrl->exportarReportesExcel(); //  crea este    en AdminController
    exit;
}

if ($path === '/admin/reportesSoporte' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ctrl = new AdminController($pdo);
    $ctrl->reportesSoporte();
    exit;
}

if ($path === '/admin/guardarTicket' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new AdminController($pdo);
    $ctrl->guardarTicket();
    exit;
}

// ==================== EMPLEADOS ====================
if ($path === '/admin/empleados' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu  se llama empleadosIndex()
    $controller->empleadosIndex();
    exit;
}

// Mostrar usuarios bloqueados (admin)
if ($path === '/admin/locked_users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    $controller->lockedUsers();
    exit;
}

// Desbloquear usuario (admin)
if (preg_match('#^/admin/desbloquear_usuario/(\d+)$#', $path, $m)) {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    $controller->desbloquearUsuario($m[1]);
    exit;
}

if ($path === '/admin/empleados/crear' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu  se llama crearEmpleado()
    $controller->crearEmpleado();
    exit;
}

if ($path === '/admin/empleados/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu  se llama guardarEmpleado()
    $controller->guardarEmpleado();
    exit;
}

if (preg_match('#^/admin/empleados/(\d+)/editar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu  se llama editarEmpleado($id)
    $controller->editarEmpleado($m[1]);
    exit;
}

if (preg_match('#^/admin/empleados/(\d+)/actualizar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu  se llama actualizarEmpleado($id)
    $controller->actualizarEmpleado($m[1]);
    exit;
}

if (preg_match('#^/admin/empleados/(\d+)/eliminar$#', $path, $m)) {
    require_once APP_ROOT . '/controllers/AdminController.php';
    $controller = new AdminController($pdo);
    // tu  se llama eliminarEmpleado($id)
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

// API para obtener detalles de una cita
if (preg_match('#^/api/citas/(\d+)$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new ApiController($pdo);
    $controller->getCitaDetalles($matches[1]);
    exit;
}

if ($path === '/super_admin/dashboard' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once APP_ROOT . '/controllers/SuperAdminController.php';
    $controller = new SuperAdminController($pdo);
    $controller->dashboard();
    exit;
}

// ==================== SUPER ADMIN: GESTION DE ROLES (ahora manejado por API) ====================

// ==================== SUPER ADMIN: CONFIGURACION GLOBAL ====================
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

// ==================== SUPER ADMIN: GESTION DE PERMISOS (CRUD) ====================
if ($path === '/super_admin/permisos' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new SuperAdminController($pdo);
    $controller->permisos();
    exit;
}
// API: Obtener permisos de un rol específico
if ($path === '/super_admin/permisos/obtener' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new SuperAdminController($pdo);
    $controller->obtenerPermisosRol();
    exit;
}
// API: Guardar permisos de un rol
if ($path === '/super_admin/permisos/guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new SuperAdminController($pdo);
    $controller->guardarPermisosRol();
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
            echo json_encode(['disponible' => false, 'msg' => 'Parametros incompletos']);
            exit;
        }

        // : verificar si el empleado tiene horario ese  y hora
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
// ==================== RECEPCIONISTA ====================

if ($path === '/vetsmart/dashboard') {
    $controller = new RecepcionistaController($pdo);
    $controller->dashboard();
    exit;
}

// Alias expli­cito al dashboard del recepcionista
if ($path === '/recepcionista/dashboard') {
    (new RecepcionistaController($pdo))->dashboard();
    exit;
}

if ($path === '/recepcionista/agenda') {
    $controller = new RecepcionistaController($pdo);
    $controller->agenda();
    exit;
}
// Módulo de reportes (Recepcionista)
if ($path === '/recepcionista/reportes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    (new RecepcionistaController($pdo))->reportes();
    exit;
}
if ($path === '/recepcionista/reportes/upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new RecepcionistaController($pdo))->reportesUpload();
    exit;
}

// Descarga de historial clínico por mascota (PDF)
if (preg_match('#^/reportes/mascota/(\\d+)/pdf$#', (string)$path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    (new ReportesController($pdo))->mascotaPdf(['id' => (int)$m[1]]);
    exit;
}
if (preg_match('#^/reportes/mascota/(\\d+)/pdf/preview$#', (string)$path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    (new ReportesController($pdo))->mascotaPdfPreview(['id' => (int)$m[1]]);
    exit;
}

if ($path === '/recepcionista/citas') {
    $controller = new RecepcionistaController($pdo);
    $controller->citas();
    exit;
}


if ($path === '/recepcionista/agenda/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $usuarioId = $_SESSION['user']['id'] ?? null;

    $stmt = $pdo->prepare("
        UPDATE citas
        SET estado = :estado,
            actualizado_por = :usuario_id,
            fecha_actualizacion = NOW()
        WHERE id = :id
    ");

    $stmt->execute([
        ':estado' => $_POST['estado'],
        ':usuario_id' => $usuarioId,
        ':id' => $_POST['id']
    ]);

    header('Location: /vetsmart/recepcionista/agenda');
    exit;
}
    // Ruta: /recepcionista/citas/ver/{id} -> devuelve JSON con detalle de la cita
if (preg_match('#^/recepcionista/citas/ver/(\d+)$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $id = (int)$matches[1];

        //  de  (opcional)
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user'])) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'no_auth', 'msg' => 'Usuario no autenticado']);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT 
                c.id,
                c.fecha,
                c.duracion_min,
                c.estado,
                c.notas,
                u_c.nombre AS cliente_nombre,
                u_c.apellido AS cliente_apellido,
                u_c.telefono AS cliente_telefono,
                m.nombre AS mascota_nombre,
                s.nombre AS servicio,
                u_e.nombre AS empleado_nombre,
                u_e.apellido AS empleado_apellido
            FROM citas c
            LEFT JOIN usuarios u_c ON c.cliente_id = u_c.id
            LEFT JOIN usuarios u_e ON c.empleado_id = u_e.id
            LEFT JOIN servicios s ON c.servicio_id = s.id
            LEFT JOIN mascotas m ON c.mascota_id = m.id
            WHERE c.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');
        if (!$cita) {
            http_response_code(404);
            echo json_encode(['error' => 'not_found', 'msg' => 'Cita no encontrada']);
            exit;
        }

        // devolver la cita como JSON
        echo json_encode($cita);
        exit;

    } catch (Throwable $e) {
        // registrar error en logs y devolver JSON con mensaje ( en dev)
        error_log("Error al obtener detalle cita: " . $e->getMessage());
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'server_error', 'msg' => $e->getMessage()]);
        exit;
    }
}
// === CITAS (Recepcionista) ===
if (preg_match('#^/citas/delete/(\d+)$#', $path, $matches)) {
    $ctrl = new CitaController($pdo);
    $ctrl->delete((int)$matches[1]);
    exit;
}

if (preg_match('#^/citas/edit/(\d+)$#', $path, $matches)) {
    $ctrl = new CitaController($pdo);
    $ctrl->edit((int)$matches[1]);
    exit;
}

if ($path === '/citas/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new CitaController($pdo);
    $ctrl->update();
    exit;
}

if ($path === '/recepcionista/citas/create') {
    $ctrl = new CitaController($pdo);
    $ctrl->create();
    exit;
}


if ($path === '/citas/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new CitaController($pdo);
    $ctrl->store();
    exit;
}

// Peluquería (Recepcionista)
if ($path === '/recepcionista/citas-peluqueria/create' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    (new CitaController($pdo))->createPeluqueria();
    exit;
}
if ($path === '/recepcionista/citas-peluqueria/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new CitaController($pdo))->guardarCitaPeluqueria();
    exit;
}

if ($path === '/recepcionista/clientes') {
    $ctrl = new RecepcionistaController($pdo);
    $ctrl->clientes();
    exit;
}
// Ver perfil de cliente
if (preg_match('#^/recepcionista/clientes/ver/(\\d+)$#', $path, $matches)) {
    (new RecepcionistaController($pdo))->verCliente((int)$matches[1]);
    exit;
}
if ($path === '/recepcionista/clientes/create') {
    $controller = new RecepcionistaController($pdo);
    $controller->createCliente();
    exit;
}

if ($path === '/recepcionista/clientes/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new RecepcionistaController($pdo);
    $controller->storeCliente();
    exit;
}

//Editar cliente
// Mostrar formulario de edicion de cliente
if (preg_match('#^/recepcionista/clientes/edit/(\d+)$#', $path, $matches)) {
    (new RecepcionistaController($pdo))->editCliente((int)$matches[1]);
    exit;
}

// Guardar cambios del cliente
if ($path === '/recepcionista/clientes/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new RecepcionistaController($pdo))->updateCliente();
    exit;
}
//  Eliminar cliente
if (preg_match('#^/recepcionista/clientes/delete/(\d+)$#', $path, $matches)) {
    $controller = new RecepcionistaController($pdo);
    $controller->deleteCliente((int)$matches[1]);
    exit;
}
//Eliminar cliente en cascada con mascotas
if (preg_match('#^/recepcionista/clientes/(\d+)/eliminar$#', $path, $m)) {
    $controller = new RecepcionistaController($pdo);
    $controller->eliminarCliente((int)$m[1]);
    exit;
}

//Modulo de Gestion de Mascotas
if ($path === '/recepcionista/mascotas') {
    $controller = new RecepcionistaController($pdo);
    $controller->mascotas();
    exit;
}
// Ver perfil de mascota
if (preg_match('#^/recepcionista/mascotas/ver/(\\d+)$#', $path, $matches)) {
    (new RecepcionistaController($pdo))->verMascota((int)$matches[1]);
    exit;
}
// Ver historial  (solo lectura) por mascota
if (preg_match('#^/recepcionista/mascotas/(\d+)/historial$#', $path, $matches)) {
    (new RecepcionistaController($pdo))->historialMascota((int)$matches[1]);
    exit;
}
// Crear mascota
if ($path === '/recepcionista/mascotas/create') {
    (new RecepcionistaController($pdo))->crearMascota();
    exit;
}

// Guardar mascota
if ($path === '/recepcionista/mascotas/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new RecepcionistaController($pdo))->guardarMascota();
    exit;
}

// Editar mascota
if (preg_match('#^/recepcionista/mascotas/edit/(\d+)$#', $path, $matches)) {
    (new RecepcionistaController($pdo))->editarMascota((int)$matches[1]);
    exit;
}

// Actualizar mascota
if ($path === '/recepcionista/mascotas/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new RecepcionistaController($pdo))->actualizarMascota();
    exit;
}

// Eliminar mascota
if (preg_match('#^/recepcionista/mascotas/delete/(\d+)$#', $path, $matches)) {
    (new RecepcionistaController($pdo))->eliminarMascota((int)$matches[1]);
    exit;
}





if ($path === '/recepcionista/ingresos') {
    $controller = new RecepcionistaController($pdo);
    $controller->ingresos();
    exit;
}

if ($path === '/recepcionista/inventario') {
    $controller = new RecepcionistaController($pdo);
    $controller->inventario();
    exit;
}
if ($path === '/recepcionista/inventario/create' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    (new RecepcionistaController($pdo))->inventarioCreate();
    exit;
}
if ($path === '/recepcionista/inventario/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new RecepcionistaController($pdo))->inventarioStore();
    exit;
}
if (preg_match('#^/recepcionista/inventario/(\d+)/edit$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    (new RecepcionistaController($pdo))->inventarioEdit((int)$m[1]);
    exit;
}
if (preg_match('#^/recepcionista/inventario/(\d+)/update$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new RecepcionistaController($pdo))->inventarioUpdate((int)$m[1]);
    exit;
}
if (preg_match('#^/recepcionista/inventario/(\d+)/delete$#', $path, $m)) {
    (new RecepcionistaController($pdo))->inventarioDelete((int)$m[1]);
    exit;
}
if (preg_match('#^/recepcionista/inventario/(\d+)/ajustar$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
(new RecepcionistaController($pdo))->inventarioAjustar((int)$m[1]);
exit;
}

// CRUD Ingresos/Egresos (Recepcionista)
if ($path === '/recepcionista/ingresos/create' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    (new RecepcionistaController($pdo))->ingresosCreate();
    exit;
}
if ($path === '/recepcionista/ingresos/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new RecepcionistaController($pdo))->ingresosStore();
    exit;
}
if (preg_match('#^/recepcionista/ingresos/(\d+)/edit$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    (new RecepcionistaController($pdo))->ingresosEdit((int)$m[1]);
    exit;
}
if (preg_match('#^/recepcionista/ingresos/(\d+)/update$#', $path, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new RecepcionistaController($pdo))->ingresosUpdate((int)$m[1]);
    exit;
}
if (preg_match('#^/recepcionista/ingresos/(\d+)/delete$#', $path, $m)) {
    (new RecepcionistaController($pdo))->ingresosDelete((int)$m[1]);
    exit;
}


    // Si nada coincide -> 404
    http_response_code(404);
    echo "Pagina no encontrada. <a href='{$basePath}/auth/login'>Ir a login</a>";
}catch (Throwable $ex) {
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
?>


