<?php $n = trim((string)($cliente["nombre"] ?? "")); $a = trim((string)($cliente["apellido"] ?? "")); $ini = mb_strtoupper(mb_substr($n,0,1).mb_substr($a,0,1)); ?><?php
// $cliente: array con datos y 'foto' si existe en tabla perfil
$foto = $cliente['foto'] ?? null;
$fotoUrl = $foto ? "<?= BASE ?>/public/assets/uploads/clientes/" . rawurlencode($foto) : null;
// Token CSRF para la subida AJAX
$csrfToken = CSRF::generateToken();
?>
<div class="perfil-container container-fluid p-0">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="page-title mb-2">
        <i class="fas fa-user-circle me-2 text-primary"></i>Mi Perfil
      </h1>
      <p class="text-muted mb-0">Gestiona tu información personal y de seguridad</p>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-4 mb-4">
      <!-- Profile Card (foto se sube de forma independiente via AJAX) -->
      <div class="card border-0 shadow-sm text-center h-100">
        <div class="card-body p-4">
          <div class="position-relative d-inline-block mb-3" id="avatar-wrapper" style="cursor: pointer;" onclick="document.getElementById('foto-input').click()" title="Haz clic para cambiar tu foto">
            <?php if ($fotoUrl): ?>
              <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto de perfil" id="avatar-img" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
            <?php else: ?>
              <div id="avatar-initials" class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-primary text-white display-4 fw-bold" style="width: 150px; height: 150px;">
                <?= htmlspecialchars($ini ?: "CL") ?>
              </div>
            <?php endif; ?>
            <!-- Overlay de cámara al hacer hover -->
            <div class="avatar-overlay rounded-circle d-flex align-items-center justify-content-center" id="avatar-overlay">
              <i class="fas fa-camera fa-2x text-white"></i>
            </div>
            <!-- Spinner de carga -->
            <div class="avatar-loading rounded-circle d-flex align-items-center justify-content-center" id="avatar-loading" style="display: none;">
              <i class="fas fa-spinner fa-spin fa-2x text-white"></i>
            </div>
          </div>
          <!-- Input oculto para la foto (fuera del form principal) -->
          <input type="file" id="foto-input" accept="image/jpeg,image/png" class="d-none" onchange="subirFotoInstantanea(this)">
          
          <h4 class="mb-1"><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></h4>
          <p class="text-muted mb-3"><?= htmlspecialchars($cliente['email']) ?></p>
          
          <div class="d-grid">
            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('foto-input').click()">
              <i class="fas fa-camera me-2"></i>Cambiar Foto
            </button>
          </div>
          <small class="text-muted d-block mt-2">JPG o PNG, máx 2MB</small>
          <div id="foto-msg" class="mt-2" style="display: none;"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <!-- Edit Form Card -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?= htmlspecialchars($mensaje['tipo'] ?? 'info') ?> alert-dismissible fade show mb-4" role="alert">
              <i class="fas fa-<?= ($mensaje['tipo'] ?? '') === 'success' ? 'check-circle' : 'info-circle' ?> me-2"></i>
              <?= htmlspecialchars($mensaje['texto'] ?? '') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form method="post" action="<?= BASE ?>/cliente/perfil/actualizar" enctype="multipart/form-data" id="perfilForm">
            <?= CSRF::inputField(); ?>

            <h5 class="card-title mb-4 text-primary"><i class="fas fa-info-circle me-2"></i>Información Personal</h5>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Apellido</label>
                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($cliente['apellido'] ?? '') ?>" required>
              </div>
            </div>

            <h5 class="card-title mb-4 text-primary"><i class="fas fa-address-card me-2"></i>Contacto</h5>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-bold">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars(($cliente['telefono_cliente'] ?? $cliente['telefono'] ?? '')) ?>" placeholder="+XX XXX XXX XXXX">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold">Dirección</label>
                <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars(($cliente['direccion_cliente'] ?? $cliente['direccion'] ?? '')) ?>" placeholder="Calle, Número, Ciudad">
              </div>
            </div>

            <h5 class="card-title mb-4 text-primary"><i class="fas fa-lock me-2"></i>Seguridad</h5>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-bold">Nueva Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la actual">
                <small class="text-muted">Mínimo 8 caracteres si desea cambiarla.</small>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
              <button type="reset" class="btn btn-secondary">
                <i class="fas fa-undo me-2"></i>Cancelar
              </button>
              <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-2"></i>Guardar Cambios
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
/**
 * Subida instantánea de foto de perfil (tipo Instagram).
 * Al seleccionar archivo: se muestra preview + se sube al servidor via AJAX.
 */
