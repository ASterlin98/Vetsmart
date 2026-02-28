<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../app/core/Controller.php";
require_once __DIR__ . "/../models/Usuario.php";
require_once __DIR__ . "/../../vendor/autoload.php";
class AuthController extends Controller
{
    public function showLogin()
    {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        $basePath = $this->basePath();
        $this->view('auth/login', compact('error', 'basePath'));
    }

    public function login()
    {
        $base = $this->basePath();

        if (!\CSRF::validate($_POST['_csrf'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            header('Location: ' . $base . '/login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new Usuario();
        $user = $userModel->findByEmail($email);

        // Si no existe usuario
        if (!$user) {
            $_SESSION['error'] = 'Credenciales inválidas';
            header('Location: ' . $base . '/login');
            exit;
        }

        // Si está bloqueado
        if ($userModel->estaBloqueado($user['id'])) {
            $_SESSION['error'] = 'Tu usuario ha sido bloqueado. Comunícate con el administrador.';
            header('Location: ' . $base . '/login');
            exit;
        }

        // Solo bloquear si no es admin/superadmin
        if (!in_array($user['role_id'], [1,2])) {
            // Buscar intentos
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT intentos FROM login_intentos WHERE usuario_id = ?");
            $stmt->execute([$user['id']]);
            $row = $stmt->fetch();
            $intentos = $row ? (int)$row['intentos'] : 0;

            if ($user && password_verify($password, $user['password'])) {
                // Login exitoso: resetear intentos
                $db->prepare("DELETE FROM login_intentos WHERE usuario_id = ?")->execute([$user['id']]);
                $_SESSION['user'] = $user;
                header('Location: ' . $base . '/dashboard');
                exit;
            } else {
                $intentos++;
                if ($row) {
                    $db->prepare("UPDATE login_intentos SET intentos = ?, ultima_fecha = NOW() WHERE usuario_id = ?")
                        ->execute([$intentos, $user['id']]);
                } else {
                    $db->prepare("INSERT INTO login_intentos (usuario_id, intentos, ultima_fecha) VALUES (?, ?, NOW())")
                        ->execute([$user['id'], $intentos]);
                }
                // Si supera 3 intentos, bloquear
                if ($intentos >= 3) {
                    $userModel->bloquear($user['id']);
                    $_SESSION['error'] = 'Tu usuario ha sido bloqueado por exceder los intentos. Comunícate con el administrador.';
                    header('Location: ' . $base . '/login');
                    exit;
                } else {
                    $restantes = 3 - $intentos;
                    $_SESSION['error'] = 'Credenciales inválidas. Te quedan ' . $restantes . ' intento(s) antes de ser bloqueado.';
                    header('Location: ' . $base . '/login');
                    exit;
                }
            }
        } else {
            // Admin/superadmin: solo validar credenciales
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header('Location: ' . $base . '/dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Credenciales inválidas';
                header('Location: ' . $base . '/login');
                exit;
            }
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: ' . $this->basePath() . '/login');
        exit;
    }

    private function redirectByRole(?string $roleName) {
        switch ($roleName) {
            case 'cliente':
                header('Location: /cliente/dashboard'); break;
            case 'veterinario': 
                header('Location: /veterinario/dashboard'); break;
            case 'recepcion':
                header('Location: /recepcionista/dashboard'); break;
            case 'admin':
                header('Location: /admin/dashboard'); break;
            case 'super_admin':
                header('Location: /super_admin/dashboard'); break;
            case 'peluquero':
                header('Location: /peluquero/dashboard'); break;
            default:
                header('Location: /login'); break;
        }
        exit;
    }

    public function forgot()
    {
        $this->view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $_SESSION['error'] = "Debes ingresar tu correo";
            header("Location: " . $this->basePath() . "/auth/forgot");
            exit;
        }

        $usuario = (new Usuario())->findByEmail($email);

        if (!$usuario) {
            $_SESSION['error'] = "No existe un usuario con ese correo";
            header("Location: " . $this->basePath() . "/auth/forgot");
            exit;
        }

        // Generar token y guardarlo en BD
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        (new Usuario())->saveResetToken($usuario['id'], $token, $expira);

        $resetUrl = rtrim(getenv('APP_BASE_URL') ?: '', '/') . '/auth/reset?token=' . $token;

        // Enviar correo con PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Config SMTP (ahora desde .env)
            $mail->isSMTP();
            $mail->Host = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('MAIL_USERNAME') ?: '';
            $mail->Password = getenv('MAIL_PASSWORD') ?: '';
            $mail->SMTPSecure = (getenv('MAIL_SECURE') === 'tls')
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = (int) (getenv('MAIL_PORT') ?: 465);
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Remitente y destinatario
            $from = getenv('MAIL_FROM') ?: $mail->Username;
            $fromName = getenv('MAIL_FROM_NAME') ?: 'VetSmart';
            $mail->setFrom($from, $fromName);
            $mail->addAddress($usuario['email'], $usuario['nombre']);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Restablece tu password - VetSmart';
            $safeName = htmlspecialchars($usuario['nombre'] ?? 'usuario', ENT_QUOTES, 'UTF-8');
            $safeUrl  = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');
            $mail->Body    = "<p>Hola <strong>{$safeName}</strong>,</p>
                              <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>VetSmart</strong>.</p>
                              <p>Haz clic en el siguiente enlace o cópialo en tu navegador:</p>
                              <p><a href=\"{$safeUrl}\">{$safeUrl}</a></p>
                              <p>Este enlace expirará en 1 hora.</p>";

            $mail->send();
            $_SESSION['success'] = "Se ha enviado un enlace de recuperación a tu correo.";
        } catch (Exception $e) {
            $_SESSION['error'] = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }

        header("Location: " . $this->basePath() . "/auth/forgot");
        exit;
    }

    public function reset()
    {
        $token = $_GET['token'] ?? null;

        if (!$token) {
            echo "Token no válido";
            exit;
        }

        $usuario = (new Usuario())->findByToken($token);

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

        if (!$token || !$password) {
            echo "Datos inválidos";
            exit;
        }

        $usuario = (new Usuario())->findByToken($token);

        if (!$usuario) {
            echo "Token inválido o expirado";
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        (new Usuario())->updatePassword($usuario['id'], $hash);

        echo "Contraseña actualizada correctamente. <a href='" . $this->basePath() . "/auth/login'>Ir al login</a>";
    }

    public function showRegister()
    {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        // Cargar site key para reCAPTCHA (si está configurado)
        $rec = require __DIR__ . '/../config/recaptcha.php';
        $siteKey = $rec['site_key'] ?? '';
        $this->view('auth/register', compact('error', 'siteKey'));
    }


    public function register()
    {
        // Validar CSRF (nombre del campo usado en views: _csrf)
        if (!\CSRF::validate($_POST['_csrf'] ?? '')) {
            die("Token inválido");
        }

        // Validar reCAPTCHA
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? null;
        $recConfig = require __DIR__ . '/../config/recaptcha.php';
        $secret = $recConfig['secret_key'] ?? '';
        if (empty($secret) || empty($recaptchaResponse)) {
            $this->view('auth/register', ['error' => 'Por favor completa el reCAPTCHA.', 'siteKey' => $recConfig['site_key'] ?? '']);
            return;
        }
        if (!$this->verifyRecaptcha($recaptchaResponse, $secret)) {
            $this->view('auth/register', ['error' => 'reCAPTCHA inválido. Intenta de nuevo.', 'siteKey' => $recConfig['site_key'] ?? '']);
            return;
        }

        $usuarioModel = new Usuario();

        $docusu = trim($_POST['docusu']);
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $telefono = trim($_POST['telefono']);

        // Validar duplicados
        if ($usuarioModel->existsByEmailOrDoc($email, $docusu)) {
            $this->view("auth/register", [
                "error" => "Ya existe un usuario con ese correo o documento.",
                'siteKey' => $recConfig['site_key'] ?? ''
            ]);
            return;
        }

        // Crear usuario con rol cliente (role_id = 6)
        $usuarioId = $usuarioModel->createClient(
            $nombre,
            $apellido,
            $docusu,
            $email,
            $telefono,
            password_hash($password, PASSWORD_BCRYPT)
        );

        if (!$usuarioId) {
            $this->view("auth/register", [
                "error" => "Error al crear la cuenta. Por favor intenta de nuevo.",
                'siteKey' => $recConfig['site_key'] ?? ''
            ]);
            return;
        }

        // Autologin
        $_SESSION['user'] = [
            'id' => $usuarioId,
            'role_id' => 6,
            'role_name' => 'cliente',
            'email' => $email,
            'nombre' => $nombre,
            'apellido' => $apellido
        ];

        header("Location: " . $this->basePath() . "/cliente/dashboard");
        exit;
    }

    public function storeClient()
    {
        $nombre   = $_POST['nombre']   ?? '';
        $apellido = $_POST['apellido'] ?? '';
        $docusu   = $_POST['docusu']   ?? '';
        $email    = $_POST['email']    ?? '';
        $telefono = $_POST['telefono'] ?? null;
        $password = $_POST['password'] ?? '';

        // Validaciones básicas
        if (empty($nombre) || empty($apellido) || empty($docusu) || empty($email) || empty($password)) {
            $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
            header("Location: " . $this->basePath() . "/auth/register");
            exit;
        }

        // Validar reCAPTCHA antes de crear
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? null;
        $recConfig = require __DIR__ . '/../config/recaptcha.php';
        $secret = $recConfig['secret_key'] ?? '';
        if (empty($secret) || empty($recaptchaResponse) || !$this->verifyRecaptcha($recaptchaResponse, $secret)) {
            $_SESSION['error'] = "Por favor completa el reCAPTCHA correctamente.";
            header("Location: " . $this->basePath() . "/auth/register");
            exit;
        }

        $usuarioModel = new Usuario();

        // Evitar duplicados
        if ($usuarioModel->findByEmail($email)) {
            $_SESSION['error'] = "El correo ya está registrado.";
            header("Location: " . $this->basePath() . "/auth/register");
            exit;
        }
        if ($usuarioModel->findByDocusu($docusu)) {
            $_SESSION['error'] = "El documento ya está registrado.";
            header("Location: " . $this->basePath() . "/auth/register");
            exit;
        }

        // Guardar usuario
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userId = $usuarioModel->createClient($nombre, $apellido, $docusu, $email, $telefono, $hash);

        if ($userId) {
            // Login automático
            $_SESSION['user'] = $usuarioModel->findByEmail($email);
            header("Location: " . $this->basePath() . "/dashboard");
            exit;
        } else {
            $_SESSION['error'] = "Error al registrar el usuario.";
            header("Location: " . $this->basePath() . "/auth/register");
            exit;
        }
    }

    /**
     * Obtiene el base path configurado (.env APP_BASE_URL) o fallback /vetsmart.
     */
    private function basePath(): string
    {
        $url = getenv('APP_BASE_URL') ?: '';
        // Si es URL absoluta, extraer sólo la ruta base
        if (strpos($url, 'http') === 0) {
            $parts = parse_url($url);
            $path = $parts['path'] ?? '';
            return rtrim($path, '/') ?: '/';
        }
        return rtrim($url, '/') ?: '/';
    }

    /**
     * Verifica la respuesta de reCAPTCHA v3 con el servidor de Google.
     * Devuelve true si success=true y el score está por encima del threshold.
     * Para v3, también valida el score contra el threshold configurado.
     */
    private function verifyRecaptcha(string $token, string $secret): bool
    {
        if (empty($token) || empty($secret)) return false;

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = http_build_query([
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        // Preferir cURL si está disponible
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para dev/local
            $res = curl_exec($ch);
            if ($res === false) {
                curl_close($ch);
                return false;
            }
            curl_close($ch);
        } else {
            // Fallback a file_get_contents
            $opts = ['http' => ['method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n", 'content' => $data, 'timeout' => 5]];
            $context = stream_context_create($opts);
            $res = @file_get_contents($url, false, $context);
            if ($res === false) return false;
        }

        $obj = json_decode($res, true);
        
        // Validar success
        if (!isset($obj['success']) || $obj['success'] !== true) {
            return false;
        }

        // Para reCAPTCHA v3, validar el score contra el threshold
        $recConfig = require __DIR__ . '/../config/recaptcha.php';
        $threshold = $recConfig['score_threshold'] ?? 0.5;
        $score = $obj['score'] ?? 0;

        // Si el score está por debajo del threshold, rechazar
        if ($score < $threshold) {
            // Opcionalmente registrar esto: error_log("reCAPTCHA v3 score bajo: {$score}, threshold: {$threshold}");
            return false;
        }

        return true;
    }
}
