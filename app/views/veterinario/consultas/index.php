<?php
// app/views/veterinario/consultas/index.php
$consultas = $consultas ?? [];
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Consultas</h2>
    <a href="/vetsmart/veterinario/consultas/crear" class="btn btn-success">+ Nueva Consulta</a>
  </div>

  <?php if (empty($consultas)): ?>
    <div class="alert alert-info">No tienes consultas registradas.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Mascota</th>
            <th>Motivo</th>
            <th>Veterinario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($consultas as $consulta): ?>
  <tr>
    <td><?= $consulta['creado_en'] ?></td>
    <td><?= $consulta['nombre_mascota'] ?></td>
    <td><?= $consulta['motivo'] ?></td>
    <td><?= htmlspecialchars($consulta['nombre_veterinario'] ?? 'Sin nombre') ?></td>
            <td>
              <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/ver" class="btn btn-sm btn-primary">Ver</a>
              <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/editar" class="btn btn-sm btn-warning">Editar</a>
              <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta consulta?')">Eliminar</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
