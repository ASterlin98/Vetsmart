<?php
// app/views/veterinario/dashboard.php (versión mejorada)
$totalMascotas = $totalMascotas ?? 0;
$totalClientes = $totalClientes ?? 0;
$upcomingCount = $upcomingCount ?? 0;
$todayAppointments = $todayAppointments ?? [];
$upcomingList = $upcomingList ?? [];
$assignedMascotas = $assignedMascotas ?? [];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard Veterinario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background:#f6f8fb; font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; }
    .card-hero { border-radius:12px; box-shadow:0 10px 30px rgba(20,24,40,0.06); }
    .stat { font-size:1.5rem; font-weight:700; }
    .small-muted { color:#6b7280; }
    .table-fixed td { overflow-wrap: anywhere; }
  </style>
</head>
<body>

<div class="container py-4">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-0"><i class="bi bi-house-heart me-2"></i> Dashboard Veterinario</h2>
      <small class="text-muted">Resumen rápido de tu actividad</small>
    </div>
    <div class="d-flex gap-2">
      <a href="/vetsmart/veterinario/mis-citas" class="btn btn-primary">
        <i class="bi bi-calendar3"></i> Ver calendario
      </a>
      <a href="/vetsmart/veterinario/pacientes" class="btn btn-outline-secondary">
        <i class="bi bi-people"></i> Pacientes
      </a>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
      <div class="card card-hero p-3 h-100">
        <div class="d-flex align-items-center">
          <div class="me-3 display-6 text-primary"><i class="bi bi-heart-pulse"></i></div>
          <div>
            <div class="small-muted">Mascotas</div>
            <div class="stat"><?= number_format($totalMascotas) ?></div>
            <div class="small text-muted">Registradas</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card card-hero p-3 h-100">
        <div class="d-flex align-items-center">
          <div class="me-3 display-6 text-success"><i class="bi bi-people-fill"></i></div>
          <div>
            <div class="small-muted">Clientes</div>
            <div class="stat"><?= number_format($totalClientes) ?></div>
            <div class="small text-muted">Cuentas</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card card-hero p-3 h-100">
        <div class="d-flex align-items-center">
          <div class="me-3 display-6 text-danger"><i class="bi bi-bell-fill"></i></div>
          <div>
            <div class="small-muted">Próximas citas</div>
            <div class="stat"><?= number_format($upcomingCount) ?></div>
            <div class="small text-muted">Asignadas</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card card-hero p-3 h-100">
        <div class="d-flex align-items-center">
          <div class="me-3 display-6 text-info"><i class="bi bi-calendar-day"></i></div>
          <div>
            <div class="small-muted">Hoy</div>
            <div class="stat"><?= number_format(count($todayAppointments)) ?></div>
            <div class="small text-muted">Citas</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <div class="row g-4">
    <!-- Left column -->
    <div class="col-lg-7">
      <!-- Próximas citas -->
      <div class="card mb-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
          <span><i class="bi bi-clock-history me-2"></i> Próximas citas</span>
          <small><?= number_format($upcomingCount) ?> total</small>
        </div>
        <div class="card-body p-0">
          <?php if (empty($upcomingList)): ?>
            <div class="p-3 text-center text-muted">No hay citas próximas asignadas a ti.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0 small">
                <thead class="table-light">
                  <tr>
                    <th style="width:140px">Fecha</th>
                    <th>Mascota</th>
                    <th>Cliente</th>
                    <th style="width:150px">Servicio</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($upcomingList as $c): ?>
                    <tr>
                      <td>
                        <div class="fw-semibold"><?= htmlspecialchars(date('d/m/Y', strtotime($c['fecha']))) ?></div>
                        <div class="small text-muted"><?= htmlspecialchars(date('H:i', strtotime($c['fecha']))) ?></div>
                      </td>
                      <td><?= htmlspecialchars($c['nombre_mascota'] ?? '-') ?></td>
                      <td class="small text-muted"><?= htmlspecialchars(trim(($c['cliente_nombre'] ?? '') . ' ' . ($c['cliente_apellido'] ?? ''))) ?></td>
                      <td><?= htmlspecialchars($c['servicio'] ?? ($c['servicio_id'] ?? '-')) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Citas de hoy -->
      <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between">
          <span><i class="bi bi-calendar-check me-2"></i> Citas de hoy</span>
          <small><?= number_format(count($todayAppointments)) ?> citas</small>
        </div>
        <div class="card-body">
          <?php if (empty($todayAppointments)): ?>
            <div class="text-center text-muted">No tienes citas hoy.</div>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($todayAppointments as $t): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars($t['nombre_mascota'] ?? '-') ?></div>
                    <div class="small text-muted"><?= htmlspecialchars(trim(($t['cliente_nombre'] ?? '') . ' ' . ($t['cliente_apellido'] ?? ''))) ?></div>
                  </div>
                  <div class="text-end">
                    <div class="fw-bold"><?= date('H:i', strtotime($t['fecha'])) ?></div>
                    <a href="/vetsmart/veterinario/mascotas/<?= htmlspecialchars($t['mascota_id'] ?? '') ?>/historial" class="btn btn-sm btn-outline-light mt-2" data-bs-toggle="tooltip" title="Ver historial">
                      <i class="bi bi-journal-text"></i>
                    </a>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Right column -->
    <div class="col-lg-5">
      <!-- Mascotas asignadas -->
      <div class="card mb-3">
        <div class="card-header bg-info text-white"><i class="bi bi-shield-heart me-2"></i> Mascotas asignadas</div>
        <div class="card-body p-0">
          <?php if (empty($assignedMascotas)): ?>
            <div class="p-3 text-center text-muted">No tienes mascotas asignadas.</div>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($assignedMascotas as $m): ?>
                <li class="list-group-item d-flex align-items-start gap-3">
                  <div style="width:56px;height:56px;border-radius:8px;overflow:hidden;background:#f1f1f1;display:flex;align-items:center;justify-content:center;">
                    <?php if (!empty($m['foto'])): ?>
                      <img src="<?= htmlspecialchars($m['foto']) ?>" alt="" style="max-width:100%; max-height:100%;">
                    <?php else: ?>
                      <i class="bi bi-image text-muted"></i>
                    <?php endif; ?>
                  </div>
                  <div class="flex-fill">
                    <div class="d-flex justify-content-between">
                      <div>
                        <div class="fw-semibold"><?= htmlspecialchars($m['nombre'] ?? '-') ?></div>
                        <div class="small text-muted"><?= htmlspecialchars(trim(($m['nombre_dueno'] ?? '') . ' ' . ($m['apellido_dueno'] ?? ''))) ?></div>
                        <?php if (!empty($m['proxima_cita'])): ?>
                          <div class="small text-muted">Próx: <?= htmlspecialchars(date('d/m/Y H:i', strtotime($m['proxima_cita']))) ?></div>
                        <?php endif; ?>
                      </div>
                      <a href="/vetsmart/veterinario/mascotas/<?= htmlspecialchars($m['id'] ?? '') ?>/historial" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Ver historial">
                        <i class="bi bi-journal-text"></i>
                      </a>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

      <!-- Acciones rápidas -->
      <div class="card card-hero">
        <div class="card-body text-center">
          <a href="/vetsmart/veterinario/mis-citas" class="btn btn-primary w-100 mb-2"><i class="bi bi-calendar2-week"></i> Ir a mis citas</a>
          <a href="/vetsmart/veterinario/pacientes" class="btn btn-outline-secondary w-100"><i class="bi bi-folder2-open"></i> Ver pacientes</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  if (typeof bootstrap === 'undefined') return;
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el=>{
    new bootstrap.Tooltip(el);
  });
})();
</script>
</body>
</html>
