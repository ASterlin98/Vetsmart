<div class="card shadow-sm">
  <div class="card-header bg-warning text-dark">
    <h5 class="mb-0">✏️ Editar Vacuna</h5>
  </div>
  <div class="card-body">
    <form method="POST" action="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/vacunas/<?= $vacuna['id'] ?>/actualizar">
      
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre de la Vacuna *</label>
        <input 
          type="text" 
          id="nombre"
          name="nombre" 
          class="form-control" 
          value="<?= htmlspecialchars($vacuna['nombre']) ?>" 
          required>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <label for="fecha" class="form-label">Fecha Aplicación *</label>
          <input 
            type="date" 
            id="fecha"
            name="fecha" 
            class="form-control" 
            value="<?= $vacuna['fecha_aplicacion'] ?>" 
            required>
        </div>

        <div class="col-md-6">
          <label for="proxima" class="form-label">Próxima Dosis</label>
          <input 
            type="date" 
            id="proxima"
            name="proxima" 
            class="form-control" 
            value="<?= $vacuna['proxima_dosis'] ?>">
        </div>
      </div>

      <div class="mt-3">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea 
          id="observaciones"
          name="observaciones" 
          class="form-control" 
          rows="3"
          placeholder="Notas adicionales"><?= htmlspecialchars($vacuna['descripcion']) ?></textarea>
      </div>

      <div class="mt-4 text-end">
        <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
        <a href="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/vacunas" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
