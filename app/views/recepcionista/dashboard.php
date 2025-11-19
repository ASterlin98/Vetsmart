<div class="dashboard-recepcionista">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="page-title mb-2">
        <i class="fas fa-tachometer-alt me-2 text-success"></i>Panel Principal del Recepcionista
      </h1>
      <p class="text-muted mb-0">Resumen general del día <?= date('d/m/Y') ?></p>
    </div>
  </div>

  <!-- Main Stats Grid -->
  <div class="row g-3 mb-4">
    <!-- Citas de hoy -->
    <div class="col-xl-3 col-md-6">
      <div class="card stat-card border-0">
        <div class="card-body text-center p-4">
          <div class="stat-icon mb-3">
            <i class="fas fa-calendar-day fa-2x text-success"></i>
          </div>
          <h3 class="stat-number text-dark mb-2"><?= htmlspecialchars($citasHoy) ?></h3>
          <p class="stat-label text-muted mb-0">Citas de Hoy</p>
          <small class="text-muted">Registradas en la fecha actual</small>
        </div>
      </div>
    </div>

    <!-- Citas pendientes -->
    <div class="col-xl-3 col-md-6">
      <div class="card stat-card border-0">
        <div class="card-body text-center p-4">
          <div class="stat-icon mb-3">
            <i class="fas fa-clock fa-2x text-warning"></i>
          </div>
          <h3 class="stat-number text-dark mb-2"><?= htmlspecialchars($pendientes) ?></h3>
          <p class="stat-label text-muted mb-0">Pendientes</p>
          <small class="text-muted">En espera de atención</small>
        </div>
      </div>
    </div>

    <!-- Total de clientes -->
    <div class="col-xl-3 col-md-6">
      <div class="card stat-card border-0">
        <div class="card-body text-center p-4">
          <div class="stat-icon mb-3">
            <i class="fas fa-users fa-2x text-primary"></i>
          </div>
          <h3 class="stat-number text-dark mb-2"><?= htmlspecialchars($totalClientes) ?></h3>
          <p class="stat-label text-muted mb-0">Clientes</p>
          <small class="text-muted">Registrados en el sistema</small>
        </div>
      </div>
    </div>

    <!-- Total de mascotas -->
    <div class="col-xl-3 col-md-6">
      <div class="card stat-card border-0">
        <div class="card-body text-center p-4">
          <div class="stat-icon mb-3">
            <i class="fas fa-paw fa-2x text-info"></i>
          </div>
          <h3 class="stat-number text-dark mb-2"><?= htmlspecialchars($totalMascotas) ?></h3>
          <p class="stat-label text-muted mb-0">Mascotas</p>
          <small class="text-muted">Registradas por los clientes</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Financial Overview -->
  <div class="row g-3 mb-4">
    <div class="col-xl-4 col-md-6">
      <div class="card stat-card border-0">
        <div class="card-body p-4">
          <div class="d-flex align-items-center">
            <div class="stat-icon-sm me-3">
              <i class="fas fa-arrow-trend-up fa-lg text-success"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Ingresos (hoy)</h6>
              <h4 class="text-success mb-0">$ <?= number_format((float)($ingresosHoy ?? 0), 2) ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6">
      <div class="card stat-card border-0">
        <div class="card-body p-4">
          <div class="d-flex align-items-center">
            <div class="stat-icon-sm me-3">
              <i class="fas fa-arrow-trend-down fa-lg text-danger"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Egresos (hoy)</h6>
              <h4 class="text-danger mb-0">$ <?= number_format((float)($egresosHoy ?? 0), 2) ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6">
      <div class="card stat-card border-0">
        <div class="card-body p-4">
          <div class="d-flex align-items-center">
            <div class="stat-icon-sm me-3">
              <i class="fas fa-scale-balanced fa-lg text-dark"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1">Balance (hoy)</h6>
              <?php $balance = (float)($ingresosHoy ?? 0) - (float)($egresosHoy ?? 0); ?>
              <h4 class="<?= $balance >= 0 ? 'text-success' : 'text-danger' ?> mb-0">$ <?= number_format($balance, 2) ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="row g-4 mb-4">
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0">
              <i class="fas fa-chart-pie text-success me-2"></i>Citas de hoy por estado
            </h6>
          </div>
          <div class="chart-container">
            <canvas id="chartEstados" height="250"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0">
              <i class="fas fa-chart-column text-success me-2"></i>Top servicios (30 días)
            </h6>
          </div>
          <div class="chart-container">
            <canvas id="chartServicios" height="250"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions & Next Appointments -->
  <div class="row g-4">
    <!-- Quick Actions -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h6 class="card-title mb-3">
            <i class="fas fa-bolt text-warning me-2"></i>Accesos Rápidos
          </h6>
          <div class="d-grid gap-2">
            <a href="/vetsmart/recepcionista/agenda" class="btn btn-outline-success btn-lg d-flex align-items-center justify-content-between py-3">
              <span>
                <i class="fas fa-calendar-alt me-2"></i>Ver Agenda
              </span>
              <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/vetsmart/recepcionista/clientes" class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-between py-3">
              <span>
                <i class="fas fa-users me-2"></i>Ver Clientes
              </span>
              <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/vetsmart/recepcionista/mascotas" class="btn btn-outline-info btn-lg d-flex align-items-center justify-content-between py-3">
              <span>
                <i class="fas fa-paw me-2"></i>Ver Mascotas
              </span>
              <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/vetsmart/recepcionista/citas/create" class="btn btn-outline-warning btn-lg d-flex align-items-center justify-content-between py-3">
              <span>
                <i class="fas fa-plus-circle me-2"></i>Nueva Cita
              </span>
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Next Appointments -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0">
              <i class="fas fa-calendar-day text-success me-2"></i>Próximas Citas de Hoy
            </h6>
            <a href="/vetsmart/recepcionista/agenda" class="btn btn-sm btn-outline-success">
              <i class="fas fa-external-link-alt me-1"></i>Ver Agenda Completa
            </a>
          </div>
          
          <?php if (!empty($proximas ?? [])): ?>
            <div class="appointments-list">
              <?php foreach (($proximas ?? []) as $p): ?>
                <div class="appointment-item">
                  <div class="appointment-time">
                    <span class="time-badge"><?= htmlspecialchars($p['hora'] ?? '-') ?></span>
                  </div>
                  <div class="appointment-details">
                    <div class="appointment-main">
                      <strong><?= htmlspecialchars($p['cliente'] ?? '-') ?></strong>
                      <span class="text-muted">con</span>
                      <strong><?= htmlspecialchars($p['mascota'] ?? '-') ?></strong>
                    </div>
                    <div class="appointment-meta">
                      <span class="service"><?= htmlspecialchars($p['servicio'] ?? '-') ?></span>
                      <span class="separator">•</span>
                      <span class="employee"><?= htmlspecialchars($p['empleado'] ?? '-') ?></span>
                    </div>
                  </div>
                  <div class="appointment-status">
                    <span class="status-badge status-<?= strtolower($p['estado'] ?? 'pendiente') ?>">
                      <?= htmlspecialchars(ucfirst($p['estado'] ?? 'Pendiente')) ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
              <p class="text-muted mb-0">No hay citas programadas para hoy</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.dashboard-recepcionista {
  max-width: 100%;
}

