<?php
// app/models/Usuario.php
class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array {
        $sql = "SELECT u.*, r.nombre AS role_name FROM usuarios u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE u.email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByDocusu(string $docusu): ?array {
        $sql = "SELECT u.*, r.nombre AS role_name FROM usuarios u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE u.docusu = :docusu LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':docusu' => $docusu]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Bloquea un usuario (excepto admin/superadmin)
     */
    public function bloquear($id) {
        $sql = "UPDATE usuarios SET is_blocked = 1 WHERE id = ? AND role_id NOT IN (1,2)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Desbloquea un usuario
     */
    public function desbloquear($id) {
        $sql = "UPDATE usuarios SET is_blocked = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica si el usuario está bloqueado
     */
    public function estaBloqueado($id) {
        $sql = "SELECT is_blocked FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? (bool)$row['is_blocked'] : false;
    }

public function saveResetToken($id, $token, $expira) {
    $sql = "UPDATE usuarios SET reset_token = ?, reset_expira = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$token, $expira, $id]);
}

public function findByToken($token) {
    $sql = "SELECT * FROM usuarios WHERE reset_token = ? AND reset_expira > NOW()";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function updatePassword($id, $hash) {
    $sql = "UPDATE usuarios 
            SET password = ?, reset_token = NULL, reset_expira = NULL 
            WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$hash, $id]);
}

    public function createClient($nombre, $apellido, $docusu, $email, $telefono, $password)
    {
        $sql = "INSERT INTO usuarios (nombre, apellido, docusu, email, password, role_id) 
                VALUES (:nombre, :apellido, :docusu, :email, :password, 6)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':docusu' => $docusu,
            ':email' => $email,
            ':password' => $password
        ]);

        if ($ok) {
            $userId = $this->db->lastInsertId();

            // Guardar en cliente_detalles
            $sql2 = "INSERT INTO usuario (id, telefono) VALUES (:id, :telefono)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([':id' => $userId, ':telefono' => $telefono]);

            return $userId;
        }

        return false;
    }

    public function existsByEmailOrDoc(string $email, string $doc): bool
    {
        $sql = "SELECT idusu FROM usuarios WHERE email = ? OR ndusu = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email, $doc]);
        return (bool) $stmt->fetch();
    }

    /**
     * Obtiene todos los usuarios que no son clientes (staff).
     * @return array
     */
    public function getStaff() {
        $sql = "SELECT id, nombre, apellido FROM usuarios WHERE role_id != 6 ORDER BY nombre, apellido";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
