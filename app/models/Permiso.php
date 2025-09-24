<?php

class Permiso {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getAllGroupedByModulo()
    {
        $sql = "SELECT id, modulo, nombre, descripcion, accion FROM permisos WHERE activo = 1 ORDER BY modulo, orden";
        $stmt = $this->db->query($sql);
        $result = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $modulo = $row['modulo'];
            if (!isset($result[$modulo])) {
                $result[$modulo] = [];
            }
            $result[$modulo][] = $row;
        }

        return $result;
    }

    public function getPermisosPorRol($role_id)
    {
        $sql = "SELECT permiso_id FROM rol_permisos WHERE role_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$role_id]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'permiso_id');
    }

    public function asignarPermisosARol($role_id, $permiso_ids, $usuario_id)
    {
        $this->db->beginTransaction();

        $this->db->prepare("DELETE FROM rol_permisos WHERE role_id = ?")->execute([$role_id]);

        $stmt = $this->db->prepare("INSERT INTO rol_permisos (role_id, permiso_id, concedido_por) VALUES (?, ?, ?)");
        foreach ($permiso_ids as $permiso_id) {
            $stmt->execute([$role_id, $permiso_id, $usuario_id]);
        }

        $this->db->commit();
    }
}
