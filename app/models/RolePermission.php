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
        if ($role_id == 1) {
            error_log("SECURITY: Attempted to modify Super Admin permissions. Denied.");
            return false;
        }

        error_log("DB_TXN: Starting transaction for role_id: " . $role_id);
        $this->db->beginTransaction();

        try {
            // 1. Eliminar todos los permisos actuales para este rol
            $stmt_delete = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $stmt_delete->execute([$role_id]);
            error_log("DB_TXN: Deleted existing permissions for role_id: " . $role_id . ". Rows affected: " . $stmt_delete->rowCount());

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

                error_log("DB_TXN: Preparing to insert new permissions. SQL: " . $sql . " with values: " . json_encode($values));

                $stmt_insert = $this->db->prepare($sql);
                $stmt_insert->execute($values);
                error_log("DB_TXN: Inserted new permissions for role_id: " . $role_id . ". Rows affected: " . $stmt_insert->rowCount());
            } else {
                error_log("DB_TXN: No new permissions to insert for role_id: " . $role_id);
            }

            $this->db->commit();
            error_log("DB_TXN: Transaction committed successfully for role_id: " . $role_id);
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("DB_TXN_EXCEPTION: Transaction rolled back for role $role_id: " . $e->getMessage());
            return false;
        }
    }
}