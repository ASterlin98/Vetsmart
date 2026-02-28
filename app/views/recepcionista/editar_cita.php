<div class="appointment-edit-container">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="page-title mb-2">
        <i class="fas fa-edit me-2 text-primary"></i>Editar Cita
      </h1>
      <p class="text-muted mb-0">Actualice la información de la cita programada</p>
    </div>
    <div class="appointment-badge">
      <span class="badge bg-light text-dark">
        <i class="fas fa-hashtag me-1"></i>ID: <?= htmlspecialchars($cita['id']) ?>
      </span>
    </div>
  </div>

  <!-- Main Form -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form action="<?= BASE ?>/citas/update" method="POST">
        <?= CSRF::inputField(); ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($cita['id']) ?>">

        <!-- Client & Pet Information -->
        <div class="section-header mb-4">
          <h5 class="section-title">
            <i class="fas fa-user-friends me-2 text-success"></i>Cliente y Mascota
          </h5>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label fw-medium">
              Cliente <span class="text-danger">*</span>
            </label>
            <select name="cliente_id" id="cliente_id" class="form-select" required>
              <option value="">Seleccionar cliente...</option>
              <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $cita['cliente_id'] == $c['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-medium">
              Mascota <span class="text-danger">*</span>
            </label>
            <select name="mascota_id" id="mascota_id" class="form-select" required>
              <option value="">Seleccionar mascota...</option>
              <?php foreach ($mascotas as $m): ?>
                <option value="<?= $m['id'] ?>" <?= $cita['mascota_id'] == $m['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Se actualiza al seleccionar un cliente</div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-medium">
              Empleado <span class="text-danger">*</span>
            </label>
            <select name="empleado_id" id="empleado_id" class="form-select" required>
              <option value="">Seleccionar empleado...</option>
              <?php foreach ($empleados as $e): ?>
                <option value="<?= $e['id'] ?>" data-role="<?= htmlspecialchars(strtolower((string)($e['rol'] ?? ''))) ?>" <?= $cita['empleado_id'] == $e['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Appointment Details -->
        <div class="section-header mb-4">
          <h5 class="section-title">
            <i class="fas fa-calendar-alt me-2 text-info"></i>Detalles de la Cita
          </h5>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label fw-medium">
              Servicio <span class="text-danger">*</span>
            </label>
            <select name="servicio_id" id="servicio_id" class="form-select" required>
              <option value="">Seleccionar servicio...</option>
              <?php foreach ($servicios as $s): ?>
                <option value="<?= $s['id'] ?>" data-name="<?= htmlspecialchars(strtolower((string)$s['nombre'])) ?>" <?= $cita['servicio_id'] == $s['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($s['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-medium">
              Fecha y Hora <span class="text-danger">*</span>
            </label>
            <input type="datetime-local" name="fecha" class="form-control" 
                   value="<?= date('Y-m-d\TH:i', strtotime($cita['fecha'])) ?>" required>
            <div class="form-text">Fecha y hora de la cita</div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-medium">Estado</label>
            <select name="estado" class="form-select">
              <option value="pendiente" <?= $cita['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
              <option value="completada" <?= $cita['estado'] === 'completada' ? 'selected' : '' ?>>Completada</option>
            </select>
            <div class="form-text">Estado actual de la cita</div>
          </div>
        </div>

        <!-- Additional Information -->
        <div class="section-header mb-4">
          <h5 class="section-title">
            <i class="fas fa-sticky-note me-2 text-warning"></i>Información Adicional
          </h5>
        </div>

        <div class="mb-4">
          <label class="form-label fw-medium">Notas y Observaciones</label>
          <textarea name="notas" rows="4" class="form-control" 
                    placeholder="Ingrese cualquier observación, nota adicional o instrucciones especiales para esta cita..."><?= htmlspecialchars($cita['notas'] ?? '') ?></textarea>
          <div class="form-text">Información adicional relevante para la cita</div>
        </div>

        <!-- Current Appointment Info -->
        <div class="current-info bg-light rounded p-3 mb-4">
          <h6 class="fw-medium mb-3">
            <i class="fas fa-info-circle me-2 text-primary"></i>Información Actual de la Cita
          </h6>
          <div class="row">
            <div class="col-md-6">
              <small class="text-muted">Cliente actual:</small>
              <div class="fw-medium"><?= htmlspecialchars($cita['cliente_nombre'] ?? '') ?> <?= htmlspecialchars($cita['cliente_apellido'] ?? '') ?></div>
            </div>
            <div class="col-md-6">
              <small class="text-muted">Mascota actual:</small>
              <div class="fw-medium"><?= htmlspecialchars($cita['mascota_nombre'] ?? '') ?></div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions border-top pt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="<?= BASE ?>/recepcionista/citas" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
            <button type="submit" class="btn btn-success px-4">
              <i class="fas fa-save me-1"></i>Actualizar Cita
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.appointment-edit-container {
  max-width: 100%;
}

.page-title {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.6rem;
}

.appointment-badge .badge {
  font-size: 0.8rem;
  padding: 0.5rem 0.75rem;
  border: 1px solid #dee2e6;
}

.section-header {
  border-bottom: 2px solid #e9ecef;
  padding-bottom: 0.5rem;
}

.section-title {
  color: #495057;
  font-weight: 600;
  font-size: 1.1rem;
}

.form-label {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.form-control, .form-select {
  border-radius: 6px;
  border: 1px solid #dee2e6;
  transition: all 0.2s ease;
  padding: 0.75rem;
}

.form-control:focus, .form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
  transform: translateY(-1px);
}

.form-text {
  font-size: 0.8rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

.btn {
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.2s ease;
  padding: 0.75rem 1.5rem;
}

.btn-success {
  background: linear-gradient(135deg, #198754, #20c997);
  border: none;
}

.btn-success:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}

.btn-outline-secondary {
  border: 1px solid #6c757d;
  color: #6c757d;
}

.btn-outline-secondary:hover {
  background-color: #6c757d;
  border-color: #6c757d;
  transform: translateY(-1px);
}

.form-actions {
  background-color: #f8f9fa;
  margin: 0 -1.5rem -1.5rem;
  padding: 1.5rem !important;
}

.current-info {
  border-left: 3px solid #0dcaf0;
}

.current-info h6 {
  color: #495057;
}

/* Loading state */
.loading {
  position: relative;
  opacity: 0.7;
}

.loading::after {
  content: '';
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #0d6efd;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: translateY(-50%) rotate(0deg); }
  100% { transform: translateY(-50%) rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.4rem;
  }
  
  .card-body {
    padding: 1.5rem !important;
  }
  
  .form-actions {
    margin: 0 -1.5rem -1.5rem;
    padding: 1.5rem !important;
  }
  
  .d-flex.justify-content-between.align-items-center {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .appointment-badge {
    margin-top: 0.5rem;
  }
}

@media (max-width: 576px) {
  .appointment-edit-container {
    padding: 0.5rem;
  }
  
  .card-body {
    padding: 1rem !important;
  }
  
  .form-actions {
    margin: 0 -1rem -1rem;
    padding: 1rem !important;
  }
  
  .d-flex.gap-2 {
    flex-direction: column;
    width: 100%;
  }
  
  .d-flex.gap-2 .btn {
    width: 100%;
    margin-bottom: 0.5rem;
  }
  
  .row.g-3 {
    margin: 0 -0.5rem;
  }
  
  .row.g-3 > [class*="col-"] {
    padding: 0 0.5rem;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const clienteSelect = document.getElementById('cliente_id');
  const mascotaSelect = document.getElementById('mascota_id');

  // Client-Pet relationship handler
  clienteSelect.addEventListener('change', async function() {
    const clienteId = this.value;
    
    mascotaSelect.innerHTML = '<option value="">Cargando mascotas...</option>';
    mascotaSelect.disabled = true;
    mascotaSelect.classList.add('loading');

    if (!clienteId) {
      mascotaSelect.innerHTML = '<option value="">Seleccionar mascota...</option>';
      mascotaSelect.disabled = false;
      mascotaSelect.classList.remove('loading');
      return;
    }

    try {
      const response = await fetch(`<?= BASE ?>/api/clientes/${clienteId}/mascotas`);
      
      if (!response.ok) {
        throw new Error('Error al cargar mascotas');
      }
      
      const mascotas = await response.json();
      mascotaSelect.innerHTML = '<option value="">Seleccionar mascota</option>';
      
      if (mascotas.length === 0) {
        mascotaSelect.innerHTML = '<option value="">El cliente no tiene mascotas registradas</option>';
      } else {
        mascotas.forEach(m => {
          const opt = document.createElement('option');
          opt.value = m.id;
          opt.textContent = m.nombre;
          mascotaSelect.appendChild(opt);
        });
        mascotaSelect.disabled = false;
      }
      
    } catch (err) {
      console.error('Error:', err);
      mascotaSelect.innerHTML = '<option value="">Error al cargar las mascotas</option>';
    } finally {
      mascotaSelect.classList.remove('loading');
    }
  });

  // Form validation
  const form = document.querySelector('form');
  const requiredFields = form.querySelectorAll('select[required], input[required]');

  // Real-time validation
  requiredFields.forEach(field => {
    field.addEventListener('change', function() {
      if (this.value.trim()) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
      } else {
        this.classList.remove('is-valid');
      }
    });
  });

  // Form submission validation
  form.addEventListener('submit', function(e) {
    let isValid = true;

    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        field.classList.add('is-invalid');
        isValid = false;
      } else {
        field.classList.remove('is-invalid');
      }
    });

    // Validate mascota select
    if (mascotaSelect.disabled || !mascotaSelect.value) {
      mascotaSelect.classList.add('is-invalid');
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
      // Scroll to first invalid field
      const firstInvalid = form.querySelector('.is-invalid');
      if (firstInvalid) {
        firstInvalid.scrollIntoView({ 
          behavior: 'smooth', 
          block: 'center' 
        });
        firstInvalid.focus();
      }
    }
  });

  // Set minimum datetime to current time for future appointments
  const fechaInput = form.querySelector('input[type="datetime-local"]');
  const now = new Date();
  const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
  
  // Only set min if the current appointment date is in the future
  const appointmentDate = new Date(fechaInput.value);
  if (appointmentDate > now) {
    fechaInput.min = localDateTime;
  }

  // Initialize form validation states
  requiredFields.forEach(field => {
    if (field.value.trim()) {
      field.classList.add('is-valid');
    }
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const servicioSelect = document.getElementById('servicio_id');
  const empleadoSelect = document.getElementById('empleado_id');
  if (!servicioSelect || !empleadoSelect) return;
  const allEmpleadoOptions = Array.from(empleadoSelect.querySelectorAll('option'));

  const isGrooming = (nombre) => {
    const n = (nombre || '').toLowerCase();
    return n.includes('peluquer') || n.includes('bañ') || n.includes('ban') || n.includes('cort') || n.includes('spa');
  };

  function applyEmployeeFilter() {
    const opt = servicioSelect.options[servicioSelect.selectedIndex];
    const svcName = opt ? (opt.getAttribute('data-name') || opt.textContent) : '';
    const grooming = isGrooming(svcName);

    const current = empleadoSelect.value;
    const orig = allEmpleadoOptions.filter(o => o.value !== '');
    empleadoSelect.innerHTML = '';
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Seleccionar empleado...';
    empleadoSelect.appendChild(placeholder);

    orig.forEach(o => {
      const role = (o.getAttribute('data-role') || '').toLowerCase();
      if (!grooming || role === 'peluquero') {
        empleadoSelect.appendChild(o.cloneNode(true));
      }
    });

    if (current) {
      const found = Array.from(empleadoSelect.options).some(x => x.value === current);
      empleadoSelect.value = found ? current : '';
    }
  }

  servicioSelect.addEventListener('change', applyEmployeeFilter);
  applyEmployeeFilter();
});
</script>
