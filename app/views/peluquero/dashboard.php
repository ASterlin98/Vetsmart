
<div class="peluquero-dashboard">
  <!-- Header Section -->
  <div class="header-section mb-5">
    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <div class="section-icon">
          <i class="fas fa-cut"></i>
        </div>
        <div>
          <h1 class="page-title mb-1">Dashboard de Peluquería</h1>
          <p class="page-subtitle">Resumen de actividades y citas programadas</p>
        </div>
      </div>
      <div class="header-actions d-flex gap-2">
        <a class="btn btn-primary" href="/vetsmart/peluquero/agenda">
          <i class="fas fa-calendar-alt me-2"></i>Ver Agenda
        </a>
        <a class="btn btn-outline-primary" href="/vetsmart/peluquero/servicios">
          <i class="fas fa-concierge-bell me-2"></i>Servicios
        </a>
      </div>
    </div>
  </div>

  <!-- Stats Overview -->
  <div class="stats-overview mb-5">
    <div class="row g-4">
      <div class="col-md-3">
        <a class="stat-card d-block text-decoration-none" href="/vetsmart/peluquero/citas?desde=<?= date('Y-m-d') ?>&hasta=<?= date('Y-m-d') ?>" title="Ver citas de hoy">
          <div class="stat-icon bg-primary">
            <i class="fas fa-calendar-day"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?= (int)($citasHoy ?? 0) ?></div>
            <div class="stat-label">Citas de Hoy</div>
            <div class="stat-trend text-success">
              <i class="fas fa-paw me-1"></i>Programadas
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-3">
        <a class="stat-card d-block text-decoration-none" href="/vetsmart/peluquero/citas?estado=confirmada" title="Ver citas en proceso">
          <div class="stat-icon bg-info">
            <i class="fas fa-spinner"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?= (int)($enProceso ?? 0) ?></div>
            <div class="stat-label">En Proceso</div>
            <div class="stat-trend text-info">
              <i class="fas fa-clock me-1"></i>Activas
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-3">
        <a class="stat-card d-block text-decoration-none" href="/vetsmart/peluquero/citas?estado=pendiente" title="Ver citas pendientes">
          <div class="stat-icon bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?= (int)($pendientes ?? 0) ?></div>
            <div class="stat-label">Pendientes</div>
            <div class="stat-trend text-warning">
              <i class="fas fa-hourglass-half me-1"></i>Por atender
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-3">
        <a class="stat-card d-block text-decoration-none" href="/vetsmart/peluquero/citas?estado=completada" title="Ver citas completadas hoy">
          <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-content">
            <div class="stat-number"><?= (int)($completadasHoy ?? 0) ?></div>
            <div class="stat-label">Completadas Hoy</div>
            <div class="stat-trend text-success">
              <i class="fas fa-trophy me-1"></i>Finalizadas
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>  

  <!-- Próximas Citas (estilo recepción adaptado) -->
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0">
              <i class="fas fa-calendar-day text-success me-2"></i>Próximas Citas
            </h6>
            <a href="/vetsmart/peluquero/agenda" class="btn btn-sm btn-outline-success">
              <i class="fas fa-external-link-alt me-1"></i>Ver Agenda Completa
            </a>
          </div>

          <?php if (!empty($proximas ?? [])): ?>
            <div class="appointments-list">
              <?php foreach (($proximas ?? []) as $p): ?>
                <?php 
                  $estadoRaw = strtolower((string)($p['estado'] ?? 'pendiente'));
                  $estado = $estadoRaw === 'confirmada' ? 'en_proceso' : ($estadoRaw === 'completada' ? 'completada' : $estadoRaw);
                ?>
                <div class="appointment-item">
                  <div class="appointment-time">
                    <span class="time-badge"><?= htmlspecialchars($p['hora'] ?? '-') ?></span>
                    <small class="text-muted d-block"><?= htmlspecialchars($p['fecha'] ?? '-') ?></small>
                  </div>
                  <div class="appointment-details">
                    <div class="appointment-main">
                      <strong><?= htmlspecialchars($p['mascota'] ?? '-') ?></strong>
                      <span class="text-muted">con</span>
                      <strong><?= htmlspecialchars($p['cliente'] ?? '-') ?></strong>
                    </div>
                    <div class="appointment-meta">
                      <span class="service"><?= htmlspecialchars($p['servicio'] ?? '-') ?></span>
                    </div>
                  </div>
                  <div class="appointment-status">
                    <span class="status-badge status-<?= $estado === 'pendiente' ? 'warning' : ($estado === 'en_proceso' ? 'info' : ($estado === 'completada' ? 'success' : 'secondary')) ?>">
                      <?= htmlspecialchars(ucfirst($estado)) ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
              <p class="text-muted mb-0">No hay citas programadas</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Acceso rápido -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h6 class="card-title mb-3"><i class="fas fa-bolt text-warning me-2"></i>Accesos Rápidos</h6>
          <div class="d-grid gap-2">
            <a href="/vetsmart/peluquero/agenda" class="btn btn-outline-success d-flex align-items-center justify-content-between py-2">
              <span><i class="fas fa-calendar-alt me-2"></i>Ver Agenda</span>
              <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/vetsmart/peluquero/citas" class="btn btn-outline-primary d-flex align-items-center justify-content-between py-2">
              <span><i class="fas fa-scissors me-2"></i>Ver Citas</span>
              <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/vetsmart/peluquero/servicios" class="btn btn-outline-info d-flex align-items-center justify-content-between py-2">
              <span><i class="fas fa-concierge-bell me-2"></i>Servicios</span>
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.peluquero-dashboard {
  padding: 1.5rem 0;
}

