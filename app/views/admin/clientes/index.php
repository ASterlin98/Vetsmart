<?php
// app/views/admin/clientes/index.php
// UTF-8 sin BOM
$clientes = $clientes ?? [];
if (!is_array($clientes)) $clientes = [];

/**
 * Genera un color de fondo a partir del texto (para avatar con iniciales)
 */
function avatar_color_for($text) {
    $hash = md5($text);
    // coger 3 pares hex y limitar brillo
    $r = hexdec(substr($hash, 0, 2));
    $g = hexdec(substr($hash, 2, 2));
    $b = hexdec(substr($hash, 4, 2));
    // reducir luminosidad si muy claro
    $factor = 0.85;
    $r = (int)($r * $factor);
    $g = (int)($g * $factor);
    $b = (int)($b * $factor);
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Listado de Clientes</title>

  <!-- Bootstrap CSS (si tu layout ya lo incluye, puedes quitar esta línea) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Estilo "nacho" para la tabla */
    body { background: #f3f6fb; font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
    .card-nacho { border: 0; border-radius: 12px; box-shadow: 0 6px 20px rgba(17,24,39,0.06); }
    .table-nacho thead th { position: sticky; top: 0; background: linear-gradient(180deg,#ffffff,#f7fafc); font-weight:600; z-index:10; }
    .table-nacho tbody tr:hover { background: #f8fafc; transform: translateY(-1px); transition: all .12s ease; }
    .avatar-circle {
      width:44px; height:44px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center;
      color: #fff; font-weight:700; font-size:0.95rem;
      box-shadow: 0 2px 8px rgba(10,20,40,0.06);
    }
    .small-muted { color:#6b7280; font-size:0.9rem; }
    .badge-small { font-size:0.72rem; padding:.35em .5em; border-radius:6px; }
    .actions .btn { margin-right:6px; }
    .search-input { max-width:380px; }
    .table-sm td, .table-sm th { padding:.55rem .6rem; }
    .nowrap { white-space: nowrap; }
    .truncate { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display:inline-block; vertical-align:middle; }
    @media (max-width: 768px) {
      .avatar-circle { width:36px; height:36px; font-size:0.85rem; }
      .search-input { max-width: 100%; }
    }
  </style>
</head>
<body>
<div class="container py-5">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
    <div>
      <h2 class="mb-1">Listado de Clientes</h2>
      <div class="small-muted">Clientes registrados en el sistema</div>
    </div>

    <div class="d-flex gap-2 align-items-center">
      <input id="filterInput" type="search" class="form-control form-control-sm search-input" placeholder="🔎 Buscar por nombre, documento, email o ciudad">
      <button type="button" class="btn btn-outline-secondary btn-sm" id="resetFilter">Limpiar</button>

      <!-- Botón que abre modal (pequeño y centrado) -->
      <button type="button"
              class="btn btn-primary btn-sm"
              data-bs-toggle="modal"
              data-bs-target="#nuevoClienteModal">
        ➕ Nuevo Cliente
      </button>
    </div>
  </div>

  <div class="card card-nacho mb-4">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm table-nacho mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Cliente</th>
                    <th>Documento</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Ciudad</th>
                    <th class="text-end" style="width:200px">Acciones</th>
                </tr>
            </thead>
            <tbody id="clientesTableBody">
                <?php if (!empty($clientes)): ?>
                    <?php foreach ($clientes as $c):
                        $cid = $c['idusu'] ?? $c['id'] ?? '';
                        $nombre = trim(($c['nombre'] ?? '') . ' ' . ($c['apellido'] ?? ''));
                        $initial = strtoupper(substr(trim($c['nombre'] ?? ''), 0, 1) ?: substr($nombre ?: 'U', 0, 1));
                        $bg = avatar_color_for($nombre ?: $c['email'] ?? 'u');
                    ?>
                        <tr data-search="<?= htmlspecialchars(strtolower($nombre . ' ' . ($c['docusu'] ?? '') . ' ' . ($c['email'] ?? '') . ' ' . ($c['ciudad'] ?? ''))) ?>">
                            <td>
                              <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle" style="background: <?= $bg ?>; border-radius:8px;"><?= htmlspecialchars($initial) ?></div>
                                <div>
                                  <div style="font-weight:600;"><?= htmlspecialchars($nombre ?: 'Sin nombre') ?></div>
                                  <div class="small-muted truncate"><?= htmlspecialchars($c['email'] ?? '') ?></div>
                                </div>
                              </div>
                            </td>
                            <td class="nowrap"><?= htmlspecialchars($c['docusu'] ?? '-') ?></td>
                            <td class="truncate"><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                            <td class="nowrap"><?= htmlspecialchars($c['telefono'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['ciudad'] ?? '-') ?></td>
                            <td class="text-end actions nowrap">
                                <a href="/vetsmart/admin/clientes/<?= urlencode($cid) ?>" class="btn btn-sm btn-info" title="Ver">👁️</a>
                                <a href="/vetsmart/admin/clientes/<?= urlencode($cid) ?>/editar" class="btn btn-sm btn-warning" title="Editar">✏️</a>
                                <a href="/vetsmart/admin/clientes/<?= urlencode($cid) ?>/eliminar" class="btn btn-sm btn-danger" title="Eliminar"
                                   onclick="return confirm('¿Eliminar cliente?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">No hay clientes registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="text-muted small">Mostrando <span id="rowsCount"><?= count($clientes) ?></span> clientes</div>
</div>

<!-- Modal: Nuevo Cliente (small + centered) -->
<div class="modal fade" id="nuevoClienteModal" tabindex="-1" aria-labelledby="nuevoClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form id="formNuevoCliente" method="POST" action="/vetsmart/admin/clientes/guardar" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="nuevoClienteLabel">Nuevo Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div id="nuevoClienteErrors" class="text-danger small mb-2" style="display:none;"></div>

          <div class="mb-2">
            <label for="nombre" class="form-label small">Nombre</label>
            <input id="nombre" name="nombre" type="text" class="form-control form-control-sm" required>
          </div>

          <div class="mb-2">
            <label for="apellido" class="form-label small">Apellido</label>
            <input id="apellido" name="apellido" type="text" class="form-control form-control-sm" required>
          </div>

          <div class="mb-2">
            <label for="docusu" class="form-label small">Documento</label>
            <input id="docusu" name="docusu" type="text" class="form-control form-control-sm" required>
          </div>

          <div class="mb-2">
            <label for="email" class="form-label small">Email</label>
            <input id="email" name="email" type="email" class="form-control form-control-sm" required>
          </div>

          <div class="mb-2">
            <label for="telefono" class="form-label small">Teléfono</label>
            <input id="telefono" name="telefono" type="text" class="form-control form-control-sm">
          </div>

          <div class="mb-2">
            <label for="ciudad" class="form-label small">Ciudad</label>
            <input id="ciudad" name="ciudad" type="text" class="form-control form-control-sm">
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnGuardarNuevoCliente" class="btn btn-sm btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap bundle JS (incluye Popper). Si tu layout ya lo incluye, quita esta línea -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
  // Filtro en vivo simple (cliente-side)
  const input = document.getElementById('filterInput');
  const tbody = document.getElementById('clientesTableBody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  const countEl = document.getElementById('rowsCount');
  const resetBtn = document.getElementById('resetFilter');

  function normalize(s){ return (s||'').toString().trim().toLowerCase(); }

  function applyFilter() {
    const q = normalize(input.value);
    let visible = 0;
    rows.forEach(tr => {
      const hay = normalize(tr.getAttribute('data-search') || '');
      const ok = q === '' || hay.indexOf(q) !== -1;
      tr.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });
    if (countEl) countEl.textContent = visible;
  }

  if (input) {
    input.addEventListener('input', applyFilter);
  }
  if (resetBtn) {
    resetBtn.addEventListener('click', function(){
      input.value = '';
      applyFilter();
    });
  }

  // Previene doble submit del modal (mejora UX)
  const form = document.getElementById('formNuevoCliente');
  const btn = document.getElementById('btnGuardarNuevoCliente');
  if (form) {
    form.addEventListener('submit', function(e){
      if (!form.checkValidity()) {
        form.reportValidity();
        e.preventDefault();
        return;
      }
      if (btn) btn.disabled = true;
      // no AJAX por ahora: submit tradicional. botón se reactiva al cerrar modal.
      setTimeout(()=>{ if (btn) btn.disabled = false; }, 5000);
    });
  }
})();
</script>
</body>
</html>
