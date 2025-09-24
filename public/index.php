<?php
// public/index.php
declare(strict_types=1);
session_start();

define('APP_ROOT', dirname(__DIR__) . '/app');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/core/Database.php';

$pdo = Database::getInstance();

require __DIR__ . '/../app/models/Usuario.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Servicio.php';
require_once APP_ROOT . '/models/Vacuna.php';
require_once APP_ROOT . '/models/Mascota.php';
require_once APP_ROOT . '/models/Cliente.php';

require __DIR__ . '/../app/core/CSRF.php';
require __DIR__ . '/../app/core/Controller.php';
require __DIR__ . '/../app/core/Auth.php';
require __DIR__ . '/../app/controllers/AuthController.php';
require __DIR__ . '/../app/core/Router.php'; 
require __DIR__ . '/../app/controllers/SuperAdminController.php';

require_once APP_ROOT . '/controllers/ClientesController.php';
require __DIR__ . '/../app/controllers/VeterinarioController.php';
require __DIR__ . '/../app/controllers/ServiciosController.php';
require_once APP_ROOT . '/controllers/ConsultasController.php';

// Detectar base path (subcarpeta donde vive la app)
$basePath = '/vetsmart';
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


// Eliminar el prefijo basePath de la URI
$path = $requestUri;
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
if ($path === '' || $path === false) {
    $path = '/';
}

// Instancia del AuthController (usa para login/forgot/reset)
$authController = new AuthController();

// Router mínimo (rutas existentes)
if ($path === '/' || ($path === '/login' && $_SERVER['REQUEST_METHOD'] === 'GET')) {
    $authController->showLogin();
    exit;
}

// También permitimos /auth/login para consistencia
if ($path === '/auth/login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController->showLogin();
    exit;
}

// POST /login o POST /auth/login -> procesa login
if (($path === '/login' || $path === '/auth/login') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login();
    exit;
}

// GET /logout or /auth/logout -> cerrar sesión
if ($path === '/logout' || $path === '/auth/logout') {
    $authController->logout();
    exit;
}

// GET /auth/register -> muestra formulario de registro
if ($path === '/auth/register' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController->showRegister();
    exit;
}

// POST /auth/register -> procesa el registro
if ($path === '/auth/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->register();
    exit;
}

// GET /auth/forgot -> formulario "olvidé mi clave"
if ($path === '/auth/forgot' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController->forgot();
    exit;
}

if ($path === '/auth/storeClient' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->storeClient();
    exit;
}

// POST /auth/sendResetLink -> procesa envío de correo (form forgot)
if ($path === '/auth/sendResetLink' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->sendResetLink();
    exit;
}

// GET /auth/reset?token=xxx -> formulario para nueva contraseña
if ($path === '/auth/reset' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController->reset();
    exit;
}

// POST /auth/updatePassword -> guarda la nueva contraseña
if ($path === '/auth/updatePassword' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->updatePassword();
    exit;
}

/* ---------------------------
   DASHBOARD según rol (ya tenías esto)
   --------------------------- */
// Asegúrate de tener esto al inicio (junto a otros require):
require_once APP_ROOT . '/controllers/VeterinarioController.php';
// require_once APP_ROOT . '/controllers/DashboardController.php'; // si lo usas

if ($path === '/dashboard') {
    if (empty($_SESSION['user'])) {
        header('Location: ' . $basePath . '/login');
        exit;
    }

    $role = $_SESSION['user']['role_name'] ?? null;

    if ($role === 'veterinario') {
        // Instancia el controlador de veterinario con PDO para que prepare datos reales
        $controller = new VeterinarioController($pdo);
        $controller->dashboard();
        exit;
    }

    // fallback para otros roles (puedes extender según necesites)
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
            $controller->view("super_admin/dashboard", [], "main_superadmin");
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
    $controller = new SuperAdminController($pdo); // Usa tu clase base Controller si requiere PDO
    $controller->permisos();
    exit;
}

if (preg_match('#^/superadmin/permisos/rol/(\d+)$#', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new SuperAdminController($pdo);
    $_GET['role_id'] = $matches[1];
    $controller->getPermisosPorRol();
    exit;
}

if ($path === '/superadmin/permisos/actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new SuperAdminController($pdo);
    $controller->actualizarPermisos();
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

// Ver consulta
if (preg_match('#^/veterinario/consultas/(\d+)/ver$#', $path, $m)) {
    $controller = new VeterinarioController($pdo);
    $controller->verConsulta($m[1]);
    exit;
}

// Editar consulta
if (preg_match('#^/veterinario/consultas/(\d+)/editar$#', $path, $m)) {
    $controller = new VeterinarioController($pdo);
    $controller->editarConsulta($m[1]);
    exit;
}

// Actualizar consulta (POST)
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


// Servicios

// === RUTAS PARA GESTIÓN DE SERVICIOS (Solo Admin) ===
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
        // usar modelo Mascota si existe
        $mascotaModel = new Mascota($pdo);
        if (method_exists($mascotaModel, 'getByDueno')) {
            $data = $mascotaModel->getByDueno($clienteId);
        } else {
            // fallback: consulta directa comprobando varios nombres posibles de columna
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

// Si nada coincide -> 404
http_response_code(404);
echo "Página no encontrada. <a href='{$basePath}/auth/login'>Ir a login</a>";

