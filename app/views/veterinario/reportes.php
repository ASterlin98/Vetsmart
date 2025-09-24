<div class="container mt-4">
  <h2 class="mb-4"><i class="bi bi-bar-chart-line"></i> Reportes del Veterinario</h2>

  <!-- Filtros -->
  <form class="row g-3 mb-4" method="GET" action="">
    <div class="col-md-3">
      <label>Desde</label>
      <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <label>Hasta</label>
      <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>">
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button class="btn btn-primary w-100" type="submit">Filtrar</button>
    </div>
  </form>

  <!-- Exportar a PDF: usar POST y enviar los mismos nombres 'desde' y 'hasta' -->
<form method="POST" action="/vetsmart/veterinario/reportes/exportar">
  <input type="hidden" name="desde" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>">
  <input type="hidden" name="hasta" value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>">
  <button type="submit" class="btn btn-danger">Exportar PDF</button>
</form>



  <!-- Sección Consultas -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">📋 Consultas realizadas</div>
    <div class="card-body">
      <?php if (empty($consultas)): ?>
        <p class="text-muted">No hay consultas en el rango seleccionado.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered small">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Mascota</th>
                <th>Motivo</th>
                <th>Veterinario</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($consultas as $c): ?>
                <tr>
                  <td><?= htmlspecialchars($c['creado_en']) ?></td>
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
          <table class="table table-bordered small">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Mascota</th>
                <th>Cliente</th>
                <th>Servicio</th>
                <th>Notas</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($citas as $c): ?>
                <tr>
                  <td><?= htmlspecialchars($c['fecha']) ?></td>
                  <td><?= htmlspecialchars($c['nombre_mascota']) ?></td>
                  <td><?= htmlspecialchars($c['cliente_nombre'] . ' ' . $c['cliente_apellido']) ?></td>
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
