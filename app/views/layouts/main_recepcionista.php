<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Recepcionista - VetSmart</title>
  <?php require __DIR__ . '/admin_head.php'; ?>
</head>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$roleName = $_SESSION['user']['role_name'] ?? '';
$roleAlt  = $_SESSION['user']['role'] ?? '';
if (empty($_SESSION['user']) || ($roleName !== 'recepcionista' && $roleAlt !== 'recepcionista')) {
  header('Location: /vetsmart/login');
  exit;
}
?>

<body>
  <!-- Header superior -->
  <header class="header">
    <button class="menu-toggle d-lg-none">
      <i class="fas fa-bars"></i>
    </button>
    <h1><i class="fas fa-paw me-2"></i>VetSmart</h1>
    <a href="/vetsmart/logout" class="btn btn-light btn-sm logout-btn">
      <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesion
    </a>
  </header>

  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-header">
        <h2>
          <i class="fas fa-user-circle me-2"></i>
          Bienvenido, 
          <?= htmlspecialchars($_SESSION['user']['nombre'] ?? '') ?>
          <?= htmlspecialchars($_SESSION['user']['apellido'] ?? '') ?>
        </h2>
      </div>
      
      <nav class="sidebar-nav">
        <div class="nav-item">
          <a href="/vetsmart/recepcionista/dashboard" class="nav-link">
            <span class="nav-icon"><i class="fas fa-home"></i></span>
            <span class="nav-text">Dashboard</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="/vetsmart/recepcionista/agenda" class="nav-link">
            <span class="nav-icon"><i class="fas fa-calendar-day"></i></span>
            <span class="nav-text">Agenda Diaria</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="/vetsmart/recepcionista/citas" class="nav-link">
            <span class="nav-icon"><i class="fas fa-calendar-check"></i></span>
            <span class="nav-text">Gestion de Citas</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="/vetsmart/recepcionista/clientes" class="nav-link">
            <span class="nav-icon"><i class="fas fa-users"></i></span>
            <span class="nav-text">Gestion de Clientes</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="/vetsmart/recepcionista/mascotas" class="nav-link">
            <span class="nav-icon"><i class="fas fa-paw"></i></span>
            <span class="nav-text">Gestion de Mascotas</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="/vetsmart/recepcionista/ingresos" class="nav-link">
            <span class="nav-icon"><i class="fas fa-money-bill-wave"></i></span>
            <span class="nav-text">Ingresos / Egresos</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="/vetsmart/recepcionista/inventario" class="nav-link">
            <span class="nav-icon"><i class="fas fa-boxes"></i></span>
            <span class="nav-text">Inventario Basico</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="/vetsmart/recepcionista/reportes" class="nav-link">
            <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
            <span class="nav-text">Reportes</span>
          </a>
        </div>
      </nav>
    </div>

    <!-- Contenido -->
    <div class="content">
      <main class="content-main fade-in">
        <?= $content ?? '' ?>
      </main>

      <footer>
        © <?= date("Y") ?> VetSmart. Todos los derechos reservados.
      </footer>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
          crossorigin="anonymous"></script>
  
  <?php require __DIR__ . '/admin_footer.php'; ?>

</body>
</html>
