<?php
// app/controllers/AuthController.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/models/Usuario.php';

class AuthController extends Controller
{
    protected string $basePath;

    public function __construct($pdo = null)
    {
        parent::__construct($pdo);
        // base path para construir URLs (define APP_BASE_URL en .env o ajusta)
        $this->basePath = getenv('APP_BASE_URL') ?: 'http://localhost/vetsmart';
    }

    public function showLogin()
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        $basePath = '/vetsmart';
        $this->view('auth/login', compact('error', 'success', 'basePath'));
    }

    public function login()
    {
        if (!\CSRF::validate($_POST['_csrf'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            header('Location: /vetsmart/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $_SESSION['error'] = 'Credenciales inválidas';
            header('Location: /vetsmart/login');
            exit;
        }

        $userModel = new Usuario($this->db);
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Normalizar datos esenciales a la sesión
            $_SESSION['user'] = [
                'id' => $user['id'],
                'role_name' => $user['role_name'] ?? '',
                'nombre' => $user['nombre'] ?? ($user['nomusu'] ?? ''),
                'apellido' => $user['apellido'] ?? ($user['apeusu'] ?? ''),
                'email' => $user['email'] ?? ''
            ];
            header('Location: ' . $this->basePath . '/dashboard');
            exit;
        }

        // mensaje genérico para no filtrar existencia de usuarios
        $_SESSION['error'] = 'Credenciales inválidas';
        header('Location: ' . $this->basePath . '/login');
        exit;
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: ' . $this->basePath . '/login');
        exit;
    }

    public function forgot()
    {
        $this->view('auth/forgot_password');
    }

    /**
     * Envía el enlace de recuperación de contraseña usando PHPMailer.
     * Protecciones:
     * - Validación de email
     * - Rate limit simple por sesión (5 envíos por hora)
     * - Mensaje genérico para evitar enumeración de usuarios
     */
    public function sendResetLink()
    {
        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Debes ingresar un correo válido.";
            header("Location: {$this->basePath}/auth/forgot");
            exit;
        }

        // Rate-limit simple por sesión
        if (!isset($_SESSION['reset_email_sent'])) {
            $_SESSION['reset_email_sent'] = ['count' => 0, 'ts' => time()];
        }
        // resetear contador cada hora
        if (time() - $_SESSION['reset_email_sent']['ts'] > 3600) {
            $_SESSION['reset_email_sent'] = ['count' => 0, 'ts' => time()];
        }
        if ($_SESSION['reset_email_sent']['count'] >= 5) {
            $_SESSION['error'] = "Has enviado demasiadas solicitudes. Intenta más tarde.";
            header("Location: {$this->basePath}/auth/forgot");
            exit;
        }

        $usuarioModel = new Usuario($this->db);
        $usuario = $usuarioModel->findByEmail($email);

        // Generamos token y guardamos sólo si el usuario existe.
        // **Importante**: para evitar enumeración, retornamos el mismo mensaje aunque no exista el usuario.
        $token = null;
        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));
            try {
                $usuarioModel->saveResetToken($usuario['id'], $token, $expira);
            } catch (Exception $e) {
                error_log("Error guardando token reset: " . $e->getMessage());
                // no mostramos error al usuario para no filtrar detalles internos
            }
        }

        // Incrementar contador rate-limit
        $_SESSION['reset_email_sent']['count']++;

        // Construir reset URL sólo si hay token (si no hay, no revelamos que no existe)
        if ($token) {
            $resetUrl = rtrim($this->basePath, '/') . "/auth/reset?token=" . urlencode($token);
        } else {
            // fake url for UX (no será enviado)
            $resetUrl = rtrim($this->basePath, '/') . "/auth/forgot";
        }

        // Preparar envío de correo: si no existe usuario o falla el envío, devolvemos mensaje genérico
        $mailSent = false;
        if ($token) {
            // Config desde env
            $mailHost = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
            $mailUser = getenv('MAIL_USERNAME') ?: null;
            $mailPass = getenv('MAIL_PASSWORD') ?: null;
            $mailPort = getenv('MAIL_PORT') ? (int)getenv('MAIL_PORT') : 465;
            $mailSecure = getenv('MAIL_SECURE') ?: 'smtps';
            $mailFrom = getenv('MAIL_FROM') ?: $mailUser;
            $mailFromName = getenv('MAIL_FROM_NAME') ?: 'VetSmart';

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $mailHost;
                $mail->SMTPAuth = true;
                $mail->Username = $mailUser;
                $mail->Password = $mailPass;
                // PHPMailer v6+ acepta constantes ENCRYPTION_SMTPS or ENCRYPTION_STARTTLS
                if (strtolower($mailSecure) === 'starttls' || strtolower($mailSecure) === 'tls') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                } else {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                }
                $mail->Port = $mailPort;

                // Evitar problemas SSL en entornos de desarrollo (no recomendado en prod)
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ]
                ];

                $mail->setFrom($mailFrom, $mailFromName);
                $mail->addAddress($usuario['email'], $usuario['nombre'] ?? '');

                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de contraseña - VetSmart';
                $body = "<p>Hola " . htmlspecialchars($usuario['nombre'] ?? '') . ",</p>";
                $body .= "<p>Haz clic en el siguiente enlace para restablecer tu contraseña. El enlace expira en 1 hora.</p>";
                $body .= "<p><a href=\"" . htmlspecialchars($resetUrl) . "\">" . htmlspecialchars($resetUrl) . "</a></p>";
                $body .= "<p>Si no solicitaste este cambio, puedes ignorar este correo.</p>";
                $mail->Body = $body;

                $mail->send();
                $mailSent = true;
            } catch (Exception $e) {
                error_log("PHPMailer error sending reset link: " . $e->getMessage());
                // no mostramos detalle al usuario
                $mailSent = false;
            }
        }

        // Mensaje genérico para evitar enumeración de usuarios
        $_SESSION['success'] = "Si existe una cuenta asociada a ese correo, recibirás un enlace para restablecer la contraseña (revísalo en la bandeja de entrada y en spam).";
        header("Location: {$this->basePath}/auth/forgot");
        exit;
    }

    public function reset()
    {
        $token = $_GET['token'] ?? null;
        if (!$token) {
            echo "Token no válido";
            exit;
        }

        $usuarioModel = new Usuario($this->db);
        $usuario = $usuarioModel->findByToken($token);

        if (!$usuario) {
            echo "Token inválido o expirado";
            exit;
        }

        $this->view('auth/reset_password', compact('token'));
    }

    public function updatePassword()
    {
        $token = $_POST['token'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$token || !$password || strlen($password) < 6) {
            echo "Datos inválidos (contraseña mínima 6 caracteres)";
            exit;
        }

        $usuarioModel = new Usuario($this->db);
        $usuario = $usuarioModel->findByToken($token);

        if (!$usuario) {
            echo "Token inválido o expirado";
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $usuarioModel->updatePassword($usuario['id'], $hash);

        // eliminar token tras uso (importante)
        $usuarioModel->clearResetToken($usuario['id']);

        echo "Contraseña actualizada correctamente. <a href='{$this->basePath}/auth/login'>Ir al login</a>";
    }

    // ... resto de métodos (register, storeClient, etc.) sin cambios significativos ...
}
