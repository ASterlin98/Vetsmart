<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0">💉 Registrar nueva vacuna</h5>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= BASE ?>/veterinario/vacunas/guardar">
      <input type="hidden" name="mascota_id" value="<?= $mascota['id'] ?>">

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Nombre de la vacuna</label>
          <input type="text" name="nombre" class="form-control" placeholder="Ej: Rabia, Parvo" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Fecha de aplicación</label>
          <input type="date" name="fecha_aplicacion" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Próxima dosis</label>
          <input type="date" name="proxima_dosis" class="form-control">
        </div>
        <div class="col-12">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" placeholder="Notas adicionales (opcional)" rows="2"></textarea>
        </div>
      </div>

      <div class="mt-3 text-end">
        <button class="btn btn-success" type="submit">➕ Agregar vacuna</button>
      </div>
    </form>
  </div>
</div>


<div class="card shadow-sm">
  <div class="card-header bg-light">
    <h5 class="mb-0">📋 Vacunas registradas</h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Fecha Aplicación</th>
            <th>Próxima Dosis</th>
            <th>Descripcion</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($vacunas)): ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-3">No hay vacunas registradas</td>
            </tr>
          <?php else: ?>
            <?php foreach ($vacunas as $v): ?>
              <tr>
                <td class="fw-semibold"><?= htmlspecialchars($v['nombre']) ?></td>
                <td><?= htmlspecialchars($v['fecha_aplicacion']) ?></td>
                <td><?= $v['proxima_dosis'] ? htmlspecialchars($v['proxima_dosis']) : '<span class="text-muted">-</span>' ?></td>
                <td><?= $v['descripcion'] ? htmlspecialchars($v['descripcion']) : '<span class="text-muted">Sin descripción</span>' ?></td>
                <td class="text-center">
                  <a href="<?= BASE ?>/veterinario/mascotas/<?= $mascota['id'] ?>/vacunas/<?= $v['id'] ?>/editar" 
                     class="btn btn-sm btn-warning me-1">✏️</a>
                  <a href="<?= BASE ?>/veterinario/vacunas/<?= $v['id'] ?>/mascota/<?= $mascota['id'] ?>/eliminar" 
                     class="btn btn-sm btn-danger"
                     onclick="return confirm('¿Eliminar esta vacuna?')">🗑️</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer text-end">
    <a href="<?= BASE ?>/veterinario/mascotas/<?= $mascota['id'] ?>/historial" class="btn btn-secondary">⬅️ Volver al historial</a>
  </div>
</div>
