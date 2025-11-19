<?php
// app/models/Cita.php
class Cita {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Crear nueva cita
    public function crear(array $data) {
        $sql = "INSERT INTO citas (
                    cliente_id, mascota_id, empleado_id, servicio_id,
                    fecha, duracion_min, estado, notas, creado_por, creado_en
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['cliente_id'],
            $data['mascota_id'],
            $data['empleado_id'],
            $data['servicio_id'],
            $data['fecha'],              
            $data['duracion_min'] ?? 30,
            $data['estado'] ?? 'programada',
            $data['notas'] ?? null,
            $data['creado_por'] ?? ($_SESSION['user']['id'] ?? null)
        ]);
        return $this->db->lastInsertId();
    }

    // Obtener citas de un veterinario (para el calendario)
    public function getPorVeterinario($veterinario_id) {
        $sql = "SELECT c.id, c.fecha, c.mascota_id, m.nombre AS nombre_mascota, c.servicio_id
                FROM citas c
                JOIN mascotas m ON c.mascota_id = m.id
                WHERE c.empleado_id = ?
                ORDER BY c.fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$veterinario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una cita por id (API)
    public function getById($id) {
        $sql = "SELECT c.*, m.nombre AS nombre_mascota, u.nombre AS cliente_nombre, u.apellido AS cliente_apellido
                FROM citas c
                LEFT JOIN mascotas m ON c.mascota_id = m.id
                LEFT JOIN usuarios u ON c.cliente_id = u.id
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar cita
    public function actualizar($id, array $data) {
        $sql = "UPDATE citas SET mascota_id = ?, servicio_id = ?, fecha = ?, duracion_min = ?, estado = ?, notas = ?
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['mascota_id'],
            $data['servicio_id'],
            $data['fecha'],
            $data['duracion_min'] ?? 30,
            $data['estado'] ?? 'programada',
            $data['notas'] ?? null,
            $id
        ]);
    }

    // Eliminar cita
