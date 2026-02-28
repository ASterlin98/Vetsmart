<div class="container-fluid mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-4 text-gray-800">Reportes</h1>
        <a href="<?= BASE ?>/admin/reportesSoporte" class="btn btn-primary mb-4">Crear Ticket de Soporte</a>
    </div>

  <!-- Filtros -->
  <form class="row mb-4" method="get" action="">
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
      <a href="<?= BASE ?>/admin/exportarReportesExcel?desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>" class="btn btn-success">📊 Exportar Excel</a>
    </div>
  </form>
  
<div class="row">
  <div class="col-md-6">
    <div class="card mb-4 shadow-sm">
      <div class="card-body text-center">
        <h5 class="card-title">🔝 Servicios más solicitados</h5>
        <canvas id="chartServicios" style="max-width: 100%; height: 715px;"></canvas>
        <ul class="mt-3 text-start">
          <?php foreach ($topServicios as $s): ?>
            <li><?= $s['nombre'] ?>: <strong><?= $s['cantidad'] ?></strong> citas</li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-md-6">
      <div class="card mb-4 shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">👥 Empleados con más citas</h5>
          <canvas id="chartEmpleados" style="max-width: 100%; height: 80px;"></canvas>
          <ul class="mt-3 text-start">
            <?php foreach ($topEmpleados as $e): ?>
              <li><?= $e['empleado'] ?>: <strong><?= $e['cantidad'] ?></strong> citas</li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Chart Servicios
  const ctxServicios = document.getElementById('chartServicios');
  new Chart(ctxServicios, {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_column($topServicios, 'nombre')) ?>,
      datasets: [{
        label: 'Cantidad de Citas',
        data: <?= json_encode(array_column($topServicios, 'cantidad')) ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    }
  });

  // Chart Empleados
  const ctxEmpleados = document.getElementById('chartEmpleados');
  new Chart(ctxEmpleados, {
    type: 'pie',
    data: {
      labels: <?= json_encode(array_column($topEmpleados, 'empleado')) ?>,
      datasets: [{
        label: 'Citas atendidas',
        data: <?= json_encode(array_column($topEmpleados, 'cantidad')) ?>,
        backgroundColor: [
          'rgba(255, 99, 132, 0.6)',
          'rgba(54, 162, 235, 0.6)',
          'rgba(255, 206, 86, 0.6)',
          'rgba(75, 192, 192, 0.6)',
          'rgba(153, 102, 255, 0.6)'
        ]
      }]
    }
  });
</script>
