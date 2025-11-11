<?php $n = trim((string)($cliente["nombre"] ?? "")); $a = trim((string)($cliente["apellido"] ?? "")); $ini = mb_strtoupper(mb_substr($n,0,1).mb_substr($a,0,1)); ?><?php
// $cliente: array con datos y 'foto' si existe en tabla perfil
$foto = $cliente['foto'] ?? null;
$fotoUrl = $foto ? "/vetsmart/assets/uploads/clientes/" . rawurlencode($foto) : "https://via.placeholder.com/150x150?text=Foto";
?>
<div class="perfil-container">
  <!-- Header Section -->
  <div class="header-section mb-4">
    <div class="d-flex align-items-center">
      <div class="section-icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div>
        <h2 class="h4 mb-1">Mi Perfil</h2>
        <p class="text-muted mb-0">Gestiona tu información personal</p>
      </div>
    </div>
  </div>

  <div class="card perfil-card border-0 shadow-sm">
    <div class="card-body p-4">
      <!-- Alert Messages -->
      <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?= htmlspecialchars($mensaje['tipo'] ?? 'info') ?> alert-dismissible fade show mb-4" role="alert">
          <i class="fas fa-<?= ($mensaje['tipo'] ?? '') === 'success' ? 'check-circle' : 'info-circle' ?> me-2"></i>
          <?= htmlspecialchars($mensaje['texto'] ?? '') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <form method="post" action="/vetsmart/cliente/perfil/actualizar" enctype="multipart/form-data" class="perfil-form">
        <?= CSRF::inputField(); ?>
        
        <div class="row g-4">
          <!-- Photo Section -->
          <div class="col-12 col-lg-4">
            <div class="photo-section text-center">
              <div class="photo-container mb-3">
                <div class="profile-photo initials" id="avatarIniciales"><?php echo htmlspecialchars($ini ?: "CL"); ?></div>
                <div class="photo-overlay">
                  <i class="fas fa-camera"></i>
                </div>
              </div>
              <div class="photo-upload">
                <label class="file-upload-btn btn btn-outline-primary btn-sm w-100">
                  <i class="fas fa-upload me-2"></i>Cambiar foto
                  <input type="file" name="foto" accept="image/*" class="d-none" onchange="previewImage(this)" />
                </label>
                <small class="text-muted d-block mt-2">La imagen se puede subir, pero el avatar se muestra con iniciales</small>
              </div>
            </div>
          </div>

          <!-- Form Fields -->
          <div class="col-12 col-lg-8">
            <div class="form-section">
              <div class="row g-3">
                <!-- Personal Information -->
                <div class="col-12">
                  <h6 class="section-title mb-3">
                    <i class="fas fa-user me-2"></i>Información Personal
                  </h6>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">
                    <i class="fas fa-signature me-1 text-muted"></i>
                    Nombre
                  </label>
                  <input type="text" name="nombre" class="form-control form-control-lg" 
                         value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>" 
                         placeholder="Tu nombre" required />
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">
                    <i class="fas fa-signature me-1 text-muted"></i>
                    Apellido
                  </label>
                  <input type="text" name="apellido" class="form-control form-control-lg" 
                         value="<?= htmlspecialchars($cliente['apellido'] ?? '') ?>" 
                         placeholder="Tu apellido" required />
                </div>

                <!-- Contact Information -->
                <div class="col-12 mt-4">
                  <h6 class="section-title mb-3">
                    <i class="fas fa-address-book me-2"></i>Información de Contacto
                  </h6>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">
                    <i class="fas fa-envelope me-1 text-muted"></i>
                    Correo Electrónico
                  </label>
                  <input type="email" name="email" class="form-control form-control-lg" 
                         value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" 
                         placeholder="correo@ejemplo.com" required />
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">
                    <i class="fas fa-phone me-1 text-muted"></i>
                    Teléfono
                  </label>
                  <input type="text" name="telefono" class="form-control form-control-lg" 
                         value="<?= htmlspecialchars(($cliente['telefono_cliente'] ?? $cliente['telefono'] ?? '')) ?>" 
                         placeholder="+1 234 567 8900" />
                </div>
                
                <div class="col-12">
                  <label class="form-label">
                    <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                    Dirección
                  </label>
                  <input type="text" name="direccion" class="form-control form-control-lg" 
                         value="<?= htmlspecialchars(($cliente['direccion_cliente'] ?? $cliente['direccion'] ?? '')) ?>" 
                         placeholder="Tu dirección completa" />
                </div>

                <!-- Security Section -->
                <div class="col-12 mt-4">
                  <h6 class="section-title mb-3">
                    <i class="fas fa-lock me-2"></i>Seguridad
                  </h6>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">
                    <i class="fas fa-key me-1 text-muted"></i>
                    Nueva Contraseña
                  </label>
                  <input type="password" name="password" class="form-control form-control-lg" 
                         placeholder="Dejar en blanco para no cambiar" />
                  <small class="text-muted">Mínimo 8 caracteres</small>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="form-actions mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
                <button type="reset" class="btn btn-outline-secondary btn-lg ms-2">
                  <i class="fas fa-undo me-2"></i>Restablecer
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.perfil-container {
  padding: 1.5rem 0;
}

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

