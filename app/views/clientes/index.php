<h2 class="mb-4">Listado de Clientes</h2>
<a href="/vetsmart/admin/clientes/crear" class="btn btn-primary mb-3">➕ Nuevo Cliente</a>

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>Nombre</th>
            <th>Documento</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Ciudad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></td>
                <td><?= htmlspecialchars($c['docusu']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['telefono']) ?></td>
                <td><?= htmlspecialchars($c['ciudad']) ?></td>
                <td>
                    <a href="/vetsmart/admin/clientes/<?= $c['idusu'] ?>" class="btn btn-sm btn-info">👁️ Ver</a>
                    <a href="/vetsmart/admin/clientes/<?= $c['idusu'] ?>/editar" class="btn btn-sm btn-warning">✏️ Editar</a>
                    <a href="/vetsmart/admin/clientes/<?= $c['idusu'] ?>/eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar cliente?')">🗑️ Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
