<?php
declare(strict_types=1);

use Dompdf\Dompdf;
use Dompdf\Options;

class ReportesController extends Controller
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    private function verificarPermisosCliente(int $clienteId): void
    {
        $u   = $_SESSION['user'] ?? [];
        $rol = $u['role_name'] ?? ($u['role'] ?? '');
        $propio = (int)($u['id'] ?? 0) === $clienteId && $rol === 'cliente';
        $staff  = in_array((string)$rol, ['admin','superadmin','recepcionista','veterinario'], true);

        if (!($propio || $staff)) {
            http_response_code(403);
            exit('Acceso denegado');
        }
    }

    public function clientePdf(array $params): void
    {
        $clienteId = (int)($params['id'] ?? 0);
        $this->verificarPermisosCliente($clienteId);

        [$cliente, $mascotas, $citas, $clinico] = $this->cargarDatos($clienteId);
        $html = $this->renderizarVistaPdf($cliente, $mascotas, $citas, $clinico);

        $doc = trim((string)($cliente['documento'] ?? 'cliente_' . $clienteId));
        $this->emitirPdf($html, "ReporteCliente_{$doc}.pdf", true);
    }

    public function clientePdfPreview(array $params): void
    {
        $clienteId = (int)($params['id'] ?? 0);
        $this->verificarPermisosCliente($clienteId);

        [$cliente, $mascotas, $citas, $clinico] = $this->cargarDatos($clienteId);
        $html = $this->renderizarVistaPdf($cliente, $mascotas, $citas, $clinico);

        $doc = trim((string)($cliente['documento'] ?? 'cliente_' . $clienteId));
        $this->emitirPdf($html, "ReporteCliente_{$doc}.pdf", false);
    }

    public function peluqueroPdf(array $params): void
    {
        $empleadoId = (int)($params['id'] ?? 0);
        $this->verificarPermisosPeluquero($empleadoId);

        [$empleado, $kpis, $topServicios, $ultimas] = $this->cargarDatosPeluquero($empleadoId);
        $html = $this->renderizarVistaPeluquero($empleado, $kpis, $topServicios, $ultimas);
        $doc = 'peluquero_' . ($empleado['empleado_id'] ?? $empleadoId);
        $this->emitirPdf($html, "ReportePeluquero_{$doc}.pdf", true);
    }

    public function peluqueroPdfPreview(array $params): void
    {
        $empleadoId = (int)($params['id'] ?? 0);
        $this->verificarPermisosPeluquero($empleadoId);

        [$empleado, $kpis, $topServicios, $ultimas] = $this->cargarDatosPeluquero($empleadoId);
        $html = $this->renderizarVistaPeluquero($empleado, $kpis, $topServicios, $ultimas);
        $doc = 'peluquero_' . ($empleado['empleado_id'] ?? $empleadoId);
        $this->emitirPdf($html, "ReportePeluquero_{$doc}.pdf", false);
    }

    // =============== Mascota (historial clínico) ===============
    private function verificarPermisosMascota(): void
    {
        $u   = $_SESSION['user'] ?? [];
        $rol = strtolower((string)($u['role_name'] ?? ($u['role'] ?? '')));
        $staff = in_array($rol, ['admin','superadmin','recepcionista','veterinario'], true);
        if (!$staff) {
            http_response_code(403);
            exit('Acceso denegado');
        }
    }

    public function mascotaPdf(array $params): void
    {
        $this->verificarPermisosMascota();
        $mascotaId = (int)($params['id'] ?? 0);
        if ($mascotaId <= 0) { http_response_code(400); exit('Mascota inválida'); }

        [$mascota, $citas, $consultas, $notas] = $this->cargarDatosMascota($mascotaId);
        $html = $this->renderizarVistaMascotaPdf($mascota, $citas, $consultas, $notas);
        $nombre = trim((string)($mascota['nombre'] ?? 'mascota_' . $mascotaId));
        $this->emitirPdf($html, "Historial_Clinico_{$nombre}.pdf", true);
    }

    public function mascotaPdfPreview(array $params): void
    {
        $this->verificarPermisosMascota();
        $mascotaId = (int)($params['id'] ?? 0);
        if ($mascotaId <= 0) { http_response_code(400); exit('Mascota inválida'); }

        [$mascota, $citas, $consultas, $notas] = $this->cargarDatosMascota($mascotaId);
        $html = $this->renderizarVistaMascotaPdf($mascota, $citas, $consultas, $notas);
        $nombre = trim((string)($mascota['nombre'] ?? 'mascota_' . $mascotaId));
        $this->emitirPdf($html, "Historial_Clinico_{$nombre}.pdf", false);
    }

    private function cargarDatosMascota(int $mascotaId): array
    {
        // Datos principales
        $mStmt = $this->pdo->prepare("SELECT m.*, u.nombre AS dueno_nombre, u.apellido AS dueno_apellido FROM mascotas m LEFT JOIN usuarios u ON u.id = m.dueno_id WHERE m.id = ?");
        $mStmt->execute([$mascotaId]);
        $mascota = $mStmt->fetch(PDO::FETCH_ASSOC) ?: ['id' => $mascotaId, 'nombre' => 'Mascota'];

        // Citas
        $cStmt = $this->pdo->prepare("SELECT DATE(fecha) AS fecha, TIME(fecha) AS hora, estado FROM citas WHERE mascota_id = ? ORDER BY fecha DESC");
        $cStmt->execute([$mascotaId]);
        $citas = $cStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Consultas clínicas
        $qStmt = $this->pdo->prepare("SELECT creado_en AS fecha, motivo, diagnostico FROM consultas WHERE mascota_id = ? ORDER BY creado_en DESC");
        $qStmt->execute([$mascotaId]);
        $consultas = $qStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Notas
        $nStmt = $this->pdo->prepare("SELECT creado_en AS fecha, nota FROM notas_mascotas WHERE mascota_id = ? ORDER BY creado_en DESC");
        $nStmt->execute([$mascotaId]);
        $notas = $nStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [$mascota, $citas, $consultas, $notas];
    }

    private function renderizarVistaMascotaPdf(array $mascota, array $citas, array $consultas, array $notas): string
    {
        $logoPath = __DIR__ . '/../../public/assets/img/logo.png';
        $logoB64  = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode((string)file_get_contents($logoPath)) : '';
        ob_start();
        $logoB64 = $logoB64;
        require APP_ROOT . '/views/reportes/mascota_pdf.php';
        return (string)ob_get_clean();
    }

    private function verificarPermisosPeluquero(int $empleadoId): void
    {
        $u = $_SESSION['user'] ?? [];
        $rol = (string)($u['role_name'] ?? ($u['role'] ?? ''));
        // Para perfil Peluquero: no permitir generar/visualizar reportes
        // Solo personal autorizado (admin, superadmin, recepcionista)
        $staff = in_array($rol, ['admin','superadmin','recepcionista'], true);
        if (!$staff) {
            http_response_code(403);
            exit('Acceso denegado');
        }
    }

    private function cargarDatosPeluquero(int $empleadoId): array
    {
        // Datos base desde usuarios + emp_det (sin depender de v_empleado)
        $qEmp = $this->pdo->prepare(
            "SELECT 
                u.id AS empleado_id,
                u.id AS usuario_id,
                CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.apellido,'')) AS nombre_completo,
                u.docusu AS documento,
                u.email,
                u.telefono,
                COALESCE(e.especialidad,'Peluquero') AS cargo,
                COALESCE(e.activo,1) AS estado,
                e.fecha_ingreso,
                NULL AS salon
             FROM usuarios u
             LEFT JOIN emp_det e ON e.usuario_id = u.id
             WHERE u.id = ?"
        );
        $qEmp->execute([$empleadoId]);
        $empleado = $qEmp->fetch(PDO::FETCH_ASSOC) ?: ['empleado_id' => $empleadoId];

        // Mes actual
        $ini = (new DateTime('first day of this month'))->format('Y-m-d');
        $fin = (new DateTime('last day of this month'))->format('Y-m-d');

        // KPIs del mes
        $kpis = [
            'citas_atendidas' => 0,
            'tasa_finalizacion' => 0.0,
            'ticket_promedio' => 0.0,
            'ingresos' => 0.0,
        ];

        // cantidad de citas completadas
        $ids = $this->groomingServiceIds();
        $q1 = $this->pdo->prepare("SELECT COUNT(*) FROM citas WHERE empleado_id=? AND DATE(fecha) BETWEEN ? AND ? AND estado='completada' AND servicio_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")");
        $q1->execute(array_merge([$empleadoId, $ini, $fin], $ids));
        $kpis['citas_atendidas'] = (int)$q1->fetchColumn();

        // tasa finalización: completadas / (no canceladas)
        $q2 = $this->pdo->prepare("SELECT 
              SUM(CASE WHEN estado='completado' THEN 1 ELSE 0 END) AS comp,
              SUM(CASE WHEN estado<>'cancelado' THEN 1 ELSE 0 END) AS total
            FROM (
                SELECT estado FROM citas WHERE empleado_id=? AND DATE(fecha) BETWEEN ? AND ? AND servicio_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")
            ) t");
        $q2->execute(array_merge([$empleadoId, $ini, $fin], $ids));
        $row = $q2->fetch(PDO::FETCH_ASSOC) ?: ['comp'=>0,'total'=>0];
        $comp = (int)$row['comp'];
        $total = (int)$row['total'];
        $kpis['tasa_finalizacion'] = (float)($total > 0 ? (100.0 * $comp / $total) : 0);

        // ticket promedio e ingresos desde atenciones
        $q3 = $this->pdo->prepare("SELECT AVG(a.precio_final) AS avgp, SUM(a.precio_final) AS suma
            FROM atenciones_peluqueria a
            INNER JOIN citas c ON c.id = a.cita_id
            WHERE c.empleado_id=? AND DATE(c.fecha) BETWEEN ? AND ? AND c.estado='completada' AND c.servicio_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")");
        $q3->execute(array_merge([$empleadoId, $ini, $fin], $ids));
        $row3 = $q3->fetch(PDO::FETCH_ASSOC) ?: ['avgp'=>0,'suma'=>0];
        $kpis['ticket_promedio'] = (float)($row3['avgp'] ?? 0);
        $kpis['ingresos'] = (float)($row3['suma'] ?? 0);

        // top 5 servicios por frecuencia
        $qTop = $this->pdo->prepare("SELECT s.nombre, COUNT(*) AS cnt
            FROM citas c 
            INNER JOIN servicios s ON s.id = c.servicio_id
            WHERE c.empleado_id=? AND DATE(c.fecha) BETWEEN ? AND ? AND c.estado='completada' AND c.servicio_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")
            GROUP BY s.nombre
            ORDER BY cnt DESC
            LIMIT 5");
        $qTop->execute(array_merge([$empleadoId, $ini, $fin], $ids));
        $topServicios = $qTop->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // últimas 10 atenciones
        $qUlt = $this->pdo->prepare("SELECT DATE(c.fecha) AS fecha, TIME(c.fecha) AS hora, m.nombre AS mascota,
            CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS cliente,
            s.nombre AS servicio, a.precio_final, a.notas
            FROM atenciones_peluqueria a
            INNER JOIN citas c ON c.id = a.cita_id
            INNER JOIN servicios s ON s.id = c.servicio_id
            INNER JOIN mascotas m ON m.id = c.mascota_id
            INNER JOIN usuarios u ON u.id = c.cliente_id
            WHERE c.empleado_id=? AND c.servicio_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")
            ORDER BY c.fecha DESC
            LIMIT 10");
        $qUlt->execute(array_merge([$empleadoId], $ids));
        $ultimas = $qUlt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [$empleado, $kpis, $topServicios, $ultimas];
    }

    // identificar servicios de peluquería en catálogo general
    private function groomingServiceIds(): array
    {
        $ids = [];
        try {
            $q1 = $this->pdo->query("SELECT id FROM servicios WHERE activo=1 AND categoria='peluqueria'");
            if ($q1) {
                $rows = $q1->fetchAll(PDO::FETCH_ASSOC) ?: [];
                foreach ($rows as $r) { $ids[] = (int)$r['id']; }
            }
        } catch (Throwable $e) {
            // fallback por nombre
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

    private function renderizarVistaPeluquero(array $empleado, array $kpis, array $topServicios, array $ultimas): string
    {
        $logoPath = __DIR__ . '/../../public/assets/img/logo.png';
        $logoB64  = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode((string)file_get_contents($logoPath)) : '';
        ob_start();
        $logoB64 = $logoB64; // evitar notice
        require APP_ROOT . '/views/reportes/peluquero_pdf.php';
        return (string)ob_get_clean();
    }

    private function cargarDatos(int $clienteId): array
    {
        // Cliente (tabla: usuarios)
        $stmt = $this->pdo->prepare("SELECT id, nombre, apellido, docusu AS documento, email, telefono, direccion, created_at FROM usuarios WHERE id = ?");
        $stmt->execute([$clienteId]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['documento' => 'cliente_' . $clienteId];

        // Mascotas (tabla: mascotas, campo dueno_id)
        $qMasc = $this->pdo->prepare("SELECT nombre, especie, raza, sexo, edad FROM mascotas WHERE dueno_id = ? ORDER BY nombre ASC");
        $qMasc->execute([$clienteId]);
        $mascotas = $qMasc->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Citas (últimas 10) - usar nombre de servicio si existe
        $qCitas = $this->pdo->prepare(
            "SELECT c.fecha, c.estado, COALESCE(s.nombre,'-') AS servicio
             FROM citas c
             LEFT JOIN servicios s ON s.id = c.servicio_id
             WHERE c.cliente_id = ?
             ORDER BY c.fecha DESC
             LIMIT 10"
        );
        $qCitas->execute([$clienteId]);
        $citas = $qCitas->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Historial clínico (últimos 10) - desde consultas
        $qClin = $this->pdo->prepare(
            "SELECT m.nombre AS mascota_nombre, c.creado_en AS fecha, c.motivo, c.diagnostico
             FROM consultas c
             INNER JOIN mascotas m ON m.id = c.mascota_id
             WHERE m.dueno_id = ?
             ORDER BY c.creado_en DESC
             LIMIT 10"
        );
        $qClin->execute([$clienteId]);
        $clinico = $qClin->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [$cliente, $mascotas, $citas, $clinico];
    }

    private function renderizarVistaPdf(array $cliente, array $mascotas, array $citas, array $clinico): string
    {
        $logoPath = __DIR__ . '/../../public/assets/img/logo.png';
        $logoB64  = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode((string)file_get_contents($logoPath)) : '';

        ob_start();
        $logoB64 = $logoB64; // evitar notice
        require APP_ROOT . '/views/reportes/cliente_pdf.php';
        return (string)ob_get_clean();
    }

    private function emitirPdf(string $html, string $filename, bool $download): void
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->setChroot(__DIR__ . '/../../');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // numeración de páginas
        $canvas = $dompdf->get_canvas();
        if ($canvas) {
            $canvas->page_text(520, 815, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, [0,0,0]);
        }

        $dompdf->stream($filename, ['Attachment' => $download]);
    }
}
