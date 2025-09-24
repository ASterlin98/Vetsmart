

<form method="POST" action="/vetsmart/veterinario/vacunas/guardar" class="mb-4">
  <input type="hidden" name="mascota_id" value="<?= $mascota['id'] ?>">

  <div class="row">
    <div class="col-md-4">
      <input type="text" name="nombre" class="form-control" placeholder="Nombre de vacuna" required>
    </div>
    <div class="col-md-3">
      <input type="date" name="fecha_aplicacion" class="form-control" required>
    </div>
    <div class="col-md-3">
      <input type="date" name="proxima_dosis" class="form-control">
    </div>
    <div class="col-md-2">
      <button class="btn btn-success w-100" type="submit">Agregar</button>
    </div>
  </div>
  <div class="mt-2">
    <textarea name="descripcion" class="form-control" placeholder="Observaciones"></textarea>
  </div>
</form>

<table class="table table-striped">
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Fecha Aplicación</th>
      <th>Próxima Dosis</th>
      <th>Observaciones</th>
      <th>Acción</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($vacunas as $v): ?>
      <tr>
        <td><?= htmlspecialchars($v['nombre']) ?></td>
        <td><?= htmlspecialchars($v['fecha_aplicacion']) ?></td>
        <td><?= htmlspecialchars($v['proxima_dosis']) ?></td>
        <td><?= htmlspecialchars($v['descripcion']) ?></td>
        <td>
          <a class="btn btn-sm btn-danger" href="/vetsmart/veterinario/vacunas/<?= $v['id'] ?>/mascota/<?= $mascota['id'] ?>/eliminar" onclick="return confirm('¿Eliminar vacuna?')">Eliminar</a>
          <a href="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/vacunas/<?= $v['id'] ?>/editar" class="btn btn-sm btn-warning">✏️ Editar</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/historial" class="btn btn-secondary">Volver al historial</a>

  </tbody>
</table>
