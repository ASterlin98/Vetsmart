<?php
// app/controllers/PasswordController.php
require_once "../app/core/Controller.php";

class PasswordController extends Controller
{
    public function forgot()
    {
        // carga la vista auth/forgot_password.php
        $this->view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $_SESSION['error'] = "Debes ingresar tu correo";
            header("Location: " . BASE . "/password/forgot");
            exit;
        }

        $usuario = (new Usuario())->findByEmail($email);

        if (!$usuario) {
            $_SESSION['error'] = "No existe un usuario con ese correo";
            header("Location: " . BASE . "/password/forgot");
            exit;
        }

        // Generar token
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        (new Usuario())->saveResetToken($usuario['id'], $token, $expira);

        // Link de prueba (luego lo mandamos con PHPMailer)
        $resetUrl = rtrim(getenv('APP_BASE_URL') ?: '', '/') . "/password/reset?token=" . $token;

        echo "Link de recuperación: <a href='$resetUrl'>$resetUrl</a>";
    }

    public function reset()
    {
        $token = $_GET['token'] ?? null;

        if (!$token) {
            echo "Token no válido";
            exit;
        }

        // carga la vista auth/reset_password.php
        $this->view('auth/reset_password', compact('token'));
    }

    public function updatePassword()
    {
        $token = $_POST['token'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$token || !$password) {
            echo "Datos inválidos";
            exit;
        }

        $usuario = (new Usuario())->findByToken($token);

        if (!$usuario) {
            echo "Token inválido o expirado";
            exit;
        }

        // actualizar clave (hash)
        $hash = password_hash($password, PASSWORD_BCRYPT);
        (new Usuario())->updatePassword($usuario['id'], $hash);

        echo "Contraseña actualizada correctamente. <a href='" . BASE . "/login'>Ir al login</a>";
    }
}
