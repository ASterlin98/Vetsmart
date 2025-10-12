<?php
// app/api/permissions_api.php
header('Content-Type: application/json');

// Bootstrap de la aplicación (simplificado)
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/core/Database.php';
require_once APP_ROOT . '/models/RolePermission.php';

// --- Manejo de la solicitud ---
try {
    $pdo = Database::getInstance();
    $rolePermissionModel = new RolePermission($pdo);

    // Determinar la acción basada en el método de solicitud
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // --- OBTENER PERMISOS PARA UN ROL ---
        $role_id = filter_input(INPUT_GET, 'role_id', FILTER_VALIDATE_INT);

        if (!$role_id || $role_id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de rol no válido.']);
            exit;
        }

        $all_permissions = $rolePermissionModel->getAllPermissionsGroupedByModule();
        $assigned_permissions = $rolePermissionModel->getPermissionIdsByRoleId($role_id);

        echo json_encode([
            'all_permissions' => $all_permissions,
            'assigned_permissions' => $assigned_permissions
        ]);

    } elseif ($method === 'POST') {
        // --- ACTUALIZAR PERMISOS PARA UN ROL ---
        $input = json_decode(file_get_contents('php://input'), true);

        $role_id = $input['role_id'] ?? null;
        $permission_ids = $input['permission_ids'] ?? [];

        if (empty($role_id) || !is_numeric($role_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de rol no válido en el cuerpo de la solicitud.']);
            exit;
        }

        $permission_ids = array_map('intval', $permission_ids);

        $success = $rolePermissionModel->updatePermissionsForRole($role_id, $permission_ids);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Permisos actualizados correctamente.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No se pudieron actualizar los permisos.']);
        }

    } else {
        http_response_code(405); // Método no permitido
        echo json_encode(['error' => 'Método no permitido.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    error_log("API Exception in permissions_api.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}