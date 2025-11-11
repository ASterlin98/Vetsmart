<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Recepcionista - VetSmart</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #198754;
      --sidebar-bg: #212529;
      --sidebar-hover: #343a40;
      --active-color: #0d6efd;
      --header-height: 70px;
      --sidebar-width: 250px;
      --transition-speed: 0.3s;
      /* Evitar salto horizontal al aparecer/desaparecer la barra de desplazamiento */
      scrollbar-gutter: stable;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      display: flex;
      flex-direction: column; /* Apila header y wrapper en columna para evitar desbordamiento horizontal */
      min-height: 100vh;
      background: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
    }

    /* Header Styles */
    .header {
      width: 100%;
      height: var(--header-height);
      background: var(--primary-color);
      color: #fff;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1030;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .header h1 {
      font-size: 1.5rem;
      margin: 0;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .logout-btn {
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.3);
      transition: all var(--transition-speed);
    }

    .logout-btn:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: translateY(-2px);
    }

    /* Main Wrapper */
    .wrapper {
      display: flex;
      width: 100%;
      margin-top: var(--header-height);
    }

    /* Sidebar Styles */
    .sidebar {
      width: var(--sidebar-width);
      background: var(--sidebar-bg);
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 1.5rem 1rem;
      min-height: calc(100vh - var(--header-height));
      transition: all var(--transition-speed);
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar-header {
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header h2 {
      font-size: 1.1rem;
      font-weight: 600;
      margin: 0;
      color: #e9ecef;
    }

    .sidebar-nav {
      flex: 1;
    }

    .nav-item {
      margin-bottom: 0.5rem;
    }

    .nav-link {
      color: #ddd;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      transition: all var(--transition-speed);
      font-weight: 500;
    }

    .nav-link:hover {
      background: var(--sidebar-hover);
      color: #fff;
      transform: translateX(5px);
    }

    .nav-link.active {
      background: var(--active-color);
      color: #fff;
      box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }

    .nav-icon {
      margin-right: 0.75rem;
      font-size: 1.1rem;
      width: 20px;
      text-align: center;
    }

    /* Content Area */
    .content {
      flex: 1;
      padding: 2rem;
      transition: all var(--transition-speed);
      /* Permitir scroll horizontal del contenido si alguna tabla es muy ancha
         (evita que el layout parezca "correrse" a la derecha) */
      overflow-x: auto;
    }

    .content-main {
      background: #fff;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      min-height: calc(100vh - var(--header-height) - 6rem);
    }

    /* Footer */
    footer {
      text-align: center;
      margin-top: 2rem;
      font-size: 0.9rem;
      color: #6c757d;
      padding: 1rem;
      border-top: 1px solid #e9ecef;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
      .sidebar {
        width: 70px;
        padding: 1rem 0.5rem;
      }

      .sidebar-header h2, .nav-text {
        display: none;
      }

      .nav-icon {
        margin-right: 0;
        font-size: 1.3rem;
      }

      .nav-link {
        justify-content: center;
        padding: 0.75rem 0.5rem;
      }

      .nav-link:hover {
        transform: translateY(-2px);
      }

      .content {
        padding: 1.5rem;
      }
    }

    @media (max-width: 768px) {
      .header {
        padding: 1rem;
      }

      .header h1 {
        font-size: 1.25rem;
      }

      .content {
        padding: 1rem;
      }

      .content-main {
        padding: 1.5rem;
      }
    }

    @media (max-width: 576px) {
      :root {
        --header-height: 60px;
      }

      .header h1 {
        font-size: 1.1rem;
      }

      .sidebar {
        position: fixed;
        height: calc(100vh - var(--header-height));
        z-index: 1020;
        transform: translateX(-100%);
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .content {
        width: 100%;
      }

      .menu-toggle {
        display: block;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
      }
    }

    /* Animation for page content */
    .fade-in {
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
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
  
  <script>
    // Toggle sidebar on mobile
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('active');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
      const sidebar = document.querySelector('.sidebar');
      const menuToggle = document.querySelector('.menu-toggle');
      
      if (window.innerWidth <= 576 && 
          !sidebar.contains(event.target) && 
          !menuToggle.contains(event.target) &&
          sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
      }
    });

    // Add active class to current page link
    document.addEventListener('DOMContentLoaded', function() {
      const currentPage = window.location.pathname;
      const navLinks = document.querySelectorAll('.nav-link');
      
      navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
          link.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>