/* Profile Card */
.perfil-card {
  border-radius: 20px;
  border-left: 4px solid #06b6d4;
}

/* Photo Section */
.photo-section {
  padding: 1rem;
}

.photo-container {
  position: relative;
  display: inline-block;
  border-radius: 20px;
  overflow: hidden;
}

.profile-photo {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 20px;
  border: 3px solid #e2e8f0;
  transition: all 0.3s ease;
}

.photo-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.3s ease;
  border-radius: 20px;
}

.photo-container:hover .photo-overlay {
  opacity: 1;
}

.photo-container:hover .profile-photo {
  transform: scale(1.05);
}

.file-upload-btn {
  border-radius: 10px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.file-upload-btn:hover {
  transform: translateY(-1px);
}

/* Form Sections */
.section-title {
  color: #1e293b;
  font-weight: 600;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #f1f5f9;
}

.form-label {
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.5rem;
}

.form-control-lg {
  border-radius: 12px;
  border: 2px solid #e2e8f0;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.form-control-lg:focus {
  border-color: #06b6d4;
  box-shadow: 0 0 0 0.2rem rgba(6, 182, 212, 0.1);
}

/* Form Actions */
.form-actions {
  padding-top: 1.5rem;
}

.btn-lg {
  border-radius: 12px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
  border: none;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
}

.btn-outline-secondary:hover {
  transform: translateY(-2px);
}

/* Alert Styling */
.alert {
  border-radius: 12px;
  border: none;
  padding: 1rem 1.5rem;
}

.alert-success {
  background: #f0fdf4;
  color: #166534;
  border-left: 4px solid #22c55e;
}

.alert-info {
  background: #f0f9ff;
  color: #1e40af;
  border-left: 4px solid #3b82f6;
}

/* Responsive Design */
@media (max-width: 768px) {
  .perfil-container {
    padding: 1rem 0;
  }
  
  .header-section .d-flex {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .section-icon {
    margin-bottom: 1rem;
  }
  
  .photo-section {
    text-align: center;
    margin-bottom: 2rem;
  }
  
  .form-control-lg {
    font-size: 0.9rem;
    padding: 0.625rem 0.875rem;
  }
  
  .form-actions {
    text-align: center;
  }
  
  .form-actions .btn {
    width: 100%;
    margin-bottom: 0.5rem;
  }
}

@media (max-width: 576px) {
  .perfil-card .card-body {
    padding: 1.5rem;
  }
  
  .profile-photo {
    width: 120px;
    height: 120px;
  }
  
  .section-title {
    font-size: 0.9rem;
  }
}

.profile-photo.initials { display:inline-flex; align-items:center; justify-content:center; background:#0284a8; color:#fff; font-weight:800; font-size:48px; letter-spacing:1px; text-transform:uppercase; }
</style>

<script>
function previewImage(input) {
  const file = input.files && input.files[0];
  if (!file) return;
  
  // Check file size (5MB limit)
  if (file.size > 5 * 1024 * 1024) {
    alert('La imagen debe ser menor a 5MB');
    input.value = '';
    return;
  }
  
  // Check file type
  if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
    alert('Solo se permiten imágenes JPG y PNG');
    input.value = '';
    return;
  }
  
  const reader = new FileReader();
  reader.onload = function(e) {
    const preview = document.getElementById('previewFoto');
    preview.src = e.target.result;
    
    // Add loading animation
    preview.style.opacity = '0.7';
    setTimeout(() => {
      preview.style.opacity = '1';
    }, 300);
  };
  reader.readAsDataURL(file);
}

// Add click event to file upload button
document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.querySelector('input[type="file"]');
  const uploadBtn = document.querySelector('.file-upload-btn');
  
  uploadBtn.addEventListener('click', function(e) {
    e.preventDefault();
    fileInput.click();
  });
});
</script>





