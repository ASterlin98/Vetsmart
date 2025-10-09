
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Super Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { display: flex; min-height: 100vh; background: #f8f9fa; margin: 0; }
        .header {
            width: 100%;
            height: 70px;
            background: #0d6efd;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .header h1 {
            font-size: 1.25rem;
            margin: 0;
            font-weight: bold;
        }
        .wrapper {
            display: flex;
            width: 100%;
            margin-top: 70px;
        }
        .sidebar {
            width: 260px;
            background: #212529;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            min-height: calc(100vh - 70px);
        }
        .sidebar h2 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            font-weight: bold;
        }
        .sidebar a {
            color: #ddd;
            text-decoration: none;
            display: block;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        .sidebar a:hover {
            background: #343a40;
            color: #fff;
        }
        .sidebar .active {
            background: #198754;
            color: #fff;
        }
        .content {
            flex: 1;
            padding: 2rem;
        }
        footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>

<header class="header">
    <h1>VetSmart</h1>
    <a href="/vetsmart/logout" class="btn btn-light btn-sm">Cerrar Sesión</a>
</header>

<div class="wrapper">
    <div class="sidebar">
        <h2>
            Bienvenido<br>
            <?= htmlspecialchars($_SESSION['user']['nombre'] ?? '') ?>
            <?= htmlspecialchars($_SESSION['user']['apellido'] ?? '') ?>
        </h2>

        <?php $currentUri = $_SERVER['REQUEST_URI']; ?>
            <a href="/vetsmart/super_admin/dashboard" class="<?= strpos($currentUri, '/super_admin/dashboard') !== false ? 'active' : '' ?>">🏠 Dashboard</a>

            <a href="/vetsmart/super_admin/usuarios" class="<?= strpos($currentUri, '/super_admin/usuarios') !== false ? 'active' : '' ?>">👤 Gestión de Usuarios</a>

            <a href="/vetsmart/super_admin/permisos" class="<?= strpos($currentUri, '/super_admin/permisos') !== false ? 'active' : '' ?>">🔐 Gestión de Permisos</a>

            <a href="/vetsmart/super_admin/gestion" class="<?= strpos($currentUri, '/super_admin/gestion') !== false ? 'active' : '' ?>">🛡️ Gestión de Roles</a>

            <a href="/vetsmart/super_admin/clinicas" class="<?= strpos($currentUri, '/super_admin/clinicas') !== false ? 'active' : '' ?>">🏥 Clínicas</a>

            <a href="/vetsmart/super_admin/configuracion" class="<?= strpos($currentUri, '/super_admin/configuracion') !== false ? 'active' : '' ?>">⚙️ Configuración Global</a>

            <a href="/vetsmart/super_admin/reportes" class="<?= strpos($currentUri, '/super_admin/reportes') !== false ? 'active' : '' ?>">📊 Reportes</a>

            <a href="/vetsmart/soporte" class="<?= strpos($currentUri, '/soporte') !== false ? 'active' : '' ?>">🆘 Centro de Soporte</a>
    </div>

    <div class="content">
        <main class="p-4">
            <?= $content ?? '' ?>
        </main>

        <footer>
            © <?= date("Y") ?> VetSmart. Todos los derechos reservados.
        </footer>
    </div>
</div>

</body>
</html>
