<?php
// app/views/veterinario/consultas/ver.php
$consulta = $consulta ?? null;
if (!$consulta) { echo "<div class='container py-4'>Consulta no encontrada.</div>"; return; }
?>
<div class="container py-4">
  <h2>Consulta #<?= htmlspecialchars($consulta['id']) ?></h2>
  <p><strong>Mascota:</strong> <?= htmlspecialchars($consulta['nombre_mascota'] ?? '-') ?></p>
  <p><strong>Fecha:</strong> <?= htmlspecialchars($consulta['creado_en']) ?></p>
  <hr>
  <h5>Motivo</h5>
  <p><?= nl2br(htmlspecialchars($consulta['motivo'] ?? '-')) ?></p>

  <h5>Examen</h5>
  <p><?= nl2br(htmlspecialchars($consulta['examen'] ?? '-')) ?></p>

  <h5>Diagnóstico</h5>
  <p><?= nl2br(htmlspecialchars($consulta['diagnostico'] ?? '-')) ?></p>

  <h5>Tratamiento</h5>
  <p><?= nl2br(htmlspecialchars($consulta['tratamiento'] ?? '-')) ?></p>

  <h5>Recomendaciones</h5>
  <p><?= nl2br(htmlspecialchars($consulta['recomendaciones'] ?? '-')) ?></p>

  <h5>Notas</h5>
  <p><?= nl2br(htmlspecialchars($consulta['notas'] ?? '-')) ?></p>

  <div class="mt-3">
    <a href="/vetsmart/veterinario/consultas/editar/<?= $consulta['id'] ?>" class="btn btn-warning">Editar</a>
    <form action="/vetsmart/veterinario/consultas/eliminar/<?= $consulta['id'] ?>" method="POST" style="display:inline" onsubmit="return confirm('Eliminar consulta?')">
      <button class="btn btn-danger">Eliminar</button>
    </form>
    <a href="/vetsmart/veterinario/consultas" class="btn btn-secondary">Volver</a>
  </div>
</div>
