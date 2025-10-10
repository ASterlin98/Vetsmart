<?php require_once APP_ROOT . '/helpers/auth_helper.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Tailwind CDN (rápido para dev) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <meta charset="UTF-8">
  <title>Panel del Recepcionista</title>

  <!-- Bootstrap (si lo usas) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <style>
    main {
      flex: 1;
    }

    body {
      display: flex;
      min-height: 100vh;
      background: #f8f9fa;
      margin: 0;
      flex-direction: column;
    }

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
      z-index: 30; /* <- importante */
    }

    .header h1 {
      font-size: 1.25rem;
      margin: 0;
      font-weight: bold;
    }

    .wrapper {
      display: flex;
      flex: 1; /* <-- el wrapper se expande para empujar el footer hacia abajo */
      margin-top: 70px;
    }

    .sidebar {
      width: 250px;
      background: #212529;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 1rem;
      min-height: calc(100vh - 70px);
      z-index: 20;
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
      display: flex;
      flex-direction: column; /* para que el footer quede después del contenido */
      padding: 25px 20px 0px 20px;
    }

    footer {
      text-align: center;
      font-size: 0.9rem;
      color: #666;
      margin-top: auto; /* <-- empuja el footer al final */
      padding: 1rem 0;
    }

    .modal-root-high-z {
      z-index: 99999 !important;
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
      <a href="/vetsmart/veterinario/dashboard"
         class="<?= strpos($_SERVER['REQUEST_URI'], '/veterinario/dashboard') !== false ? 'active' : '' ?>">
        🏠 Dashboard
      </a>
      <?php endif; ?>

      <?php if (has_permission('citas.view')): ?>
      <a href="/vetsmart/veterinario/mis-citas"
         class="<?= (strpos($_SERVER['REQUEST_URI'], '/mis-citas') !== false || strpos($_SERVER['REQUEST_URI'], '/citas') !== false) ? 'active' : '' ?>">
        📅 Mis Citas
      </a>
      <?php endif; ?>

      <?php if (has_permission('pacientes.view')): ?>
      <a href="/vetsmart/veterinario/pacientes"
         class="<?= strpos($_SERVER['REQUEST_URI'], '/pacientes') !== false ? 'active' : '' ?>">
        🐾 Pacientes
      </a>
      <?php endif; ?>

      <?php if (has_permission('consultas.view')): ?>
      <a href="/vetsmart/veterinario/consultas"
         class="<?= strpos($_SERVER['REQUEST_URI'], '/consultas') !== false ? 'active' : '' ?>">
        💉 Consultas
      </a>
      <?php endif; ?>

      <?php if (has_permission('reportes.view')): ?>
      <a href="/vetsmart/veterinario/reportes" class="<?= strpos($_SERVER['REQUEST_URI'], '/reportes') !== false ? 'active' : '' ?>">
        📊 Reportes
      </a>
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

  <?php
    $modalPath = __DIR__ . '/../veterinario/citas/_modal.php';
    if (file_exists($modalPath)) {
        require $modalPath;
    }
  ?>

  <!-- Opcional: small script to ensure modal root has very high z (si necesitas) -->
  <script>
    (function(){
      const modal = document.getElementById('citaModal');
      if (modal) {
        modal.classList.add('modal-root-high-z');
      }
    })();
  </script>
</body>
</html>
