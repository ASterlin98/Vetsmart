<?php
// app/models/Consulta.php
class Consulta {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function crear(array $data) {
        $sql = "INSERT INTO consultas
                (mascota_id, empleado_id, motivo, examen, diagnostico, tratamiento, recomendaciones, notas, creado_por, creado_en)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['mascota_id'],
            $data['empleado_id'] ?? null,
            $data['motivo'] ?? null,
            $data['examen'] ?? null,
            $data['diagnostico'] ?? null,
            $data['tratamiento'] ?? null,
            $data['recomendaciones'] ?? null,
            $data['notas'] ?? null,
            $data['creado_por'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

public function getById($id) {
    $sql = "SELECT c.*, m.nombre AS nombre_mascota, u.nombre AS nombre_veterinario, u.apellido AS apellido_veterinario
            FROM consultas c
            LEFT JOIN mascotas m ON c.mascota_id = m.id
            LEFT JOIN usuarios u ON c.empleado_id = u.id
            WHERE c.id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function getByMascota($mascota_id) {
        $sql = "SELECT c.*, u.nombre AS nombre_empleado, u.apellido AS apellido_empleado
                FROM consultas c
                LEFT JOIN usuarios u ON c.empleado_id = u.id
                WHERE c.mascota_id = ?
                ORDER BY c.creado_en DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mascota_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByVeterinario($id, $desde = null, $hasta = null) {
        $sql = "SELECT c.*, 
                    m.nombre AS nombre_mascota, 
                    CONCAT(u.nombre, ' ', u.apellido) AS nombre_veterinario
                FROM consultas c
                JOIN mascotas m ON c.mascota_id = m.id
                LEFT JOIN usuarios u ON c.empleado_id = u.id
                WHERE c.empleado_id = ?";

        $params = [$id];

        // Normalizar filtros de fecha: tratar los valores como fechas (YYYY-MM-DD)
        if ($desde && $hasta) {
            $desde_dt = date('Y-m-d', strtotime($desde));
            $hasta_dt = date('Y-m-d', strtotime($hasta));
            // Usar DATE() para incluir todas las horas del día final
            $sql .= " AND DATE(c.creado_en) BETWEEN ? AND ?";
            $params[] = $desde_dt;
            $params[] = $hasta_dt;
        } else {
            if ($desde) {
                $desde_dt = date('Y-m-d', strtotime($desde));
                $sql .= " AND DATE(c.creado_en) >= ?";
                $params[] = $desde_dt;
            }
            if ($hasta) {
                $hasta_dt = date('Y-m-d', strtotime($hasta));
                $sql .= " AND DATE(c.creado_en) <= ?";
                $params[] = $hasta_dt;
            }
        }

        $sql .= " ORDER BY c.creado_en DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function actualizar($id, array $data)
{
    $campos = [];
    $params = [':id' => $id];

    foreach ($data as $campo => $valor) {
        $campos[] = "$campo = :$campo";
        $params[":$campo"] = $valor;
    }

    $sql = "UPDATE consultas SET " . implode(', ', $campos) . " WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute($params);
}

public function eliminar($id) {
    $stmt = $this->db->prepare("DELETE FROM consultas WHERE id = ?");
    return $stmt->execute([$id]);
}

    public function getLatest($limit = 10) {
        $sql = "SELECT c.*, m.nombre AS nombre_mascota
                FROM consultas c
                LEFT JOIN mascotas m ON c.mascota_id = m.id
                ORDER BY c.creado_en DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int)$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
