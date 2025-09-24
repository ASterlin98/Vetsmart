<?php
// app/views/veterinario/dashboard.php
$totalMascotas = $totalMascotas ?? 0;
$totalClientes = $totalClientes ?? 0;
$upcomingCount = $upcomingCount ?? 0;
$todayAppointments = $todayAppointments ?? [];
$upcomingList = $upcomingList ?? [];
$assignedMascotas = $assignedMascotas ?? [];
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">🐾 Dashboard Veterinario</h1>
    <a href="/vetsmart/veterinario/mis-citas" class="btn btn-outline-primary">Ver mi calendario "cambio 4"</a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Mascotas (total)</h6>
          <h2 class="mb-0"><?= number_format($totalMascotas) ?></h2>
          <small class="text-muted">Registradas</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Clientes</h6>
          <h2 class="mb-0"><?= number_format($totalClientes) ?></h2>
          <small class="text-muted">Cuentas de cliente</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Próximas citas</h6>
          <h2 class="mb-0"><?= number_format($upcomingCount) ?></h2>
          <small class="text-muted">Asignadas a ti</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Hoy</h6>
          <h2 class="mb-0"><?= number_format(count($todayAppointments)) ?></h2>
          <small class="text-muted">Citas hoy</small>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header">
          <strong>Próximas citas</strong>
        </div>
        <div class="card-body p-0">
          <?php if (empty($upcomingList)): ?>
            <div class="p-3">No hay citas próximas asignadas a ti.</div>
          <?php else: ?>
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Mascota</th>
                  <th>Cliente</th>
                  <th>Servicio</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($upcomingList as $c): ?>
                  <tr>
                    <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($c['fecha']))) ?></td>
                    <td><?= htmlspecialchars($c['nombre_mascota'] ?? '-') ?></td>
                    <td><?= htmlspecialchars(trim(($c['cliente_nombre'] ?? '') . ' ' . ($c['cliente_apellido'] ?? ''))) ?></td>
                    <td><?= htmlspecialchars($c['servicio'] ?? ($c['servicio_id'] ?? '-')) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header">
          <strong>Citas de hoy</strong>
        </div>
        <div class="card-body">
          <?php if (empty($todayAppointments)): ?>
            <div>No tienes citas hoy.</div>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($todayAppointments as $t): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <strong><?= htmlspecialchars($t['nombre_mascota'] ?? '-') ?></strong>
                    <div class="small text-muted"><?= htmlspecialchars(trim(($t['cliente_nombre'] ?? '') . ' ' . ($t['cliente_apellido'] ?? ''))) ?></div>
                  </div>
                  <div class="text-end">
                    <div><?= date('H:i', strtotime($t['fecha'])) ?></div>
                    <a href="/vetsmart/veterinario/mascotas/<?= $t['mascota_id'] ?>/historial" class="btn btn-sm btn-outline-secondary mt-1">Historial</a>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card">
        <div class="card-header"><strong>Mascotas asignadas a ti</strong></div>
        <div class="card-body">
          <?php if (empty($assignedMascotas)): ?>
            <div>No tienes mascotas asignadas (a través de citas).</div>
          <?php else: ?>
            <ul class="list-group">
              <?php foreach ($assignedMascotas as $m): ?>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <div>
                      <strong><?= htmlspecialchars($m['nombre']) ?></strong>
                      <div class="small text-muted"><?= htmlspecialchars(trim(($m['nombre_dueno'] ?? '') . ' ' . ($m['apellido_dueno'] ?? ''))) ?></div>
                      <?php if (!empty($m['proxima_cita'])): ?>
                        <div class="small text-muted">Próx: <?= htmlspecialchars(date('Y-m-d H:i', strtotime($m['proxima_cita']))) ?></div>
                      <?php endif; ?>
                    </div>
                    <div>
                      <a href="/vetsmart/veterinario/mascotas/<?= $m['id'] ?>/historial" class="btn btn-sm btn-outline-primary">Ver</a>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-body text-center">
          <a href="/vetsmart/veterinario/mis-citas" class="btn btn-primary w-100 mb-2">Ir a mis citas</a>
          <a href="/vetsmart/veterinario/pacientes" class="btn btn-outline-secondary w-100">Ver pacientes</a>
        </div>
      </div>
    </div>
  </div>
</div>