public function eliminar($id)
{
    $stmt = $this->db->prepare("DELETE FROM citas WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

    // Historial por mascota
    public function getHistorialPorVeterinario($veterinario_id, $desde = null, $hasta = null) {
        $sql = "SELECT c.*, 
                    m.nombre AS nombre_mascota,
                    u.nombre AS cliente_nombre,
                    u.apellido AS cliente_apellido,
                    s.nombre AS nombre_servicio
                FROM citas c
                JOIN mascotas m ON c.mascota_id = m.id
                JOIN usuarios u ON c.cliente_id = u.id
                JOIN servicios s ON c.servicio_id = s.id
                WHERE c.empleado_id = ?";
        
        $params = [$veterinario_id];

        if ($desde) {
            $sql .= " AND c.fecha >= ?";
            $params[] = $desde;
        }

        if ($hasta) {
            $sql .= " AND c.fecha <= ?";
            $params[] = $hasta;
        }

        $sql .= " ORDER BY c.fecha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function countUpcomingByVeterinario($veterinarioId) {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM citas WHERE empleado_id = :id AND fecha >= NOW()");
    $stmt->execute([':id' => $veterinarioId]);
    return $stmt->fetchColumn();
}

public function getTodayByVeterinario($veterinarioId) {
    $sql = "
        SELECT c.*, m.nombre AS nombre_mascota, u.nombre AS cliente_nombre, u.apellido AS cliente_apellido
        FROM citas c
        INNER JOIN mascotas m ON c.mascota_id = m.id
        INNER JOIN usuarios u ON c.cliente_id = u.id
        WHERE c.empleado_id = :id AND DATE(c.fecha) = CURDATE()
        ORDER BY c.fecha ASC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $veterinarioId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getUpcomingByVeterinario($veterinarioId, $limite = 8) {
    $sql = "
        SELECT c.*, m.nombre AS nombre_mascota,
               u.nombre AS cliente_nombre, u.apellido AS cliente_apellido,
               s.nombre AS servicio
        FROM citas c
        INNER JOIN mascotas m ON c.mascota_id = m.id
        INNER JOIN usuarios u ON c.cliente_id = u.id
        INNER JOIN servicios s ON c.servicio_id = s.id
        WHERE c.empleado_id = :id AND c.fecha >= NOW()
        ORDER BY c.fecha ASC
        LIMIT :limite
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $veterinarioId, PDO::PARAM_INT);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Total citas
    public function contarPorVeterinario($veterinario_id) {
        $sql = "SELECT COUNT(*) FROM citas WHERE empleado_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$veterinario_id]);
        return (int) $stmt->fetchColumn();
    }

    // Mascotas únicas
    public function contarMascotasUnicas($veterinario_id) {
        $sql = "SELECT COUNT(DISTINCT mascota_id) FROM citas WHERE empleado_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$veterinario_id]);
        return (int) $stmt->fetchColumn();
    }

    // Servicios más usados
    public function topServiciosPorVeterinario($veterinario_id) {
        $sql = "SELECT s.nombre, COUNT(*) AS total
                FROM citas c
                JOIN servicios s ON c.servicio_id = s.id
                WHERE c.empleado_id = ?
                GROUP BY s.nombre
                ORDER BY total DESC
                LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$veterinario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByVeterinario($id) {
        $sql = "SELECT COUNT(*) FROM citas WHERE empleado_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }

    public function countMascotasAtendidas($id) {
        $sql = "SELECT COUNT(DISTINCT mascota_id) FROM citas WHERE empleado_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }

    public function sumIngresosPorVeterinario($id) {
        $sql = "SELECT SUM(s.precio) 
                FROM citas c
                JOIN servicios s ON c.servicio_id = s.id
                WHERE c.empleado_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }

public function getPorMascota(int $mascotaId): array {
    try {
        $sql = "
            SELECT 
                c.id, c.fecha, c.estado, c.notas,
                c.mascota_id, c.cliente_id, c.empleado_id, c.servicio_id,
                m.nombre AS nombre_mascota,
                u_cliente.nombre AS cliente_nombre, u_cliente.apellido AS cliente_apellido,
                s.nombre AS servicio,
                u_vet.nombre AS vet_nombre, u_vet.apellido AS vet_apellido
            FROM citas c
            LEFT JOIN mascotas m ON c.mascota_id = m.id
            LEFT JOIN usuarios u_cliente ON c.cliente_id = u_cliente.id
            LEFT JOIN servicios s ON c.servicio_id = s.id
            LEFT JOIN usuarios u_vet ON c.empleado_id = u_vet.id
            WHERE c.mascota_id = ?
            ORDER BY c.fecha DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mascotaId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Armar campo "veterinario" para la vista
        foreach ($rows as &$r) {
            $r['veterinario'] = trim(($r['vet_nombre'] ?? '') . ' ' . ($r['vet_apellido'] ?? ''));
        }

        return $rows ?: [];
    } catch (PDOException $e) {
        error_log("Cita::getPorMascota error: " . $e->getMessage());
        return [];
    }
}

public function listarAgenda(array $filtros = []): array {
    $sql = "
        SELECT 
            c.id, c.fecha, c.hora, c.estado, c.notas,
            cli.nombre AS cliente_nombre, cli.apellido AS cliente_apellido,
            m.nombre AS mascota_nombre,
            s.nombre AS servicio_nombre, s.precio,
            emp.nombre AS empleado_nombre, emp.apellido AS empleado_apellido
        FROM citas c
        INNER JOIN usuarios cli ON c.cliente_id = cli.id
        INNER JOIN mascotas m ON c.mascota_id = m.id
        INNER JOIN servicios s ON c.servicio_id = s.id
        INNER JOIN usuarios emp ON c.empleado_id = emp.id
        WHERE 1=1
    ";

    $params = [];

    // Filtro por rango de fechas (mes actual por defecto)
    $desde = $filtros['desde'] ?? date('Y-m-01');
    $hasta = $filtros['hasta'] ?? date('Y-m-t');
    $sql .= " AND DATE(c.fecha) BETWEEN :desde AND :hasta";
    $params[':desde'] = $desde;
    $params[':hasta'] = $hasta;

    // Filtro por empleado
    if (!empty($filtros['empleado_id'])) {
        $sql .= " AND c.empleado_id = :empleado";
        $params[':empleado'] = (int)$filtros['empleado_id'];
    }

    // Filtro por servicio
    if (!empty($filtros['servicio_id'])) {
        $sql .= " AND c.servicio_id = :servicio";
        $params[':servicio'] = (int)$filtros['servicio_id'];
    }

    // Filtro por estado
    if (!empty($filtros['estado'])) {
        $sql .= " AND c.estado = :estado";
        $params[':estado'] = $filtros['estado'];
    }

    $sql .= " ORDER BY c.fecha ASC, c.hora ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function disponibleParaActualizar($empleado_id, $fecha, $idCita = null) {
    $sql = "SELECT COUNT(*) FROM citas 
            WHERE empleado_id = :emp 
              AND fecha = :fecha";

    $params = [
        ':emp' => $empleado_id,
        ':fecha' => $fecha
    ];

    if ($idCita) {
        $sql .= " AND id != :id";
        $params[':id'] = $idCita;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() == 0; // true si disponible
}
}
