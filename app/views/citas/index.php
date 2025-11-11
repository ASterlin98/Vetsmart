<h1>Gestión de Citas</h1>

<a href="/vetsmart/recepcionista/citas/create" class="btn btn-primary mb-3">➕ Nueva Cita</a>

<table class="table table-bordered table-hover">
  <thead class="table-success">
    <tr>
      <th>ID</th>
      <th>Fecha</th>
      <th>Cliente</th>
      <th>Empleado</th>
      <th>Servicio</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($citas)): ?>
      <?php foreach ($citas as $c): ?>
        <tr>
          <td><?= htmlspecialchars($c['id']) ?></td>
          <td><?= htmlspecialchars($c['fecha']) ?></td>
          <td><?= htmlspecialchars($c['cliente_nombre'] . ' ' . $c['cliente_apellido']) ?></td>
          <td><?= htmlspecialchars($c['empleado_nombre'] . ' ' . $c['empleado_apellido']) ?></td>
          <td><?= htmlspecialchars($c['servicio']) ?></td>
          <td><?= htmlspecialchars($c['estado']) ?></td>
          <td>
            <a href="/vetsmart/recepcionista/citas/edit/<?= $c['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="7" class="text-center">No hay citas registradas</td></tr>
    <?php endif; ?>
  </tbody>
</table>
