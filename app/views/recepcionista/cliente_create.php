<div class="client-registration-container">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="page-title mb-2">
        <i class="fas fa-user-plus me-2 text-success"></i>Registrar Nuevo Cliente
      </h1>
      <p class="text-muted mb-0">Complete la información del cliente y sus mascotas</p>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if (!empty($mensajeError ?? null)): ?>
    <div class="alert alert-danger d-flex align-items-center">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <?= $mensajeError['texto'] ?>
    </div>
  <?php endif; ?>

  <!-- Main Form -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form action="<?= BASE ?>/recepcionista/clientes/store" method="POST" enctype="multipart/form-data" id="formCliente">
        <?= CSRF::inputField(); ?>
        
        <!-- Client Information Section -->
        <div class="section-header mb-4">
          <h5 class="section-title">
            <i class="fas fa-user me-2 text-primary"></i>Información del Cliente
          </h5>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" placeholder="Ingrese el nombre" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Apellido <span class="text-danger">*</span></label>
            <input type="text" name="apellido" class="form-control" placeholder="Ingrese el apellido" required>
          </div>
          
          <div class="col-md-4">
            <label class="form-label fw-medium">Documento <span class="text-danger">*</span></label>
            <input type="text" name="documento" class="form-control" placeholder="Número de documento" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">Teléfono</label>
            <input type="text" name="telefono" class="form-control" placeholder="Numero de contacto">
          </div>
          
          <div class="col-12">
            <label class="form-label fw-medium">Dirección</label>
            <input type="text" name="direccion" class="form-control" placeholder="Direccion completa">
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-medium">Contraseña <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" placeholder="Minimo 6 caracteres" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Confirmar Contraseña <span class="text-danger">*</span></label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Repita la contraseña" required>
          </div>
        <div class="col-md-6"> <label class="form-label fw-medium">Foto de perfil</label><input type="file" name="foto" accept="image/*" class="form-control"><div class="form-text">JPG, PNG, GIF. Máx 2MB.</div></div></div>

        <!-- Pets Section -->
        <div class="section-header mb-4">
          <h5 class="section-title">
            <i class="fas fa-paw me-2 text-warning"></i>Mascotas
          </h5>
          <p class="section-subtitle text-muted">Agregue una o varias mascotas del cliente (opcional)</p>
        </div>

        <div class="alert alert-info py-2 px-3 mb-3">
          <i class="fas fa-info-circle me-1"></i> La foto de perfil se muestra con las iniciales del cliente.
        </div>
        <div id="mascotasContainer">
          <!-- Template -->
          <div class="mascota-item card mb-3 border">
            <div class="card-body">
              <div class="row g-2">
                <div class="col-md-4">
                  <label class="form-label">Nombre</label>
                  <input type="text" name="pets[0][nombre]" class="form-control" placeholder="Nombre de la mascota">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Especie</label>
                  <input type="text" name="pets[0][especie]" class="form-control" placeholder="Ej: Canino">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Raza</label>
                  <input type="text" name="pets[0][raza]" class="form-control" placeholder="Ej: Labrador">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Edad</label>
                  <input type="number" name="pets[0][edad]" class="form-control" placeholder="0" min="0">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Sexo</label>
                  <select name="pets[0][sexo]" class="form-select">
                    <option value="">Seleccionar</option>
                    <option value="Macho">Macho</option>
                    <option value="Hembra">Hembra</option>
                  </select>
                </div>
              </div>
              <div class="text-end mt-2">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-mascota">
                  <i class="fas fa-times me-1"></i>Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <button type="button" id="addMascotaBtn" class="btn btn-outline-primary">
            <i class="fas fa-plus me-1"></i>Agregar Mascota
          </button>
        </div>

        <!-- Form Actions -->
        <div class="form-actions border-top pt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="<?= BASE ?>/recepcionista/clientes" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-success px-4">
              <i class="fas fa-save me-1"></i>Guardar Cliente
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.client-registration-container {
  max-width: 100%;
}

.page-title {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.6rem;
}

.section-header {
  border-bottom: 2px solid #e9ecef;
  padding-bottom: 0.5rem;
}

.section-title {
  color: #495057;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.section-subtitle {
  font-size: 0.9rem;
}

.mascota-item {
  transition: all 0.2s ease;
}

.mascota-item:hover {
  border-color: #0d6efd !important;
}

.form-label {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.form-control, .form-select {
  border-radius: 6px;
  border: 1px solid #dee2e6;
  transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
}

.btn {
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn-success {
  background: linear-gradient(135deg, #198754, #20c997);
  border: none;
}

.btn-success:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
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
  
  .btn {
    padding: 0.5rem 1rem;
  }
}

@media (max-width: 576px) {
  .client-registration-container {
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
}
</style>

<script>
// Ocultar campo de foto: usamos avatar por iniciales
// document.addEventListener('DOMContentLoaded', function(){
//   const form = document.getElementById('formCliente');
//   if (!form) return;
//   const file = form.querySelector('input[name="foto"]');
//   if (file) {
//     const lbl = file.previousElementSibling; // label
//     const help = file.nextElementSibling;   // form-text
//     // file.remove(); if (help) help.remove(); if (lbl && lbl.tagName==='LABEL') lbl.remove();
//   }
// });

document.addEventListener('DOMContentLoaded', function() {
  let idx = 1;
  const container = document.getElementById('mascotasContainer');
  const addBtn = document.getElementById('addMascotaBtn');

  // Add new pet form
  addBtn.addEventListener('click', function() {
    const template = container.querySelector('.mascota-item').cloneNode(true);
    
    // Update input names with new index
    template.querySelectorAll('input, select').forEach(input => {
      const name = input.getAttribute('name');
      if (name) {
        input.setAttribute('name', name.replace(/\[\d+\]/, `[${idx}]`));
        input.value = '';
      }
    });
    
    container.appendChild(template);
    idx++;
    attachRemoveListeners();
  });

  // Attach remove listeners
  function attachRemoveListeners() {
    const removeButtons = container.querySelectorAll('.btn-remove-mascota');
    
    removeButtons.forEach(button => {
      // Remove existing listeners
      button.replaceWith(button.cloneNode(true));
    });

    // Add new listeners
    container.querySelectorAll('.btn-remove-mascota').forEach(button => {
      button.addEventListener('click', function() {
        const items = container.querySelectorAll('.mascota-item');
        
        if (items.length <= 1) {
          // Clear inputs if it's the only item
          items[0].querySelectorAll('input, select').forEach(input => {
            input.value = '';
          });
          return;
        }
        
        this.closest('.mascota-item').remove();
      });
    });
  }

  // Initial attachment
  attachRemoveListeners();

  // Form validation
  const form = document.getElementById('formCliente');
  form.addEventListener('submit', function(e) {
    const password = form.querySelector('input[name="password"]').value;
    const confirmPassword = form.querySelector('input[name="confirm_password"]').value;
    
    if (password !== confirmPassword) {
      e.preventDefault();
      alert('Las contraseñas no coinciden. Por favor, verifique.');
      return false;
    }
    
    if (password.length < 6) {
      e.preventDefault();
      alert('La contraseña debe tener al menos 6 caracteres.');
      return false;
    }
  });
});
</script>