.page-title {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.8rem;
}

/* Stat Cards */
.stat-card {
  background: #ffffff;
  border-radius: 12px;
  transition: all 0.3s ease;
  border: 1px solid #e9ecef;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-icon {
  color: #6c757d;
}

.stat-icon-sm {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
}

.stat-label {
  font-size: 0.9rem;
  font-weight: 600;
}

/* Appointments List */
.appointments-list {
  max-height: 400px;
  overflow-y: auto;
}

.appointment-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #f1f5f9;
  transition: background-color 0.2s ease;
}

.appointment-item:hover {
  background-color: #f8fafc;
}

.appointment-item:last-child {
  border-bottom: none;
}

.appointment-time {
  margin-right: 1rem;
  flex-shrink: 0;
}

.time-badge {
  background: #f1f5f9;
  color: #374151;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  border: 1px solid #e5e7eb;
}

.appointment-details {
  flex: 1;
}

.appointment-main {
  margin-bottom: 0.25rem;
}

.appointment-meta {
  font-size: 0.85rem;
  color: #6b7280;
}

.service {
  font-weight: 500;
}

.separator {
  margin: 0 0.5rem;
}

.employee {
  font-style: italic;
}

.appointment-status {
  margin-left: auto;
  flex-shrink: 0;
}

.status-badge {
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
  border: 1px solid;
}

.status-pendiente {
  background-color: #fff3cd;
  border-color: #ffc107;
  color: #856404;
}

.status-en.curso {
  background-color: #cce7ff;
  border-color: #0d6efd;
  color: #084298;
}

.status-completada {
  background-color: #d1e7dd;
  border-color: #198754;
  color: #0f5132;
}

/* Quick Actions */
.btn-lg {
  border-radius: 10px;
  transition: all 0.3s ease;
}

.btn-lg:hover {
  transform: translateX(5px);
}

/* Charts */
.chart-container {
  position: relative;
  height: 250px;
  width: 100%;
}

.card-title {
  color: #374151;
  font-weight: 600;
  font-size: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.5rem;
  }
  
  .stat-number {
    font-size: 1.5rem;
  }
  
  .appointment-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .appointment-status {
    margin-left: 0;
    align-self: flex-end;
  }
  
  .btn-lg {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
  }
}

@media (max-width: 576px) {
  .dashboard-recepcionista {
    padding: 0.5rem;
  }
  
  .stat-card .card-body {
    padding: 1.5rem;
  }
  
  .appointment-main {
    flex-direction: column;
    gap: 0.25rem;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize charts
  const estadosData = <?= json_encode($estadosHoy ?? []) ?>;
  const servicios = <?= json_encode($topServicios ?? []) ?>;
  
  // Estados Chart
  if (estadosData && Object.keys(estadosData).length > 0) {
    const ctx1 = document.getElementById('chartEstados');
    new Chart(ctx1, {
      type: 'doughnut',
      data: {
        labels: Object.keys(estadosData).map(label => label.charAt(0).toUpperCase() + label.slice(1)),
        datasets: [{
          data: Object.values(estadosData),
          backgroundColor: [
            '#6c757d', // Pendiente
            '#ffc107', // En curso
            '#198754', // Completada
            '#0d6efd', // Confirmada
            '#dc3545'  // Cancelada
          ],
          borderWidth: 2,
          borderColor: '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              usePointStyle: true
            }
          }
        }
      }
    });
  }
  
  // Servicios Chart
  if (servicios && servicios.length > 0) {
    const ctx2 = document.getElementById('chartServicios');
    new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: servicios.map(r => r.servicio),
        datasets: [{
          label: 'Atenciones',
          data: servicios.map(r => parseInt(r.total || 0)),
          backgroundColor: '#20c997',
          borderColor: '#198754',
          borderWidth: 1,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0,0,0,0.1)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  }
  
  // Add click animations to quick action buttons
  const quickActionBtns = document.querySelectorAll('.btn-lg');
  quickActionBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      this.style.transform = 'scale(0.95)';
      setTimeout(() => {
        this.style.transform = '';
      }, 150);
    });
  });
});
</script>