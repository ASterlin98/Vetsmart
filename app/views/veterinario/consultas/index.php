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

<!-- Modal Bootstrap vacío -->
<div class="modal fade" id="consultaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="consultaModalContent">
      <!-- Se inyecta contenido por AJAX aquí -->
    </div>
  </div>
</div>

<!-- Bootstrap JS (si aún no está en tu layout; si ya lo tienes, puedes quitar esta línea) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para AJAX y mostrar modal (marca la petición como X-Requested-With) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Inicializa modal (Bootstrap 5)
  var modalEl = document.getElementById('consultaModal');
  var modal = new bootstrap.Modal(modalEl, {});

  /**
   * Cargar contenido para el modal usando fetch marcado como AJAX.
   * Añade headers y credentials para mantener sesión.
   * Si no existe 'ajax=1' en la URL, lo añade como fallback (opcional).
   */
  function cargarModalConsulta(url) {
    // si la url no contiene ?ajax=1, podemos añadirlo para mayor seguridad en el servidor
    var urlObj = new URL(url, window.location.origin);
    if (!urlObj.searchParams.has('ajax')) {
      urlObj.searchParams.set('ajax', '1');
    }

    fetch(urlObj.toString(), {
      method: 'GET',
      credentials: 'same-origin', // enviar cookies (mantener sesión)
      headers: {
        'X-Requested-With': 'XMLHttpRequest', // marca la petición como AJAX
        'Accept': 'text/html'
      }
    })
    .then(function(response) {
      // Si el servidor redirige al login, algunos servidores devuelven 200 con HTML del login.
      // Hacemos un chequeo básico por el status y por contenido.
      if (!response.ok) {
        throw new Error('HTTP ' + response.status);
      }
      return response.text().then(function(text) {
        // Detección simple: si la respuesta contiene "Ir a login" o "Página no encontrada" muy genérico,
        // puedes ajustar según tu layout de login.
        var lower = text.toLowerCase();
        if (lower.includes('ir a login') || lower.includes('página no encontrada') || lower.includes('login')) {
          throw new Error('Sesión expirada o respuesta inesperada (posible redirect a login).');
        }
        return text;
      });
    })
    .then(function(html) {
      document.getElementById('consultaModalContent').innerHTML = html;
      modal.show();

      // Opcional: si el contenido del modal tiene formularios que se envían, puedes engancharlos aquí
      // para evitar recargar la página. (No habilitado por defecto.)
    })
    .catch(function(err) {
      console.error('Error al cargar modal:', err);
      alert('No se pudo cargar la consulta. Comprueba la sesión o revisa la consola (Network).');
    });
  }

  // Delegación: añadir listeners a los botones de ver y editar
  document.querySelectorAll('.btn-ver-consulta').forEach(function(btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var id = this.dataset.id;
      cargarModalConsulta('/vetsmart/veterinario/consultas/' + id + '/ver');
    });
  });

  document.querySelectorAll('.btn-editar-consulta').forEach(function(btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var id = this.dataset.id;
      cargarModalConsulta('/vetsmart/veterinario/consultas/' + id + '/editar');
    });
  });

  // Si quieres limpiar contenido cuando se oculta el modal (opcional)
  modalEl.addEventListener('hidden.bs.modal', function () {
    document.getElementById('consultaModalContent').innerHTML = '';
  });
});
</script>
