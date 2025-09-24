<?php
// app/views/veterinario/consultas/index.php
$consultas = $consultas ?? [];
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-journal-medical"></i> Consultas registradas</h2>
  </div>

  <?php if (empty($consultas)): ?>
    <div class="alert alert-info">No tienes consultas registradas.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle small">
        <thead class="table-light">
          <tr>
            <th>📅 Fecha</th>
            <th>🐾 Mascota</th>
            <th>📝 Motivo</th>
            <th>👨‍⚕️ Veterinario</th>
            <th class="text-center">⚙️ Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($consultas as $consulta): ?>
            <tr>
              <td><?= htmlspecialchars($consulta['creado_en']) ?></td>
              <td><?= htmlspecialchars($consulta['nombre_mascota']) ?></td>
              <td><?= htmlspecialchars($consulta['motivo']) ?></td>
              <td><?= htmlspecialchars($consulta['nombre_veterinario'] ?? 'Sin nombre') ?></td>
              <td class="text-center">
                <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/ver"
                   class="btn btn-sm btn-outline-primary btn-ver-consulta"
                   data-bs-toggle="tooltip" data-bs-placement="top" title="Ver consulta"
                   data-id="<?= $consulta['id'] ?>"><i class="bi bi-eye"></i></a>

                <a href="/vetsmart/veterinario/consultas/<?= $consulta['id'] ?>/eliminar"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('¿Eliminar esta consulta?')"
                   data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar consulta">
                   <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Modal para ver/editar -->
<div class="modal fade" id="consultaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="consultaModalContent">
      <!-- Se inyecta contenido por AJAX -->
    </div>
  </div>
</div>

<!-- Bootstrap Bundle (ya incluido en layout en la mayoría de los casos) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para AJAX dinámico en modales -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = new bootstrap.Modal(document.getElementById('consultaModal'));
  const content = document.getElementById('consultaModalContent');

  function cargarModalConsulta(url) {
    const urlObj = new URL(url, window.location.origin);
    if (!urlObj.searchParams.has('ajax')) {
      urlObj.searchParams.set('ajax', '1');
    }

    fetch(urlObj.toString(), {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html'
      }
    })
    .then(res => {
      if (!res.ok) throw new Error('Error HTTP: ' + res.status);
      return res.text();
    })
    .then(html => {
      content.innerHTML = html;
      modal.show();
    })
    .catch(err => {
      console.error('Error al cargar modal:', err);
      alert('Error al cargar la consulta. Ver consola.');
    });
  }

  document.querySelectorAll('.btn-ver-consulta').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      cargarModalConsulta(`/vetsmart/veterinario/consultas/${this.dataset.id}/ver`);
    });
  });

  document.querySelectorAll('.btn-editar-consulta').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      cargarModalConsulta(`/vetsmart/veterinario/consultas/${this.dataset.id}/editar`);
    });
  });

  // Opcional: limpiar modal al cerrar
  document.getElementById('consultaModal').addEventListener('hidden.bs.modal', function () {
    content.innerHTML = '';
  });

  // Activar tooltips de Bootstrap
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
});
</script>
