<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Peluquero | VetSmart</title>
  <?php require __DIR__ . '/admin_head.php'; ?>
</head>
<body>

  <!-- Header Elegante -->
  <header class="header">
    <h1><i class="fas fa-paw"></i> VetSmart</h1>
    <div class="d-flex align-items-center gap-2">
      <a href="/vetsmart/logout" class="btn-logout">
        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
      </a>
    </div>
  </header>

  <div class="wrapper">
    <!-- Sidebar Elegante - FIJO -->
    <div class="sidebar">
      <div class="user-info">
        <div class="user-avatar">
          <?= strtoupper(substr(htmlspecialchars($_SESSION['user']['nombre'] ?? 'P'), 0, 1)) ?>
        </div>
        <h2>
          <?= htmlspecialchars($_SESSION['user']['nombre'] ?? '') ?>
          <?= htmlspecialchars($_SESSION['user']['apellido'] ?? '') ?>
        </h2>
        <p>Peluquero</p>
      </div>
      
      <nav class="sidebar-nav">
        <a href="/vetsmart/peluquero/dashboard" class="nav-item active">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </a>
        <a href="/vetsmart/peluquero/agenda" class="nav-item">
          <i class="fas fa-calendar-alt"></i>
          <span>Mi Agenda</span>
        </a>
        <a href="/vetsmart/peluquero/citas" class="nav-item">
          <i class="fas fa-scissors"></i>
          <span>Citas de Peluquería</span>
        </a>
        <a href="/vetsmart/peluquero/clientes" class="nav-item">
          <i class="fas fa-dog"></i>
          <span>Clientes y Mascotas</span>
        </a>
        <a href="/vetsmart/peluquero/servicios" class="nav-item">
          <i class="fas fa-concierge-bell"></i>
          <span>Servicios Ofrecidos</span>
        </a>
      </nav>
    </div>

    <!-- Content Area Elegante - CON MARGEN PARA EL SIDEBAR FIJO -->
    <div class="content">
      <main class="main-content">
        <?= $content ?? 'Contenido del dashboard se cargará aquí' ?>
      </main>

      <footer>
        © <?= date("Y") ?> VetSmart. Todos los derechos reservados.
      </footer>
    </div>
  </div>

  <?php require __DIR__ . '/admin_footer.php'; ?>

</body>
</html>