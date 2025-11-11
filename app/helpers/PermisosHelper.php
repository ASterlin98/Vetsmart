<?php
// app/helpers/PermisosHelper.php

class PermisosHelper {
    public static function tiene($modulo, $accion) {
        if (!isset($_SESSION['permisos']) || !is_array($_SESSION['permisos'])) {
            return false;
        }

        // Los permisos están en formato: ['citas.ver', 'clientes.crear', ...]
        $clave = "{$modulo}.{$accion}";
        return in_array($clave, $_SESSION['permisos']);
    }

    public static function cargarPermisosDelUsuario($db, $roleId) {
        $sql = "SELECT p.modulo, p.accion
                FROM permisos p
                INNER JOIN rol_permisos rp ON p.id = rp.permiso_id
                WHERE rp.role_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$roleId]);
        $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $_SESSION['permisos'] = array_map(function ($permiso) {
            return "{$permiso['modulo']}.{$permiso['accion']}";
        }, $permisos);
    }
}