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
        $sql = "SELECT u.*, cd.telefono, cd.direccion, cd.ciudad, cd.fecha_registro
                FROM usuarios u
                LEFT JOIN cliente_detalles cd ON u.id = cd.idusu
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function checkDuplicados(string $email, string $docusu, ?int $excludeId = null): array
    {
        $errors = [];
        // Check email
        $sql = "SELECT id FROM usuarios WHERE email = :email";
        $params = [':email' => $email];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        if ($stmt->fetch()) {
            $errors[] = "El correo electrónico ya está registrado.";
        }

        // Check docusu
        $sql = "SELECT id FROM usuarios WHERE docusu = :docusu";
        $params = [':docusu' => $docusu];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        if ($stmt->fetch()) {
            $errors[] = "El documento ya está registrado.";
        }

        return $errors;
    }

    /**
     * Obtener cliente con detalles (telefono, direccion, ciudad) y foto desde tabla perfil si existe.
     */
    public function getByIdWithDetails(int $id): array
    {
        try {
            $sql = "SELECT u.id, u.nombre, u.apellido, u.docusu, u.email, u.telefono, u.direccion,
                           cd.telefono AS telefono_cliente, cd.direccion AS direccion_cliente, cd.ciudad,
                           p.foto AS foto
                    FROM usuarios u
                    LEFT JOIN cliente_detalles cd ON cd.idusu = u.id
                    LEFT JOIN perfil p ON p.usuario_id = u.id
                    WHERE u.id = :id";
            $st = $this->db->prepare($sql);
            $st->execute([':id'=>$id]);
            $r = $st->fetch(PDO::FETCH_ASSOC);
            return $r ?: [];
        } catch (Throwable $e) {
            error_log('Cliente::getByIdWithDetails error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualiza datos básicos del perfil (usuarios + cliente_detalles upsert).
     */
    public function updatePerfilBasico(int $id, array $data): void
    {
        // Actualizar usuarios
        $u = $this->db->prepare("UPDATE usuarios SET nombre=:n, apellido=:a, email=:e, telefono=:t, direccion=:d WHERE id=:id");
        $u->execute([':n'=>$data['nombre'], ':a'=>$data['apellido'], ':e'=>$data['email'], ':t'=>$data['telefono'], ':d'=>$data['direccion'], ':id'=>$id]);
        // Upsert en cliente_detalles
        $ex = $this->db->prepare("SELECT id FROM cliente_detalles WHERE idusu = :id");
        $ex->execute([':id'=>$id]);
        if ($ex->fetch()) {
            $cd = $this->db->prepare("UPDATE cliente_detalles SET telefono=:t, direccion=:d, ciudad=:c WHERE idusu=:id");
            $cd->execute([':t'=>$data['telefono'], ':d'=>$data['direccion'], ':c'=>null, ':id'=>$id]);
        } else {
            $cd = $this->db->prepare("INSERT INTO cliente_detalles (idusu, telefono, direccion, ciudad, fecha_registro) VALUES (:id,:t,:d,:c, NOW())");
            $cd->execute([':id'=>$id, ':t'=>$data['telefono'], ':d'=>$data['direccion'], ':c'=>null]);
        }
    }

    public function updatePassword(int $id, string $hash): void
    {
        $st = $this->db->prepare("UPDATE usuarios SET password = :p WHERE id = :id");
        $st->execute([':p'=>$hash, ':id'=>$id]);
    }

    public function updateFotoPerfil(int $id, string $filename): void
    {
        // upsert en perfil
        try {
            $ex = $this->db->prepare("SELECT usuario_id FROM perfil WHERE usuario_id = :id");
            $ex->execute([':id'=>$id]);
            if ($ex->fetch()) {
                $up = $this->db->prepare("UPDATE perfil SET foto = :f WHERE usuario_id = :id");
                $up->execute([':f'=>$filename, ':id'=>$id]);
            } else {
                $in = $this->db->prepare("INSERT INTO perfil (usuario_id, foto) VALUES (:id, :f)");
                $in->execute([':id'=>$id, ':f'=>$filename]);
            }
        } catch (Throwable $e) {
            // Silencioso para no romper flujo si no existe la tabla
        }
    }

    public function crear($data) {
        // La validación de duplicados ahora se hace en el controlador
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
    }

    public function actualizar($id, $data) {
        // 1. Actualizar la tabla de usuarios (sin el teléfono)
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, docusu=?, email=? WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['apellido'],
            $data['docusu'],
            $data['email'],
            $id
        ]);

        // 2. Upsert en cliente_detalles
        $checkSql = "SELECT idusu FROM cliente_detalles WHERE idusu = ?";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([$id]);

        if ($checkStmt->fetch()) {
            // Update
            $sql2 = "UPDATE cliente_detalles SET telefono=?, direccion=?, ciudad=? WHERE idusu=?";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([
                $data['telefono'] ?? null,
                $data['direccion'] ?? null,
                $data['ciudad'] ?? null,
                $id
            ]);
        } else {
            // Insert
            $sql2 = "INSERT INTO cliente_detalles (idusu, telefono, direccion, ciudad, fecha_registro) VALUES (?, ?, ?, ?, NOW())";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([
                $id,
                $data['telefono'] ?? null,
                $data['direccion'] ?? null,
                $data['ciudad'] ?? null
            ]);
        }
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
