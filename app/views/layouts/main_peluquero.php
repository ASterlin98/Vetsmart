<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Peluquero | VetSmart</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #8b5cf6;
      --primary-light: #a78bfa;
      --primary-dark: #7c3aed;
      --bg-primary: #ffffff;
      --bg-secondary: #f8fafc;
      --bg-sidebar: #1e293b;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --text-light: #94a3b8;
      --border-color: #e2e8f0;
      --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg-secondary);
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
    }

    /* Header Elegante */
    .header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 70px;
      z-index: 1000;
      box-shadow: var(--shadow);
      backdrop-filter: blur(10px);
    }
    
    .header h1 {
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .header h1 i {
      font-size: 1.3rem;
      background: rgba(255, 255, 255, 0.2);
      padding: 8px;
      border-radius: 10px;
      backdrop-filter: blur(10px);
    }
    
    .btn-logout {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: white;
      padding: 0.6rem 1.2rem;
      border-radius: 10px;
      font-weight: 600;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 8px;
      backdrop-filter: blur(10px);
      text-decoration: none;
    }
    
    .btn-logout:hover {
      background: rgba(255, 255, 255, 0.25);
      color: white;
      transform: translateY(-2px);
    }

    /* Main Layout */
    .wrapper {
      display: flex;
      width: 100%;
      margin-top: 70px;
      min-height: calc(100vh - 70px);
    }

    /* Sidebar Elegante - FIJO */
    .sidebar {
      width: 280px;
      background: var(--bg-sidebar);
      color: white;
      display: flex;
      flex-direction: column;
      padding: 2rem 1.5rem;
      min-height: calc(100vh - 70px);
      box-shadow: var(--shadow);
      position: fixed;
      left: 0;
      top: 70px;
      overflow-y: auto;
      z-index: 900;
    }

    .user-info {
      margin-bottom: 2.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid #374151;
      text-align: center;
    }

    .user-avatar {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      color: white;
      font-size: 1.5rem;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }

    .user-info h2 {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: white;
    }

    .user-info p {
      color: var(--text-light);
      font-size: 0.9rem;
      font-weight: 500;
    }

    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 1rem 1rem;
      border-radius: 12px;
      color: var(--text-light);
      text-decoration: none;
      font-weight: 500;
      position: relative;
      overflow: hidden;
      /* SIN TRANSICIONES PARA CAMBIOS DE MÓDULO */
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
      transition: transform 0.2s ease; /* Solo transición suave para hover */
    }

    .nav-item i {
      width: 20px;
      text-align: center;
      font-size: 1.1rem;
      color: var(--text-light);
      transition: color 0.2s ease; /* Solo transición suave para hover */
    }

    .nav-item:hover {
      background: #374151;
      color: white;
    }

    .nav-item:hover::before {
      transform: scaleY(1);
    }

    .nav-item:hover i {
      color: var(--primary-light);
    }

    .nav-item.active {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
      /* SIN TRANSICIÓN PARA CAMBIOS DE ESTADO ACTIVO */
    }

    .nav-item.active i {
      color: white;
    }

    .nav-item.active::before {
      display: none;
    }

    /* Content Area Elegante - CON MARGEN PARA EL SIDEBAR FIJO */
    .content {
      flex: 1;
      padding: 2rem;
      background: var(--bg-secondary);
      overflow-y: auto;
      margin-left: 280px;
    }

    .main-content {
      background: var(--bg-primary);
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 2rem;
      min-height: calc(100vh - 180px);
      border: 1px solid var(--border-color);
    }

    /* Footer */
    footer {
      text-align: center;
      margin-top: 2rem;
      padding: 1.5rem;
      font-size: 0.9rem;
      color: var(--text-secondary);
      border-top: 1px solid var(--border-color);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .wrapper {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
        min-height: auto;
        padding: 1.5rem;
        position: relative;
        top: 0;
      }
      
      .sidebar-nav {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.5rem;
      }
      
      .nav-item {
        flex: 1;
        min-width: 140px;
        justify-content: center;
        text-align: center;
      }
      
      .content {
        padding: 1.5rem;
        margin-left: 0;
      }
      
      .header {
        padding: 1rem 1.5rem;
      }
      
      .header h1 {
        font-size: 1.3rem;
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
        padding: 1rem;
      }
      
      .header h1 {
        font-size: 1.2rem;
      }
      
      .content {
        padding: 1rem;
      }
      
      .main-content {
        padding: 1.5rem;
      }
    }

    /* Scrollbar personalizado */
    ::-webkit-scrollbar {
      width: 6px;
    }

    ::-webkit-scrollbar-track {
      background: var(--bg-secondary);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--primary-light);
      border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: var(--primary);
    }
  </style>
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

  <script>
    // Script para manejar el estado activo del menú SIN TRANSICIONES MOLESTAS
    document.addEventListener('DOMContentLoaded', function() {
      // Remover clase active de todos los items primero
      const navItems = document.querySelectorAll('.nav-item');
      navItems.forEach(item => item.classList.remove('active'));
      
      // Determinar qué página está activa basado en la URL actual
      const currentPath = window.location.pathname;
      let activeNavItem = null;
      
      if (currentPath.includes('/agenda')) {
        activeNavItem = document.querySelector('a[href*="/agenda"]');
      } else if (currentPath.includes('/citas')) {
        activeNavItem = document.querySelector('a[href*="/citas"]');
      } else if (currentPath.includes('/clientes')) {
        activeNavItem = document.querySelector('a[href*="/clientes"]');
      } else if (currentPath.includes('/servicios')) {
        activeNavItem = document.querySelector('a[href*="/servicios"]');
      } else {
        // Por defecto, Dashboard está activo
        activeNavItem = document.querySelector('a[href*="/dashboard"]');
      }
      
      if (activeNavItem) {
        // Aplicar la clase active sin transición
        activeNavItem.classList.add('active');
      }
      
      // Prevenir transiciones molestas al hacer clic en los enlaces
      navItems.forEach(item => {
        item.addEventListener('click', function(e) {
          // Deshabilitar temporalmente las transiciones para cambios de estado activo
          navItems.forEach(nav => {
            nav.style.transition = 'none';
          });
          
          // Restaurar transiciones después de un breve tiempo
          setTimeout(() => {
            navItems.forEach(nav => {
              nav.style.transition = '';
            });
          }, 10);
        });
      });
    });

    // Efectos interactivos adicionales
    document.addEventListener('DOMContentLoaded', function() {
      // Añadir efecto de carga progresiva a los items del menú
      const navItems = document.querySelectorAll('.nav-item');
      navItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
      });

      // Efecto de parpadeo suave para el avatar
      const userAvatar = document.querySelector('.user-avatar');
      if (userAvatar) {
        userAvatar.style.animation = 'pulse 3s infinite';
      }
    });

  </script>

</body>
</html>