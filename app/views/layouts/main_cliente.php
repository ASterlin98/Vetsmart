<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Cliente | VetSmart</title>
  <?php require __DIR__ . '/admin_head.php'; ?>
</head>
<body>

  <!-- Header Elegante -->
  <header class="header">
    <h1><i class="fas fa-paw"></i> VetSmart</h1>
    <a href="/vetsmart/logout" class="btn-logout">
      <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
    </a>
  </header>

  <div class="wrapper">
    <!-- Sidebar Elegante -->
    <div class="sidebar">
      <div class="user-info">
        <?php
          $n = trim((string)($_SESSION['user']['nombre'] ?? ''));
          $a = trim((string)($_SESSION['user']['apellido'] ?? ''));
          $ini = mb_strtoupper(mb_substr($n, 0, 1) . mb_substr($a, 0, 1));
        ?>
        <div class="user-avatar"><?= htmlspecialchars($ini ?: 'CL') ?></div>
        <h2>Bienvenido, <?= htmlspecialchars($_SESSION['user']['nombre'] ?? '') ?> <?= htmlspecialchars($_SESSION['user']['apellido'] ?? '') ?></h2>
        <p>Panel de Control </p>
      </div>
      
      <nav class="sidebar-nav">
        <a href="/vetsmart/cliente/dashboard" class="nav-item">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </a>
        <a href="/vetsmart/cliente/citas" class="nav-item">
          <i class="fas fa-calendar-alt"></i>
          <span>Mis Citas</span>
        </a>
        <a href="/vetsmart/cliente/mascotas" class="nav-item">
          <i class="fas fa-paw"></i>
          <span>Mis Mascotas</span>
        </a>
        <a href="/vetsmart/cliente/historial" class="nav-item">
          <i class="fas fa-file-medical"></i>
          <span>Historial Clinico</span>
        </a>
        <a href="/vetsmart/cliente/reportes" class="nav-item">
          <i class="fas fa-chart-bar"></i>
          <span>Reportes</span>
        </a>
      </nav>
    </div>

    <!-- Content Area Elegante -->
    <div class="content">
      <main class="main-content">
        <?= $content ?? 'Contenido dinÃ¡mico se cargarÃ¡ aquÃ­ segÃºn la vista activa' ?>
        
        <!-- Las tarjetas solo aparecerÃ¡n en el Dashboard -->
        <!-- Para otras vistas, se mostrarÃ¡ el contenido especÃ­fico de cada secciÃ³n -->
      </main>

      <footer>
        © <?= date("Y") ?> VetSmart. Todos los derechos reservados.
      </footer>
    </div>
  </div>

  <?php require __DIR__ . '/admin_footer.php'; ?>

</body>
</html>
