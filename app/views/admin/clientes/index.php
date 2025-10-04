<?php
// app/views/admin/clientes/index.php
// UTF-8 sin BOM
$clientes = $clientes ?? [];
if (!is_array($clientes)) $clientes = [];

function avatar_color_for($text) {
    $hash = md5($text);
    $r = hexdec(substr($hash, 0, 2));
    $g = hexdec(substr($hash, 2, 2));
    $b = hexdec(substr($hash, 4, 2));
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial; }
    .card-nacho { border: 0; border-radius: 12px; box-shadow: 0 6px 20px rgba(17,24,39,0.06); }
    .table-nacho thead th { position: sticky; top: 0; background: linear-gradient(180deg,#ffffff,#f7fafc); font-weight:600; z-index:10; }
    .table-nacho tbody tr:hover { background: #f8fafc; transform: translateY(-1px); transition: all .12s ease; }
    .avatar-circle { width:44px; height:44px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:0.95rem; box-shadow: 0 2px 8px rgba(10,20,40,0.06); }
    .small-muted { color:#6b7280; font-size:0.9rem; }
    .actions .btn { margin-right:6px; }
    .search-input { max-width:380px; }
    .table-sm td, .table-sm th { padding:.55rem .6rem; }
    .nowrap { white-space: nowrap; }
    .truncate { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display:inline-block; vertical-align:middle; }
    .dl-key { width: 36%; font-weight:600; }
    .pet-item { border-radius:8px; padding:8px; background:#fff; box-shadow:0 2px 6px rgba(20,24,40,0.04); margin-bottom:8px; }
  </style>
</head>
<body>
<div class="container-fluid py-3">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
    <div>
      <h2 class="fw-bold">Listado de Clientes</h2>
      <div class="small-muted">Clientes registrados en el sistema</div>
    </div>

    <div class="d-flex gap-2">
      <input id="filterInput" type="search" class="form-control" placeholder="🔎 Buscar por nombre, documento, email o ciudad">

      <!-- Botón que abre modal (pequeño y centrado) -->
      <button type="button"
              class="btn btn-primary btn-sm"
              data-bs-toggle="modal"
              data-bs-target="#nuevoClienteModal">
        ➕ Nuevo Cliente
      </button>
    </div>
  </div>

  <div class="card p-3">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Cliente</th>
                    <th>Documento</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Ciudad</th>
                    <th>Dirección</th>
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
                        $searchAttr = strtolower($nombre . ' ' . ($c['docusu'] ?? '') . ' ' . ($c['email'] ?? '') . ' ' . ($c['ciudad'] ?? ''));
                    ?>
                        <tr data-search="<?= htmlspecialchars($searchAttr, ENT_QUOTES) ?>">
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
                            <td class="nowrap"><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                            <td class="nowrap"><?= htmlspecialchars($c['telefono'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['ciudad'] ?? '-') ?></td>
                            <td class="nowrap"><?= htmlspecialchars($c['direccion'] ?? '-') ?></td>
                            <td class="text-end actions nowrap">
                                <!-- VER: abre modal de solo lectura; usamos data-* para pasar valores -->
                                <button type="button"
                                        class="btn btn-sm btn-info btn-view-client"
                                        data-id="<?= htmlspecialchars($cid, ENT_QUOTES) ?>"
                                        data-nombre="<?= htmlspecialchars($c['nombre'] ?? '', ENT_QUOTES) ?>"
                                        data-apellido="<?= htmlspecialchars($c['apellido'] ?? '', ENT_QUOTES) ?>"
                                        data-docusu="<?= htmlspecialchars($c['docusu'] ?? '', ENT_QUOTES) ?>"
                                        data-email="<?= htmlspecialchars($c['email'] ?? '', ENT_QUOTES) ?>"
                                        data-telefono="<?= htmlspecialchars($c['telefono'] ?? '', ENT_QUOTES) ?>"
                                        data-direccion="<?= htmlspecialchars($c['direccion'] ?? '', ENT_QUOTES) ?>"
                                        data-ciudad="<?= htmlspecialchars($c['ciudad'] ?? '', ENT_QUOTES) ?>"
                                        data-direccion="<?= htmlspecialchars($c['direccion'] ?? '', ENT_QUOTES) ?>"
                                        title="Ver">👁️</button>

                                <!-- EDITAR: abre modal editar -->
                                <button type="button"
                                        class="btn btn-sm btn-warning btn-edit-client"
                                        data-id="<?= htmlspecialchars($cid, ENT_QUOTES) ?>"
                                        data-nombre="<?= htmlspecialchars($c['nombre'] ?? '', ENT_QUOTES) ?>"
                                        data-apellido="<?= htmlspecialchars($c['apellido'] ?? '', ENT_QUOTES) ?>"
                                        data-docusu="<?= htmlspecialchars($c['docusu'] ?? '', ENT_QUOTES) ?>"
                                        data-email="<?= htmlspecialchars($c['email'] ?? '', ENT_QUOTES) ?>"
                                        data-telefono="<?= htmlspecialchars($c['telefono'] ?? '', ENT_QUOTES) ?>"
                                        data-ciudad="<?= htmlspecialchars($c['ciudad'] ?? '', ENT_QUOTES) ?>"
                                        data-direccion="<?= htmlspecialchars($c['direccion'] ?? '', ENT_QUOTES) ?>"
                                        title="Editar">✏️</button>

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

          <div class="mb-2">
            <label for="direccion" class="form-label small">Dirección</label><label class="form-label small"></label>
            <input id="direccion" name="direccion" type="text" class="form-control form-control-sm">
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

<!-- Modal: Editar Cliente (small + centered) -->
<div class="modal fade" id="editarClienteModal" tabindex="-1" aria-labelledby="editarClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form id="formEditarCliente" method="POST" action="" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="editarClienteLabel">Editar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div id="editarClienteErrors" class="text-danger small mb-2" style="display:none;"></div>

          <input type="hidden" name="id" id="edit_id">

          <div class="mb-2">
            <label for="edit_nombre" class="form-label small">Nombre</label>
            <input id="edit_nombre" name="nombre" type="text" class="form-control form-control-sm" required>
          </div>

          <div class="mb-2">
            <label for="edit_apellido" class="form-label small">Apellido</label>
            <input id="edit_apellido" name="apellido" type="text" class="form-control form-control-sm" required>
          </div>

          <div class="mb-2">
            <label for="edit_docusu" class="form-label small">Documento</label>
            <input id="edit_docusu" name="docusu" type="text" class="form-control form-control-sm" required>
          </div>

          <div class="mb-2">
            <label for="edit_email" class="form-label small">Email</label>
            <input id="edit_email" name="email" type="email" class="form-control form-control-sm" required>
          </div>

          <div class="mb-2">
            <label for="edit_telefono" class="form-label small">Teléfono</label>
            <input id="edit_telefono" name="telefono" type="text" class="form-control form-control-sm">
          </div>

          <div class="mb-2">
            <label for="edit_ciudad" class="form-label small">Ciudad</label>
            <input id="edit_ciudad" name="ciudad" type="text" class="form-control form-control-sm">
          </div>

            <div class="mb-2">
                <label for="edit_direccion" class="form-label small">Dirección</label>
                <input id="edit_direccion" name="direccion" type="text" class="form-control form-control-sm">
            </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnGuardarEditarCliente" class="btn btn-sm btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Ver Cliente (small + centered) con sección Mascotas -->
<div class="modal fade" id="verClienteModal" tabindex="-1" aria-labelledby="verClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="verClienteLabel">Ficha del Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-1">
          <dt class="col-5 dl-key">Nombre</dt>
          <dd class="col-7" id="view_nombre">-</dd>

          <dt class="col-5 dl-key">Apellido</dt>
          <dd class="col-7" id="view_apellido">-</dd>

          <dt class="col-5 dl-key">Documento</dt>
          <dd class="col-7" id="view_docusu">-</dd>

          <dt class="col-5 dl-key">Email</dt>
          <dd class="col-7" id="view_email">-</dd>

          <dt class="col-5 dl-key">Teléfono</dt>
          <dd class="col-7" id="view_telefono">-</dd>

          <dt class="col-5 dl-key">Dirección</dt>
          <dd class="col-7" id="view_direccion">-</dd>

          <dt class="col-5 dl-key">Ciudad</dt>
          <dd class="col-7" id="view_ciudad">-</dd>
        </dl>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="mb-0">Mascotas</h6>
          <a id="btn_add_pet" href="#" class="btn btn-sm btn-success">➕ Agregar mascota</a>
        </div>

        <div id="view_mascotas_body">
          <!-- Se llenará dinámicamente -->
          <div class="text-muted small">Cargando mascotas...</div>
        </div>

      </div>
      <div class="modal-footer">
        <a id="view_full_link" href="#" class="btn btn-sm btn-outline-primary">Ver ficha completa</a>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


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

  if (input) input.addEventListener('input', applyFilter);
  if (resetBtn) resetBtn.addEventListener('click', function(){ input.value = ''; applyFilter(); });

  // Previene doble submit en formularios
  function preventDoubleSubmit(form, btnId) {
    const btn = document.getElementById(btnId);
    if (!form) return;
    form.addEventListener('submit', function(e){
      if (!form.checkValidity()) {
        form.reportValidity();
        e.preventDefault();
        return;
      }
      if (btn) btn.disabled = true;
      setTimeout(()=>{ if (btn) btn.disabled = false; }, 5000);
    });
  }
  preventDoubleSubmit(document.getElementById('formNuevoCliente'), 'btnGuardarNuevoCliente');
  preventDoubleSubmit(document.getElementById('formEditarCliente'), 'btnGuardarEditarCliente');

  // EDITAR: abrir modal y rellenar campos desde data-attributes
  const editButtons = document.querySelectorAll('.btn-edit-client');
  const editarModalEl = document.getElementById('editarClienteModal');
  const editarModal = new bootstrap.Modal(editarModalEl, {});

  editButtons.forEach(btn => {
    btn.addEventListener('click', function(){
      const id = this.getAttribute('data-id') || '';
      const nombre = this.getAttribute('data-nombre') || '';
      const apellido = this.getAttribute('data-apellido') || '';
      const docusu = this.getAttribute('data-docusu') || '';
      const email = this.getAttribute('data-email') || '';
      const telefono = this.getAttribute('data-telefono') || '';
      const ciudad = this.getAttribute('data-ciudad') || '';
      const direccion = this.getAttribute('data-direccion') || '';

      document.getElementById('edit_id').value = id;
      document.getElementById('edit_nombre').value = nombre;
      document.getElementById('edit_apellido').value = apellido;
      document.getElementById('edit_docusu').value = docusu;
      document.getElementById('edit_email').value = email;
      document.getElementById('edit_telefono').value = telefono;
      document.getElementById('edit_ciudad').value = ciudad;
      document.getElementById('edit_direccion').value = direccion;

      const formEditar = document.getElementById('formEditarCliente');
      formEditar.action = '/vetsmart/admin/clientes/' + encodeURIComponent(id) + '/actualizar';

      editarModal.show();
    });
  });

  // VER: abrir modal lectura + cargar mascotas via API
  const viewButtons = document.querySelectorAll('.btn-view-client');
  const verModalEl = document.getElementById('verClienteModal');
  const verModal = new bootstrap.Modal(verModalEl, {});

  const mascotasContainer = document.getElementById('view_mascotas_body');
  const addPetBtn = document.getElementById('btn_add_pet');

  viewButtons.forEach(btn => {
    btn.addEventListener('click', function(){
      const id = this.getAttribute('data-id') || '';
      const nombre = this.getAttribute('data-nombre') || '';
      const apellido = this.getAttribute('data-apellido') || '';
      const docusu = this.getAttribute('data-docusu') || '';
      const email = this.getAttribute('data-email') || '';
      const telefono = this.getAttribute('data-telefono') || '';
      const direccion = this.getAttribute('data-direccion') || '';
      const ciudad = this.getAttribute('data-ciudad') || '';

      // poblar campos de sólo lectura
      document.getElementById('view_nombre').innerText = nombre || '-';
      document.getElementById('view_apellido').innerText = apellido || '-';
      document.getElementById('view_docusu').innerText = docusu || '-';
      document.getElementById('view_email').innerText = email || '-';
      document.getElementById('view_telefono').innerText = telefono || '-';
      document.getElementById('view_direccion').innerText = direccion || '-';
      document.getElementById('view_ciudad').innerText = ciudad || '-';

      // link a ficha completa
      const link = document.getElementById('view_full_link');
      if (link) link.href = '/vetsmart/admin/clientes/' + encodeURIComponent(id);

      // boton agregar mascota
      if (addPetBtn) addPetBtn.href = '/vetsmart/admin/clientes/' + encodeURIComponent(id) + '/mascotas/crear';

      // cargar mascotas via API
if (mascotasContainer) {
  mascotasContainer.innerHTML = '<div class="text-muted small">Cargando mascotas...</div>';
  fetch('/vetsmart/api/clientes/' + encodeURIComponent(id) + '/mascotas')
    .then(res => {
      if (!res.ok) throw new Error('Error cargando mascotas');
      return res.json();
    })
    .then(data => {
      if (!Array.isArray(data) || data.length === 0) {
        mascotasContainer.innerHTML = '<div class="text-muted small">No tiene mascotas registradas.</div>';
        return;
      }
      const frag = document.createDocumentFragment();
      data.forEach(p => {
        const div = document.createElement('div');
        div.className = 'pet-item d-flex justify-content-between align-items-center mb-2 p-2 border rounded';

        const left = document.createElement('div');
        left.innerHTML =
          '<div style="font-weight:600;">' +
          (p.nombre ? escapeHtml(p.nombre) : 'Sin nombre') +
          '</div>' +
          '<div class="small-muted">' +
          (p.especie ? escapeHtml(p.especie) : '-') +
          ' • ' +
          (p.raza ? escapeHtml(p.raza) : '-') +
          '</div>';

        const right = document.createElement('div');
        right.className = "btn-group";

        // Ver
        const viewLink = document.createElement('a');
        viewLink.className = 'btn btn-sm btn-outline-primary';
        viewLink.href =
          '/vetsmart/admin/clientes/' +
          encodeURIComponent(id) +
          '/mascotas/' +
          encodeURIComponent(p.id ?? p.ID ?? p.Id ?? '');
        viewLink.textContent = 'Ver';
        right.appendChild(viewLink);

        // Editar
        const editLink = document.createElement('a');
        editLink.className = 'btn btn-sm btn-outline-warning';
        editLink.href =
          '/vetsmart/admin/clientes/' +
          encodeURIComponent(id) +
          '/mascotas/' +
          encodeURIComponent(p.id ?? p.ID ?? p.Id ?? '') +
          '/editar';
        editLink.textContent = 'Editar';
        right.appendChild(editLink);

        // Eliminar
        const deleteLink = document.createElement('a');
        deleteLink.className = 'btn btn-sm btn-outline-danger';
        deleteLink.href =
          '/vetsmart/admin/clientes/' +
          encodeURIComponent(id) +
          '/mascotas/' +
          encodeURIComponent(p.id ?? p.ID ?? p.Id ?? '') +
          '/eliminar';
        deleteLink.textContent = 'Eliminar';
        deleteLink.onclick = function (e) {
          if (!confirm("¿Seguro que deseas eliminar esta mascota?")) {
            e.preventDefault();
          }
        };
        right.appendChild(deleteLink);

        div.appendChild(left);
        div.appendChild(right);
        frag.appendChild(div);
      });
      mascotasContainer.innerHTML = '';
      mascotasContainer.appendChild(frag);
    })
    .catch(err => {
      console.error(err);
      mascotasContainer.innerHTML =
        '<div class="text-danger small">Error cargando mascotas.</div>';
    });
}

      verModal.show();
    });
  });

  // Helpers: escapar texto para inserción en HTML
  function escapeHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s).replace(/[&<>"']/g, function (m) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
    });
  }

  // limpiar modales al cerrarlos
  editarModalEl.addEventListener('hidden.bs.modal', function () {
    document.getElementById('formEditarCliente').reset();
    document.getElementById('formEditarCliente').action = '';
    document.getElementById('editarClienteErrors').style.display = 'none';
    document.getElementById('editarClienteErrors').textContent = '';
  });

  verModalEl.addEventListener('hidden.bs.modal', function () {
    // limpiar vista
    ['view_nombre','view_apellido','view_docusu','view_email','view_telefono','view_direccion','view_ciudad'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.innerText = '-';
    });
    const link = document.getElementById('view_full_link');
    if (link) link.href = '#';
    if (mascotasContainer) mascotasContainer.innerHTML = '';
    if (addPetBtn) addPetBtn.href = '#';
  });

})();
</script>
</body>
</html>
