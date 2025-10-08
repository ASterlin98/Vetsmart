<?php
// app/controllers/SuperAdminController.php

require_once APP_ROOT . '/core/Controller.php';
// modelos que probablemente ya tengas (si alguno no existe, el controller seguirá funcionando con fallbacks)
require_once APP_ROOT . '/models/Cliente.php';
require_once APP_ROOT . '/models/Mascota.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Servicio.php';
require_once APP_ROOT . '/models/Vacuna.php';

class SuperAdminController extends Controller
{
    private $clienteModel;
    private $mascotaModel;
    private $citaModel;
    private $servicioModel;
    private $vacunaModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        // Si los modelos no existen, los require arriba fallarán; si existen, los instanciamos.
        try { $this->clienteModel  = new Cliente($pdo); } catch (\Throwable $e) { $this->clienteModel = null; }
        try { $this->mascotaModel  = new Mascota($pdo); } catch (\Throwable $e) { $this->mascotaModel = null; }
        try { $this->citaModel     = new Cita($pdo); } catch (\Throwable $e) { $this->citaModel = null; }
        try { $this->servicioModel = new Servicio($pdo); } catch (\Throwable $e) { $this->servicioModel = null; }
        try { $this->vacunaModel   = new Vacuna($pdo); } catch (\Throwable $e) { $this->vacunaModel = null; }
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
}
