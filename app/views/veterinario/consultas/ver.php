<?php
// app/views/veterinario/consultas/ver.php
$consulta = $consulta ?? null;
if (!$consulta) {
  echo "<div class='p-3 text-danger'>Consulta no encontrada.</div>";
  return;
}
?>
<div class="modal-header">
  <h5 class="modal-title">
    🩺 Consulta #<?= htmlspecialchars($consulta['id']) ?> - <?= htmlspecialchars($consulta['nombre_mascota'] ?? '-') ?>
  </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
</div>

<div class="modal-body small">
  <div class="mb-2">
    <span class="text-muted">📅 Fecha:</span>
    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($consulta['creado_en']))) ?>
  </div>

  <hr class="my-2">

  <?php
  $campos = [
    'motivo'         => '📝 Motivo',
    'examen'         => '🔬 Examen',
    'diagnostico'    => '🧾 Diagnóstico',
    'tratamiento'    => '💊 Tratamiento',
    'recomendaciones'=> '📌 Recomendaciones',
    'notas'          => '🗒️ Notas'
  ];

  foreach ($campos as $key => $label): ?>
    <div class="mb-3">
      <h6 class="mb-1"><?= $label ?></h6>
      <div class="border rounded p-2 bg-light">
        <?= nl2br(htmlspecialchars($consulta[$key] ?? '-')) ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="modal-footer d-flex justify-content-between">
  <form action="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/eliminar" method="POST"
        onsubmit="return confirm('¿Eliminar esta consulta?');" class="me-auto">
    <button type="submit" class="btn btn-danger btn-sm">🗑️ Eliminar</button>
  </form>

  <div>
    <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/editar"
       class="btn btn-warning btn-sm btn-editar-consulta"
       data-id="<?= $consulta['id'] ?>">✏️ Editar</a>

    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
  </div>
</div>
