<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="fas fa-cash-register text-success me-2"></i>Ingresos y Egresos</h1>
    <a href="<?= BASE ?>/recepcionista/ingresos/create" class="btn btn-success"><i class="fas fa-plus me-1"></i>Nuevo</a>
  </div>

  <?php if (!empty($_SESSION['mensaje'])): $m = $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
    <div class="alert alert-<?= htmlspecialchars($m['tipo']) ?>"><?= htmlspecialchars($m['texto']) ?></div>
  <?php endif; ?>

  <form class="row g-2 mb-3" method="get" action="<?= BASE ?>/recepcionista/ingresos">
    <div class="col-auto">
      <select name="tipo" class="form-select">
        <option value="">Todos</option>
        <option value="ingreso" <?= ($tipo==='ingreso'?'selected':'') ?>>Ingresos</option>
        <option value="egreso" <?= ($tipo==='egreso'?'selected':'') ?>>Egresos</option>
      </select>
    </div>
    <div class="col-auto">
      <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde ?? '') ?>" />
    </div>
    <div class="col-auto">
      <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta ?? '') ?>" />
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-success" type="submit"><i class="fas fa-filter me-1"></i>Filtrar</button>
      <a class="btn btn-outline-secondary" href="<?= BASE ?>/recepcionista/ingresos">Limpiar</a>
    </div>
  </form>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Concepto</th>
            <th class="text-end">Monto</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($items)): foreach ($items as $it): ?>
            <tr>
              <td><?= htmlspecialchars($it['fecha']) ?></td>
              <td><span class="badge <?= $it['tipo']==='ingreso'?'bg-success':'bg-danger' ?>"><?= htmlspecialchars(ucfirst($it['tipo'])) ?></span></td>
              <td>
                <div class="fw-medium"><?= htmlspecialchars($it['concepto']) ?></div>
                <?php if (!empty($it['notas'])): ?><div class="text-muted small"><?= htmlspecialchars($it['notas']) ?></div><?php endif; ?>
              </td>
              <td class="text-end">$ <?= number_format((float)$it['monto'], 2) ?></td>
              <td class="text-end">
                <a href="<?= BASE ?>/recepcionista/ingresos/<?= (int)$it['id'] ?>/edit" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                <a href="<?= BASE ?>/recepcionista/ingresos/<?= (int)$it['id'] ?>/delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar movimiento?')"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" class="text-center text-muted py-4">Sin movimientos registrados.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

