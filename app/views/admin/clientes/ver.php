<h3>Mascotas</h3>

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= BASE ?>/admin/clientes/<?php echo $cliente['id']; ?>/mascotas/crear" class="btn btn-success">
        ➕ Nueva Mascota
    </a>

    <a href="<?= BASE ?>/admin/clientes" class="btn btn-secondary">
        🔙 Volver a Clientes
    </a>
</div>

<?php if (empty($mascotas)): ?>
    <p>No hay mascotas registradas para este cliente.</p>
<?php else: ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Especie</th>
            <th>Raza</th>
            <th>Edad</th>
            <th>Peso</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mascotas as $m): ?>
        <tr>
            <td><?php echo htmlspecialchars($m['nombre']); ?></td>
            <td><?php echo htmlspecialchars($m['especie']); ?></td>
            <td><?php echo htmlspecialchars($m['raza']); ?></td>
            <td><?php echo htmlspecialchars($m['edad']); ?> años</td>
            <td><?php echo htmlspecialchars($m['peso']); ?> kg</td>
            <td>
                <!-- Editar -->
                <a href="<?= BASE ?>/admin/clientes/<?php echo $cliente['id']; ?>/mascotas/<?php echo $m['id']; ?>/editar" 
                   class="btn btn-sm btn-warning">✏️ Editar</a>

                <!-- Eliminar -->
                <a href="<?= BASE ?>/admin/clientes/<?php echo $cliente['id']; ?>/mascotas/<?php echo $m['id']; ?>/eliminar" 
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('¿Eliminar mascota?')">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
