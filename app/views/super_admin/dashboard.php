<?php
// app/views/super_admin/dashboard.php
$totClientes = $totalClientes ?? 0;
$totMascotas = $totalMascotas ?? 0;
$totCitas = $totalCitas ?? 0;
$totIngresos = $totalIngresos ?? 0.0;
$totVacunas = $totalVacunas ?? 0;
$citasConfirmadas = $citasConfirmadas ?? 0;
$recentActivity = $recentActivity ?? [];
$error = $error ?? null;

function fmtMoney($v) {
    return '$' . number_format((float)$v, 2, '.', ',');
}
?>
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <h1 class="fw-bold">Panel del Super Administrador</h1>
      <p class="text-muted mb-4">Resumen general del sistema</p>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger">
      <strong>Error:</strong> <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-primary fw-semibold">Clientes</div>
          <div class="display-6 fw-bold"><?= number_format($totClientes) ?></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-success fw-semibold">Mascotas</div>
          <div class="display-6 fw-bold"><?= number_format($totMascotas) ?></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-warning fw-semibold">Citas</div>
          <div class="display-6 fw-bold"><?= number_format($totCitas) ?></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-danger fw-semibold">Ingresos</div>
          <div class="display-6 fw-bold"><?= fmtMoney($totIngresos) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Actividad reciente</h5>
          <p class="text-muted small">Aquí podrás ver las acciones recientes del sistema.</p>

          <?php if (empty($recentActivity)): ?>
            <div class="text-muted">No se han detectado actividades recientes.</div>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($recentActivity as $a): 
                $tipo = htmlspecialchars($a['tipo'] ?? '');
                $actor = htmlspecialchars(trim($a['actor'] ?? ''));
                $accion = htmlspecialchars($a['accion'] ?? '');
                $detalle = htmlspecialchars($a['detalle'] ?? '');
                $time = $a['creado_en'] ?? null;
                $when = '';
                if ($time) {
                  $ts = strtotime($time);
                  if ($ts !== false) $when = date('d/m/Y H:i', $ts);
                  else $when = htmlspecialchars($time);
                }
              ?>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="fw-semibold"><?= $accion ?> <?= $tipo ? " — " . $tipo : '' ?></div>
                      <div class="small text-muted"><?= $detalle ?></div>
                      <?php if ($actor): ?><div class="small text-muted">Por: <?= $actor ?></div><?php endif; ?>
                    </div>
                    <div class="text-end small text-muted"><?= $when ?></div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="mb-2">Resumen adicional</h6>
          <div class="row">
            <div class="col-6">
              <div class="small text-muted">Vacunas registradas</div>
              <div class="fw-bold"><?= number_format($totVacunas) ?></div>
            </div>
            <div class="col-6">
              <div class="small text-muted">Citas confirmadas</div>
              <div class="fw-bold"><?= number_format($citasConfirmadas) ?></div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="col-lg-5">
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="card-title">Accesos rápidos</h6>
          <div class="d-grid gap-2">
            <a href="/vetsmart/super_admin/usuarios" class="btn btn-primary">Gestionar usuarios</a>
            <a href="/vetsmart/super_admin/configuracion" class="btn btn-outline-secondary">Configuración global</a>
            <a href="/vetsmart/super_admin/reportes" class="btn btn-outline-info">Ir a reportes</a>
          </div>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Notas</h6>
          <p class="small text-muted mb-0">Usa este panel para monitorear la actividad y acceder a los módulos del sistema.</p>
        </div>
      </div>

    </div>
  </div>

  <div class="row mt-5">
    <div class="col-12 text-center text-muted">
      © <?= date('Y') ?> VetSmart. Todos los derechos reservados.
    </div>
  </div>
</div>
