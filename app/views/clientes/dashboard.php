<div class="container">
  <h2 class="mb-4">Dashboard Cliente</h2>
  <p class="lead">Aquí puedes gestionar tus citas, mascotas y pagos.</p>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">📅 Mis Citas</h5>
          <p class="card-text">Consulta y administra tus próximas citas veterinarias.</p>
          <a href="<?= BASE ?>/cliente/citas" class="btn btn-primary">Ver Citas</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">🐶 Mis Mascotas</h5>
          <p class="card-text">Accede al perfil y cuidados de tus mascotas.</p>
          <a href="<?= BASE ?>/cliente/mascotas" class="btn btn-primary">Ver Mascotas</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">💳 Pagos</h5>
          <p class="card-text">Consulta el historial de pagos y facturas.</p>
          <a href="<?= BASE ?>/cliente/pagos" class="btn btn-primary">Ver Pagos</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">📖 Historial Clínico</h5>
          <p class="card-text">Revisa el historial clínico de tus mascotas.</p>
          <a href="<?= BASE ?>/cliente/historial" class="btn btn-primary">Ver Historial</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">📊 Reportes</h5>
          <p class="card-text">Consulta reportes sobre tus visitas y servicios usados.</p>
          <a href="<?= BASE ?>/cliente/reportes" class="btn btn-primary">Ver Reportes</a>
        </div>
      </div>
    </div>
  </div>
</div>
