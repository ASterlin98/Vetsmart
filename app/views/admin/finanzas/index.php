<div class="container-fluid py-3">
  <h1 class="fw-bold">💰 Finanzas</h1>

  <!-- Filtros -->
  <form class="row g-3 mb-4" method="get" action="">
    <div class="col-md-4">
      <label class="form-label">Desde</label>
      <input type="date" class="form-control" name="desde" value="<?= htmlspecialchars($desde) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Hasta</label>
      <input type="date" class="form-control" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <button type="submit" class="btn btn-primary me-2">Filtrar</button>
      <a href="/vetsmart/admin/exportarFinanzasExcel?desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>" class="btn btn-success">📊 Exportar Excel</a>
    </div>
  </form>

  <!-- Tabla -->
  <div class="card p-3">
    <div class="card-body">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Servicio</th>
            <th>Cliente</th>
            <th>Empleado</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($finanzas)): ?>
            <tr><td colspan="6" class="text-center">⚠️ No hay registros en este rango</td></tr>
          <?php else: ?>
            <?php foreach ($finanzas as $f): ?>
              <tr>
                <td><?= $f['id'] ?></td>
                <td><?= $f['fecha'] ?></td>
                <td><?= $f['servicio'] ?></td>
                <td><?= $f['cliente'] ?></td>
                <td><?= $f['empleado'] ?></td>
                <td>$<?= number_format($f['valor'], 0, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Totales -->
  <div class="alert alert-success mt-3">
    <strong>Total Recaudado:</strong> $<?= number_format($total, 0, ',', '.') ?>
  </div>
</div>