function subirFotoInstantanea(input) {
  const file = input.files && input.files[0];
  if (!file) return;
  
  // Validaciones del lado del cliente
  if (file.size > 2 * 1024 * 1024) {
    mostrarMensajeFoto('error', 'La imagen debe ser menor a 2MB');
    input.value = '';
    return;
  }
  
  if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
    mostrarMensajeFoto('error', 'Solo se permiten imágenes JPG y PNG');
    input.value = '';
    return;
  }

  // Mostrar preview inmediato
  const reader = new FileReader();
  reader.onload = function(e) {
    mostrarPreview(e.target.result);
  };
  reader.readAsDataURL(file);

  // Subir al servidor via AJAX
  const formData = new FormData();
  formData.append('foto', file);
  formData.append('_csrf', '<?= htmlspecialchars($csrfToken) ?>');

  // Mostrar spinner
  document.getElementById('avatar-overlay').style.display = 'none';
  document.getElementById('avatar-loading').style.display = 'flex';

  fetch('<?= BASE ?>/cliente/perfil/foto', {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    document.getElementById('avatar-loading').style.display = 'none';
    if (data.success) {
      // Actualizar imagen con la URL del servidor (evita caché)
      const img = document.getElementById('avatar-img');
      if (img) {
        img.src = data.url + '?t=' + Date.now();
      }
      mostrarMensajeFoto('success', '¡Foto actualizada exitosamente!');
    } else {
      mostrarMensajeFoto('error', data.error || 'Error al subir la foto');
    }
  })
  .catch(err => {
    document.getElementById('avatar-loading').style.display = 'none';
    mostrarMensajeFoto('error', 'Error de conexión. Intenta de nuevo.');
    console.error('Error subida foto:', err);
  });

  // Limpiar input para permitir re-selección del mismo archivo
  input.value = '';
}

function mostrarPreview(dataUrl) {
  const wrapper = document.getElementById('avatar-wrapper');
  let img = document.getElementById('avatar-img');
  
  if (img) {
    img.src = dataUrl;
  } else {
    // Si se mostraban iniciales, reemplazar con imagen
    const initials = document.getElementById('avatar-initials');
    if (initials) { initials.remove(); }
    
    img = document.createElement('img');
    img.id = 'avatar-img';
    img.alt = 'Foto de perfil';
    img.className = 'rounded-circle img-thumbnail';
    img.style.cssText = 'width: 150px; height: 150px; object-fit: cover;';
    img.src = dataUrl;
    wrapper.insertBefore(img, wrapper.querySelector('.avatar-overlay'));
  }
}

function mostrarMensajeFoto(tipo, texto) {
  const msgDiv = document.getElementById('foto-msg');
  msgDiv.style.display = 'block';
  msgDiv.className = 'mt-2 alert alert-' + (tipo === 'success' ? 'success' : 'danger') + ' py-1 px-2 small';
  msgDiv.innerHTML = '<i class="fas fa-' + (tipo === 'success' ? 'check-circle' : 'exclamation-circle') + ' me-1"></i>' + texto;
  
  // Auto-ocultar después de 4 segundos
  setTimeout(function() {
    msgDiv.style.display = 'none';
  }, 4000);
}
</script>

<style>
.page-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #2c3e50;
}
.card {
  transition: transform 0.2s;
}
/* Overlay de cámara sobre el avatar */
#avatar-wrapper {
  position: relative;
}
.avatar-overlay {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 150px;
  height: 150px;
  background: rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s ease;
}
#avatar-wrapper:hover .avatar-overlay {
  opacity: 1;
}
/* Loading spinner */
.avatar-loading {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 150px;
  height: 150px;
  background: rgba(0, 0, 0, 0.6);
}
</style>





