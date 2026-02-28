<?php
declare(strict_types=1);
// Espera: $clientes
?>
<div class="container py-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="mb-0">Clientes y Mascotas</h2>
    <a class="btn btn-outline-secondary" href="<?= BASE ?>/peluquero/citas">Ver Citas</a>
  </div>

  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>Cliente</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Mascotas</th>
          <th class="text-end">Citas</th>
          <th class="text-end">Pend.</th>
          <th class="text-end">En proc.</th>
          <th class="text-end">Compl.</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($clientes)): ?>
          <tr><td colspan="8" class="text-muted">Sin clientes asociados a tus citas de peluquería.</td></tr>
        <?php else: foreach ($clientes as $row): ?>
          <tr>
            <td><?= htmlspecialchars((string)($row['cliente'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($row['email'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($row['telefono'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($row['mascotas'] ?? '')) ?></td>
            <td class="text-end"><?= (int)($row['total_citas'] ?? 0) ?></td>
            <td class="text-end"><span class="badge bg-warning"><?= (int)($row['pendientes'] ?? 0) ?></span></td>
            <td class="text-end"><span class="badge bg-info"><?= (int)($row['en_proceso'] ?? 0) ?></span></td>
            <td class="text-end"><span class="badge bg-success"><?= (int)($row['completadas'] ?? 0) ?></span></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

