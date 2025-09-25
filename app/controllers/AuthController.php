<?php
// app/controllers/AuthController.php
// app/controllers/AuthController.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    // Mostrar formulario de "Olvidé mi contraseña"
    public function forgot() {
        require __DIR__ . '/../views/auth/forgot_password.php';
    }

    // Enviar enlace de restablecimiento
    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vetsmart/auth/forgot');
            exit;
        }

        $email = $_POST['email'] ?? '';
        if (!$email) {
            $_SESSION['error'] = "Ingresa un correo válido.";
            header('Location: /vetsmart/auth/forgot');
            exit;
        }

        $user = $this->usuarioModel->findByEmail($email);
        if (!$user) {
            $_SESSION['error'] = "No existe un usuario con ese correo.";
            header('Location: /vetsmart/auth/forgot');
            exit;
        }

        // Generar token y expiración
        $token = bin2hex(random_bytes(16));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->usuarioModel->saveResetToken($user['id'], $token, $expira);

        // Preparar enlace
        $resetLink = "http://localhost/vetsmart/auth/reset?token=$token";

        // Enviar correo
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'andres.rojast98@gmail.com';
            $mail->Password = 'mcuqiwkzqagwkusc';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ];

            $mail->setFrom('andres.rojast98@gmail.com', 'VetSmart');
            $mail->addAddress($email, $user['nombre']);

            $mail->isHTML(true);
            $mail->Subject = 'Restablecer contraseña VetSmart';
            $mail->Body = "
                <p>Hola {$user['nombre']},</p>
                <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>Este enlace expirará en 1 hora.</p>
            ";

            $mail->send();
            $_SESSION['success'] = "Se ha enviado un enlace a tu correo.";
        } catch (Exception $e) {
            $_SESSION['error'] = "No se pudo enviar el correo. Error: " . $mail->ErrorInfo;
        }

        header('Location: /vetsmart/auth/forgot');
    }

        public function login() {
        require __DIR__ . '/../views/auth/login.php';
    }

    // Mostrar formulario de restablecimiento
    public function reset() {
        $token = $_GET['token'] ?? '';
        if (!$token) {
            $_SESSION['error'] = "Token inválido.";
            header('Location: /vetsmart/auth/forgot');
            exit;
        }

        require __DIR__ . '/../views/auth/reset_password.php';
    }

    // Actualizar contraseña
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vetsmart/auth/login');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$token || !$password) {
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            header("Location: /vetsmart/auth/reset?token=$token");
            exit;
        }

        $user = $this->usuarioModel->findByToken($token);
        if (!$user) {
            $_SESSION['error'] = "Token inválido o expirado.";
            header('Location: /vetsmart/auth/forgot');
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->usuarioModel->updatePassword($user['id'], $hash);

        $_SESSION['success'] = "Contraseña actualizada. Ya puedes iniciar sesión.";
        header('Location: /vetsmart/auth/login');
    }
}

