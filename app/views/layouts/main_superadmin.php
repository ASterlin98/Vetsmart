<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Super Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-family: 'Inter', system-ui, sans-serif;
      background-color: #f1f5f9;
      transition: background-color 0.3s, color 0.3s;
    }

    .header {
      background: linear-gradient(90deg, #0d6efd, #0a58ca);
      color: #fff;
      height: 70px;
      padding: 0 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: fixed;
      width: 100%;
      top: 0;
      left: 0;
      z-index: 30;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .wrapper {
      display: flex;
      flex: 1;
      margin-top: 70px;
      transition: all 0.3s ease;
    }

    /* Sidebar */
    .sidebar {
      width: 260px;
      background: #212529;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 1.5rem 1rem;
      min-height: calc(100vh - 70px);
      box-shadow: 4px 0 10px rgba(0, 0, 0, 0.15);
      transition: width 0.3s ease;
      overflow: hidden;
    }

    .sidebar.collapsed {
      width: 80px;
    }

    .sidebar h2 {
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
      font-weight: 600;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding-bottom: 0.5rem;
      color: #e5e7eb;
      white-space: nowrap;
      overflow: hidden;
      transition: opacity 0.3s ease;
    }

    .sidebar.collapsed h2 {
      opacity: 0;
      pointer-events: none;
    }

    .sidebar a {
      color: #d1d5db;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      padding: 0.6rem 0.8rem;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .sidebar a:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
      transform: translateX(4px);
    }

    .sidebar .active {
      background: #198754;
      color: #fff;
      font-weight: 600;
      box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.3);
    }

    /* Ocultar texto cuando está colapsada */
    .sidebar.collapsed a span {
      display: none;
    }

    .sidebar.collapsed a {
      justify-content: center;
    }

    .content {
      flex: 1;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      background: #f9fafb;
      transition: background-color 0.3s, color 0.3s;
    }

    main {
      animation: fadeIn 0.3s ease-in-out;
    }

    footer {
      text-align: center;
      font-size: 0.9rem;
      color: #6b7280;
      margin-top: auto;
      padding: 1rem 0;
      border-top: 1px solid #e5e7eb;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Botones */
    #themeToggle, #sidebarToggle {
      background: rgba(255, 255, 255, 0.15);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 0.4rem 0.6rem;
      cursor: pointer;
      transition: all 0.3s;
    }

    #themeToggle:hover, #sidebarToggle:hover {
      background: rgba(255, 255, 255, 0.25);
    }

    #sidebarToggle {
      font-size: 1.3rem;
      margin-right: 0.75rem;
    }

    /* 🌙 MODO OSCURO */
    body.dark {
      background-color: #111827;
      color: #e5e7eb;
    }

    body.dark .header {
      background: linear-gradient(90deg, #1e3a8a, #1e40af);
      color: #f9fafb;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
    }

    body.dark .sidebar {
      background: #0f172a;
      color: #e2e8f0;
    }

    body.dark .sidebar a {
      color: #94a3b8;
    }

    body.dark .sidebar a:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    body.dark .sidebar .active {
      background: #14532d;
      color: #fff;
    }

    body.dark .content {
      background: #1e293b;
    }

    body.dark main {
      background: #334155 !important;
      color: #f1f5f9;
    }

    body.dark footer {
      border-top-color: #334155;
      color: #94a3b8;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="header">
    <div class="flex items-center">
      <button id="sidebarToggle" title="Mostrar/Ocultar menú">☰</button>
      <h1 class="text-lg font-bold tracking-wide">🛠️ VetSmart SuperAdmin</h1>
    </div>

    <div class="flex items-center gap-2">
      <button id="themeToggle" title="Cambiar tema">🌙</button>
      <a href="/vetsmart/logout" class="btn btn-light btn-sm shadow-sm hover:bg-gray-200 transition">
        Cerrar Sesión
      </a>
    </div>
  </header>

  <div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <h2>
        Bienvenido<br>
        <span class="text-blue-300">
          <?= htmlspecialchars($_SESSION['user']['nombre'] ?? '') ?>
          <?= htmlspecialchars($_SESSION['user']['apellido'] ?? '') ?>
        </span>
      </h2>

      <?php $currentUri = $_SERVER['REQUEST_URI']; ?>

      <a href="/vetsmart/super_admin/dashboard" class="<?= strpos($currentUri, '/super_admin/dashboard') !== false ? 'active' : '' ?>">
        🏠 <span>Dashboard</span>
      </a>

      <a href="/vetsmart/super_admin/permisos" class="<?= strpos($currentUri, '/super_admin/permisos') !== false ? 'active' : '' ?>">
        🔐 <span>Gestión de Permisos</span>
      </a>

      <a href="/vetsmart/super_admin/configuracion" class="<?= strpos($currentUri, '/super_admin/configuracion') !== false ? 'active' : '' ?>">
        ⚙️ <span>Configuración Global</span>
      </a>

      <a href="/vetsmart/super_admin/reportes" class="<?= strpos($currentUri, '/super_admin/reportes') !== false ? 'active' : '' ?>">
        📊 <span>Reportes</span>
      </a>

      <a href="/vetsmart/soporte" class="<?= strpos($currentUri, '/soporte') !== false ? 'active' : '' ?>">
        🆘 <span>Centro de Soporte</span>
        <?php if (isset($unseen_tickets) && $unseen_tickets > 0): ?>
          <span class="badge bg-danger ms-auto"><?= $unseen_tickets ?></span>
        <?php endif; ?>
      </a>
    </aside>

    <!-- Contenido -->
    <div class="content">
      <main class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
        <?= $content ?? '' ?>
      </main>

      <footer>
        © <?= date("Y") ?> VetSmart. Todos los derechos reservados.
      </footer>
    </div>
  </div>

  <script>
    // 🌙 Modo oscuro
    const toggleBtn = document.getElementById('themeToggle');
    const body = document.body;

    if (localStorage.getItem('theme') === 'dark') {
      body.classList.add('dark');
      toggleBtn.textContent = '🌞';
    }

    toggleBtn.addEventListener('click', () => {
      body.classList.toggle('dark');
      const isDark = body.classList.contains('dark');
      toggleBtn.textContent = isDark ? '🌞' : '🌙';
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });

    // 📁 Sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (localStorage.getItem('sidebar') === 'collapsed') {
      sidebar.classList.add('collapsed');
    }

    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      localStorage.setItem('sidebar', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
    });
  </script>
</body>
</html>
