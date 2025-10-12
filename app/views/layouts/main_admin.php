<!-- app/views/layouts/main_recepcionista.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Recepcionista</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: #f8f9fa;
      margin: 0;
      transition: background-color 0.3s, color 0.3s;
    }

    .header {
      width: 100%;
      height: 70px;
      background: #198754;
      color: #fff;
      padding: 0 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 30;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .header h1 {
      font-size: 1.25rem;
      margin: 0;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .wrapper {
      display: flex;
      flex: 1;
      margin-top: 70px;
      transition: all 0.3s ease;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background: #212529;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 1.2rem;
      min-height: calc(100vh - 70px);
      transition: width 0.3s ease;
      overflow: hidden;
    }

    .sidebar.collapsed {
      width: 80px;
    }

    .sidebar h2 {
      font-size: 1.1rem;
      margin-bottom: 1.2rem;
      font-weight: bold;
      transition: opacity 0.3s ease;
    }

    .sidebar.collapsed h2 {
      opacity: 0;
      pointer-events: none;
    }

    .sidebar a {
      color: #ddd;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0.5rem 0.75rem;
      border-radius: 8px;
      margin-bottom: 0.4rem;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .sidebar a:hover {
      background: #343a40;
      color: #fff;
      transform: translateX(3px);
    }

    .sidebar .active {
      background: #0d6efd;
      color: #fff;
      font-weight: bold;
    }

    .sidebar.collapsed a span {
      display: none;
    }

    .sidebar.collapsed a {
      justify-content: center;
    }

    /* Contenido */
    .content {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 25px 20px 0 20px;
      transition: background-color 0.3s, color 0.3s;
    }

    footer {
      text-align: center;
      font-size: 0.9rem;
      color: #666;
      margin-top: auto;
      padding: 1rem 0;
    }

    /* Botones */
    #sidebarToggle, #themeToggle {
      background: rgba(255, 255, 255, 0.15);
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 0.4rem 0.6rem;
      cursor: pointer;
      transition: all 0.3s;
    }

    #sidebarToggle:hover, #themeToggle:hover {
      background: rgba(255, 255, 255, 0.25);
    }

    /* 🌙 Modo oscuro */
    body.dark {
      background-color: #121212;
      color: #e5e5e5;
    }

    body.dark .header {
      background: #14532d;
    }

    body.dark .sidebar {
      background: #0f172a;
      color: #e2e8f0;
    }

    body.dark .sidebar a {
      color: #9ca3af;
    }

    body.dark .sidebar a:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    body.dark .sidebar .active {
      background: #166534;
    }

    body.dark footer {
      color: #9ca3af;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="d-flex align-items-center gap-2">
      <button id="sidebarToggle" title="Mostrar/Ocultar menú">☰</button>
      <h1>💼 VetSmart Administrador</h1>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button id="themeToggle" title="Cambiar tema">🌙</button>
      <a href="/vetsmart/logout" class="btn btn-light btn-sm">Cerrar Sesión</a>
    </div>
  </header>

  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <h2>
        Bienvenido<br>
        <?= htmlspecialchars($_SESSION['user']['nombre'] ?? '') ?>
        <?= htmlspecialchars($_SESSION['user']['apellido'] ?? '') ?>
      </h2>

      <a href="/vetsmart/admin/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>">🏠 <span>Dashboard</span></a>
      <a href="/vetsmart/admin/empleados" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/empleados') !== false ? 'active' : '' ?>">👥 <span>Gestión de Empleados</span></a>
      <a href="/vetsmart/admin/agenda" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/agenda') !== false ? 'active' : '' ?>">📅 <span>Agenda General</span></a>
      <a href="/vetsmart/admin/horarios" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/horarios') !== false ? 'active' : '' ?>">⏰ <span>Gestión de Horarios</span></a>
      <a href="/vetsmart/admin/clientes" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/clientes') !== false ? 'active' : '' ?>">🐶 <span>Gestión de Clientes</span></a>
      <a href="/vetsmart/admin/servicios" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/servicios') !== false ? 'active' : '' ?>">🛠️ <span>Gestión de Servicios</span></a>
      <a href="/vetsmart/admin/finanzas" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/finanzas') !== false ? 'active' : '' ?>">💰 <span>Finanzas</span></a>
      <a href="/vetsmart/admin/reportes" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/reportes') !== false ? 'active' : '' ?>">📊 <span>Reportes</span></a>
      <a href="/vetsmart/admin/soporte" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/soporte') !== false ? 'active' : '' ?>">
        🆘 <span>Soporte</span>
        <?php if (isset($unseen_tickets_admin) && $unseen_tickets_admin > 0): ?>
          <span class="badge bg-danger ms-auto"><?= $unseen_tickets_admin ?></span>
        <?php endif; ?>
      </a>
    </div>

    <!-- Contenido -->
    <div class="content">
      <main class="p-4">
        <?= $content ?? '' ?>
      </main>

      <footer>
        © <?= date("Y") ?> VetSmart. Todos los derechos reservados.
      </footer>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

    // Mantener estado del sidebar
    if (localStorage.getItem('sidebar') === 'collapsed') {
      sidebar.classList.add('collapsed');
    }

    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      localStorage.setItem('sidebar', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
    });

    // Modo oscuro persistente
    if (localStorage.getItem('theme') === 'dark') {
      body.classList.add('dark');
      themeToggle.textContent = '🌞';
    }

    themeToggle.addEventListener('click', () => {
      body.classList.toggle('dark');
      const isDark = body.classList.contains('dark');
      themeToggle.textContent = isDark ? '🌞' : '🌙';
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
