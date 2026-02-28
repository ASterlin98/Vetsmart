<?php
// $citas, $mensaje
?>
<div class="citas-dashboard">
  <!-- Header Section -->
  <div class="header-section mb-4">
    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <div class="section-icon">
          <i class="fas fa-calendar-check"></i>
        </div>
        <div>
          <h1 class="page-title mb-1">Mis Citas</h1>
          <p class="page-subtitle">Gestiona todas tus citas programadas</p>
          <?php if (!empty($resumen)): ?>
          <div class="mt-2 d-flex flex-wrap gap-2">
            <span class="badge bg-warning-subtle text-dark border">Pendientes: <strong><?= (int)($resumen['pendiente'] ?? 0) ?></strong></span>
            <span class="badge bg-info-subtle text-dark border">Confirmadas: <strong><?= (int)($resumen['confirmada'] ?? 0) ?></strong></span>
            <span class="badge bg-success-subtle text-dark border">Completadas: <strong><?= (int)($resumen['completada'] ?? 0) ?></strong></span>
            <span class="badge bg-secondary-subtle text-dark border">Canceladas: <strong><?= (int)($resumen['cancelada'] ?? 0) ?></strong></span>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <a href="<?= BASE ?>/cliente/citas/agendar" class="btn btn-primary btn-lg">
        <i class="fas fa-plus me-2"></i>Agendar Nueva Cita
      </a>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if (!empty($mensaje)): ?>
    <div class="alert alert-<?= htmlspecialchars($mensaje['tipo'] ?? 'info') ?> alert-dismissible fade show mb-4" role="alert">
      <i class="fas fa-<?= ($mensaje['tipo'] ?? '') === 'success' ? 'check-circle' : 'info-circle' ?> me-2"></i>
      <?= htmlspecialchars($mensaje['texto'] ?? '') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Citas List -->
  <div class="citas-section">
    <?php if (!empty($citas)): ?>
      <div class="citas-grid">
        <?php foreach ($citas as $c): ?>
          <?php 
          $estado = strtolower((string)($c['estado'] ?? ''));
          $isUpcoming = in_array($estado, ['pendiente', 'confirmada']);
          ?>
          <div class="cita-card <?= $isUpcoming ? 'upcoming' : '' ?>">
            <!-- Card Header -->
            <div class="cita-header">
              <div class="cita-main-info">
                <div class="cita-date">
                  <i class="fas fa-calendar-day"></i>
                  <span><?= htmlspecialchars($c['fecha'] ?? '') ?></span>
                </div>
                <div class="cita-status">
                  <span class="status-badge status-<?= $estado ?>">
                    <i class="status-icon <?= $estado === 'pendiente' ? 'fa-clock' : ($estado === 'confirmada' ? 'fa-check' : ($estado === 'completada' ? 'fa-check-double' : 'fa-times')) ?>"></i>
                    <?= htmlspecialchars($c['estado'] ?? '') ?>
                  </span>
                </div>
              </div>
            </div>

            <!-- Card Body -->
            <div class="cita-body">
              <div class="cita-details">
                <div class="detail-item">
                  <div class="detail-icon">
                    <i class="fas fa-paw"></i>
                  </div>
                  <div class="detail-content">
                    <div class="detail-label">Mascota</div>
                    <div class="detail-value"><?= htmlspecialchars($c['mascota'] ?? '-') ?></div>
                  </div>
                </div>
                
                <div class="detail-item">
                  <div class="detail-icon">
                    <i class="fas fa-stethoscope"></i>
                  </div>
                  <div class="detail-content">
                    <div class="detail-label">Servicio</div>
                    <div class="detail-value"><?= htmlspecialchars($c['servicio'] ?? '-') ?></div>
                  </div>
                </div>
                
                <div class="detail-item">
                  <div class="detail-icon">
                    <i class="fas fa-user-md"></i>
                  </div>
                  <div class="detail-content">
                    <div class="detail-label">Veterinario</div>
                    <div class="detail-value"><?= htmlspecialchars($c['veterinario'] ?? 'Por asignar') ?></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card Footer -->
            <div class="cita-footer">
              <?php if ($isUpcoming): ?>
                <!-- Reagendar Form -->
                <div class="action-section mb-3">
                  <div class="reagendar-form">
                    <form method="post" action="<?= BASE ?>/cliente/citas/reagendar" class="reagendar-form-inner">
                      <?= CSRF::inputField(); ?>
                      <input type="hidden" name="id" value="<?= (int)$c['id'] ?>" />
                      <div class="input-group">
                        <input type="datetime-local" name="fecha" class="form-control" 
                               min="<?= date('Y-m-d\TH:i') ?>" required />
                        <button type="submit" class="btn btn-outline-primary">
                          <i class="fas fa-calendar-day me-1"></i>Reagendar
                        </button>
                      </div>
                    </form>
                  </div>
                </div>

                <!-- Cancel Button -->
                <div class="action-section">
                  <form method="post" action="<?= BASE ?>/cliente/citas" 
                        class="cancel-form" 
                        onsubmit="return confirmCancelation(event, this)">
                    <?= CSRF::inputField(); ?>
                    <input type="hidden" name="accion" value="cancelar" />
                    <input type="hidden" name="id" value="<?= (int)$c['id'] ?>" />
                    <button type="submit" class="btn btn-danger w-100">
                      <i class="fas fa-times me-2"></i>Cancelar Cita
                    </button>
                  </form>
                </div>
              <?php else: ?>
                <div class="no-actions">
                  <i class="fas fa-info-circle me-2"></i>
                  No disponible para modificaciones
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <!-- Empty State -->
      <div class="empty-state text-center py-5">
        <div class="empty-icon mb-4">
          <i class="fas fa-calendar-times"></i>
        </div>
        <h3 class="empty-title mb-3">No tienes citas programadas</h3>
        <p class="empty-description mb-4">
          Cuando programes una cita, aparecerá listada aquí junto con todas sus opciones de gestión.
        </p>
        <a href="<?= BASE ?>/cliente/citas/agendar" class="btn btn-primary btn-lg">
          <i class="fas fa-plus me-2"></i>Agendar Mi Primera Cita
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
.citas-dashboard {
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

/* Citas Grid */
.citas-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 1.5rem;
}

