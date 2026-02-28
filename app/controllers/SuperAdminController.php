<?php
// app/controllers/SuperAdminController.php

require_once APP_ROOT . '/core/Controller.php';
// modelos que probablemente ya tengas (si alguno no existe, el controller seguirá funcionando con fallbacks)
require_once APP_ROOT . '/models/Cliente.php';
require_once APP_ROOT . '/models/Mascota.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Servicio.php';
require_once APP_ROOT . '/models/Vacuna.php';

require_once APP_ROOT . '/models/Config.php';

// Nuevo: modelo centralizado para estadísticas
require_once APP_ROOT . '/models/SuperAdmin.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SuperAdminController extends Controller
{
    private $clienteModel;
    private $mascotaModel;
    private $citaModel;
    private $servicioModel;
    private $vacunaModel;

    private $configModel;

    private $superAdminModel; // NUEVO

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        // Si los modelos no existen, los require arriba fallarán; si existen, los instanciamos.
        try { $this->clienteModel  = new Cliente($pdo); } catch (\Throwable $e) { $this->clienteModel = null; }
        try { $this->mascotaModel  = new Mascota($pdo); } catch (\Throwable $e) { $this->mascotaModel = null; }
        try { $this->citaModel     = new Cita($pdo); } catch (\Throwable $e) { $this->citaModel = null; }
        try { $this->servicioModel = new Servicio($pdo); } catch (\Throwable $e) { $this->servicioModel = null; }
        try { $this->vacunaModel   = new Vacuna($pdo); } catch (\Throwable $e) { $this->vacunaModel = null; }

        try { $this->configModel = new Config($pdo); } catch (\Throwable $e) { $this->configModel = null; }

        // Instanciar SuperAdmin model (si existe)
        try { $this->superAdminModel = new SuperAdmin($pdo); } catch (\Throwable $e) { $this->superAdminModel = null; }
    }

    public function dashboard()
    {
        // Data for stats cards
        $totalUsuarios = $this->superAdminModel->countTotalUsuarios();
        $ticketsAbiertos = $this->superAdminModel->countTicketsAbiertos();
        $citasHoy = $this->superAdminModel->countCitasHoy();

        // Data for charts
        $usuariosPorRol = $this->superAdminModel->countUsuariosPorRol();
        $ticketsPorPrioridad = $this->superAdminModel->countTicketsPorPrioridad();

        // Data for recent activity (with pagination)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 3;
        $offset = ($page - 1) * $limit;
        $recentActivity = $this->superAdminModel->getRecentActivity($limit, $offset);
        $totalActivities = $this->superAdminModel->countTotalActivities();
        $totalPages = ceil($totalActivities / $limit);

        // Data for recent tickets
        $recentTickets = $this->superAdminModel->getRecentTickets(5);

        $data = [
            'totalUsuarios' => $totalUsuarios,
            'ticketsAbiertos' => $ticketsAbiertos,
            'citasHoy' => $citasHoy,
            'usuariosPorRol' => json_encode($usuariosPorRol),
            'ticketsPorPrioridad' => json_encode($ticketsPorPrioridad),
            'recentActivity' => $recentActivity,
            'recentTickets' => $recentTickets,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ];

        $this->view('super_admin/dashboard', $data, 'main_superadmin');
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
            header('Location: ' . BASE . '/super_admin/configuracion');
            exit;
        }

        if (!$this->configModel) {
            // Manejar el error, quizás redirigir con un mensaje
            header('Location: ' . BASE . '/super_admin/configuracion?error=model_unavailable');
            exit;
        }

        // Sanitizar y preparar los datos del POST.
        // Se asume que los nombres de los campos del formulario coinciden con las claves de la BD.
        $settings = $_POST;

        // Opcional: eliminar el token CSRF si se está usando uno
        // unset($settings['csrf_token']);

        $success = $this->configModel->updateSettings($settings);

        if ($success) {
            header('Location: ' . BASE . '/super_admin/configuracion?success=true');
        } else {
            header('Location: ' . BASE . '/super_admin/configuracion?error=update_failed');
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
            WHERE DATE(c.fecha) BETWEEN :desde AND :hasta AND c.estado IN ('completada', 'confirmada')
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
            WHERE DATE(c.fecha) BETWEEN :desde AND :hasta AND c.estado IN ('completada', 'confirmada')
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

        // 1. Top 5 Servicios más Rentables (para el gráfico)
        $stmt = $this->db->prepare("
            SELECT s.nombre, SUM(s.precio) as total_ingresos
            FROM citas c
            JOIN servicios s ON c.servicio_id = s.id
            WHERE DATE(c.fecha) BETWEEN :desde AND :hasta AND c.estado IN ('completada', 'confirmada')
            GROUP BY s.nombre
            ORDER BY total_ingresos DESC
            LIMIT 5
        ");
        $stmt->execute($params);
        $topServicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Datos generales de citas (para la tabla)
        $stmt = $this->db->prepare("
            SELECT
                c.id as cita_id, c.fecha, c.estado,
                s.nombre as servicio_nombre, s.precio as servicio_precio,
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

        // --- Hoja 1: Reporte General (con gráfico) ---
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte General');

        // Encabezados de la tabla de servicios para el gráfico
        $sheet->setCellValue('J1', 'Top 5 Servicios Rentables');
        $sheet->getStyle('J1')->getFont()->setBold(true);
        $sheet->fromArray(['Servicio', 'Ingresos'], NULL, 'J2');
        $sheet->fromArray($topServicios, NULL, 'J3');
        $dataRowCount = count($topServicios) + 2;

        // Encabezados de la tabla principal
        $headers = ['ID Cita', 'Fecha', 'Estado', 'Servicio', 'Precio', 'Cliente', 'Empleado'];
        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Datos de la tabla principal
        $sheet->fromArray($citas, NULL, 'A2');

        // Autoajustar columnas
        foreach (range('A', 'G') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        foreach (range('J', 'K') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        // --- Creación del Gráfico ---
        $dataSeriesLabels = [
            new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Reporte General!$K$2', null, 1),
        ];
        $xAxisTickValues = [
            new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Reporte General!$J$3:$J$' . $dataRowCount, null, count($topServicios)),
        ];
        $dataSeriesValues = [
            new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Reporte General!$K$3:$K$' . $dataRowCount, null, count($topServicios)),
        ];

        $series = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART,
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea(null, [$series]);
        $legend = new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_RIGHT, null, false);
        $title = new \PhpOffice\PhpSpreadsheet\Chart\Title('Top 5 Servicios más Rentables');
        $yAxisLabel = new \PhpOffice\PhpSpreadsheet\Chart\Title('Ingresos ($)');

        $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
            'chart1',
            $title,
            $legend,
            $plotArea,
            true,
            0,
            null,
            $yAxisLabel
        );

        $chart->setTopLeftPosition('A' . (count($citas) + 5));
        $chart->setBottomRightPosition('H' . (count($citas) + 20));
        $sheet->addChart($chart);

        // --- Descargar Archivo ---
        $filename = "reporte_general_{$desde}_a_{$hasta}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet, $chart);
        $writer->save('php://output');
        exit;
    }

}
