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
              <td><?= htmlspecialchars($consulta['creado_en']) ?></td>
              <td><?= htmlspecialchars($consulta['nombre_mascota']) ?></td>
              <td><?= htmlspecialchars($consulta['motivo']) ?></td>
              <td><?= htmlspecialchars($consulta['nombre_veterinario'] ?? 'Sin nombre') ?></td>
              <td>
                <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/ver"
                   class="btn btn-sm btn-primary btn-ver-consulta"
                   data-id="<?= $consulta['id'] ?>">Ver</a>

                <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/editar"
                   class="btn btn-sm btn-warning btn-editar-consulta"
                   data-id="<?= $consulta['id'] ?>">Editar</a>

                <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/eliminar"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('¿Eliminar esta consulta?')">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- ✅ Modal Bootstrap vacío -->
<div class="modal fade" id="consultaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="consultaModalContent">
      <!-- Se inyecta contenido por AJAX aquí -->
    </div>
  </div>
</div>

<!-- ✅ Bootstrap JS (si aún no está en tu layout) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ Script para AJAX y mostrar modal -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = new bootstrap.Modal(document.getElementById('consultaModal'));

  function cargarModalConsulta(url) {
    fetch(url)
      .then(response => {
        if (!response.ok) throw new Error('Error al cargar contenido');
        return response.text();
      })
      .then(html => {
        document.getElementById('consultaModalContent').innerHTML = html;
        modal.show();
      })
      .catch(err => {
        console.error(err);
        alert('Error al cargar consulta.');
      });
  }

  document.querySelectorAll('.btn-ver-consulta').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const id = this.dataset.id;
      cargarModalConsulta(`/vetsmart/veterinario/consultas/${id}/ver`);
    });
  });

  document.querySelectorAll('.btn-editar-consulta').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const id = this.dataset.id;
      cargarModalConsulta(`/vetsmart/veterinario/consultas/${id}/editar`);
    });
  });
});
</script>
