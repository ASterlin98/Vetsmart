<?php
// app/helpers/auth_helper.php

if (!function_exists('has_permission')) {
    /**
     * Checks if the logged-in user has a specific permission.
     *
     * @param string $permission The permission key to check (e.g., 'citas.view').
     * @return bool True if the user has the permission, false otherwise.
     */
    function has_permission(string $permission): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Super Admin (role_id = 1) always has all permissions
        if (isset($_SESSION['user']['role_id']) && $_SESSION['user']['role_id'] == 1) {
            return true;
        }

        // Check if the permission exists in the user's session
        $userPermissions = $_SESSION['user']['permissions'] ?? [];
        return in_array($permission, $userPermissions, true);
    }
}