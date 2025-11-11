<h2>Editar Mascota</h2>
<form method="POST" action="/vetsmart/admin/clientes/<?= $cliente_id ?>/mascotas/<?= $mascota['id'] ?>/actualizar">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($mascota['nombre']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Especie</label>
        <input type="text" name="especie" class="form-control" value="<?= htmlspecialchars($mascota['especie']) ?>">
    </div>
    <div class="mb-3">
        <label>Raza</label>
        <input type="text" name="raza" class="form-control" value="<?= htmlspecialchars($mascota['raza']) ?>">
    </div>
    <div class="mb-3">
        <label>Edad</label>
        <input type="number" name="edad" class="form-control" value="<?= htmlspecialchars($mascota['edad']) ?>">
    </div>
    <div class="mb-3">
        <label>Peso (kg)</label>
        <input type="number" step="0.01" name="peso" class="form-control" value="<?= htmlspecialchars($mascota['peso']) ?>">
    </div>
    <div class="mb-3">
        <label>Notas</label>
        <textarea name="notas" class="form-control"><?= htmlspecialchars($mascota['notas']) ?></textarea>
    </div>
    <button class="btn btn-success">Actualizar Mascota</button>
</form>
