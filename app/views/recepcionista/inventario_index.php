<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="fas fa-boxes-stacked text-success me-2"></i>Inventario</h1>
    <a href="<?= BASE ?>/recepcionista/inventario/create" class="btn btn-success"><i class="fas fa-plus me-1"></i>Nuevo producto</a>
  </div>

  <?php if (!empty($_SESSION['mensaje'])): $m = $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
    <div class="alert alert-<?= htmlspecialchars($m['tipo']) ?>"><?= htmlspecialchars($m['texto']) ?></div>
  <?php endif; ?>

  <form class="row g-2 mb-3" method="get" action="<?= BASE ?>/recepcionista/inventario">
    <div class="col-auto"><input class="form-control" type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por nombre o categoria"></div>
    <div class="col-auto"><button class="btn btn-outline-success" type="submit"><i class="fas fa-search me-1"></i>Buscar</button></div>
    <?php if ($q !== ''): ?><div class="col-auto"><a class="btn btn-outline-secondary" href="<?= BASE ?>/recepcionista/inventario">Limpiar</a></div><?php endif; ?>
  </form>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Producto</th>
            <th>Categoria</th>
            <th>SKU</th>
            <th>Unidad</th>
            <th class="text-end">Stock</th>
            <th class="text-end">Costo</th>
            <th class="text-end">Precio</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($items)): foreach ($items as $p): ?>
            <tr>
              <td class="fw-medium text-dark"><?= htmlspecialchars($p['nombre']) ?></td>
              <td><?= htmlspecialchars($p['categoria'] ?? '-') ?></td>
              <td><?= htmlspecialchars($p['sku'] ?? '-') ?></td>
              <td><?= htmlspecialchars($p['unidad'] ?? '-') ?></td>
              <td class="text-end"><?= number_format((float)($p['stock'] ?? 0), 2) ?></td>
              <td class="text-end">$ <?= number_format((float)($p['costo'] ?? 0), 2) ?></td>
              <td class="text-end">$ <?= number_format((float)($p['precio'] ?? 0), 2) ?></td>
              <td class="text-end">
                <form action="<?= BASE ?>/recepcionista/inventario/<?= (int)$p['id'] ?>/ajustar" method="post" class="d-inline-flex align-items-center gap-1">
                  <?= CSRF::inputField(); ?>
                  <select name="tipo" class="form-select form-select-sm" style="width:auto">
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                  </select>
                  <input type="number" name="cantidad" step="0.01" min="0" class="form-control form-control-sm" style="width:90px" placeholder="Cant.">
                  <button class="btn btn-sm btn-outline-success" title="Ajustar stock"><i class="fas fa-plus-minus"></i></button>
                </form>
                <a href="<?= BASE ?>/recepcionista/inventario/<?= (int)$p['id'] ?>/edit" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                <a href="<?= BASE ?>/recepcionista/inventario/<?= (int)$p['id'] ?>/delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar producto?')" title="Eliminar"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="8" class="text-center text-muted py-4">Sin productos en inventario.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
