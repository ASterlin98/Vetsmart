<h1 class="fw-bold">⚙️ Dashboard Administrador</h1>

<div class="row g-4">
  <!-- Empleados -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 h-100 hover-card bg-light">
      <div class="card-body text-center">
        <div class="display-5 text-primary mb-2">👥</div>
        <h5 class="fw-bold">Empleados</h5>
        <p class="text-muted">Gestiona recepcionistas, veterinarios y peluqueros.</p>
        <a href="<?= BASE ?>/admin/empleados" class="btn btn-outline-primary btn-sm">Ir a Empleados</a>
      </div>
    </div>
  </div>

  <!-- Agenda -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 h-100 hover-card bg-light">
      <div class="card-body text-center">
        <div class="display-5 text-success mb-2">📅</div>
        <h5 class="fw-bold">Agenda General</h5>
        <p class="text-muted">Supervisa la agenda completa de la clínica.</p>
        <a href="<?= BASE ?>/admin/agenda" class="btn btn-outline-success btn-sm">Ver Agenda</a>
      </div>
    </div>
  </div>

  <!-- Citas -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 h-100 hover-card bg-light">
      <div class="card-body text-center">
        <div class="display-5 text-warning mb-2">📌</div>
        <h5 class="fw-bold">Gestión de Citas</h5>
        <p class="text-muted">Controla las citas de clientes y mascotas.</p>
        <a href="<?= BASE ?>/admin/citas" class="btn btn-outline-warning btn-sm">Ir a Citas</a>
      </div>
    </div>
  </div>

  <!-- Clientes -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 h-100 hover-card bg-light">
      <div class="card-body text-center">
        <div class="display-5 text-info mb-2">🐶</div>
        <h5 class="fw-bold">Clientes</h5>
        <p class="text-muted">Administra clientes y sus mascotas.</p>
        <a href="<?= BASE ?>/admin/clientes" class="btn btn-outline-info btn-sm">Ver Clientes</a>
      </div>
    </div>
  </div>

  <!-- Inventario -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 h-100 hover-card bg-light">
      <div class="card-body text-center">
        <div class="display-5 text-secondary mb-2">📦</div>
        <h5 class="fw-bold">Inventario</h5>
        <p class="text-muted">Gestiona productos y medicamentos.</p>
        <a href="<?= BASE ?>/admin/inventario" class="btn btn-outline-secondary btn-sm">Ir a Inventario</a>
      </div>
    </div>
  </div>

  <!-- Finanzas -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 h-100 hover-card bg-light">
      <div class="card-body text-center">
        <div class="display-5 text-danger mb-2">💰</div>
        <h5 class="fw-bold">Finanzas</h5>
        <p class="text-muted">Controla ingresos, egresos y balances.</p>
        <a href="<?= BASE ?>/admin/finanzas" class="btn btn-outline-danger btn-sm">Ver Finanzas</a>
      </div>
    </div>
  </div>

  <!-- Reportes -->
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm border-0 h-100 hover-card bg-light">
      <div class="card-body text-center">
        <div class="display-5 text-dark mb-2">📊</div>
        <h5 class="fw-bold">Reportes</h5>
        <p class="text-muted">Reportes detallados del rendimiento.</p>
        <a href="<?= BASE ?>/admin/reportes" class="btn btn-outline-dark btn-sm">Ver Reportes</a>
      </div>
    </div>
  </div>
</div>

<!-- Estilos extra -->
<style>
  .hover-card {
    transition: transform .2s, box-shadow .2s;
    border-radius: 12px;
  }
  .hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
  }
</style>
