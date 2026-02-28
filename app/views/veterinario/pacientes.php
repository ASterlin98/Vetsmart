<h2>Pacientes</h2>

<div class="table-responsive">
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Especie</th>
            <th>Raza</th>
            <th>Edad</th>
            <th>Dueño</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mascotas as $m): ?>
        <tr>
            <td><?= htmlspecialchars($m['nombre']) ?></td>
            <td><?= htmlspecialchars($m['especie']) ?></td>
            <td><?= htmlspecialchars($m['raza']) ?></td>
            <td><?= htmlspecialchars($m['edad']) ?> años</td>
            <td><?= htmlspecialchars($m['nombre_dueno'] . ' ' . $m['apellido_dueno']) ?></td>
            <td><?= htmlspecialchars($m['telefono_dueno']) ?></td>
            <td>
                <a href="<?= BASE ?>/veterinario/mascotas/<?= $m['id'] ?>/historial" class="btn btn-sm btn-info">📋 Historial</a>
                <a href="<?= BASE ?>/veterinario/mascotas/<?= $m['id'] ?>/agendar" class="btn btn-sm btn-primary">📅 Agendar Cita</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>