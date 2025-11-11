<?php
declare(strict_types=1);
// Espera: $citas, $estado, $desde, $hasta
?>
<div class="container py-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="mb-0">Citas de Peluquería</h2>
    <a href="/vetsmart/peluquero/servicios" class="btn btn-outline-secondary">Servicios</a>
  </div>

  <form method="get" class="row g-2 mb-3">
    <div class="col-md-3">
      <label class="form-label small">Estado</label>
      <select name="estado" class="form-select form-select-sm">
        <option value="">Todos</option>
        <?php foreach ([
          'pendiente' => 'Pendiente',
          'confirmada' => 'En proceso',
          'completada' => 'Completada',
          'cancelada' => 'Cancelada',
        ] as $val=>$label): ?>
          <option value="<?= $val ?>" <?= ($estado ?? '')===$val?'selected':'' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small">Desde</label>
      <input type="date" name="desde" value="<?= htmlspecialchars((string)($desde ?? '')) ?>" class="form-control form-control-sm">
    </div>
    <div class="col-md-3">
      <label class="form-label small">Hasta</label>
      <input type="date" name="hasta" value="<?= htmlspecialchars((string)($hasta ?? '')) ?>" class="form-control form-control-sm">
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button class="btn btn-primary btn-sm w-100">Filtrar</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>Fecha</th><th>Hora</th><th>Cliente</th><th>Mascota</th><th>Servicio</th><th>Estado</th><th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($citas)): ?>
          <tr><td colspan="7" class="text-muted">No hay registros.</td></tr>
        <?php else: foreach ($citas as $c): ?>
          <tr>
            <td><?= htmlspecialchars((string)($c['fecha'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($c['hora'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($c['cliente'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($c['mascota'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($c['servicio'] ?? '')) ?></td>
            <td>
              <?php $e = (string)($c['estado'] ?? '');
                $cls = $e==='pendiente'?'warning':($e==='confirmada'?'info':($e==='completada'?'success':($e==='cancelada'?'secondary':'secondary')));
                $label = $e==='confirmada'?'en_proceso':($e==='completada'?'completada':$e);
              ?>
              <span class="badge bg-<?= $cls ?>"><?= htmlspecialchars($label) ?></span>
            </td>
            <td class="text-end">
              <?php if (($c['estado'] ?? '') === 'pendiente'): ?>
                <form method="post" action="/vetsmart/peluquero/citas/atender" class="d-inline">
                  <?= CSRF::inputField() ?>
                  <input type="hidden" name="cita_id" value="<?= (int)$c['id'] ?>">
                  <button class="btn btn-sm btn-info">Atender</button>
                </form>
              <?php endif; ?>
              <?php if (in_array((string)($c['estado'] ?? ''), ['confirmada','pendiente'], true)): ?>
                <form method="post" action="/vetsmart/peluquero/citas/finalizar" class="d-inline" onsubmit="return confirm('¿Finalizar servicio y registrar precio fijo del servicio?');">
                  <?= CSRF::inputField() ?>
                  <input type="hidden" name="cita_id" value="<?= (int)$c['id'] ?>">
                  <span class="me-2 small text-muted">Precio: $ <?= number_format((float)($c['precio'] ?? 0), 2) ?></span>
                  <input type="text" name="notas" class="form-control form-control-sm d-inline w-auto" placeholder="Notas opcionales">
                  <button class="btn btn-sm btn-success">Finalizar</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
