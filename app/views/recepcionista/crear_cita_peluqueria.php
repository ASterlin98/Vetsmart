<?php
declare(strict_types=1);
// Espera: $clientes, $peluqueros, $servicios, $mascotas
?>
<div class="container py-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="mb-0">Agendar Cita de Peluquería</h2>
    <a href="/vetsmart/recepcionista/agenda" class="btn btn-outline-secondary">Volver a Agenda</a>
  </div>

  <?php if (!empty($_SESSION['mensaje'])): $m = $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
    <div class="alert alert-<?= htmlspecialchars($m['tipo'] ?? 'info') ?>"><?= htmlspecialchars($m['texto'] ?? '') ?></div>
  <?php endif; ?>

  <form method="post" action="/vetsmart/recepcionista/citas-peluqueria/store" class="card p-3 shadow-sm border-0">
    <?= CSRF::inputField() ?>
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Cliente</label>
        <select name="cliente_id" class="form-select" required>
          <option value="">Seleccione…</option>
          <?php foreach ($clientes as $c): ?>
            <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Mascota</label>
        <select name="mascota_id" class="form-select" required>
          <option value="">Seleccione…</option>
          <?php foreach ($mascotas as $m): ?>
            <option value="<?= (int)$m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Peluquero</label>
        <select name="peluquero_id" class="form-select" required>
          <option value="">Seleccione…</option>
          <?php foreach ($peluqueros as $p): ?>
            <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Servicio</label>
        <select name="servicio_id" class="form-select" required>
          <option value="">Seleccione…</option>
          <?php foreach ($servicios as $s): ?>
            <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Fecha</label>
        <input type="date" name="fecha" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Hora</label>
        <input type="time" name="hora" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Observaciones</label>
        <input type="text" name="observaciones" class="form-control" placeholder="Comentarios opcionales">
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Guardar</button>
    </div>
  </form>
</div>

