<h2>Editar Vacuna</h2>

<form method="POST" action="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/vacunas/<?= $vacuna['id'] ?>/actualizar">
  <div class="mb-3">
    <label for="nombre" class="form-label">Nombre de la Vacuna *</label>
    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($vacuna['nombre']) ?>" required>
  </div>

  <div class="mb-3">
    <label for="fecha" class="form-label">Fecha Aplicación *</label>
    <input type="date" name="fecha" class="form-control" value="<?= $vacuna['fecha_aplicacion'] ?>" required>
  </div>

  <div class="mb-3">
    <label for="proxima" class="form-label">Próxima Dosis</label>
    <input type="date" name="proxima" class="form-control" value="<?= $vacuna['proxima_dosis'] ?>">
  </div>

  <div class="mb-3">
    <label for="observaciones" class="form-label">Observaciones</label>
    <textarea name="observaciones" class="form-control"><?= htmlspecialchars($vacuna['descripcion']) ?></textarea>
  </div>

  <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
  <a href="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/vacunas" class="btn btn-secondary">Cancelar</a>
</form>
