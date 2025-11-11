<?php
// app/models/Mascota.php
class Mascota {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Obtener todas las mascotas con info del dueño.
     * NOTA: usa la columna 'dueno_id' tal y como esta en tu esquema.
     */
    public function getTodasConDueño(): array {
        $sql = "SELECT m.*, u.nombre AS nombre_dueno, u.apellido AS apellido_dueno, u.telefono AS telefono_dueno
                FROM mascotas m
                LEFT JOIN usuarios u ON m.dueno_id = u.id
                ORDER BY m.nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getByDueno($dueno_id): array {
        $sql = "SELECT * FROM mascotas WHERE dueno_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dueno_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getById($id): ?array {
        $sql = "SELECT * FROM mascotas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function crear($dueno_id, $data) {
        $sql = "INSERT INTO mascotas (dueno_id, nombre, especie, raza, edad, peso, foto, notas, creado_en)
                VALUES (?, ?, ?, ?, ?, ?, NULL, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $dueno_id,
            $data['nombre'],
            $data['especie'] ?? null,
            $data['raza'] ?? null,
            $data['edad'] ?? null,
            $data['peso'] ?? null,
            $data['notas'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function actualizar($id, $data): bool {
        $sql = "UPDATE mascotas SET nombre = ?, especie = ?, raza = ?, edad = ?, peso = ?, notas = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['especie'] ?? null,
            $data['raza'] ?? null,
            $data['edad'] ?? null,
            $data['peso'] ?? null,
            $data['notas'] ?? null,
            $id
        ]);
    }

    public function eliminar($id): bool {
        $sql = "DELETE FROM mascotas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Conteo total de mascotas (devuelve int)
     */
    public function countAll(): int {
        $val = $this->db->query("SELECT COUNT(*) FROM mascotas")->fetchColumn();
        return (int)$val;
    }

    /**
     * ultimas mascotas creadas (con fallback seguro para LIMIT)
     */
    public function getLatest($limit = 5): array {
        $baseSql = "SELECT m.*, u.nombre AS nombre_dueno, u.apellido AS apellido_dueno
                FROM mascotas m
                JOIN usuarios u ON m.dueno_id = u.id
                ORDER BY m.creado_en DESC
                LIMIT ";
        $safeLimit = (int)$limit;
        $sql = $baseSql . $safeLimit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Mascotas asignadas a veterinario (segun citas futuras).
     * Incluye fallback si el driver no permite bind en LIMIT.
     */
    public function getAssignedToVeterinario($veterinarioId, $limite = 8): array {
        $sql = "
            SELECT m.id, m.nombre,
                   u.nombre AS nombre_dueno, u.apellido AS apellido_dueno,
                   MIN(c.fecha) AS proxima_cita
            FROM mascotas m
            INNER JOIN citas c ON c.mascota_id = m.id
            INNER JOIN usuarios u ON m.dueno_id = u.id
            WHERE c.empleado_id = :vid AND c.fecha >= NOW()
            GROUP BY m.id
            ORDER BY proxima_cita ASC
            LIMIT :limite
        ";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':vid', (int)$veterinarioId, PDO::PARAM_INT);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            // fallback concatenando el li­mite (seguro porque casteamos a int)
            $safeLimit = (int)$limite;
            $sql2 = str_replace('LIMIT :limite', "LIMIT {$safeLimit}", $sql);
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([':vid' => (int)$veterinarioId]);
            return $stmt2->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
    }

    /**
     * Busqueda segura por nombre/especie/raza o nombre del dueño
     * Escapa % y _ antes de construir LIKE.
     */
    public function searchWithOwner(string $q): array {
        // Escapar caracteres LIKE especiales
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
        $like = '%' . $escaped . '%';
        $sql = "SELECT m.*, u.nombre AS nombre_dueno, u.apellido AS apellido_dueno, u.telefono AS telefono_dueno
                FROM mascotas m
                LEFT JOIN usuarios u ON m.dueno_id = u.id
                WHERE m.nombre LIKE ? OR m.especie LIKE ? OR m.raza LIKE ? OR CONCAT(u.nombre,' ',u.apellido) LIKE ?
                ORDER BY m.nombre ASC
                LIMIT 200";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getVacunasPorMascota($id): array {
        $sql = "SELECT * FROM vacunas WHERE mascota_id = ? ORDER BY fecha_aplicacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtener mascota con datos del dueño
     */
public function getByIdConDueno($id): ?array {
    $sql = "SELECT 
                m.*, 
                u.nombre AS nombre_dueno, 
                u.apellido AS apellido_dueno, 
                u.email AS email_dueno,
                cd.telefono AS telefono_dueno
            FROM mascotas m
            LEFT JOIN usuarios u ON m.dueno_id = u.id
            LEFT JOIN cliente_detalles cd ON cd.idusu = u.id
            WHERE m.id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res ?: null;
}


}
