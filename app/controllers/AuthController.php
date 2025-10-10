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
        $basePath = '/vetsmart'; // pásalo siempre a la vista
        $this->view('auth/login', compact('error', 'basePath'));
    }

    public function login()
    {
        if (!\CSRF::validate($_POST['_csrf'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            header('Location: /vetsmart/login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new Usuario();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Cargar permisos del rol
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT p.nombre
                FROM permisos p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = :role_id AND p.activo = 1
            ");
            $stmt->execute(['role_id' => $user['role_id']]);
            $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

            $user['permissions'] = $permissions;
            $_SESSION['user'] = $user;

            header('Location: /vetsmart/dashboard');
            exit;
        }

        $_SESSION['error'] = 'Credenciales inválidas';
        header('Location: /vetsmart/login');
        exit;
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /vetsmart/login');
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
            header("Location: /vetsmart/auth/forgot");
            exit;
        }

        $usuario = (new Usuario())->findByEmail($email);

        if (!$usuario) {
            $_SESSION['error'] = "No existe un usuario con ese correo";
            header("Location: /vetsmart/auth/forgot");
            exit;
        }

        // Generar token y guardarlo en BD
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        (new Usuario())->saveResetToken($usuario['id'], $token, $expira);

        $resetUrl = "http://localhost/vetsmart/auth/reset?token=" . $token;

        // Enviar correo con PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Config SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'andres.rojast98@gmail.com'; // tu correo
            $mail->Password = 'pcmasukjpxvmsyvg'; // clave de aplicación (no la clave normal)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Remitente y destinatario
            $mail->setFrom('andres.rojast98@gmail.com', 'VetSmart');
            $mail->addAddress($usuario['email'], $usuario['nombre']);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de contraseña - VetSmart';
            $mail->Body    = "Hola <b>{$usuario['nombre']}</b>,<br><br>
                              Haz clic en el siguiente enlace para restablecer tu contraseña:<br>
                              <a href='{$resetUrl}'>{$resetUrl}</a><br><br>
                              Este enlace expirará en 1 hora.";

            $mail->send();
            $_SESSION['success'] = "Se ha enviado un enlace de recuperación a tu correo.";
        } catch (Exception $e) {
            $_SESSION['error'] = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }

        header("Location: /vetsmart/auth/forgot");
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

        echo "Contraseña actualizada correctamente. <a href='/vetsmart/auth/login'>Ir al login</a>";
    }

    public function showRegister()
    {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        $this->view('auth/register', compact('error'));
    }


    public function register()
    {
        if (!\CSRF::validate($_POST['csrf_token'] ?? '')) {
            die("Token inválido");
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
                "error" => "Ya existe un usuario con ese correo o documento."
            ]);
            return;
        }

        // Crear usuario con rol cliente (role_id = 6)
        $usuarioId = $usuarioModel->create([
            'docusu' => $docusu,
            'nomusu' => $nombre,
            'apeusu' => $apellido,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role_id' => 6
        ]);

        // Insertar en cliente_detalles
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO cliente_detalles (idusu, telefono) VALUES (?, ?)");
        $stmt->execute([$usuarioId, $telefono]);

        // Autologin
        $_SESSION['user'] = [
            'id' => $usuarioId,
            'role_name' => 'cliente',
            'email' => $email,
            'nombre' => $nombre,
            'apellido' => $apellido
        ];

        header("Location: /vetsmart/cliente/dashboard");
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
            header("Location: /vetsmart/auth/register");
            exit;
        }

        $usuarioModel = new Usuario();

        // Evitar duplicados
        if ($usuarioModel->findByEmail($email)) {
            $_SESSION['error'] = "El correo ya está registrado.";
            header("Location: /vetsmart/auth/register");
            exit;
        }
        if ($usuarioModel->findByDocusu($docusu)) {
            $_SESSION['error'] = "El documento ya está registrado.";
            header("Location: /vetsmart/auth/register");
            exit;
        }

        // Guardar usuario
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userId = $usuarioModel->createClient($nombre, $apellido, $docusu, $email, $telefono, $hash);

        if ($userId) {
            // Login automático
            $_SESSION['user'] = $usuarioModel->findByEmail($email);
            header("Location: /vetsmart/dashboard");
            exit;
        } else {
            $_SESSION['error'] = "Error al registrar el usuario.";
            header("Location: /vetsmart/auth/register");
            exit;
        }
    }
}