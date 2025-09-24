<?php
// app/models/Cliente.php
class Cliente {
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Devuelve todos los clientes como array.
     * Retorna [] en caso de error o si no hay resultados.
     * Alias id para compatibilidad con la vista.
     */
    public function getAll(): array {
        try {
            $sql = "SELECT u.id AS id, u.nombre, u.apellido, u.docusu, u.email, cd.telefono, cd.direccion, cd.ciudad
                    FROM usuarios u
                    LEFT JOIN cliente_detalles cd ON u.id = cd.idusu
                    WHERE u.role_id = 6
                    ORDER BY u.nombre ASC, u.apellido ASC";
            $stmt = $this->db->query($sql);
            if ($stmt === false) return [];
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows ?: [];
        } catch (PDOException $e) {
            // Loguea el error en vez de imprimirlo
            error_log("Cliente::getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Versión compacta para dropdowns (id + nombre_completo).
     */
    public function getDropdown(): array {
        try {
            $sql = "SELECT u.id AS id, CONCAT(u.nombre, ' ', COALESCE(u.apellido, '')) AS nombre_completo
                    FROM usuarios u
                    WHERE u.role_id = 6
                    ORDER BY u.nombre ASC, u.apellido ASC";
            $stmt = $this->db->query($sql);
            if ($stmt === false) return [];
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows ?: [];
        } catch (PDOException $e) {
            error_log("Cliente::getDropdown error: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        $sql = "SELECT u.*, cd.telefono AS telefono_cliente, cd.direccion, cd.ciudad, cd.fecha_registro
                FROM usuarios u
                LEFT JOIN cliente_detalles cd ON u.id = cd.idusu
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function crear($data) {
        try {
            $sql = "INSERT INTO usuarios (nombre, apellido, docusu, email, telefono, password, role_id, estado, creado_en)
                    VALUES (?, ?, ?, ?, ?, ?, 6, 1, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['nombre'],
                $data['apellido'],
                $data['docusu'],
                $data['email'],
                $data['telefono'],
                password_hash($data['password'], PASSWORD_BCRYPT)
            ]);

            $idUsuario = $this->db->lastInsertId();

            $sql2 = "INSERT INTO cliente_detalles (idusu, telefono, direccion, ciudad, fecha_registro)
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([
                $idUsuario,
                $data['telefono'] ?? null,
                $data['direccion'] ?? null,
                $data['ciudad'] ?? null
            ]);
            return $idUsuario;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new Exception("El correo o documento ya está registrado.");
            } else {
                throw $e;
            }
        }
    }

    public function actualizar($id, $data) {
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, docusu=?, email=?, telefono=? WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['apellido'],
            $data['docusu'],
            $data['email'],
            $data['telefono'],
            $id
        ]);

        $sql2 = "UPDATE cliente_detalles SET telefono=?, direccion=?, ciudad=? WHERE idusu=?";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $id
        ]);
    }

    public function eliminar($id) {
        $this->db->prepare("DELETE FROM cliente_detalles WHERE idusu = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
    }

    public function countAll() {
        $val = $this->db->query("SELECT COUNT(*) FROM usuarios WHERE role_id = 6")->fetchColumn();
        return (int)$val;
    }

    public function searchByNombre($nombre) {
        $sql = "SELECT u.*, cd.telefono, cd.direccion, cd.ciudad
                FROM usuarios u
                LEFT JOIN cliente_detalles cd ON u.id = cd.idusu
                WHERE u.role_id = 6 AND (u.nombre LIKE ? OR u.apellido LIKE ?)";
        $stmt = $this->db->prepare($sql);
        $like = '%' . $nombre . '%';
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
