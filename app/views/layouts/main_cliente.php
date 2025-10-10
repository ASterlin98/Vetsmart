<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Cliente</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { display: flex; min-height: 100vh; background: #f8f9fa; margin:0; }
    .header {
      width: 100%;
      height: 70px;
      background: #0dcaf0; /* celeste para cliente */
      color: #fff;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
    }
    .header h1 {
      font-size: 1.25rem;
      margin: 0;
      font-weight: bold;
    }
    .wrapper {
      display: flex;
      width: 100%;
      margin-top: 70px; /* espacio para el header fijo */
    }
    .sidebar {
      width: 250px;
      background: #212529;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 1rem;
      min-height: calc(100vh - 70px);
    }
    .sidebar p {
      font-size: 0.95rem;
      margin-bottom: 1rem;
      color: #adb5bd;
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
      background: #0dcaf0;
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

  <!-- Header superior -->
  <header class="header">
    <h1>VetSmart</h1>
    <a href="/vetsmart/logout" class="btn btn-light btn-sm">Cerrar Sesión</a>
  </header>

  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2>
        Bienvenido 
        <?= htmlspecialchars($_SESSION['user']['nombre'] ?? '') ?>
        <?= htmlspecialchars($_SESSION['user']['apellido'] ?? '') ?>.
      </h2>
      <?php if (has_permission('dashboard.view')): ?>
        <a href="/vetsmart/cliente/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : '' ?>">🏠 Dashboard</a>
      <?php endif; ?>
      <?php if (has_permission('citas.view_own')): ?>
        <a href="/vetsmart/cliente/citas" class="<?= strpos($_SERVER['REQUEST_URI'], 'citas') !== false ? 'active' : '' ?>">📅 Mis Citas</a>
      <?php endif; ?>
      <?php if (has_permission('mascotas.view_own')): ?>
        <a href="/vetsmart/cliente/mascotas" class="<?= strpos($_SERVER['REQUEST_URI'], 'mascotas') !== false ? 'active' : '' ?>">🐾 Mis Mascotas</a>
      <?php endif; ?>
      <?php if (has_permission('historial.view_own')): ?>
        <a href="/vetsmart/cliente/historial" class="<?= strpos($_SERVER['REQUEST_URI'], 'historial') !== false ? 'active' : '' ?>">📖 Historial Clínico</a>
      <?php endif; ?>
      <?php if (has_permission('pagos.view')): ?>
        <a href="/vetsmart/cliente/pagos" class="<?= strpos($_SERVER['REQUEST_URI'], 'pagos') !== false ? 'active' : '' ?>">💳 Pagos</a>
      <?php endif; ?>
      <?php if (has_permission('reportes.view_own')): ?>
        <a href="/vetsmart/cliente/reportes" class="<?= strpos($_SERVER['REQUEST_URI'], 'reportes') !== false ? 'active' : '' ?>">📊 Reportes</a>
      <?php endif; ?>
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
