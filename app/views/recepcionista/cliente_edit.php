<div class="client-edit-container">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="page-title mb-2">
        <i class="fas fa-user-edit me-2 text-primary"></i>Editar Cliente
      </h1>
      <p class="text-muted mb-0">Actualice la información del cliente</p>
    </div>
    <div class="client-badge">
      <span class="badge bg-light text-dark">
        <i class="fas fa-id-card me-1"></i>ID: <?= htmlspecialchars($cliente['id']) ?>
      </span>
    </div>
  </div>

  <!-- Carnet + Form -->
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <div class="mb-2">
            <?php if (!empty($cliente['foto']) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/assets/uploads/clientes/' . $cliente['foto'])): ?>
               <div style="width:160px;height:160px;border-radius:12px;overflow:hidden;margin:0 auto;">
                  <img src="/vetsmart/public/assets/uploads/clientes/<?= htmlspecialchars($cliente['foto']) ?>" alt="Foto Cliente" style="width:100%;height:100%;object-fit:cover;">
               </div>
            <?php else: ?>
              <div style="width:160px;height:160px;border-radius:12px;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-size:48px;color:#6c757d;" class="mx-auto">
                <?= strtoupper(substr($cliente['nombre'],0,1) . substr($cliente['apellido'],0,1)) ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="fw-bold fs-5 mb-1"><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></div>
          <div class="text-muted small mb-1">ID: <?= htmlspecialchars($cliente['id']) ?> · Doc: <?= htmlspecialchars($cliente['docusu'] ?? '-') ?></div>
          <div class="text-muted small">Tel: <?= htmlspecialchars($cliente['telefono'] ?? '-') ?> · Email: <?= htmlspecialchars($cliente['email'] ?? '-') ?></div>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form action="/vetsmart/recepcionista/clientes/update" method="POST" enctype="multipart/form-data" id="formClienteEdit">
        <?= CSRF::inputField(); ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($cliente['id']) ?>">

        <!-- Personal Information -->
        <div class="section-header mb-4">
          <h5 class="section-title">
            <i class="fas fa-id-card me-2 text-success"></i>Información Personal
          </h5>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre" 
                   value="<?= htmlspecialchars($cliente['nombre']) ?>" 
                   placeholder="Ingrese el nombre" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Apellido <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="apellido" 
                   value="<?= htmlspecialchars($cliente['apellido']) ?>" 
                   placeholder="Ingrese el apellido" required>
          </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium">Actualizar Foto</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
            <div class="form-text">Subir nueva foto para reemplazar la actual.</div>
        </div>

        <!-- Contact Information -->
        <div class="section-header mb-4">
          <h5 class="section-title">
            <i class="fas fa-address-book me-2 text-info"></i>Información de Contacto
          </h5>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-medium">Correo electrónico <span class="text-danger">*</span></label>
            <input type="email" class="form-control" name="email" 
                   value="<?= htmlspecialchars($cliente['email']) ?>" 
                   placeholder="correo@ejemplo.com" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Teléfono</label>
            <input type="text" class="form-control" name="telefono" 
                   value="<?= htmlspecialchars($cliente['telefono']) ?>" 
                   placeholder="Numero de contacto">
          </div>
        </div>

        <!-- Additional Information -->
        <div class="section-header mb-4">
          <h5 class="section-title">
            <i class="fas fa-home me-2 text-warning"></i>Información Adicional
          </h5>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-medium">Dirección</label>
            <input type="text" class="form-control" name="direccion" 
                   value="<?= htmlspecialchars($cliente['direccion']) ?>" 
                   placeholder="Direccion completa">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Documento</label>
            <input type="text" class="form-control" name="documento" 
                   value="<?= htmlspecialchars($cliente['docusu']) ?>" 
                   placeholder="Numero de documento">
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions border-top pt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="/vetsmart/recepcionista/clientes" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-success px-4">
              <i class="fas fa-save me-1"></i>Guardar Cambios
            </button>
          </div>
        </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.client-edit-container {
  max-width: 100%;
}

.page-title {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.6rem;
}

.client-badge .badge {
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
  margin-bottom: 0.25rem;
  font-size: 1.1rem;
}

.form-label {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.form-control {
  border-radius: 6px;
  border: 1px solid #dee2e6;
  transition: all 0.2s ease;
  padding: 0.75rem;
}

.form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
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
  
  .client-badge {
    margin-top: 0.5rem;
  }
}

@media (max-width: 576px) {
  .client-edit-container {
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

/* Animation for form elements */
.form-control {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Enhanced focus states */
.form-control:focus {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(13, 110, 253, 0.15);
}
</style>

<script>
// Ocultar input de foto en edición (avatar por iniciales)
// document.addEventListener('DOMContentLoaded', function(){
//   const form = document.getElementById('formClienteEdit');
//   if (!form) return;
//   const file = form.querySelector('input[name="foto"]');
//   if (file) {
//     const wrapper = file.closest('.row') || file.parentElement;
//     // if (wrapper) wrapper.remove(); else file.remove();
//   }
// });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Add form validation
  const form = document.querySelector('form');
  const inputs = form.querySelectorAll('input[required]');
  
  inputs.forEach(input => {
    input.addEventListener('blur', function() {
      if (!this.value.trim()) {
        this.classList.add('is-invalid');
      } else {
        this.classList.remove('is-invalid');
      }
    });
    
    input.addEventListener('input', function() {
      if (this.value.trim()) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
      } else {
        this.classList.remove('is-valid');
      }
    });
  });
  
  // Form submission enhancement
  form.addEventListener('submit', function(e) {
    let isValid = true;
    
    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.classList.add('is-invalid');
        isValid = false;
      }
    });
    
    if (!isValid) {
      e.preventDefault();
      // Smooth scroll to first invalid input
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
});
</script>
