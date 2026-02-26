<div class="pet-registration-container">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="page-title mb-2">
        <i class="fas fa-paw me-2 text-success"></i>Registrar Nueva Mascota
      </h1>
      <p class="text-muted mb-0">Complete la informacin de la nueva mascota</p>
    </div>
  </div>

  <!-- Main Form -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form action="/vetsmart/recepcionista/mascotas/store" method="POST" enctype="multipart/form-data" class="row g-4">
        <?= CSRF::inputField(); ?>
        
        <!-- Basic Information -->
        <div class="col-12">
          <h5 class="section-title mb-3">
            <i class="fas fa-info-circle me-2 text-primary"></i>Informacin Basica
          </h5>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-medium">
            Nombre de la Mascota <span class="text-danger">*</span>
          </label>
          <input type="text" name="nombre" class="form-control" 
                 placeholder="Ingrese el nombre de la mascota" required>
          <div class="form-text">Nombre con el que se identifica a la mascota</div>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-medium">Especie</label>
          <select name="especie" class="form-select">
            <option value="">Seleccionar especie...</option>
            <option value="Canino">Canino</option>
            <option value="Felino">Felino</option>
            <option value="Ave">Ave</option>
            <option value="Roedor">Roedor</option>
            <option value="Reptil">Reptil</option>
            <option value="Otro">Otro</option>
          </select>
          <div class="form-text">Especie a la que pertenece la mascota</div>
        </div>

        <!-- Physical Characteristics -->
        <div class="col-12">
          <h5 class="section-title mb-3">
            <i class="fas fa-dna me-2 text-info"></i>Caracteristicas Fisicas
          </h5>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-medium">Raza</label>
          <input type="text" name="raza" class="form-control" 
                 placeholder="Ej: Labrador, Siames, etc.">
          <div class="form-text">Raza o tipo de la mascota</div>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-medium">Edad</label>
          <div class="input-group">
            <input type="number" name="edad" class="form-control" 
                   placeholder="0" min="0" max="30" step="0.5">
            <span class="input-group-text">Datos</span>
          </div>
          <div class="form-text">Edad en años (puede usar decimales)</div>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-medium">Sexo</label>
          <select name="sexo" class="form-select">
            <option value="">Seleccionar...</option>
            <option value="Macho">Macho</option>
            <option value="Hembra">Hembra</option>
          </select>
          <div class="form-text">Sexo de la mascota</div>
        </div>

        <!-- Owner Information -->
        <div class="col-12">
          <h5 class="section-title mb-3">
            <i class="fas fa-user me-2 text-warning"></i>Informacion del Dueño
          </h5>
        </div>

        <div class="col-12">
          <label class="form-label fw-medium">
            Dueño <span class="text-danger">*</span>
          </label>
          <select name="dueno_id" class="form-select" required>
            <option value="">Seleccione un cliente...</option>
            <?php foreach ($clientes as $cli): ?>
              <option value="<?= htmlspecialchars($cli['id']) ?>">
                <?= htmlspecialchars($cli['nombre'] . ' ' . $cli['apellido']) ?>
                <?php if (!empty($cli['documento'])): ?>
                  - <?= htmlspecialchars($cli['documento']) ?>
                <?php endif; ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Seleccione el cliente dueño de la mascota</div>
        </div>

        <!-- Additional Information (Optional) -->
        <div class="col-12">
          <h5 class="section-title mb-3">
            <i class="fas fa-sticky-note me-2 text-success"></i>Informacion Adicional
          </h5>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-medium">Color</label>
          <input type="text" name="color" class="form-control" 
                 placeholder="Ej: Negro, Blanco, Marron, etc.">
        </div>

        <div class="col-md-6">
          <label class="form-label fw-medium">Peso Aproximado</label>
          <div class="input-group">
            <input type="number" name="peso" class="form-control" 
                   placeholder="0" min="0" max="100" step="0.1">
            <span class="input-group-text">kg</span>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-medium">Foto de la Mascota</label>
          <input type="file" name="foto" accept="image/*" class="form-control">
          <div class="form-text">JPG, PNG, GIF. Máx 2MB.</div>
        </div>

        <div class="col-12">
          <label class="form-label fw-medium">Observaciones</label>
          <textarea name="observaciones" class="form-control" rows="3" 
                    placeholder="Ingrese cualquier observación relevante sobre la mascota (comportamiento, alergias, condiciones especiales, etc.)"></textarea>
        </div>

        <div class="alert alert-info py-2 px-3">
          <i class="fas fa-info-circle me-1"></i> La foto de la mascota se representa con la inicial de su nombre.
        </div>

        <!-- Form Actions -->
        <div class="col-12">
          <div class="border-top pt-4">
            <div class="d-flex gap-2 justify-content-end">
              <a href="/vetsmart/recepcionista/mascotas" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
              </a>
              <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-save me-1"></i>Registrar Mascota
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.pet-registration-container {
  max-width: 100%;
}

