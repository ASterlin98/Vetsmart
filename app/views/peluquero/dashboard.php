
<div class="peluquero-dashboard">
  <!-- Header Section -->
  <div class="header-section mb-5">
    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <div class="section-icon">
          <i class="fas fa-cut"></i>
        </div>
        <div>
          <h1 class="page-title mb-1">Mi Panel de Peluquería</h1>
          <p class="page-subtitle" id="fechaHoy"></p>
        </div>
      </div>
      <div class="header-actions d-flex gap-2">
        <a class="btn btn-primary" href="/vetsmart/peluquero/agenda">
          <i class="fas fa-calendar-alt me-2"></i>Ver Agenda
        </a>
        <a class="btn btn-success" href="/vetsmart/peluquero/agenda/agendar">
          <i class="fas fa-plus me-2"></i>Agendar Cita
        </a>
      </div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="stats-overview mb-5">
    <div class="row g-3">
      <div class="col-lg-3 col-md-6">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?= (int)($citasHoy ?? 0) ?></div>
            <div class="stat-label">Citas Hoy</div>
            <div class="stat-hint">Agendadas para hoy</div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-spinner"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?= (int)($enProceso ?? 0) ?></div>
            <div class="stat-label">En Atención</div>
            <div class="stat-hint">Actualmente en proceso</div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?= (int)($pendientes ?? 0) ?></div>
            <div class="stat-label">Por Atender</div>
            <div class="stat-hint">Próximas citas</div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?= (int)($completadasHoy ?? 0) ?></div>
            <div class="stat-label">Completadas Hoy</div>
            <div class="stat-hint">Servicios finalizados</div>
          </div>
        </div>
      </div>
    </div>
  </div>  

  <!-- Citas de Hoy y Próximas -->
  <div class="row g-4 mb-5">
    <!-- Próximas Citas Hoy -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-light border-bottom">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
              <i class="fas fa-list text-primary me-2"></i>Próximas Citas
            </h6>
            <span class="badge bg-primary"><?= count($proximas ?? []) ?></span>
          </div>
        </div>
        <div class="card-body p-0">
          <?php 
            // Debug: mostrar cuantas citas hay
            $totalProximas = count($proximas ?? []);
          ?>
          <?php if (!empty($proximas ?? [])): ?>
            <div class="appointments-timeline">
              <?php foreach (($proximas ?? []) as $index => $cita): ?>
                <?php 
                  $estado = strtolower((string)($cita['estado'] ?? 'pendiente'));
                  $badgeClass = $estado === 'pendiente' ? 'warning' : ($estado === 'confirmada' ? 'info' : ($estado === 'completada' ? 'success' : 'secondary'));
                  $icono = $estado === 'pendiente' ? 'clock' : ($estado === 'confirmada' ? 'play' : ($estado === 'completada' ? 'check' : 'question'));
                ?>
                <div class="appointment-timeline-item <?= $index !== count($proximas) - 1 ? 'border-bottom' : '' ?>">
                  <div class="appointment-timeline-marker">
                    <div class="timeline-dot bg-<?= $badgeClass ?>">
                      <i class="fas fa-<?= $icono ?>"></i>
                    </div>
                  </div>
                  <div class="appointment-timeline-content">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <h6 class="mb-1">
                          <i class="fas fa-paw me-2 text-muted"></i>
                          <strong><?= htmlspecialchars($cita['mascota'] ?? 'Sin mascota') ?></strong>
                        </h6>
                        <p class="text-muted mb-1">
                          <small><i class="fas fa-user me-1"></i><?= htmlspecialchars($cita['cliente'] ?? 'Sin cliente') ?></small>
                        </p>
                      </div>
                      <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($estado) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <p class="mb-1">
                          <i class="fas fa-cut me-2 text-primary"></i>
                          <strong><?= htmlspecialchars($cita['servicio'] ?? 'Servicio') ?></strong>
                        </p>
                        <small class="text-muted">
                          <i class="fas fa-clock me-1"></i><?= htmlspecialchars($cita['hora'] ?? '00:00') ?> | 
                          <i class="fas fa-calendar me-1"></i><?= htmlspecialchars($cita['fecha'] ?? date('Y-m-d')) ?>
                        </small>
                      </div>
                      <a href="/vetsmart/peluquero/citas" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-arrow-right"></i>
                      </a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-5 px-3">
              <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
              <p class="text-muted mb-0">No hay citas próximas</p>
              <a href="/vetsmart/peluquero/agenda/agendar" class="btn btn-sm btn-primary mt-3">
                <i class="fas fa-plus me-1"></i>Agendar Nueva Cita
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Panel de Acciones Rápidas -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-bottom">
          <h6 class="mb-0">
            <i class="fas fa-bolt text-warning me-2"></i>Acciones Rápidas
          </h6>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="/vetsmart/peluquero/agenda/agendar" class="btn btn-primary d-flex align-items-center justify-content-between">
              <span><i class="fas fa-calendar-plus me-2"></i>Nueva Cita</span>
              <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/vetsmart/peluquero/agenda" class="btn btn-outline-primary d-flex align-items-center justify-content-between">
              <span><i class="fas fa-calendar-alt me-2"></i>Ver Agenda</span>
              <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/vetsmart/peluquero/clientes" class="btn btn-outline-secondary d-flex align-items-center justify-content-between">
              <span><i class="fas fa-users me-2"></i>Clientes</span>
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Tip Card -->
      <div class="card border-0 shadow-sm bg-gradient-info">
        <div class="card-body text-white">
          <h6 class="card-title mb-2">
            <i class="fas fa-lightbulb me-2"></i>Consejo del Día
          </h6>
          <p class="small mb-0">Mantén un registro detallado de cada mascota para ofrecer mejor servicio personalizado a tus clientes.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.peluquero-dashboard {
  padding: 1.5rem 0;
}

