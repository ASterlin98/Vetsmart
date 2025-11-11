<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Cliente | VetSmart</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
:root {
  /* Tonos principales - mÃ¡s oscuros */
  --primary: #0284a8;
  --primary-light: #0ea5c6;
  --primary-dark: #03627c;

  /* Secundarios - cÃ¡lidos, mÃ¡s sobrios */
  --secondary: #d97706;
  --secondary-dark: #b45309;

  /* Acentos - verdes con mÃ¡s profundidad */
  --accent: #059669;
  --accent-dark: #047857;

  /* Neutros oscuros */
  --dark: #0f172a;
  --light: #f1f5f9;

  /* Grises */
  --gray: #475569;
  --gray-light: #94a3b8;

  /* Gradientes */
  --gradient-primary: linear-gradient(135deg, #03627c 0%, #0284a8 100%);
  --gradient-secondary: linear-gradient(135deg, #b45309 0%, #d97706 100%);
  --gradient-accent: linear-gradient(135deg, #047857 0%, #059669 100%);

  /* Estilos base */
  --border-radius: 16px;
  --shadow: 0 6px 24px rgba(0, 0, 0, 0.15);
  --shadow-hover: 0 12px 30px rgba(0, 0, 0, 0.25);
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
      color: var(--dark);
      line-height: 1.6;
      min-height: 100vh;
    }
    
    /* Header Elegante */
    .header {
      background: var(--gradient-primary);
      color: white;
      padding: 1rem 2.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 80px;
      z-index: 1000;
      box-shadow: var(--shadow);
      backdrop-filter: blur(10px);
    }
    
    .header h1 {
      font-size: 1.75rem;
      font-weight: 700;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .header h1 i {
      font-size: 1.5rem;
      background: rgba(255, 255, 255, 0.2);
      padding: 10px;
      border-radius: 12px;
      backdrop-filter: blur(10px);
    }
    
    .btn-logout {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: var(--border-radius);
      font-weight: 600;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 10px;
      backdrop-filter: blur(10px);
    }
    
    .btn-logout:hover {
      background: rgba(255, 255, 255, 0.25);
      color: white;
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }
    
    /* Main Layout */
    .wrapper {
      display: flex;
      width: 100%;
      margin-top: 80px;
      min-height: calc(100vh - 80px);
    }
    
    /* Sidebar Elegante */
    .sidebar {
      width: 300px;
      background: white;
      color: var(--dark);
      display: flex;
      flex-direction: column;
      padding: 2.5rem 2rem;
      box-shadow: var(--shadow);
      transition: var(--transition);
      z-index: 900;
      border-right: 1px solid var(--gray-light);
      position: fixed;
      top: 80px;
      left: 0;
      bottom: 0;
      height: calc(100vh - 80px);
      overflow-y: auto;
    }
    
    .user-info {
      margin-bottom: 2.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid var(--gray-light);
      text-align: center;
    }
    
    .user-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: var(--gradient-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      color: white;
      font-size: 1.5rem;
      font-weight: 600;
    }
    
    .user-info h2 {
      font-size: 1.35rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: var(--dark);
    }
    
    .user-info p {
      color: var(--gray);
      font-size: 0.95rem;
      font-weight: 500;
    }
    
    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }
    
    .nav-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 1rem 1.25rem;
      border-radius: var(--border-radius);
      color: var(--dark);
      text-decoration: none;
      font-weight: 600;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }
    
    .nav-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 4px;
      background: var(--primary);
      transform: scaleY(0);
      transition: var(--transition);
    }
    
    .nav-item i {
      width: 24px;
      text-align: center;
      font-size: 1.2rem;
      color: var(--gray);
      transition: var(--transition);
    }
    
    .nav-item:hover {
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      color: var(--primary-dark);
      transform: translateX(8px);
      box-shadow: var(--shadow);
    }
    
    .nav-item:hover::before {
      transform: scaleY(1);
    }
    
    .nav-item:hover i {
      color: var(--primary);
      transform: scale(1.1);
    }
    
    .nav-item.active {
      background: var(--gradient-primary);
      color: white;
      box-shadow: var(--shadow);
    }
    
    .nav-item.active i {
      color: white;
    }
    
    .nav-item.active::before {
      display: none;
    }
    
    /* Content Area Elegante */
    .content {
      flex: 1;
      padding: 2.5rem;
      background: transparent;
      overflow-y: auto;
      margin-left: 300px; /* espacio para sidebar fija */
    }
    
    .main-content {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 2.5rem;
      min-height: 70vh;
      border: 1px solid var(--gray-light);
    }
    
    /* Footer */
    footer {
      text-align: center;
      margin-top: 3rem;
      padding: 2rem;
      font-size: 0.9rem;
      color: var(--gray);
      border-top: 1px solid var(--gray-light);
    }
    
    /* Tarjetas de Ejemplo (Solo para Dashboard) */
    .dashboard-card {
      background: white;
      border-radius: var(--border-radius);
      padding: 2rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--gray-light);
      transition: var(--transition);
      height: 100%;
    }
    
    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-hover);
    }
    
    .card-icon {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .icon-primary {
      background: var(--gradient-primary);
      color: white;
    }
    
    .icon-secondary {
      background: var(--gradient-secondary);
      color: white;
    }
    
    .icon-accent {
      background: var(--gradient-accent);
      color: white;
    }
    
    .dashboard-card h5 {
      font-weight: 700;
      margin-bottom: 1rem;
      color: var(--dark);
    }
    
    .dashboard-card p {
      color: var(--gray);
      margin-bottom: 1.5rem;
    }
    
    .btn-elegant {
      padding: 0.75rem 1.5rem;
      border-radius: var(--border-radius);
      font-weight: 600;
      transition: var(--transition);
      border: none;
    }
    
    .btn-primary-elegant {
      background: var(--gradient-primary);
      color: white;
    }
    
    .btn-primary-elegant:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }
    
    /* Responsive Design */
    @media (max-width: 1200px) {
      .sidebar {
        width: 280px;
      }
    }
    
    @media (max-width: 992px) {
      .sidebar {
        width: 250px;
        padding: 2rem 1.5rem;
      }
    }
    
    @media (max-width: 768px) {
      .wrapper { flex-direction: column; }
      .sidebar {
        position: static;
        width: 100%;
        height: auto;
        min-height: auto;
        padding: 2rem;
        overflow: visible;
      }
      .content { margin-left: 0; padding: 1.5rem; }
      .sidebar-nav {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
      }
      
      .nav-item {
        flex: 1;
        min-width: 160px;
        justify-content: center;
        text-align: center;
      }
      
      .nav-item:hover {
        transform: translateY(-5px);
      }
      
      .content {
        padding: 2rem;
      }
      
      .header {
        padding: 1rem 2rem;
      }
    }
    
    @media (max-width: 576px) {
      .sidebar-nav {
        flex-direction: column;
      }
      
      .nav-item {
        min-width: 100%;
      }
      
      .header {
        padding: 1rem 1.5rem;
      }
      
      .header h1 {
        font-size: 1.5rem;
      }
      
      .content {
        padding: 1.5rem;
      }
      
      .main-content {
        padding: 2rem;
      }
    }
  </style>
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

  <script>
    // Script para manejar el estado activo del menÃº
    document.addEventListener('DOMContentLoaded', function() {
      // Remover clase active de todos los items primero
      const navItems = document.querySelectorAll('.nav-item');
      navItems.forEach(item => item.classList.remove('active'));
      
      // Determinar quÃ© pÃ¡gina estÃ¡ activa basado en la URL actual
      const currentPath = window.location.pathname;
      let activeNavItem = null;
      
      if (currentPath.includes('/citas')) {
        activeNavItem = document.querySelector('a[href*="/citas"]');
      } else if (currentPath.includes('/mascotas')) {
        activeNavItem = document.querySelector('a[href*="/mascotas"]');
      } else if (currentPath.includes('/historial')) {
        activeNavItem = document.querySelector('a[href*="/historial"]');
      } else if (currentPath.includes('/pagos')) {
        activeNavItem = document.querySelector('a[href*="/pagos"]');
      } else if (currentPath.includes('/reportes')) {
        activeNavItem = document.querySelector('a[href*="/reportes"]');
      } else {
        // Por defecto, Dashboard estÃ¡ activo
        activeNavItem = document.querySelector('a[href*="/dashboard"]');
      }
      
      if (activeNavItem) {
        activeNavItem.classList.add('active');
      }
    });
  </script>

</body>
</html>
