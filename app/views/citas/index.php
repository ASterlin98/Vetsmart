<h1>Gestión de Citas</h1>

<?php if (puede('citas','crear')): ?>
  <a href="/vetsmart/citas/create" class="btn btn-primary">Nuevas
<?php endif; ?>

<table class="table">
  <thead> ... </thead>
  <tbody>
    <?php foreach ($citas as $c): ?>
      <tr>
        <td><?= $c['id'] ?></td>
        <td><?= $c['fecha'] ?></td>
        <td>
          <?php if (puede('citas','editar')): ?>
            <a href="/vetsmart/citas/edit/<?= $c['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
          <?php endif; ?>

          <?php if (puede('citas','eliminar')): ?>
            <a href="/vetsmart/citas/delete/<?= $c['id'] ?>" class="btn btn-sm btn-danger">Eliminar</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
