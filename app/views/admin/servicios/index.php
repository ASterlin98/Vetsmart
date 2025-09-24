<h2>Gestión de Servicios</h2>
<a href="/vetsmart/admin/servicios/crear" class="btn btn-primary mb-3">➕ Nuevo Servicio</a>

<?php if (empty($servicios)): ?>
    <p>No hay servicios registrados.</p>
<?php else: ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio (COP)</th>
            <th>Duración</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($servicios as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['nombre']) ?></td>
            <td><?= htmlspecialchars($s['descripcion']) ?></td>
            <td>$<?= number_format($s['precio'], 0, ',', '.') ?></td>
            <td><?= $s['duracion_min'] ?> min</td>
            <td><?= $s['activo'] ? 'Activo' : 'Inactivo' ?></td>
            <td>
                <a href="/vetsmart/admin/servicios/<?= $s['id'] ?>/editar" class="btn btn-sm btn-warning">✏️</a>
                <a href="/vetsmart/admin/servicios/<?= $s['id'] ?>/eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este servicio?')">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
