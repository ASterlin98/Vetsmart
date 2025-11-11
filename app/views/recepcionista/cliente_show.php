<div class="container py-3">
  <div class="card perfil-card shadow mx-auto" style="max-width:420px;">
    <div class="card-body text-center">
      <?php 
        $ini = strtoupper(substr((string)($cliente['nombre'] ?? ''),0,1) . substr((string)($cliente['apellido'] ?? ''),0,1));
      ?>
      <div class="rounded-circle mb-3 bg-light d-inline-flex align-items-center justify-content-center" style="width:130px;height:130px;color:#6b7280;font-size:42px;">
        <?= htmlspecialchars($ini ?: 'U') ?>
      </div>
      <h4 class="mb-0"><?= htmlspecialchars(($cliente['nombre'] ?? '-') . ' ' . ($cliente['apellido'] ?? '')) ?></h4>
      <p class="text-muted mb-1">Documento: <?= htmlspecialchars($cliente['docusu'] ?? '-') ?></p>
      <p class="text-muted">Email: <?= htmlspecialchars($cliente['email'] ?? '-') ?></p>
      <p>Teléfono: <?= htmlspecialchars($cliente['telefono'] ?? '-') ?> — Dirección: <?= htmlspecialchars($cliente['direccion'] ?? '-') ?></p>
      <hr>
      <p class="text-muted small">ID Interno: #<?= htmlspecialchars($cliente['id'] ?? '-') ?></p>
    </div>
  </div>
  <div class="text-center mt-3">
    <a href="/vetsmart/recepcionista/clientes" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Volver</a>
    <a href="/vetsmart/recepcionista/clientes/edit/<?= (int)($cliente['id'] ?? 0) ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
  </div>
</div>

<style>
.perfil-foto { object-fit: cover; }
.perfil-card { border-radius: 16px; }
</style>
