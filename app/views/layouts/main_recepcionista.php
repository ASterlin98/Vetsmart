<!-- app/views/layouts/main_recepcionista.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Recepcionista</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { display: flex; min-height: 100vh; background: #f8f9fa; margin:0; }
    .header {
      width: 100%;
      height: 70px;
      background: #198754;
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
      background: #0d6efd;
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
        <a href="/vetsmart/recepcion/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : '' ?>">🏠 Dashboard</a>
      <?php endif; ?>
      <?php if (has_permission('agenda.view')): ?>
        <a href="/vetsmart/recepcion/agenda" class="<?= strpos($_SERVER['REQUEST_URI'], 'agenda') !== false ? 'active' : '' ?>">📅 Agenda Diaria</a>
      <?php endif; ?>
      <?php if (has_permission('citas.manage')): ?>
        <a href="/vetsmart/recepcion/citas" class="<?= strpos($_SERVER['REQUEST_URI'], 'citas') !== false ? 'active' : '' ?>">📌 Gestión de Citas</a>
      <?php endif; ?>
      <?php if (has_permission('clientes.manage')): ?>
        <a href="/vetsmart/recepcion/clientes" class="<?= strpos($_SERVER['REQUEST_URI'], 'clientes') !== false ? 'active' : '' ?>">🐶 Gestión de Clientes</a>
      <?php endif; ?>
      <?php if (has_permission('ingresos.view')): ?>
        <a href="/vetsmart/recepcion/ingresos" class="<?= strpos($_SERVER['REQUEST_URI'], 'ingresos') !== false ? 'active' : '' ?>">🐾 Ingresos / Egresos</a>
      <?php endif; ?>
      <?php if (has_permission('inventario.view')): ?>
        <a href="/vetsmart/recepcion/inventario" class="<?= strpos($_SERVER['REQUEST_URI'], 'inventario') !== false ? 'active' : '' ?>">📦 Inventario Básico</a>
      <?php endif; ?>
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

</body>
</html>