/* Cita Card */
.cita-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
  transition: all 0.3s ease;
  overflow: hidden;
}

.cita-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-color: #cbd5e1;
}

.cita-card.upcoming {
  border-left: 4px solid #06b6d4;
}

/* Card Header */
.cita-header {
  padding: 1.5rem 1.5rem 1rem;
  border-bottom: 1px solid #f1f5f9;
}

.cita-main-info {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.cita-date {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #1e293b;
  font-size: 1.1rem;
}

.cita-date i {
  color: #06b6d4;
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
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-pendiente {
  background: #fffbeb;
  color: #d97706;
  border: 1px solid #fcd34d;
}

.status-confirmada {
  background: #f0f9ff;
  color: #0369a1;
  border: 1px solid #7dd3fc;
}

.status-completada {
  background: #f0fdf4;
  color: #059669;
  border: 1px solid #86efac;
}

.status-cancelada {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fca5a5;
}

.status-icon {
  font-size: 0.7rem;
}

/* Card Body */
.cita-body {
  padding: 1.5rem;
}

.cita-details {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.detail-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.detail-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #06b6d4;
  font-size: 1rem;
  flex-shrink: 0;
}

.detail-content {
  flex: 1;
}

.detail-label {
  color: #64748b;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.25rem;
}

.detail-value {
  color: #1e293b;
  font-weight: 500;
  font-size: 0.95rem;
}

/* Card Footer */
.cita-footer {
  padding: 1rem 1.5rem 1.5rem;
  border-top: 1px solid #f1f5f9;
}

.action-section {
  margin-bottom: 1rem;
}

.reagendar-form-inner .input-group {
  display: flex;
  gap: 0.5rem;
}

.reagendar-form-inner input {
  flex: 1;
  border-radius: 8px;
  border: 1px solid #d1d5db;
  padding: 0.5rem 0.75rem;
}

.reagendar-form-inner .btn {
  border-radius: 8px;
  white-space: nowrap;
}

.cancel-form .btn {
  border-radius: 8px;
  padding: 0.75rem;
  font-weight: 500;
}

.no-actions {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1rem;
  text-align: center;
  color: #64748b;
  font-size: 0.9rem;
}

/* Empty State */
.empty-state {
  background: white;
  border-radius: 16px;
  padding: 3rem 2rem;
  border: 1px solid #f1f5f9;
}

.empty-icon {
  font-size: 4rem;
  color: #cbd5e1;
  margin-bottom: 1.5rem;
}

.empty-title {
  color: #374151;
  font-weight: 600;
  margin-bottom: 1rem;
}

.empty-description {
  color: #6b7280;
  font-size: 1.1rem;
  max-width: 500px;
  margin: 0 auto;
}

/* Responsive Design */
@media (max-width: 768px) {
  .citas-dashboard {
    padding: 1rem 0;
  }
  
  .header-section .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 1rem;
  }
  
  .section-icon {
    margin-bottom: 1rem;
  }
  
  .citas-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .cita-main-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .reagendar-form-inner .input-group {
    flex-direction: column;
  }
}

@media (max-width: 576px) {
  .cita-card {
    border-radius: 12px;
  }
  
  .cita-header,
  .cita-body,
  .cita-footer {
    padding: 1.25rem;
  }
  
  .empty-state {
    padding: 2rem 1rem;
  }
  
  .empty-icon {
    font-size: 3rem;
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

.cita-card {
  animation: fadeInUp 0.5s ease-out;
}
</style>

<script>
function confirmCancelation(event, form) {
  if (!confirm('¿Estás seguro de que deseas cancelar esta cita? Esta acción no se puede deshacer.')) {
    event.preventDefault();
    return false;
  }
  
  const button = form.querySelector('button[type="submit"]');
  const originalText = button.innerHTML;
  
  button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cancelando...';
  button.disabled = true;
  
  // Re-enable button after 3 seconds if form doesn't submit
  setTimeout(() => {
    button.innerHTML = originalText;
    button.disabled = false;
  }, 3000);
  
  return true;
}

// Add some interactive enhancements
document.addEventListener('DOMContentLoaded', function() {
  // Enhance date inputs with better UX
  const dateInputs = document.querySelectorAll('input[type="datetime-local"]');
  dateInputs.forEach(input => {
    // Set minimum date to current date/time
    const now = new Date();
    const timezoneOffset = now.getTimezoneOffset() * 60000;
    const localISOTime = new Date(now - timezoneOffset).toISOString().slice(0, 16);
    input.min = localISOTime;
    
    // Add focus effect
    input.addEventListener('focus', function() {
      this.parentElement.style.boxShadow = '0 0 0 3px rgba(6, 182, 212, 0.1)';
    });
    
    input.addEventListener('blur', function() {
      this.parentElement.style.boxShadow = 'none';
    });
  });
});
</script>
