<div class="container py-4">
  <h3>Editar nota rápida</h3>
  <form method="POST" action="/vetsmart/veterinario/mascotas/<?= $mascota_id ?>/notas/<?= $nota['id'] ?>/guardar_edicion">
    <div class="mb-3">
      <label for="nota">Nota:</label>
      <textarea name="nota" id="nota" class="form-control" rows="5"><?= htmlspecialchars($nota['nota']) ?></textarea>
    </div>
    <button class="btn btn-primary">Guardar cambios</button>
    <a href="/vetsmart/veterinario/mascotas/<?= $mascota_id ?>/historial" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
