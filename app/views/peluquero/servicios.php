<?php
declare(strict_types=1);
// Espera: $servicios
?>
<div class="container py-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="mb-0">Servicios de Peluquería</h2>
    <a class="btn btn-outline-primary" href="<?= BASE ?>/peluquero/citas">Volver a Citas</a>
  </div>

  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>Nombre</th><th>Duración (min)</th><th>Precio Base</th><th>Descripción</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($servicios)): ?>
          <tr><td colspan="4" class="text-muted">No hay servicios activos.</td></tr>
        <?php else: foreach ($servicios as $s): ?>
          <tr>
            <td><?= htmlspecialchars((string)($s['nombre'] ?? '')) ?></td>
            <td><?= (int)($s['duracion_min'] ?? 0) ?></td>
            <td>$ <?= number_format((float)($s['precio'] ?? 0), 2) ?></td>
            <td><?= htmlspecialchars((string)($s['descripcion'] ?? '')) ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
