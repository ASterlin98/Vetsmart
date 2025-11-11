<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VetSmart - Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-primary: #ffffff;
      --bg-secondary: #f8fafc;
      --bg-card: #ffffff;
      --bg-hover: #f1f5f9;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --text-muted: #94a3b8;
      --accent-primary: #06b6d4;
      --accent-secondary: #0ea5e9;
      --border-color: #e2e8f0;
      --shadow-color: rgba(0, 0, 0, 0.08);
      --shadow-hover: rgba(0, 0, 0, 0.12);
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
      padding: 0;
    }

    .dashboard-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 2rem;
    }

    .dashboard-welcome {
      background: var(--bg-primary);
      border-radius: 20px;
      padding: 2.5rem;
      box-shadow: 0 4px 20px var(--shadow-color);
      border: 1px solid var(--border-color);
      margin-bottom: 2rem;
    }

    .welcome-header {
      padding-bottom: 1.5rem;
      border-bottom: 1px solid var(--border-color);
    }

    .welcome-icon {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
      color: white;
      font-size: 1.5rem;
      box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
    }

    .welcome-header h1 {
      color: var(--text-primary);
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .welcome-header .text-muted {
      color: var(--text-muted) !important;
      font-size: 1.1rem;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .dashboard-card {
      background: var(--bg-card);
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: 0 4px 20px var(--shadow-color);
      border: 1px solid var(--border-color);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
      height: 100%;
      position: relative;
      overflow: hidden;
    }

    .dashboard-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px var(--shadow-hover);
      border-color: var(--accent-primary);
      background: var(--bg-hover);
    }

    .dashboard-card:hover::before {
      transform: scaleX(1);
    }

    .card-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      border: 1px solid rgba(6, 182, 212, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--accent-primary);
      font-size: 1.25rem;
      margin-bottom: 1rem;
      transition: all 0.3s ease;
    }

    .dashboard-card:hover .card-icon {
      background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
      color: white;
      transform: scale(1.05);
    }

    .card-content {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .card-content h3 {
      font-size: 1.2rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.75rem;
    }

    .card-content p {
      color: var(--text-secondary);
      font-size: 0.95rem;
      line-height: 1.5;
      margin-bottom: 1.5rem;
      flex: 1;
    }

    .card-stats {
      display: flex;
      align-items: baseline;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      padding: 0.75rem;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 10px;
    }

    .stat-number {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--accent-primary);
    }

    .stat-label {
      font-size: 0.85rem;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .card-link {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.75rem 1rem;
      background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
      color: white;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.9rem;
      transition: all 0.3s ease;
      margin-top: auto;
      border: none;
    }

    .card-link:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4);
      color: white;
    }

    .card-link i {
      transition: transform 0.3s ease;
    }

    .card-link:hover i {
      transform: translateX(3px);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .dashboard-container {
        padding: 1rem;
      }
      
      .dashboard-welcome {
        padding: 2rem;
        border-radius: 16px;
      }
      
      .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .welcome-header .d-flex {
        flex-direction: column;
        text-align: center;
      }
      
      .welcome-icon {
        margin-right: 0;
        margin-bottom: 1rem;
      }
    }

    @media (max-width: 480px) {
      .dashboard-card {
        padding: 1.25rem;
      }
      
      .card-stats {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 0.25rem;
      }
      
      .welcome-header h1 {
        font-size: 1.5rem;
      }
    }

    /* Efectos sutiles adicionales */
    .dashboard-card {
      backdrop-filter: blur(10px);
    }

    .welcome-icon {
      backdrop-filter: blur(10px);
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <div class="dashboard-welcome">
      <div class="welcome-header mb-4">
        <div class="d-flex align-items-center">
          <div class="welcome-icon">
            <i class="fas fa-user-circle"></i>
          </div>
          <div>
            <h1 class="h3 mb-1">Bienvenido, <?= htmlspecialchars($nombre ?? 'Usuario') ?> <?= htmlspecialchars($apellido ?? '') ?></h1>
            <p class="text-muted mb-0">Este es tu panel de control personal</p>
          </div>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="dashboard-card">
          <div class="card-icon">
            <i class="fas fa-paw"></i>
          </div>
          <div class="card-content">
            <h3>Mis Mascotas</h3>
            <p>Consulta y gestiona la información de tus mascotas registradas en el sistema</p>
            <div class="card-stats">
              <span class="stat-number"><?= (int)($mascotasTotal ?? 0) ?></span>
              <span class="stat-label">mascotas activas</span>
            </div>
            <a href="/vetsmart/cliente/mascotas" class="card-link">
              <span>Gestionar mascotas</span>
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <div class="dashboard-card">
          <div class="card-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="card-content">
            <h3>Mis Citas</h3>
            <p>Revisa el estado de tus citas y próximas atenciones programadas</p>
            <div class="card-stats">
              <span class="stat-number"><?= (int)($proximasCitas ?? 0) ?></span>
              <span class="stat-label">
                <?php if (isset($proximaCitaFecha) && $proximaCitaFecha): ?>
                  próxima: <?= htmlspecialchars($proximaCitaFecha) ?>
                <?php else: ?>
                  sin próximas
                <?php endif; ?>
              </span>
            </div>
            <a href="/vetsmart/cliente/citas" class="card-link">
              <span>Ver citas</span>
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <div class="dashboard-card">
          <div class="card-icon">
            <i class="fas fa-id-badge"></i>
          </div>
          <div class="card-content">
            <h3>Mi Perfil</h3>
            <p>Consulta y actualiza tus datos personales registrados en el sistema</p>
            <div class="card-stats">
              <span class="stat-number"><?= (int)($perfilCompleto ?? 0) ?>%</span>
              <span class="stat-label">perfil completo</span>
            </div>
            <a href="/vetsmart/cliente/perfil" class="card-link">
              <span>Ver perfil</span>
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <div class="dashboard-card">
          <div class="card-icon">
            <i class="fas fa-file-medical"></i>
          </div>
          <div class="card-content">
            <h3>Historial Clínico</h3>
            <p>Accede al historial médico completo de todas tus mascotas</p>
            <div class="card-stats">
              <span class="stat-number"><?= (int)($consultasTotales ?? 0) ?></span>
              <span class="stat-label">consultas totales</span>
            </div>
            <a href="/vetsmart/cliente/historial" class="card-link">
              <span>Ver historial</span>
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
        
        <div class="dashboard-card">
          <div class="card-icon">
            <i class="fas fa-chart-bar"></i>
          </div>
          <div class="card-content">
            <h3>Reportes</h3>
            <p>Visualiza reportes y estadísticas del cuidado de tus mascotas</p>
            <div class="card-stats">
              <span class="stat-number"><?= (int)($reportesDisponibles ?? 1) ?></span>
              <span class="stat-label">reportes disponibles</span>
            </div>
            <a href="/vetsmart/cliente/reportes" class="card-link">
              <span>Ver reportes</span>
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Efectos interactivos adicionales
    document.addEventListener('DOMContentLoaded', function() {
      // Añadir efecto de carga progresiva a las tarjetas
      const cards = document.querySelectorAll('.dashboard-card');
      cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
      });

      // Efecto de parpadeo suave para números importantes
      const statNumbers = document.querySelectorAll('.stat-number');
      statNumbers.forEach(number => {
        if (parseInt(number.textContent) > 0) {
          number.style.animation = 'pulse 2s infinite';
        }
      });
    });

    // Añadir animación de pulso
    const style = document.createElement('style');
    style.textContent = `
      @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
      }
      
      .dashboard-card {
        animation: fadeInUp 0.6s ease-out both;
      }
      
      @keyframes fadeInUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>