/* Header Section */
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
}

.page-title {
  color: #1e293b;
  font-weight: 700;
  font-size: 1.75rem;
  margin-bottom: 0.25rem;
}

.page-subtitle {
  color: #64748b;
  font-size: 1rem;
  margin: 0;
}

.header-actions .btn {
  border-radius: 8px;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
}

/* Stats Overview */
.stats-overview {
  padding: 0 0.5rem;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s ease;
  height: 100%;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
  border-color: #e2e8f0;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
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

.stat-trend {
  font-size: 0.75rem;
  font-weight: 500;
}

/* Report Actions */
.report-actions .card {
  border-radius: 12px;
  border-left: 4px solid #10b981;
}

.report-actions .card-body {
  padding: 1rem 1.5rem;
}

/* Citas Section */
.citas-section .card {
  border-radius: 16px;
  border-left: 4px solid #06b6d4;
}

.card-header {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
  border-bottom: 1px solid #e2e8f0;
  padding: 1.25rem 1.5rem;
}

.card-header .card-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: rgba(6, 182, 212, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  color: #06b6d4;
  font-size: 1rem;
}

.card-body {
  padding: 1.5rem;
}

/* Table Styles */
.table-container {
  border-radius: 12px;
  overflow: hidden;
}

.table-header {
  background: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
}

.table-header th {
  border: none;
  padding: 1rem 0.75rem;
  font-weight: 600;
  color: #475569;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.table-row {
  transition: background-color 0.2s ease;
  border-bottom: 1px solid #f1f5f9;
}

.table-row:hover {
  background-color: #f8fafc;
}

.table td {
  padding: 1rem 0.75rem;
  border: none;
  vertical-align: middle;
}

/* Cell Styles */
.datetime-cell .date-main {
  font-weight: 500;
  color: #1e293b;
  font-size: 0.95rem;
}

.datetime-cell .time-sub {
  font-size: 0.8rem;
  margin-top: 2px;
}

.client-name {
  font-weight: 500;
  color: #1e293b;
}

.pet-info .pet-name {
  font-weight: 500;
  color: #1e293b;
  font-size: 0.95rem;
}

.pet-info .pet-species {
  font-size: 0.8rem;
  margin-top: 2px;
}

.service-badge {
  background: #f0f9ff;
  color: #0369a1;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  border: 1px solid #bae6fd;
}

/* Status Badges */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: capitalize;
}

.status-warning {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
}

.status-info {
  background: #dbeafe;
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.status-success {
  background: #dcfce7;
  color: #166534;
  border: 1px solid #bbf7d0;
}

.status-secondary {
  background: #f3f4f6;
  color: #6b7280;
  border: 1px solid #d1d5db;
}

.status-icon {
  font-size: 0.7rem;
}

/* Empty State */
.empty-state {
  padding: 2rem 1rem;
}

.empty-state i {
  opacity: 0.5;
}

.empty-state h6 {
  margin-bottom: 0.5rem;
}

/* Responsive Design */
/* Estilos lista de próximas citas (adaptados) */
.appointments-list { display: flex; flex-direction: column; gap: 0.75rem; }
.appointment-item { display: grid; grid-template-columns: 110px 1fr auto; align-items: center; gap: 1rem; padding: 0.75rem 1rem; border: 1px solid #eef2f7; border-radius: 12px; }
.appointment-time { text-align: left; }
.time-badge { background: #e8f5e9; color: #16a34a; padding: 0.25rem 0.5rem; border-radius: 6px; font-weight: 600; font-size: 0.9rem; }
.appointment-main { color: #111827; }
.appointment-meta { color: #6b7280; font-size: 0.85rem; }
.appointment-status .status-badge { padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.75rem; }

@media (max-width: 768px) {
  .peluquero-dashboard {
    padding: 1rem 0;
  }
  
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
    margin-bottom: 0.5rem;
  }
  
  .stat-card {
    text-align: center;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .report-actions .d-flex {
    flex-direction: column;
    gap: 1rem;
  }
  
  .report-actions .btn {
    width: 100%;
  }
  
  .table td {
    padding: 0.75rem 0.5rem;
  }

  .appointment-item { grid-template-columns: 1fr; text-align: left; }
  .appointment-status { margin-top: 0.5rem; }
}

@media (max-width: 576px) {
  .card-body {
    padding: 1rem;
  }
  
  .section-icon {
    width: 40px;
    height: 40px;
    font-size: 1rem;
    margin-right: 0.75rem;
  }
  
  .page-title {
    font-size: 1.5rem;
  }
  
  .stat-number {
    font-size: 1.75rem;
  }
}

/* Animations */
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

.stat-card {
  animation: fadeInUp 0.5s ease-out both;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }
</style>

<script>
// Efectos interactivos adicionales
document.addEventListener('DOMContentLoaded', function() {
  // Efecto de pulso para números importantes
  const statNumbers = document.querySelectorAll('.stat-number');
  statNumbers.forEach(number => {
    if (parseInt(number.textContent) > 0) {
      number.style.animation = 'pulse 2s infinite';
    }
  });
  
  // Añadir tooltips para servicios en móviles
  if (window.innerWidth <= 768) {
    const serviceBadges = document.querySelectorAll('.service-badge');
    serviceBadges.forEach(badge => {
      badge.title = badge.textContent;
    });
  }
});

// Añadir animación de pulso
const style = document.createElement('style');
style.textContent = `
  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
  }
`;
document.head.appendChild(style);
</script>
