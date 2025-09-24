<?php
// app/views/veterinario/pacientes/index.php
$mascotas = $mascotas ?? [];
$q = $q ?? '';
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>🐾 Pacientes</h2>
    <div class="d-flex">
      <form class="me-2" method="GET" action="/vetsmart/veterinario/pacientes">
        <div class="input-group">
          <input id="searchInput" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" type="search" class="form-control" placeholder="Buscar por nombre, especie, raza o dueño...">
          <button class="btn btn-outline-secondary" type="submit">Buscar</button>
          <button id="resetBtn" type="button" class="btn btn-outline-danger" title="Limpiar búsqueda">✖</button>
        </div>
      </form>
      <a href="/vetsmart/veterinario/mis-citas" class="btn btn-primary ms-2">Ir a mi calendario</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <?php if (empty($mascotas)): ?>
        <div class="p-4 text-center text-muted">No se encontraron mascotas.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table id="tablaMascotas" class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Edad</th>
                <th>Dueño</th>
                <th>Teléfono</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($mascotas as $m): ?>
                <tr>
                  <td><?= htmlspecialchars($m['nombre'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($m['especie'] ?? '-', ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars($m['raza'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= ($m['edad'] === null || $m['edad'] === '') ? '-' : (int)$m['edad'] . ' años' ?></td>
                  <td><?= htmlspecialchars(trim(($m['nombre_dueno'] ?? '') . ' ' . ($m['apellido_dueno'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($m['telefono_dueno'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary" href="/vetsmart/veterinario/mascotas/<?= htmlspecialchars($m['id'], ENT_QUOTES, 'UTF-8') ?>/historial">
                      📖 Ver historial
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- JS: búsqueda cliente-side + atajos -->
<script>
(function () {
  const searchInput = document.getElementById('searchInput');
  const resetBtn = document.getElementById('resetBtn');
  const tabla = document.getElementById('tablaMascotas');
  if (!tabla) return;

  // filtro instantáneo en el cliente (sin refrescar): busca en varias columnas
  searchInput.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    const rows = tabla.tBodies[0].rows;
    for (let r of rows) {
      const text = (r.textContent || r.innerText || '').toLowerCase();
      r.style.display = q === '' ? '' : (text.indexOf(q) !== -1 ? '' : 'none');
    }
  });

  // botón reset limpia input y muestra todas las filas
  resetBtn.addEventListener('click', function () {
    searchInput.value = '';
    searchInput.dispatchEvent(new Event('input'));
    // además limpia querystring (opcional): redirigir a la ruta sin ?q=
    const url = window.location.pathname;
    window.history.replaceState({}, '', url);
  });

})();
</script>