/* Header */
.header-section {
  padding: 0 0.5rem;
}

.section-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  color: white;
  font-size: 1.25rem;
  box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
}

.page-title {
  color: #1e293b;
  font-weight: 700;
  font-size: 1.75rem;
  margin-bottom: 0.25rem;
}

.page-subtitle {
  color: #64748b;
  font-size: 0.95rem;
  margin: 0;
}

/* Stats Cards */
.stats-overview {
  padding: 0 0.5rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s ease;
  height: 100%;
}

.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1;
  margin-bottom: 0.25rem;
}

.stat-label {
  color: #64748b;
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.stat-hint {
  font-size: 0.75rem;
  color: #94a3b8;
}

/* Card Styles */
.card {
  border-radius: 12px !important;
  border: 1px solid #e2e8f0 !important;
}

.card-header {
  background: #f8fafc !important;
  border-bottom: 1px solid #e2e8f0 !important;
  padding: 1.25rem 1.5rem !important;
  border-radius: 12px 12px 0 0 !important;
}

.card-body {
  padding: 1.5rem !important;
}

/* Appointments Timeline */
.appointments-timeline {
  display: flex;
  flex-direction: column;
}

.appointment-timeline-item {
  display: grid;
  grid-template-columns: 60px 1fr;
  gap: 1rem;
  padding: 1.25rem 1.5rem;
  position: relative;
}

.appointment-timeline-marker {
  display: flex;
  justify-content: center;
  position: relative;
}

.timeline-dot {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.appointment-timeline-item:not(:last-child)::after {
  content: '';
  position: absolute;
  left: 29px;
  top: 60px;
  width: 2px;
  height: calc(100% + 25px);
  background: linear-gradient(to bottom, rgba(6, 182, 212, 0.3), transparent);
}

.appointment-timeline-content {
  padding: 0.5rem 0;
}

.appointment-timeline-content h6 {
  color: #1e293b;
  font-weight: 600;
}

.appointment-timeline-content p {
  margin: 0;
  color: #64748b;
}

/* Buttons */
.btn-primary {
  background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
  border: none;
  font-weight: 600;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #0891b2 0%, #0284c7 100%);
}

.btn-outline-primary {
  border: 2px solid #06b6d4;
  color: #06b6d4;
}

.btn-outline-primary:hover {
  background: #06b6d4;
  color: white;
}

.btn-outline-secondary {
  border: 2px solid #cbd5e1;
  color: #64748b;
}

.btn-outline-secondary:hover {
  background: #f1f5f9;
  border-color: #94a3b8;
}

/* Badge Styles */
.badge {
  padding: 0.35rem 0.75rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Gradient Info Card */
.bg-gradient-info {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

/* Responsive */
@media (max-width: 768px) {
  .header-section .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 1rem;
  }

  .header-actions {
    width: 100%;
  }

  .header-actions .btn {
    width: 100%;
  }

  .stat-card {
    flex-direction: column;
    text-align: center;
  }

  .appointment-timeline-item {
    grid-template-columns: 50px 1fr;
    padding: 1rem;
  }

  .timeline-dot {
    width: 35px;
    height: 35px;
    font-size: 0.9rem;
  }

  .appointment-timeline-item::after {
    left: 24px;
  }

  .card-body {
    padding: 1rem !important;
  }
}

@media (max-width: 576px) {
  .page-title {
    font-size: 1.5rem;
  }

  .stat-number {
    font-size: 1.75rem;
  }

  .appointment-timeline-item {
    grid-template-columns: 45px 1fr;
  }
}
</style>

<script>
// Mostrar fecha actual
document.addEventListener('DOMContentLoaded', function() {
  const fechaElement = document.getElementById('fechaHoy');
  if (fechaElement) {
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormato = new Date().toLocaleDateString('es-ES', opciones);
    fechaElement.textContent = 'Hoy, ' + fechaFormato.charAt(0).toUpperCase() + fechaFormato.slice(1);
  }

  // Animaciones
  const statCards = document.querySelectorAll('.stat-card');
  statCards.forEach((card, index) => {
    card.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s forwards`;
    card.style.opacity = '0';
  });
});

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</script>
