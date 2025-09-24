<?php
// app/views/veterinario/consultas/editar.php
$consulta = $consulta ?? null;
if (!$consulta) {
  echo "<div class='p-3'>Consulta no encontrada.</div>";
  return;
}
?>

<div class="modal-header">
  <h5 class="modal-title">Editar Consulta #<?= htmlspecialchars($consulta['id']) ?></h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
</div>

<form action="/vetsmart/veterinario/consultas/actualizar/<?= $consulta['id'] ?>" method="POST">
  <div class="modal-body">
    <div class="mb-3">
      <label>Mascota</label>
      <input class="form-control" value="<?= htmlspecialchars($consulta['nombre_mascota'] ?? '-') ?>" disabled>
    </div>

    <div class="mb-3">
      <label>Motivo</label>
      <textarea name="motivo" class="form-control" rows="2"><?= htmlspecialchars($consulta['motivo'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label>Examen</label>
      <textarea name="examen" class="form-control" rows="3"><?= htmlspecialchars($consulta['examen'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label>Diagnóstico</label>
      <textarea name="diagnostico" class="form-control" rows="2"><?= htmlspecialchars($consulta['diagnostico'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label>Tratamiento</label>
      <textarea name="tratamiento" class="form-control" rows="2"><?= htmlspecialchars($consulta['tratamiento'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label>Recomendaciones</label>
      <textarea name="recomendaciones" class="form-control" rows="2"><?= htmlspecialchars($consulta['recomendaciones'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label>Notas</label>
      <textarea name="notas" class="form-control" rows="2"><?= htmlspecialchars($consulta['notas'] ?? '') ?></textarea>
    </div>
  </div>

  <div class="modal-footer">
    <button class="btn btn-primary">Guardar cambios</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
  </div>
</form>
