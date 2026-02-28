<?php
declare(strict_types=1);
// Espera: $agenda (['Y-m-d' => [citas...]]), $desde, $hasta
?>
<div class="container py-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="mb-0">Mi agenda (semana)</h2>
    <div class="d-flex gap-2">
      <a class="btn btn-primary btn-sm" href="<?= BASE ?>/peluquero/agenda/agendar">
        <i class="fas fa-plus me-2"></i>Agendar Cita
      </a>
      <?php
        $desdeDT = DateTime::createFromFormat('Y-m-d', (string)($desde ?? date('Y-m-d'))) ?: new DateTime('today');
        $prev = (clone $desdeDT)->modify('-7 days')->format('Y-m-d');
        $next = (clone $desdeDT)->modify('+7 days')->format('Y-m-d');
      ?>
      <a class="btn btn-outline-secondary btn-sm" href="<?= BASE ?>/peluquero/agenda?desde=<?= $prev ?>">« Semana anterior</a>
      <a class="btn btn-outline-secondary btn-sm" href="<?= BASE ?>/peluquero/agenda?desde=<?= date('Y-m-d') ?>">Hoy</a>
      <a class="btn btn-outline-secondary btn-sm" href="<?= BASE ?>/peluquero/agenda?desde=<?= $next ?>">Siguiente semana »</a>
    </div>
  </div>

  <p class="text-muted">Del <?= htmlspecialchars((string)($desde ?? '')) ?> al <?= htmlspecialchars((string)($hasta ?? '')) ?></p>

  <div class="row g-3">
    <?php $diasEs = [1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo']; ?>
    <?php $mesesEs = [1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre']; ?>
    <?php foreach ($agenda as $dia => $items): ?>
      <?php $dt = DateTime::createFromFormat('Y-m-d', $dia) ?: new DateTime($dia); ?>
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>
              <?php
                $diaNombre = $diasEs[(int)$dt->format('N')] ?? $dt->format('l');
                $mesNombre = $mesesEs[(int)$dt->format('n')] ?? $dt->format('F');
                echo $diaNombre . ' ' . $dt->format('j') . ' de ' . $mesNombre;
              ?>
            </strong>
            <span class="badge bg-primary"><?= count($items) ?> citas</span>
          </div>
          <ul class="list-group list-group-flush">
            <?php if (empty($items)): ?>
              <li class="list-group-item text-muted">Sin citas</li>
            <?php else: foreach ($items as $c): ?>
              <?php
                $e = (string)($c['estado'] ?? '');
                $cls = $e==='pendiente'?'warning':($e==='confirmada'?'info':($e==='completada'?'success':'secondary'));
                $label = $e==='confirmada'?'en_proceso':($e==='completada'?'completada':$e);
              ?>
              <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">
                      <i class="fas fa-clock me-1"></i><?= htmlspecialchars((string)($c['hora'] ?? '')) ?> ·
                      <span class="text-muted"><?= htmlspecialchars((string)($c['servicio'] ?? '')) ?></span>
                    </div>
                    <div class="small text-muted">
                      <?= htmlspecialchars((string)($c['mascota'] ?? '')) ?> · <?= htmlspecialchars((string)($c['cliente'] ?? '')) ?>
                    </div>
                  </div>
                  <span class="badge bg-<?= $cls ?>"><?= htmlspecialchars($label) ?></span>
                </div>
              </li>
            <?php endforeach; endif; ?>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<style>
.list-group-item { font-size: 0.95rem; }
</style>
