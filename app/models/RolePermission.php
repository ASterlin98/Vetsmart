<?php
// app/models/RolePermission.php

class RolePermission {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Obtiene todos los roles del sistema.
     * @return array
     */
    public function getAllRoles() {
        $stmt = $this->db->query("SELECT id, nombre, descripcion FROM roles ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los permisos, agrupados por módulo.
     * @return array
     */
    public function getAllPermissionsGroupedByModule() {
        $stmt = $this->db->query("SELECT id, modulo, nombre, descripcion FROM permisos ORDER BY modulo, orden");
        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($permissions as $p) {
            $grouped[$p['modulo']][] = $p;
        }
        return $grouped;
    }

    /**
     * Obtiene los IDs de los permisos asignados a un rol específico.
     * @param int $role_id
     * @return array
     */
    public function getPermissionIdsByRoleId($role_id) {
        $stmt = $this->db->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
        $stmt->execute([$role_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Actualiza los permisos para un rol.
     * Elimina los permisos existentes y luego inserta los nuevos.
     * @param int $role_id
     * @param array $permission_ids
     * @return bool
     */
    public function updatePermissionsForRole($role_id, $permission_ids) {
        // Asegurarse de que el super_admin no se pueda modificar a sí mismo
        if ($role_id == 1) {
            return false;
        }

        $this->db->beginTransaction();

        try {
            // 1. Eliminar todos los permisos actuales para este rol
            $stmt = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $stmt->execute([$role_id]);

            // 2. Insertar los nuevos permisos
            if (!empty($permission_ids)) {
                $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES ";
                $placeholders = [];
                $values = [];
                foreach ($permission_ids as $perm_id) {
                    $placeholders[] = "(?, ?)";
                    $values[] = $role_id;
                    $values[] = (int)$perm_id;
                }
                $sql .= implode(", ", $placeholders);

                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            // Opcional: registrar el error
            error_log("Error al actualizar permisos para el rol $role_id: " . $e->getMessage());
            return false;
        }
    }
}