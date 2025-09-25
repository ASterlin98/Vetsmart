<?php
// app/views/veterinario/reportes/index.php (versión mejorada)
$consultas = $consultas ?? [];
$citas = $citas ?? [];
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reportes - Veterinario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background:#f6f8fb; font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; }
    .card-hero { border-radius:12px; box-shadow: 0 10px 30px rgba(20,24,40,0.06); }
    .stat { font-size:1.25rem; font-weight:700; }
    .small-muted { color:#6b7280; }
    .table-fixed { table-layout: fixed; }
    .table-fixed td { overflow-wrap: anywhere; }
  </style>
</head>
<body>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i> Reportes del Veterinario</h2>
      <small class="text-muted">Resumen de actividad desde <strong><?= $desde ?: 'inicio' ?></strong> hasta <strong><?= $hasta ?: 'hoy' ?></strong></small>
    </div>
    <div class="d-flex gap-2">
      <form id="exportForm" method="POST" action="/vetsmart/veterinario/reportes/exportar" class="d-flex gap-2">
        <input type="hidden" name="desde" value="<?= htmlspecialchars($desde) ?>">
        <input type="hidden" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
        <button type="submit" class="btn btn-danger">Exportar PDF</button>
      </form>
    </div>
  </div>

  <!-- Filtros y rangos rápidos -->
  <div class="card card-hero mb-4 p-3">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Desde</label>
        <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Hasta</label>
        <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Rango rápido</label>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-secondary w-100" data-range="7">Últimos 7 días</button>
          <button type="button" class="btn btn-outline-secondary w-100" data-range="30">30 días</button>
        </div>
      </div>
      <div class="col-md-3 d-grid">
        <button class="btn btn-primary">Filtrar</button>
      </div>
    </form>
  </div>

  <!-- Estadísticas rápidas -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card p-3">
        <div class="small-muted">Consultas</div>
        <div class="stat text-primary"><?= count($consultas) ?></div>
        <div class="small text-muted">Total en el periodo</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <div class="small-muted">Citas atendidas</div>
        <div class="stat text-success"><?= count($citas) ?></div>
        <div class="small text-muted">Total en el periodo</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <div class="small-muted">Promedio consultas por día</div>
        <?php
          // calcular días
          $d1 = strtotime($desde ?: date('Y-m-d'));
          $d2 = strtotime($hasta ?: date('Y-m-d'));
          $days = max(1, (int)floor(abs($d2 - $d1) / 86400) + 1);
          $avg = round(count($consultas) / $days, 2);
        ?>
        <div class="stat"><?= $avg ?></div>
        <div class="small text-muted"><?= $days ?> días</div>
      </div>
    </div>
  </div>

  <!-- Gráfico resumen (uso de Chart.js) -->
  <div class="card mb-4">
    <div class="card-body">
      <canvas id="chartResumen" height="90"></canvas>
    </div>
  </div>

  <!-- Sección Consultas -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">📋 Consultas realizadas</div>
    <div class="card-body">
      <?php if (empty($consultas)): ?>
        <p class="text-muted">No hay consultas en el rango seleccionado.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered small table-fixed">
            <thead>
              <tr>
                <th style="width:14%">Fecha</th>
                <th> Mascota</th>
                <th> Motivo</th>
                <th style="width:18%"> Veterinario</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($consultas as $c): ?>
                <tr>
                  <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($c['creado_en']))) ?></td>
                  <td><?= htmlspecialchars($c['nombre_mascota']) ?></td>
                  <td><?= htmlspecialchars($c['motivo']) ?></td>
                  <td><?= htmlspecialchars($c['nombre_veterinario']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Sección Citas -->
  <div class="card mb-4">
    <div class="card-header bg-success text-white">📆 Citas atendidas</div>
    <div class="card-body">
      <?php if (empty($citas)): ?>
        <p class="text-muted">No hay citas en el rango seleccionado.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-bordered small table-fixed">
            <thead>
              <tr>
                <th style="width:14%">Fecha</th>
                <th> Mascota</th>
                <th> Cliente</th>
                <th> Servicio</th>
                <th> Notas</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($citas as $c): ?>
                <tr>
                  <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($c['fecha']))) ?></td>
                  <td><?= htmlspecialchars($c['nombre_mascota']) ?></td>
                  <td><?= htmlspecialchars(trim(($c['cliente_nombre'] ?? '') . ' ' . ($c['cliente_apellido'] ?? ''))) ?></td>
                  <td><?= htmlspecialchars($c['nombre_servicio']) ?></td>
                  <td><?= htmlspecialchars($c['notas']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Preparamos datos simplificados para el gráfico: conteo por día
(function(){
  const consultas = <?= json_encode(array_map(function($c){ return ['fecha' => substr($c['creado_en'],0,10)]; }, $consultas)); ?>;
  const citas = <?= json_encode(array_map(function($c){ return ['fecha' => substr($c['fecha'],0,10)]; }, $citas)); ?>;

  function groupByDate(items){
    const map = {};
    items.forEach(i => { map[i.fecha] = (map[i.fecha] || 0) + 1; });
    return map;
  }

  const consMap = groupByDate(consultas);
  const citasMap = groupByDate(citas);

  // obtener fechas ordenadas del periodo
  const desde = document.querySelector('input[name="desde"]').value || ''; 
  const hasta = document.querySelector('input[name="hasta"]').value || '';
  function datesBetween(start, end){
    if (!start || !end) return Object.keys(Object.assign({}, consMap, citasMap)).sort();
    const s = new Date(start); const e = new Date(end);
    const arr = [];
    for (let d = new Date(s); d <= e; d.setDate(d.getDate()+1)) arr.push(d.toISOString().slice(0,10));
    return arr;
  }

  const labels = datesBetween(desde, hasta);
  const consData = labels.map(d => consMap[d] || 0);
  const citasData = labels.map(d => citasMap[d] || 0);

  const ctx = document.getElementById('chartResumen').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        { label: 'Consultas', data: consData, backgroundColor: 'rgba(13,110,253,0.8)' },
        { label: 'Citas atendidas', data: citasData, backgroundColor: 'rgba(25,135,84,0.8)' }
      ]
    },
    options: { responsive:true, maintainAspectRatio:false, interaction:{mode:'index',intersect:false}, scales:{y:{beginAtZero:true}} }
  });

  // botones de rango rápido
  document.querySelectorAll('[data-range]').forEach(btn => {
    btn.addEventListener('click', function(){
      const days = parseInt(this.dataset.range,10);
      const end = new Date();
      const start = new Date(); start.setDate(end.getDate() - (days-1));
      document.querySelector('input[name="desde"]').value = start.toISOString().slice(0,10);
      document.querySelector('input[name="hasta"]').value = end.toISOString().slice(0,10);
    });
  });

})();
</script>
</body>
</html>