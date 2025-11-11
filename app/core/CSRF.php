<?php
// app/core/CSRF.php
class CSRF {
    public static function generateToken(): string {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function inputField(): string {
        $token = self::generateToken();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token) . '">';
    }

    public static function validate(string $token): bool {
        return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }
}
