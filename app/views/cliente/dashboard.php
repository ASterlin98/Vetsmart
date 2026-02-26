<?php
// Variables de estadísticas
$mascotasTotal = $mascotasTotal ?? 0;
$proximasCitas = $proximasCitas ?? 0;
$proximaCitaFecha = $proximaCitaFecha ?? null;
$perfilCompleto = $perfilCompleto ?? 0;
$consultasTotales = $consultasTotales ?? 0;
$reportesDisponibles = $reportesDisponibles ?? 1;
?>

<div class="dashboard-cliente">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="page-title mb-2">
        <i class="fas fa-home me-2 text-primary"></i>Panel Principal
      </h1>
      <p class="text-muted mb-0">Bienvenido, <?= htmlspecialchars($nombre ?? 'Usuario') ?> <?= htmlspecialchars($apellido ?? '') ?></p>
    </div>
  </div>

  <!-- Main Stats Grid -->
  <div class="row g-3 mb-4">
    <!-- Mascotas -->
    <div class="col-xl-3 col-md-6">
      <a href="/vetsmart/cliente/mascotas" class="text-decoration-none">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body text-center p-4">
            <div class="stat-icon mb-3">
              <i class="fas fa-paw fa-2x text-info"></i>
            </div>
            <h3 class="stat-number text-dark mb-2"><?= (int)$mascotasTotal ?></h3>
            <p class="stat-label text-muted mb-0">Mis Mascotas</p>
            <small class="text-muted">Registradas</small>
          </div>
        </div>
      </a>
    </div>

    <!-- Próximas Citas -->
    <div class="col-xl-3 col-md-6">
      <a href="/vetsmart/cliente/citas" class="text-decoration-none">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body text-center p-4">
            <div class="stat-icon mb-3">
              <i class="fas fa-calendar-check fa-2x text-warning"></i>
            </div>
            <h3 class="stat-number text-dark mb-2"><?= (int)$proximasCitas ?></h3>
            <p class="stat-label text-muted mb-0">Citas Programadas</p>
            <small class="text-muted">
                <?php if ($proximaCitaFecha): ?>
                    Próxima: <?= htmlspecialchars($proximaCitaFecha) ?>
                <?php else: ?>
                    No hay citas pendientes
                <?php endif; ?>
            </small>
          </div>
        </div>
      </a>
    </div>

    <!-- Historial -->
    <div class="col-xl-3 col-md-6">
      <a href="/vetsmart/cliente/historial" class="text-decoration-none">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body text-center p-4">
            <div class="stat-icon mb-3">
              <i class="fas fa-file-medical fa-2x text-success"></i>
            </div>
            <h3 class="stat-number text-dark mb-2"><?= (int)$consultasTotales ?></h3>
            <p class="stat-label text-muted mb-0">Consultas</p>
            <small class="text-muted">Historial médico</small>
          </div>
        </div>
      </a>
    </div>

    <!-- Reportes -->
    <div class="col-xl-3 col-md-6">
      <a href="/vetsmart/cliente/reportes" class="text-decoration-none">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body text-center p-4">
            <div class="stat-icon mb-3">
              <i class="fas fa-chart-bar fa-2x text-primary"></i>
            </div>
            <h3 class="stat-number text-dark mb-2"><?= (int)$reportesDisponibles ?></h3>
            <p class="stat-label text-muted mb-0">Reportes</p>
            <small class="text-muted">Disponibles</small>
          </div>
        </div>
      </a>
    </div>
  </div>

  <!-- Quick Actions Section -->
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title mb-3 text-secondary"><i class="fas fa-info-circle me-2"></i>Estado de tu Perfil</h5>
            <div class="d-flex align-items-center mb-3">
                <div class="progress flex-grow-1" style="height: 20px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= (int)$perfilCompleto ?>%;" aria-valuenow="<?= (int)$perfilCompleto ?>" aria-valuemin="0" aria-valuemax="100"><?= (int)$perfilCompleto ?>%</div>
                </div>
                <span class="ms-3 fw-bold text-muted"><?= (int)$perfilCompleto ?>% Completado</span>
            </div>
            <p class="text-muted small">Mantén tu perfil actualizado para una mejor comunicación con la veterinaria.</p>
            <a href="/vetsmart/cliente/perfil" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-user-edit me-1"></i>Editar Perfil
            </a>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                <div class="mb-3">
                    <i class="fas fa-calendar-plus fa-3x"></i>
                </div>
                <h5 class="mb-2">¿Necesitas una cita?</h5>
                <p class="mb-4 opacity-75">Agenda una nueva consulta para tu mascota rapida y facilmente.</p>
                <a href="/vetsmart/cliente/citas" class="btn btn-light text-primary fw-bold w-100">
                    Agendar Cita
                </a>
            </div>
        </div>
    </div>
  </div>
</div>

<style>
.page-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #2c3e50;
}
.stat-card {
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.stat-icon {
    width: 60px;
    height: 60px;
    background-color: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
</style>