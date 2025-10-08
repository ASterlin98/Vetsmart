<?php
// app/models/SuperAdmin.php
class SuperAdmin {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function getEstadisticas(): array {
        return [
            'clientes' => (int)$this->pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn(),
            'mascotas' => (int)$this->pdo->query("SELECT COUNT(*) FROM mascotas")->fetchColumn(),
            'citas'    => (int)$this->pdo->query("SELECT COUNT(*) FROM citas")->fetchColumn(),
            'ingresos' => (float)$this->pdo->query("SELECT COALESCE(SUM(total),0) FROM pagos")->fetchColumn()
        ];
    }

    // Stats por día para últimos N días (para gráfico)
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

        // rellenar días faltantes con 0
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

    // Últimas acciones (auditoría)
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
