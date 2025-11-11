<h2>Editar Servicio</h2>
<form method="POST" action="/vetsmart/admin/servicios/<?= $servicio['id'] ?>/actualizar">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($servicio['nombre']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($servicio['descripcion']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Precio (COP)</label>
        <input type="number" name="precio" class="form-control" value="<?= $servicio['precio'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Duración (minutos)</label>
        <input type="number" name="duracion_min" class="form-control" value="<?= $servicio['duracion_min'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Estado</label>
        <select name="activo" class="form-control">
            <option value="1" <?= $servicio['activo'] ? 'selected' : '' ?>>Activo</option>
            <option value="0" <?= !$servicio['activo'] ? 'selected' : '' ?>>Inactivo</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Actualizar</button>
    <a href="/vetsmart/admin/servicios" class="btn btn-secondary">Cancelar</a>
</form>
