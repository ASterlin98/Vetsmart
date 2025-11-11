<div class="container py-4">
  <div class="card shadow border-0 rounded-4 mx-auto" style="max-width: 480px;">
    <div class="card-body text-center">

      <!-- Foto o inicial -->
      <div class="mb-3">
        <?php if (!empty($fotoUrl)): ?>
          <img src="<?= htmlspecialchars($fotoUrl) ?>" 
               alt="Foto de <?= htmlspecialchars($mascota['nombre']) ?>" 
               class="rounded-circle shadow-sm border" 
               style="width: 160px; height: 160px; object-fit: cover;">
        <?php else: ?>
          <div class="rounded-circle bg-gradient d-flex align-items-center justify-content-center shadow-sm mx-auto" 
               style="width: 160px; height: 160px; background: linear-gradient(135deg,#e2e8f0,#f8fafc);">
            <span class="fs-1 fw-bold text-secondary"><?= htmlspecialchars(substr($mascota['nombre'] ?? 'M', 0, 1)) ?></span>
          </div>
        <?php endif; ?>
      </div>

      <!-- Datos principales -->
      <h3 class="fw-bold mb-1 text-primary"><?= htmlspecialchars($mascota['nombre'] ?? '-') ?></h3>
      <p class="text-muted mb-4">
        <?= htmlspecialchars($mascota['especie'] ?? '-') ?> 
        <span class="text-secondary">•</span> 
        <?= htmlspecialchars($mascota['raza'] ?? '-') ?>
      </p>

      <!-- Edad y Peso -->
      <div class="d-flex justify-content-center gap-4 mb-4">
        <div>
          <div class="fw-semibold fs-5 text-dark">
            <?= ($mascota['edad'] === null || $mascota['edad'] === '') ? '-' : (int)$mascota['edad'] . ' años' ?>
          </div>
          <small class="text-muted">Edad</small>
        </div>
        <div>
          <div class="fw-semibold fs-5 text-dark">
            <?= htmlspecialchars($mascota['peso'] ?? '-') ?> kg
          </div>
          <small class="text-muted">Peso</small>
        </div>
      </div>

      <!-- Línea divisoria -->
      <hr class="mb-4">

      <!-- Botones de acción -->
      <div class="d-grid gap-2">

        <!-- Botón de volver -->
        <a href="/vetsmart/admin/clientes" 
           class="btn btn-secondary mt-2">
          ← Volver a Clientes
        </a>
      </div>
    </div>
  </div>
</div>
