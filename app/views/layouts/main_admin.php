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
            <a href="/vetsmart/admin/dashboard">🏠 Dashboard</a>
            <a href="/vetsmart/admin/empleados">👥 Gestión de Empleados</a>
            <a href="/vetsmart/admin/agenda">📅 Agenda General</a>
            <a href="/vetsmart/admin/horarios" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/horarios') !== false ? 'active' : '' ?>">⏰ Gestión de Horarios</a>
            <a href="/vetsmart/admin/clientes">🐶 Gestión de Clientes</a>
            <a href="/vetsmart/admin/servicios" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/servicios') !== false ? 'active' : '' ?>">🛠️ Gestión de Servicios</a>
            <a href="/vetsmart/admin/finanzas">💰 Finanzas</a>
            <a href="/vetsmart/admin/reportes">📊 Reportes</a>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para verificar disponibilidad del empleado -->
<script>
function verificarDisponibilidad(empleadoId, fecha, hora) {
    fetch(`/vetsmart/api/disponibilidad?empleado_id=${empleadoId}&fecha=${fecha}&hora=${hora}`)
        .then(res => res.json())
        .then(data => {
            if (data.disponible) {
                alert("✅ El empleado está disponible.");
            } else {
                alert("⚠️ El empleado NO está disponible en ese horario.");
            }
        })
        .catch(err => console.error("Error consultando disponibilidad", err));
}
</script>

</body>
</html>
