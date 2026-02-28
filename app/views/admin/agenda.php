<?php
// app/views/admin/agenda.php
// Variables esperadas: $citas (array con todas las citas del mes), $servicios (array), $usuarios (array)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">📅 Agenda General (Vista Tabular)</h3>
    <div>
      <a id="exportBtn"
         href="#"
         class="btn btn-success btn-sm"
         onclick="exportarExcelConFiltros(event)">
         📊 Exportar Excel
      </a>
    </div>
  </div>

  <!-- Filtros -->
  <form method="get" class="row g-2 mb-3">
    <div class="col-md-3">
      <select name="empleado_id" class="form-select form-select-sm">
        <option value="">-- Todos los empleados --</option>
        <?php foreach (($usuarios ?? []) as $u): ?>
        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?><?= isset($u['role_nombre']) ? " — " . htmlspecialchars($u['role_nombre']) : '' ?></option>

        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="servicio_id" class="form-select form-select-sm">
        <option value="">-- Todos los servicios --</option>
        <?php foreach (($servicios ?? []) as $s): ?>
          <option value="<?= $s['id'] ?>" <?= ($_GET['servicio_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="estado" class="form-select form-select-sm">
        <option value="">-- Todos los estados --</option>
        <option value="programada" <?= ($_GET['estado'] ?? '') === 'programada' ? 'selected' : '' ?>>Programada</option>
        <option value="confirmada" <?= ($_GET['estado'] ?? '') === 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
        <option value="cancelada" <?= ($_GET['estado'] ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
      </select>
    </div>
    <div class="col-md-2">
      <input type="date" name="desde" class="form-control form-control-sm" value="<?= $_GET['desde'] ?? date('Y-m-01') ?>">
    </div>
    <div class="col-md-2">
      <input type="date" name="hasta" class="form-control form-control-sm" value="<?= $_GET['hasta'] ?? date('Y-m-t') ?>">
    </div>
    <div class="col-md-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
      <a href="<?= BASE ?>/admin/agenda" class="btn btn-outline-secondary btn-sm">Reset</a>
    </div>
  </form>

  <!-- Tabla -->
  <div class="card p-3">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Cliente</th>
            <th>Mascota</th>
            <th>Servicio</th>
            <th>Empleado</th>
            <th>Estado</th>
            <th>Notas</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($citas)): ?>
            <?php foreach ($citas as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['fecha'] ?? '') ?></td>
                <td><?= htmlspecialchars(substr($c['hora'],0,5) ?? '') ?></td>
                <td><?= htmlspecialchars($c['cliente_nombre'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['mascota_nombre'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['servicio_nombre'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['empleado_nombre'] ?? '') ?></td>
                <td>
                  <?php
                    $estado = $c['estado'] ?? 'programada';
                    $badgeClass = $estado === 'confirmada' ? 'success' : ($estado === 'cancelada' ? 'danger' : 'secondary');
                  ?>
                  <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($estado) ?></span>
                </td>
                <td><?= htmlspecialchars($c['notas'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" class="text-center text-muted">No hay citas en este rango.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
function exportarExcelConFiltros(event) {
    event.preventDefault();

    const empleado = document.querySelector('select[name="empleado_id"]').value;
    const servicio = document.querySelector('select[name="servicio_id"]').value;
    const estado = document.querySelector('select[name="estado"]').value;
    const desde = document.querySelector('input[name="desde"]').value;
    const hasta = document.querySelector('input[name="hasta"]').value;

    // Construir la URL con los parámetros
    const url = `<?= BASE ?>/admin/agenda/exportarExcel?desde=${desde}&hasta=${hasta}&empleado_id=${empleado}&servicio_id=${servicio}&estado=${estado}`;

    // Redirigir
    window.location.href = url;
}
</script>
