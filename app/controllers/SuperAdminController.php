<?php
// app/controllers/SuperAdminController.php

require_once APP_ROOT . '/core/Controller.php';
// modelos que probablemente ya tengas (si alguno no existe, el controller seguirá funcionando con fallbacks)
require_once APP_ROOT . '/models/Cliente.php';
require_once APP_ROOT . '/models/Mascota.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Servicio.php';
require_once APP_ROOT . '/models/Vacuna.php';
require_once APP_ROOT . '/models/RolePermission.php';
require_once APP_ROOT . '/models/Config.php';
require_once APP_ROOT . '/models/Permiso.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SuperAdminController extends Controller
{
    private $clienteModel;
    private $mascotaModel;
    private $citaModel;
    private $servicioModel;
    private $vacunaModel;
    private $rolePermissionModel;
    private $configModel;
    private $permisoModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        // Si los modelos no existen, los require arriba fallarán; si existen, los instanciamos.
        try { $this->clienteModel  = new Cliente($pdo); } catch (\Throwable $e) { $this->clienteModel = null; }
        try { $this->mascotaModel  = new Mascota($pdo); } catch (\Throwable $e) { $this->mascotaModel = null; }
        try { $this->citaModel     = new Cita($pdo); } catch (\Throwable $e) { $this->citaModel = null; }
        try { $this->servicioModel = new Servicio($pdo); } catch (\Throwable $e) { $this->servicioModel = null; }
        try { $this->vacunaModel   = new Vacuna($pdo); } catch (\Throwable $e) { $this->vacunaModel = null; }
        try { $this->rolePermissionModel = new RolePermission($pdo); } catch (\Throwable $e) { $this->rolePermissionModel = null; }
        try { $this->configModel = new Config($pdo); } catch (\Throwable $e) { $this->configModel = null; }
        try { $this->permisoModel = new Permiso($pdo); } catch (\Throwable $e) { $this->permisoModel = null; }
    }

    public function dashboard()
    {
        try {
            $db = $this->db ?? $this->pdo ?? null;
            if (!$db instanceof PDO) {
                throw new Exception('No se encontró conexión PDO en SuperAdminController.');
            }

            // COUNT Clientes
            $totalClientes = 0;
            try {
                // primer intento: tabla usuarios con columna role_name
                $stmt = $db->query("SELECT COUNT(*) FROM usuarios WHERE role_name = 'cliente' OR role_name IS NULL");
                $totalClientes = (int)$stmt->fetchColumn();
            } catch (\Throwable $e1) {
                // fallback: si existe tabla clientes
                try {
                    $stmt = $db->query("SELECT COUNT(*) FROM clientes");
                    $totalClientes = (int)$stmt->fetchColumn();
                } catch (\Throwable $e2) {
                    $totalClientes = 0;
                }
            }

            // COUNT Mascotas
            $totalMascotas = 0;
            try {
                $stmt = $db->query("SELECT COUNT(*) FROM mascotas");
                $totalMascotas = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                // fallback: diferentes nombres
                try {
                    $stmt = $db->query("SELECT COUNT(*) FROM pets");
                    $totalMascotas = (int)$stmt->fetchColumn();
                } catch (\Throwable $e2) {
                    $totalMascotas = 0;
                }
            }

            // COUNT Citas
            $totalCitas = 0;
            try {
                $stmt = $db->query("SELECT COUNT(*) FROM citas");
                $totalCitas = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                try {
                    $stmt = $db->query("SELECT COUNT(*) FROM appointments");
                    $totalCitas = (int)$stmt->fetchColumn();
                } catch (\Throwable $e2) {
                    $totalCitas = 0;
                }
            }

            // SUM ingresos: preferimos sumar por servicios asociados a citas
            $totalIngresos = 0.0;
            try {
                $sql = "
                    SELECT COALESCE(SUM(s.precio),0) AS total
                    FROM citas c
                    LEFT JOIN servicios s ON c.servicio_id = s.id
                ";
                $stmt = $db->query($sql);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalIngresos = $row ? (float)$row['total'] : 0.0;
            } catch (\Throwable $e) {
                // fallback: si tienes tabla pagos o ingresos
                try {
                    $stmt = $db->query("SELECT COALESCE(SUM(monto),0) FROM pagos");
                    $totalIngresos = (float)$stmt->fetchColumn();
                } catch (\Throwable $e2) {
                    $totalIngresos = 0.0;
                }
            }

            // COUNT Vacunas
            $totalVacunas = 0;
            try {
                $stmt = $db->query("SELECT COUNT(*) FROM vacunas");
                $totalVacunas = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                $totalVacunas = 0;
            }

            // Citas confirmadas
            $citasConfirmadas = 0;
            try {
                $stmt = $db->query("SELECT COUNT(*) FROM citas WHERE estado = 'confirmada'");
                $citasConfirmadas = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                $citasConfirmadas = 0;
            }

            // Actividad reciente (consulta compuesta; si falla, fallback sencillo)
            $recent = [];
            try {
                $recentSql = "
                    SELECT 'cita' AS tipo, c.id AS entidad_id,
                           CONCAT(COALESCE(u_creo.nombre,''),' ',COALESCE(u_creo.apellido,'')) AS actor,
                           'Cita creada' AS accion,
                           COALESCE(c.creado_en, c.fecha, NOW()) AS creado_en,
                           CONCAT('Mascota: ', COALESCE(m.nombre,''), ' — Servicio: ', COALESCE(s.nombre,'')) AS detalle
                    FROM citas c
                    LEFT JOIN usuarios u_creo ON c.creado_por = u_creo.id
                    LEFT JOIN mascotas m ON c.mascota_id = m.id
                    LEFT JOIN servicios s ON c.servicio_id = s.id

                    UNION ALL

                    SELECT 'usuario' AS tipo, u.id AS entidad_id,
                           CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS actor,
                           'Usuario registrado' AS accion,
                           COALESCE(u.creado_en, u.created_at, NOW()) AS creado_en,
                           CONCAT('Email: ', COALESCE(u.email,''), ' — Rol: ', COALESCE(u.role_name,'')) AS detalle
                    FROM usuarios u

                    UNION ALL

                    SELECT 'vacuna' AS tipo, v.id AS entidad_id,
                           CONCAT(COALESCE(u2.nombre,''),' ',COALESCE(u2.apellido,'')) AS actor,
                           'Vacuna registrada' AS accion,
                           COALESCE(v.creado_en, v.fecha_aplicacion, NOW()) AS creado_en,
                           CONCAT('Mascota: ', COALESCE(m3.nombre,''), ' — Vacuna: ', COALESCE(v.nombre,'')) AS detalle
                    FROM vacunas v
                    LEFT JOIN mascotas m3 ON v.mascota_id = m3.id
                    LEFT JOIN usuarios u2 ON v.creador_id = u2.id

                    ORDER BY creado_en DESC
                    LIMIT 20
                ";
                $stmt = $db->query($recentSql);
                $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {
                // fallback simple: últimas citas
                try {
                    $rows = $db->query("SELECT id, COALESCE(creado_en, fecha, NOW()) AS creado_en, CONCAT('Cita #', id) AS detalle FROM citas ORDER BY COALESCE(creado_en, fecha) DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rows as $r) {
                        $recent[] = ['tipo'=>'cita','entidad_id'=>$r['id'],'actor'=>'','accion'=>'Cita','creado_en'=>$r['creado_en'],'detalle'=>$r['detalle']];
                    }
                } catch (\Throwable $e2) {
                    $recent = [];
                }
            }

            // Datos para la vista
            $data = [
                'totalClientes'    => $totalClientes,
                'totalMascotas'    => $totalMascotas,
                'totalCitas'       => $totalCitas,
                'totalIngresos'    => $totalIngresos,
                'totalVacunas'     => $totalVacunas,
                'citasConfirmadas' => $citasConfirmadas,
                'recentActivity'   => $recent
            ];

            $this->view('super_admin/dashboard', $data, 'main_superadmin');
        } catch (\Throwable $ex) {
            error_log("SuperAdmin::dashboard error: " . $ex->getMessage());
            // enviar vista con zeros y mensaje de error
            $this->view('super_admin/dashboard', [
                'totalClientes'=>0,'totalMascotas'=>0,'totalCitas'=>0,
                'totalIngresos'=>0,'totalVacunas'=>0,'citasConfirmadas'=>0,
                'recentActivity'=>[],
                'error' => $ex->getMessage()
            ], 'main_superadmin');
        }
    }

    public function getPermisosPorRol()
    {
        header('Content-Type: application/json');

        if (!$this->rolePermissionModel) {
            http_response_code(500);
            echo json_encode(['error' => 'El modelo RolePermission no está disponible.']);
            return;
        }

        $url_parts = explode('/', $_GET['url'] ?? '');
        $role_id_from_url = end($url_parts);

        if (!is_numeric($role_id_from_url) || $role_id_from_url <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de rol no válido en la URL.']);
            return;
        }
        $role_id = (int)$role_id_from_url;

        try {
            $all_permissions = $this->rolePermissionModel->getAllPermissionsGroupedByModule();
            $assigned_permissions = $this->rolePermissionModel->getPermissionIdsByRoleId($role_id);

            echo json_encode([
                'all_permissions' => $all_permissions,
                'assigned_permissions' => $assigned_permissions
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener los permisos: ' . $e->getMessage()]);
        }
    }

    public function actualizarPermisos()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido.']);
            return;
        }

        if (!$this->rolePermissionModel) {
            http_response_code(500);
            echo json_encode(['error' => 'El modelo RolePermission no está disponible.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $role_id = $input['role_id'] ?? null;
        $permission_ids = $input['permission_ids'] ?? [];

        if (empty($role_id) || !is_numeric($role_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de rol no válido.']);
            return;
        }

        $permission_ids = array_map('intval', $permission_ids);

        try {
            $success = $this->rolePermissionModel->updatePermissionsForRole($role_id, $permission_ids);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Permisos actualizados correctamente.']);
            } else {
                 if ($role_id == 1) {
                    http_response_code(403); // Forbidden
                    echo json_encode(['success' => false, 'message' => 'No se pueden modificar los permisos del Super Administrador.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'No se pudieron actualizar los permisos.']);
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
        }
    }

    public function configuracion()
    {
        if (!$this->configModel) {
            $this->view('super_admin/config', ['error' => 'El modelo Config no está disponible.'], 'main_superadmin');
            return;
        }

        $settings = $this->configModel->getAllSettings();
        $this->view('super_admin/config', ['settings' => $settings], 'main_superadmin');
    }

    public function actualizarConfiguracion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vetsmart/super_admin/configuracion');
            exit;
        }

        if (!$this->configModel) {
            // Manejar el error, quizás redirigir con un mensaje
            header('Location: /vetsmart/super_admin/configuracion?error=model_unavailable');
            exit;
        }

        // Sanitizar y preparar los datos del POST.
        // Se asume que los nombres de los campos del formulario coinciden con las claves de la BD.
        $settings = $_POST;

        // Opcional: eliminar el token CSRF si se está usando uno
        // unset($settings['csrf_token']);

        $success = $this->configModel->updateSettings($settings);

        if ($success) {
            header('Location: /vetsmart/super_admin/configuracion?success=true');
        } else {
            header('Location: /vetsmart/super_admin/configuracion?error=update_failed');
        }
        exit;
    }

    public function reportes()
    {
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-t');

        $params = [':desde' => $desde, ':hasta' => $hasta];

        // 1. Estadísticas Generales
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) AS total_citas,
                COALESCE(SUM(s.precio), 0) AS ingresos_totales
            FROM citas c
            JOIN servicios s ON c.servicio_id = s.id
            WHERE DATE(c.fecha) BETWEEN :desde AND :hasta AND c.estado = 'completada'
        ");
        $stmt->execute($params);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Citas por Estado
        $stmt = $this->db->prepare("
            SELECT estado, COUNT(*) as cantidad
            FROM citas
            WHERE DATE(fecha) BETWEEN :desde AND :hasta
            GROUP BY estado
        ");
        $stmt->execute($params);
        $citasPorEstado = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // 3. Top 5 Servicios más Rentables
        $stmt = $this->db->prepare("
            SELECT s.nombre, SUM(s.precio) as total_ingresos
            FROM citas c
            JOIN servicios s ON c.servicio_id = s.id
            WHERE DATE(c.fecha) BETWEEN :desde AND :hasta AND c.estado = 'completada'
            GROUP BY s.nombre
            ORDER BY total_ingresos DESC
            LIMIT 5
        ");
        $stmt->execute($params);
        $topServicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Top 5 Empleados más Activos
        $stmt = $this->db->prepare("
            SELECT CONCAT(u.nombre, ' ', u.apellido) as empleado, COUNT(c.id) as total_citas
            FROM citas c
            JOIN usuarios u ON c.empleado_id = u.id
            WHERE DATE(c.fecha) BETWEEN :desde AND :hasta
            GROUP BY u.id
            ORDER BY total_citas DESC
            LIMIT 5
        ");
        $stmt->execute($params);
        $topEmpleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'desde' => $desde,
            'hasta' => $hasta,
            'stats' => $stats,
            'citasPorEstado' => $citasPorEstado,
            'topServicios' => $topServicios,
            'topEmpleados' => $topEmpleados
        ];

        $this->view('super_admin/reportes', $data, 'main_superadmin');
    }

    public function exportarReportes()
    {
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-t');

        $params = [':desde' => $desde, ':hasta' => $hasta];

        $stmt = $this->db->prepare("
            SELECT
                c.id as cita_id,
                c.fecha,
                c.estado,
                s.nombre as servicio_nombre,
                s.precio as servicio_precio,
                CONCAT(u_cli.nombre, ' ', u_cli.apellido) as cliente_nombre,
                CONCAT(u_emp.nombre, ' ', u_emp.apellido) as empleado_nombre
            FROM citas c
            JOIN servicios s ON c.servicio_id = s.id
            JOIN usuarios u_cli ON c.cliente_id = u_cli.id
            JOIN usuarios u_emp ON c.empleado_id = u_emp.id
            WHERE DATE(c.fecha) BETWEEN :desde AND :hasta
            ORDER BY c.fecha DESC
        ");
        $stmt->execute($params);
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte General');

        // Encabezados
        $headers = ['ID Cita', 'Fecha', 'Estado', 'Servicio', 'Precio', 'Cliente', 'Empleado'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Estilo para encabezados
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4285F4']]
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Datos
        $sheet->fromArray($citas, NULL, 'A2');

        // Autoajustar columnas
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Descargar archivo
        $filename = "reporte_general_{$desde}_a_{$hasta}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function permisos()
    {
        if (!$this->permisoModel || !$this->rolePermissionModel) {
            $this->view('super_admin/permisos', ['error' => 'Los modelos necesarios no están disponibles.'], 'main_superadmin');
            return;
        }

        $permisos = $this->permisoModel->getAll();
        $modulos = $this->permisoModel->getModules();
        $roles = $this->rolePermissionModel->getAllRoles(); // Fetch roles

        $permisosAgrupados = [];
        foreach ($permisos as $permiso) {
            $permisosAgrupados[$permiso['modulo']][] = $permiso;
        }

        $this->view('super_admin/permisos', [
            'permisosAgrupados' => $permisosAgrupados,
            'modulos' => $modulos,
            'roles' => $roles, // Pass roles to the view
        ], 'main_superadmin');
    }

    public function guardarPermiso()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->permisoModel) {
            header('Location: /vetsmart/super_admin/permisos');
            exit;
        }

        $data = [
            'modulo' => trim($_POST['modulo']),
            'nombre' => trim($_POST['nombre']),
            'descripcion' => trim($_POST['descripcion']),
            'accion' => trim($_POST['accion']),
            'orden' => (int)($_POST['orden'] ?? 0),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        if (!empty(trim($_POST['nuevo_modulo']))) {
            $data['modulo'] = strtolower(str_replace(' ', '_', trim($_POST['nuevo_modulo'])));
        }

        try {
            if ($this->permisoModel->create($data)) {
                $_SESSION['flash_success'] = 'Permiso creado correctamente.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo crear el permiso. Verifique los datos.';
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al crear el permiso: ' . $e->getMessage();
        }

        header('Location: /vetsmart/super_admin/permisos');
        exit;
    }

    public function actualizarPermiso()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->permisoModel) {
            header('Location: /vetsmart/super_admin/permisos');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'ID de permiso no válido.';
            header('Location: /vetsmart/super_admin/permisos');
            exit;
        }

        $data = [
            'modulo' => trim($_POST['modulo']),
            'nombre' => trim($_POST['nombre']),
            'descripcion' => trim($_POST['descripcion']),
            'accion' => trim($_POST['accion']),
            'orden' => (int)($_POST['orden'] ?? 0),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        try {
            if ($this->permisoModel->update($id, $data)) {
                $_SESSION['flash_success'] = 'Permiso actualizado correctamente.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo actualizar el permiso. Verifique los datos.';
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al actualizar el permiso: ' . $e->getMessage();
        }

        header('Location: /vetsmart/super_admin/permisos');
        exit;
    }

    public function eliminarPermiso($id)
    {
        $id = (int)$id;
        if ($id <= 0 || !$this->permisoModel) {
            header('Location: /vetsmart/super_admin/permisos');
            exit;
        }

        try {
            if ($this->permisoModel->delete($id)) {
                $_SESSION['flash_success'] = 'Permiso eliminado correctamente.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo eliminar el permiso.';
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al eliminar el permiso: ' . $e->getMessage();
        }

        header('Location: /vetsmart/super_admin/permisos');
        exit;
    }
}
