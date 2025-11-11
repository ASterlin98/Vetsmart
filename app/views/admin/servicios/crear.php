<h2>Nuevo Servicio</h2>
<form method="POST" action="/vetsmart/admin/servicios/guardar">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3" required></textarea>
    </div>
    <div class="mb-3">
        <label>Precio (COP)</label>
        <input type="number" name="precio" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Duración (minutos)</label>
        <input type="number" name="duracion_min" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Estado</label>
        <select name="activo" class="form-control">
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="/vetsmart/admin/servicios" class="btn btn-secondary">Cancelar</a>
</form>
