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
    <h1 class="h3 fw-bold">🐾 Panel Veterinario</h1>
    <a href="/vetsmart/veterinario/mis-citas" class="btn btn-outline-primary">
      📅 Ver Calendario
    </a>
  </div>

  <!-- Resumen -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-bg-primary-subtle">
        <div class="card-body">
          <h6 class="text-uppercase text-muted fw-semibold small mb-1">Mascotas</h6>
          <h3 class="fw-bold"><?= number_format($totalMascotas) ?></h3>
          <div class="text-muted small">Registradas</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-bg-info-subtle">
        <div class="card-body">
          <h6 class="text-uppercase text-muted fw-semibold small mb-1">Clientes</h6>
          <h3 class="fw-bold"><?= number_format($totalClientes) ?></h3>
          <div class="text-muted small">Cuentas activas</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-bg-success-subtle">
        <div class="card-body">
          <h6 class="text-uppercase text-muted fw-semibold small mb-1">Próximas Citas</h6>
          <h3 class="fw-bold"><?= number_format($upcomingCount) ?></h3>
          <div class="text-muted small">Asignadas a ti</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-bg-warning-subtle">
        <div class="card-body">
          <h6 class="text-uppercase text-muted fw-semibold small mb-1">Citas de Hoy</h6>
          <h3 class="fw-bold"><?= number_format(count($todayAppointments)) ?></h3>
          <div class="text-muted small">Agendadas</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Panel de citas -->
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-header bg-light fw-semibold">
          📆 Próximas Citas
        </div>
        <div class="card-body p-0">
          <?php if (empty($upcomingList)): ?>
            <div class="p-3 text-muted">No hay citas próximas asignadas.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
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
                      <td><?= htmlspecialchars($c['servicio'] ?? '-') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card mt-3 shadow-sm">
        <div class="card-header bg-light fw-semibold">
          📅 Citas de Hoy
        </div>
        <div class="card-body">
          <?php if (empty($todayAppointments)): ?>
            <div class="text-muted">No tienes citas hoy.</div>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($todayAppointments as $t): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <strong><?= htmlspecialchars($t['nombre_mascota'] ?? '-') ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars(trim(($t['cliente_nombre'] ?? '') . ' ' . ($t['cliente_apellido'] ?? ''))) ?></small>
                  </div>
                  <div class="text-end">
                    <div class="fw-bold"><?= date('H:i', strtotime($t['fecha'])) ?></div>
                    <a href="/vetsmart/veterinario/mascotas/<?= $t['mascota_id'] ?>/historial" class="btn btn-sm btn-outline-secondary mt-1">Historial</a>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Panel de mascotas asignadas -->
    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-header bg-light fw-semibold">
          🐶 Mascotas Asignadas
        </div>
        <div class="card-body">
          <?php if (empty($assignedMascotas)): ?>
            <div class="text-muted">No tienes mascotas asignadas.</div>
          <?php else: ?>
            <ul class="list-group">
              <?php foreach ($assignedMascotas as $m): ?>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <div>
                      <strong><?= htmlspecialchars($m['nombre']) ?></strong><br>
                      <small class="text-muted"><?= htmlspecialchars(trim(($m['nombre_dueno'] ?? '') . ' ' . ($m['apellido_dueno'] ?? ''))) ?></small>
                      <?php if (!empty($m['proxima_cita'])): ?>
                        <div class="small text-muted">📅 <?= date('Y-m-d H:i', strtotime($m['proxima_cita'])) ?></div>
                      <?php endif; ?>
                    </div>
                    <a href="/vetsmart/veterinario/mascotas/<?= $m['id'] ?>/historial" class="btn btn-sm btn-outline-primary">Ver</a>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

      <div class="card mt-3 shadow-sm">
        <div class="card-body text-center">
          <a href="/vetsmart/veterinario/mis-citas" class="btn btn-primary w-100 mb-2">📅 Ir a Mis Citas</a>
          <a href="/vetsmart/veterinario/pacientes" class="btn btn-outline-secondary w-100">👥 Ver Pacientes</a>
        </div>
      </div>
    </div>
  </div>
</div>