.page-title {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.6rem;
}

.section-title {
  color: #495057;
  font-weight: 600;
  font-size: 1.1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e9ecef;
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

.input-group-text {
  background-color: #f8f9fa;
  border-color: #dee2e6;
  color: #6c757d;
  font-size: 0.9rem;
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

/* Form validation states */
.is-valid {
  border-color: #198754 !important;
}

.is-invalid {
  border-color: #dc3545 !important;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.4rem;
  }
  
  .card-body {
    padding: 1.5rem !important;
  }
  
  .btn {
    padding: 0.75rem 1rem;
  }
}

@media (max-width: 576px) {
  .pet-registration-container {
    padding: 0.5rem;
  }
  
  .card-body {
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
  
  .row.g-4 {
    margin: 0 -0.5rem;
  }
  
  .row.g-4 > [class*="col-"] {
    padding: 0 0.5rem;
  }
  
  .input-group-text {
    font-size: 0.8rem;
    padding: 0.75rem 0.5rem;
  }
}

/* Custom styles for pet form */
.species-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.species-option {
  border: 2px solid #e9ecef;
  border-radius: 8px;
  padding: 0.75rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  background: white;
}

.species-option:hover {
  border-color: #0d6efd;
  transform: translateY(-2px);
}

.species-option.selected {
  border-color: #198754;
  background-color: #d4edda;
}

.species-icon {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  display: block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  const requiredFields = form.querySelectorAll('[required]');
  
  // Real-time validation
  requiredFields.forEach(field => {
    field.addEventListener('input', function() {
      if (this.value.trim()) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
      } else {
        this.classList.remove('is-valid');
      }
    });
    
    field.addEventListener('blur', function() {
      if (!this.value.trim()) {
        this.classList.add('is-invalid');
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
      }
    });
    
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
      
      // Show error message
      showNotification('Por favor, complete todos los campos requeridos', 'error');
    }
  });
  
  // Age input validation
  const ageInput = form.querySelector('input[name="edad"]');
  if (ageInput) {
    ageInput.addEventListener('input', function() {
      const value = parseFloat(this.value);
      if (value > 30) {
        this.value = 30;
        showNotification('La edad maxima permitida es 30 años', 'warning');
      }
    });
  }
  
  // Weight input validation
  const weightInput = form.querySelector('input[name="peso"]');
  if (weightInput) {
    weightInput.addEventListener('input', function() {
      const value = parseFloat(this.value);
      if (value > 100) {
        this.value = 100;
        showNotification('El peso maximo permitido es 100 kg', 'warning');
      }
    });
  }
  
  // Helper function to show notifications
  function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.form-notification');
    if (existingNotification) {
      existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `form-notification alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    notification.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the form
    form.insertBefore(notification, form.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.remove();
      }
    }, 5000);
  }
  
  // Enhanced species selection (optional feature)
  const especieSelect = form.querySelector('select[name="especie"]');
  if (especieSelect) {
    // You could enhance this with popular breed suggestions based on species
    especieSelect.addEventListener('change', function() {
      const razaInput = form.querySelector('input[name="raza"]');
      if (this.value && !razaInput.value) {
        // Clear placeholder and set focus
        razaInput.placeholder = `Ej: Raza de ${this.value.toLowerCase()}`;
        razaInput.focus();
      }
    });
  }
  
  // Initialize form states
  requiredFields.forEach(field => {
    if (field.value.trim()) {
      field.classList.add('is-valid');
    }
  });
});
</script>
<script>
// Agrega un input oculto ASCII 'dueno_id' que copia el valor del select del dueño
document.addEventListener('DOMContentLoaded', function(){
  const form = document.querySelector('form[action$="/mascotas/store"]');
  if (!form) return;
  const sel = form.querySelector('select[name$="o_id"]');
  if (!sel) return;
  let hid = form.querySelector('input[name="dueno_id"]');
  if (!hid) {
    hid = document.createElement('input');
    hid.type = 'hidden';
    hid.name = 'dueno_id';
    form.appendChild(hid);
  }
  const sync = ()=> hid.value = sel.value || '';
  sel.addEventListener('change', sync);
  sync();
});
</script>
