<h2>Crear Cliente</h2>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= BASE ?>/admin/clientes/guardar">
    <div class="row mb-3">
        <div class="col">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>" required>
        </div>
        <div class="col">
            <label>Apellido</label>
            <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($cliente['apellido'] ?? '') ?>" required>
        </div>
    </div>
    <div class="mb-3">
        <label>Documento</label>
        <input type="text" name="docusu" class="form-control" value="<?= htmlspecialchars($cliente['docusu'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label>Dirección</label>
        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Ciudad</label>
        <input type="text" name="ciudad" class="form-control" value="<?= htmlspecialchars($cliente['ciudad'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
