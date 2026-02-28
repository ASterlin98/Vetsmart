<h2>Cliente: <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></h2>
<p><strong>Documento:</strong> <?= htmlspecialchars($cliente['docusu']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($cliente['email']) ?></p>
<p><strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono_cliente']) ?></p>
<p><strong>Dirección:</strong> <?= htmlspecialchars($cliente['direccion']) ?></p>
<p><strong>Ciudad:</strong> <?= htmlspecialchars($cliente['ciudad']) ?></p>

<hr>
<h3>Mascotas</h3>
<a href="<?= BASE ?>/admin/clientes/<?= $cliente['idusu'] ?>/mascotas/crear" class="btn btn-success mb-3">➕ Nueva Mascota</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Especie</th>
            <th>Raza</th>
            <th>Edad</th>
            <th>Peso (kg)</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mascotas as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['nombre']) ?></td>
                <td><?= htmlspecialchars($m['especie']) ?></td>
                <td><?= htmlspecialchars($m['raza']) ?></td>
                <td><?= htmlspecialchars($m['edad']) ?></td>
                <td><?= htmlspecialchars($m['peso']) ?></td>
                <td>
                    <a href="<?= BASE ?>/admin/clientes/<?= $cliente['idusu'] ?>/mascotas/<?= $m['id'] ?>/editar" class="btn btn-sm btn-warning">✏️ Editar</a>
                    <a href="<?= BASE ?>/admin/clientes/<?= $cliente['idusu'] ?>/mascotas/<?= $m['id'] ?>/eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar mascota?')">🗑️</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
