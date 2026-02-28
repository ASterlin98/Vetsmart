<div class="container-fluid">
  <h1 class="h4 mb-3"><i class="fas fa-box text-success me-2"></i><?= $accion==='editar'?'Editar':'Nuevo' ?> producto</h1>
  <form action="<?= $accion==='editar'? BASE . '/recepcionista/inventario/' . (int)$item['id'] . '/update' : BASE . '/recepcionista/inventario/store' ?>" method="post" class="card p-3 shadow-sm">
    <?= CSRF::inputField(); ?>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input class="form-control" type="text" name="nombre" value="<?= htmlspecialchars($item['nombre'] ?? '') ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Categoría</label>
        <input class="form-control" type="text" name="categoria" value="<?= htmlspecialchars($item['categoria'] ?? '') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">SKU</label>
        <input class="form-control" type="text" name="sku" value="<?= htmlspecialchars($item['sku'] ?? '') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Unidad</label>
        <input class="form-control" type="text" name="unidad" value="<?= htmlspecialchars($item['unidad'] ?? 'unidad') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Stock</label>
        <input class="form-control" type="number" step="0.01" name="stock" value="<?= htmlspecialchars($item['stock'] ?? '0') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Costo</label>
        <input class="form-control" type="number" step="0.01" name="costo" value="<?= htmlspecialchars($item['costo'] ?? '0') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Precio</label>
        <input class="form-control" type="number" step="0.01" name="precio" value="<?= htmlspecialchars($item['precio'] ?? '0') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Notas</label>
        <textarea class="form-control" name="notas" rows="3"><?= htmlspecialchars($item['notas'] ?? '') ?></textarea>
      </div>
    </div>
    <div class="text-end mt-3">
      <a href="<?= BASE ?>/recepcionista/inventario" class="btn btn-outline-secondary">Cancelar</a>
      <button class="btn btn-success" type="submit"><i class="fas fa-save me-1"></i>Guardar</button>
    </div>
  </form>
</div>
