<?php
// app/views/veterinario/consultas/ver.php
$consulta = $consulta ?? null;
if (!$consulta) {
  echo "<div class='p-3'>Consulta no encontrada.</div>";
  return;
}
?>
<div class="modal-header">
  <h5 class="modal-title">Consulta #<?= htmlspecialchars($consulta['id']) ?> - <?= htmlspecialchars($consulta['nombre_mascota'] ?? '-') ?></h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
</div>

<div class="modal-body">
  <p><strong>Fecha:</strong> <?= htmlspecialchars($consulta['creado_en']) ?></p>

  <hr class="my-2">
  <h6>Motivo</h6>
  <p><?= nl2br(htmlspecialchars($consulta['motivo'] ?? '-')) ?></p>

  <h6>Examen</h6>
  <p><?= nl2br(htmlspecialchars($consulta['examen'] ?? '-')) ?></p>

  <h6>Diagnóstico</h6>
  <p><?= nl2br(htmlspecialchars($consulta['diagnostico'] ?? '-')) ?></p>

  <h6>Tratamiento</h6>
  <p><?= nl2br(htmlspecialchars($consulta['tratamiento'] ?? '-')) ?></p>

  <h6>Recomendaciones</h6>
  <p><?= nl2br(htmlspecialchars($consulta['recomendaciones'] ?? '-')) ?></p>

  <h6>Notas</h6>
  <p><?= nl2br(htmlspecialchars($consulta['notas'] ?? '-')) ?></p>
</div>

<div class="modal-footer">
  <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/editar"
     class="btn btn-warning btn-editar-consulta"
     data-id="<?= $consulta['id'] ?>">Editar</a>

  <form action="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/eliminar" method="POST"
        onsubmit="return confirm('¿Eliminar esta consulta?');" style="display:inline">
    <button class="btn btn-danger">Eliminar</button>
  </form>

  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>
