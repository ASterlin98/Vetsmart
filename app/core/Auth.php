<?php
// app/core/Auth.php
class Auth {
    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): void {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function onlyRole(string $roleName): void {
        $user = self::user();
        if (!$user || ($user['role_name'] ?? '') !== $roleName) {
            http_response_code(403);
            echo 'Acceso no autorizado';
            exit;
        }
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
