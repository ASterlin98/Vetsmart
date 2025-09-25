<?php
// app/views/veterinario/consultas/index.php (versión visual mejorada)
$consultas = $consultas ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Consultas - VetSmart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background:#f6f8fb; font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; }
    .card-list { border-radius:12px; box-shadow: 0 10px 30px rgba(20,24,40,0.06); }
    .btn-action { min-width:40px; }
    .small-muted { color:#6b7280; }
    .table-fixed { table-layout: fixed; }
    .table-fixed td { overflow-wrap: anywhere; }
    .empty-state { padding:36px; }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-0"><i class="bi bi-journal-medical me-2"></i> Consultas registradas</h3>
      <small class="text-muted">Historial de consultas clínicas</small>
    </div>
    <div class="d-flex gap-2">
      <a href="/vetsmart/veterinario/consultas/crear" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nueva consulta</a>
      <button id="exportCsv" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Exportar CSV</button>
    </div>
  </div>

  <div class="card card-list">
    <div class="card-body p-0">
      <?php if (empty($consultas)): ?>
        <div class="empty-state text-center text-muted">No tienes consultas registradas.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover table-fixed mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:12%">Fecha</th>
                <th style="width:22%">Mascota</th>
                <th style="width:30%">Motivo</th>
                <th style="width:18%">Veterinario</th>
                <th class="text-center" style="width:18%">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($consultas as $consulta): ?>
                <tr>
                  <td class="small-muted"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($consulta['creado_en'] ?? '')) ) ?></td>
                  <td>
                    <div class="fw-semibold"><?= htmlspecialchars($consulta['nombre_mascota'] ?? '-') ?></div>
                    <div class="small text-muted">ID: <?= htmlspecialchars($consulta['mascota_id'] ?? '-') ?></div>
                  </td>
                  <td style="max-width:420px; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($consulta['motivo'] ?? '-')) ?></td>
                  <td><?= htmlspecialchars($consulta['nombre_veterinario'] ?? 'Sin nombre') ?></td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                      <a href="/vetsmart/veterinario/consultas/<?= htmlspecialchars($consulta['id']) ?>/ver" 
                         class="btn btn-sm btn-outline-primary btn-action btn-ver-consulta" 
                         data-id="<?= htmlspecialchars($consulta['id']) ?>" data-bs-toggle="tooltip" title="Ver">
                        <i class="bi bi-eye"></i>
                      </a>

                      <a href="/vetsmart/veterinario/consultas/<?= htmlspecialchars($consulta['id']) ?>/editar" 
                         class="btn btn-sm btn-outline-warning btn-action btn-editar-consulta" 
                         data-id="<?= htmlspecialchars($consulta['id']) ?>" data-bs-toggle="tooltip" title="Editar">
                        <i class="bi bi-pencil"></i>
                      </a>

                      <form action="/vetsmart/veterinario/consultas/<?= htmlspecialchars($consulta['id']) ?>/eliminar" method="POST" onsubmit="return confirm('¿Eliminar esta consulta?');">
                        <button type="submit" class="btn btn-sm btn-outline-danger btn-action" data-bs-toggle="tooltip" title="Eliminar">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
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

<!-- Modal para ver/editar (contenido cargado por AJAX) -->
<div class="modal fade" id="consultaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content" id="consultaModalContent"></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('consultaModal');
  const modal = new bootstrap.Modal(modalEl);
  const content = document.getElementById('consultaModalContent');

  // función para cargar contenido por AJAX (agrega ?ajax=1)
  async function cargarModalConsulta(url) {
    try {
      const urlObj = new URL(url, window.location.origin);
      if (!urlObj.searchParams.has('ajax')) urlObj.searchParams.set('ajax', '1');
      const res = await fetch(urlObj.toString(), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const html = await res.text();
      content.innerHTML = html;
      modal.show();
    } catch (err) {
      console.error('Error al cargar modal:', err);
      alert('No se pudo cargar el contenido. Revisa la consola.');
    }
  }

  // enlazar botones ver/editar
  document.querySelectorAll('.btn-ver-consulta').forEach(btn => {
    btn.addEventListener('click', function (e) { e.preventDefault(); cargarModalConsulta(`/vetsmart/veterinario/consultas/${this.dataset.id}/ver`); });
  });
  document.querySelectorAll('.btn-editar-consulta').forEach(btn => {
    btn.addEventListener('click', function (e) { e.preventDefault(); cargarModalConsulta(`/vetsmart/veterinario/consultas/${this.dataset.id}/editar`); });
  });

  // limpiar modal al cerrar
  modalEl.addEventListener('hidden.bs.modal', () => content.innerHTML = '');

  // tooltips
  const tipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tipList.forEach(t => new bootstrap.Tooltip(t));

  // Export CSV simple (cliente-side)
  document.getElementById('exportCsv').addEventListener('click', function () {
    const rows = Array.from(document.querySelectorAll('table tbody tr'));
    if (!rows.length) return alert('No hay datos para exportar');
    const csv = [];
    csv.push(['Fecha','Mascota','Motivo','Veterinario'].join(','));
    rows.forEach(r => {
      const cols = r.querySelectorAll('td');
      const colsText = [
        cols[0].innerText.trim(),
        '"' + cols[1].innerText.replace(/\"/g,'""').trim() + '"',
        '"' + cols[2].innerText.replace(/\"/g,'""').trim() + '"',
        '"' + cols[3].innerText.replace(/\"/g,'""').trim() + '"'
      ];
      csv.push(colsText.join(','));
    });
    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = 'consultas.csv'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
  });

});
</script>
</body>
</html>