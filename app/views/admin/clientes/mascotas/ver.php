<div class="container py-4">
  <div class="card shadow-sm border-0 rounded-3 mx-auto" style="max-width: 420px;">
    <div class="card-body text-center">

      <!-- Foto o fallback -->
      <div id="avatarWrap" class="mb-3">
        <?php if (!empty($fotoUrl)): ?>
          <img id="avatarImg" src="<?= htmlspecialchars($fotoUrl) ?>" 
               alt="Foto mascota" 
               class="rounded-circle shadow-sm" 
               style="width: 160px; height: 160px; object-fit: cover;">
        <?php else: ?>
          <div id="avatarFallback" 
               class="rounded-circle bg-light d-flex align-items-center justify-content-center shadow-sm mx-auto" 
               style="width: 160px; height: 160px;">
            <span class="fs-1 text-secondary"><?= htmlspecialchars(substr($mascota['nombre'] ?? 'M', 0, 1)) ?></span>
          </div>
        <?php endif; ?>
      </div>

      <!-- Nombre, especie y raza -->
      <h3 class="mb-1"><?= htmlspecialchars($mascota['nombre'] ?? '-') ?></h3>
      <p class="text-muted mb-3">
        <?= htmlspecialchars($mascota['especie'] ?? '-') ?> 
        • <?= htmlspecialchars($mascota['raza'] ?? '-') ?>
      </p>

      <!-- Edad y peso -->
      <div class="d-flex justify-content-center gap-4 mb-4">
        <div class="text-center">
          <div class="fw-bold fs-5">
            <?= ($mascota['edad'] === null || $mascota['edad'] === '') ? '-' : (int)$mascota['edad'] . ' años' ?>
          </div>
          <small class="text-muted">Edad</small>
        </div>
        <div class="text-center">
          <div class="fw-bold fs-5">
            <?= htmlspecialchars($mascota['peso'] ?? '-') ?> kg
          </div>
          <small class="text-muted">Peso</small>
        </div>
      </div>

      <hr>

      <!-- Acciones foto 
      <div class="mt-3">
        <form id="fotoForm" 
              action="/vetsmart/veterinario/mascotas/<?= (int)$mascota['id'] ?>/actualizar-foto" 
              method="POST" 
              enctype="multipart/form-data">
          <div class="mb-2">
            <input id="fileInput" type="file" name="foto" accept="image/*" class="form-control form-control-sm">
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-sm">
              📸 <?= $fotoFsExists ? 'Cambiar foto' : 'Subir foto' ?>
            </button>
          </div>
        </form>

        <?php if (!empty($fotoUrl)): ?>
          <form action="/vetsmart/veterinario/mascotas/<?= (int)$mascota['id'] ?>/eliminar-foto" 
                method="POST" 
                onsubmit="return confirm('¿Eliminar la foto actual?');" 
                class="mt-2">
            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
              🗑️ Eliminar foto
            </button>
          </form>
        <?php endif; ?>
      </div>
-->
    </div>
  </div>
</div>
