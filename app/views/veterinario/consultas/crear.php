<?php
// app/views/veterinario/consultas/crear.php
$mascota = $mascota ?? null;
$cita = $cita ?? null;
?>
<div class="container py-4">
  <h2>Nueva Consulta</h2>

  <form action="/vetsmart/veterinario/consultas/guardar" method="POST">
    <?php if ($cita): ?>
      <div class="mb-3">
        <div class="card border-0 shadow-sm p-2">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold">Cita relacionada (ID: <?= htmlspecialchars($cita['id']) ?>)</div>
              <div class="small text-muted">Fecha: <?= htmlspecialchars($cita['fecha'] ?? ($cita['creado_en'] ?? '')) ?></div>
              <div class="small">Servicio: <?= htmlspecialchars($cita['servicio_nombre'] ?? ($cita['servicio_id'] ?? '-')) ?></div>
            </div>
            <div class="text-end small text-muted">Estado: <?= htmlspecialchars($cita['estado'] ?? '-') ?></div>
          </div>
        </div>
      </div>
      <input type="hidden" name="cita_id" value="<?= htmlspecialchars($cita['id']) ?>">
    <?php endif; ?>
    <div class="mb-3">
      <label> Mascota *</label>
      <select name="mascota_id" class="form-select" required>
        <?php if ($mascota): ?>
          <option value="<?= $mascota['id'] ?>"><?= htmlspecialchars($mascota['nombre']) ?></option>
        <?php else: ?>
          <option value="">Seleccionar mascota (desde Pacientes o usando buscador)</option>
        <?php endif; ?>
      </select>
      <?php if (!$mascota): ?>
        <small class="text-muted">Puedes crear la consulta desde la ficha de la mascota para autoseleccionarla.</small>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label>Motivo</label>
      <textarea name="motivo" class="form-control" rows="2"><?= htmlspecialchars($cita['notas'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label>Examen físico / hallazgos</label>
      <textarea name="examen" class="form-control" rows="3"></textarea>
    </div>

    <div class="mb-3">
      <label>Diagnóstico</label>
      <textarea name="diagnostico" class="form-control" rows="2"></textarea>
    </div>

    <div class="mb-3">
      <label>Tratamiento</label>
      <textarea name="tratamiento" class="form-control" rows="2"></textarea>
    </div>

    <div class="mb-3">
      <label>Recomendaciones</label>
      <textarea name="recomendaciones" class="form-control" rows="2"></textarea>
    </div>

    <div class="mb-3">
      <label>Notas internas</label>
      <textarea name="notas" class="form-control" rows="2"><?= htmlspecialchars($cita['notas'] ?? '') ?></textarea>
    </div>

    <button class="btn btn-primary">Guardar Consulta</button>
    <a href="/vetsmart/veterinario/consultas" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
