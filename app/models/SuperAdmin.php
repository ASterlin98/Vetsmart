<?php
// app/models/SuperAdmin.php
class SuperAdmin {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function getEstadisticas(): array {
        try {
            // Clientes: usuarios cuyo rol es 'cliente'
            $sqlClientes = "
                SELECT COUNT(*) FROM usuarios u
                JOIN roles r ON u.role_id = r.id
                WHERE r.nombre = 'cliente'
            ";
            $clientes = (int)$this->pdo->query($sqlClientes)->fetchColumn();
        } catch (\Throwable $e) {
            error_log("SuperAdmin::getEstadisticas clientes error: " . $e->getMessage());
            $clientes = 0;
        }

        try {
            $mascotas = (int)$this->pdo->query("SELECT COUNT(*) FROM mascotas")->fetchColumn();
        } catch (\Throwable $e) {
            error_log("SuperAdmin::getEstadisticas mascotas error: " . $e->getMessage());
            $mascotas = 0;
        }

        try {
            $citas = (int)$this->pdo->query("SELECT COUNT(*) FROM citas")->fetchColumn();
        } catch (\Throwable $e) {
            error_log("SuperAdmin::getEstadisticas citas error: " . $e->getMessage());
            $citas = 0;
        }

        try {
            // Ingresos: sumar el precio del servicio asociado a citas completadas
            $sqlIngresos = "
                SELECT COALESCE(SUM(s.precio), 0) AS total
                FROM citas c
                LEFT JOIN servicios s ON c.servicio_id = s.id
                WHERE c.estado IN ('completada', 'confirmada')
            ";
            $ingresos = (float)$this->pdo->query($sqlIngresos)->fetchColumn();
        } catch (\Throwable $e) {
            error_log("SuperAdmin::getEstadisticas ingresos error: " . $e->getMessage());
            $ingresos = 0.0;
        }

        return [
            'clientes' => $clientes,
            'mascotas' => $mascotas,
            'citas'    => $citas,
            'ingresos' => $ingresos
        ];
    }

    // (Conservé los demás métodos que tenías si los necesitas)
    public function getCitasPorDias(int $dias = 7): array {
        $sql = "
          SELECT DATE(fecha) AS dia, COUNT(*) AS total
          FROM citas
          WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
          GROUP BY DATE(fecha)
          ORDER BY DATE(fecha) ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':dias' => $dias]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d/m', strtotime($d));
            $found = false;
            foreach ($rows as $r) {
                if ($r['dia'] === $d) { $data[] = (int)$r['total']; $found = true; break; }
            }
            if (!$found) $data[] = 0;
        }
        return ['labels' => $labels, 'data' => $data];
    }

    public function getUltimasAcciones(int $limit = 10): array {
        $sql = "
          SELECT a.*, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido
          FROM auditoria a
          LEFT JOIN usuarios u ON a.usuario_id = u.id
          ORDER BY a.creado_en DESC
          LIMIT :lim
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
