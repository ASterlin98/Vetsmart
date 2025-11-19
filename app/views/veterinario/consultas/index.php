<?php
// app/views/veterinario/consultas/index.php (versión visual mejorada con Tabs y estilo profesional)
$consultas = $consultas ?? [];
$mascotas = $mascotas ?? [];
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
    body {
      background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Arial;
    }
    h3.fw-bold { font-weight: 700; color: #1e293b; }
    .text-muted { color: #6b7280 !important; }

    .card-list {
      border-radius: 14px;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
      background-color: #ffffff;
      border: none;
    }
    .table { margin-bottom: 0; border-radius: 8px; overflow: hidden; }
    .table thead {
      background-color: #f9fafb;
      color: #374151;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.02em;
    }
    .table tbody tr:hover { background-color: #f3f4f6; transition: 0.2s ease; }

    .btn-outline-primary, .btn-outline-danger, .btn-outline-info {
      border-radius: 8px;
      transition: all 0.2s ease-in-out;
    }
    .btn-outline-primary:hover { background-color: #3b82f6; color: #fff; }
    .btn-outline-danger:hover { background-color: #ef4444; color: #fff; }
    .btn-outline-info:hover { background-color: #0ea5e9; color: #fff; }

    .nav-tabs { border-bottom: 2px solid #e5e7eb; }
    .nav-tabs .nav-link {
      color: #6b7280; font-weight: 500; border: none;
      padding: 10px 20px; border-radius: 8px 8px 0 0;
      transition: all 0.2s ease;
    }
    .nav-tabs .nav-link:hover { color: #2563eb; background-color: #f1f5ff; }
    .nav-tabs .nav-link.active {
      color: #1e293b; background-color: #fff;
      border-bottom: 3px solid #3b82f6; font-weight: 600;
    }

    .empty-state { padding: 60px 20px; }
    .empty-state i { font-size: 3rem; color: #d1d5db; margin-bottom: 10px; }
    .badge-status {
      background-color: #e0f2fe; color: #0369a1;
      font-weight: 600; border-radius: 6px; padding: 4px 10px;
    }
    .table-fixed td { overflow-wrap: anywhere; }
    .btn-action { min-width:40px; }
    .small-muted { color:#6b7280; }
  </style>
</head>
<body>

<div class="container-fluid py-3">
  <!-- Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
      <div class="me-3 bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
        <i class="bi bi-journal-medical" style="font-size:1.5rem;"></i>
      </div>
      <div>
        <h3 class="fw-bold mb-0">Centro de Consultas</h3>
        <small class="text-muted">Gestiona tus consultas y pacientes con facilidad</small>
      </div>
    </div>
    <span class="badge-status">VetSmart</span>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-4" id="consultasTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="tab-consultas" data-bs-toggle="tab" data-bs-target="#content-consultas" type="button" role="tab">
        <i class="bi bi-journal-medical me-1"></i> Consultas
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tab-pacientes" data-bs-toggle="tab" data-bs-target="#content-pacientes" type="button" role="tab">
        <i class="bi bi-list-check me-1"></i> Pacientes
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tab-citas" data-bs-toggle="tab" data-bs-target="#content-citas" type="button" role="tab">
        <i class="bi bi-calendar-check me-1"></i> Citas
      </button>
    </li>
  </ul>

  <!-- Contenido Tabs -->
  <div class="tab-content" id="consultasTabContent">
    <!-- TAB 1: CONSULTAS -->
    <div class="tab-pane fade show active" id="content-consultas" role="tabpanel">
      <div class="card card-list">
        <div class="card-body p-0">
          <?php if (empty($consultas)): ?>
            <div class="empty-state text-center text-muted">
              <i class="bi bi-inbox"></i><br>
              No tienes consultas registradas.
            </div>
          <?php else: ?>
            <div class="card p-3 border-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
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
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <!-- TAB 2: CITAS -->
    <div class="tab-pane fade" id="content-citas" role="tabpanel">
      <div class="card card-list">
        <div class="card-body p-0">
          <?php $citas = $citas ?? []; ?>
          <?php if (empty($citas)): ?>
            <div class="empty-state text-center text-muted">
              <i class="bi bi-inbox"></i><br>
              No tienes citas registradas.
            </div>
          <?php else: ?>
            <div class="card p-3 border-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th style="width:18%">Fecha</th>
                      <th style="width:28%">Mascota</th>
                      <th style="width:24%">Servicio</th>
                      <th style="width:12%">Estado</th>
                      <th class="text-center" style="width:18%">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($citas as $c): ?>
                      <tr>
                        <td class="small-muted"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($c['fecha'] ?? '')) ) ?></td>
                        <td>
                          <div class="fw-semibold"><?= htmlspecialchars($c['nombre_mascota'] ?? '-') ?></div>
                          <div class="small text-muted">ID: <?= htmlspecialchars($c['mascota_id'] ?? '-') ?></div>
                        </td>
                        <td><?= htmlspecialchars($c['servicio_id'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($c['estado'] ?? '-') ?></td>
                        <td class="text-center">
                          <div class="d-flex justify-content-center gap-1">
                            <a href="/vetsmart/veterinario/consultas/crear/<?= htmlspecialchars($c['mascota_id'] ?? '') ?>?cita_id=<?= htmlspecialchars($c['id']) ?>"
                              class="btn btn-sm btn-outline-primary btn-action" title="Crear consulta desde esta cita">
                              <i class="bi bi-plus-lg"></i>
                            </a>
                            <a href="/vetsmart/veterinario/mascotas/<?= htmlspecialchars($c['mascota_id'] ?? '') ?>/historial" class="btn btn-sm btn-outline-info btn-action" title="Ver historial de la mascota">
                              <i class="bi bi-eye"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- TAB 2: PACIENTES -->
    <div class="tab-pane fade" id="content-pacientes" role="tabpanel">
      <div class="card card-list">
        <div class="card-body p-0">
          <?php if (empty($mascotas)): ?>
            <div class="empty-state text-center text-muted">
              <i class="bi bi-inbox"></i><br>
              No hay pacientes registrados.
            </div>
          <?php else: ?>
            <div class="card p-3 border-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th style="width:15%">Nombre</th>
                      <th style="width:12%">Especie</th>
                      <th style="width:12%">Raza</th>
                      <th style="width:8%">Edad</th>
                      <th style="width:20%">Dueño</th>
                      <th style="width:13%">Teléfono</th>
                      <th class="text-center" style="width:20%">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($mascotas as $m): ?>
                      <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($m['nombre']) ?></td>
                        <td><?= htmlspecialchars($m['especie'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($m['raza'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($m['edad'] ?? '-') ?> años</td>
                        <td><?= htmlspecialchars(($m['nombre_dueno'] ?? '') . ' ' . ($m['apellido_dueno'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($m['telefono_dueno'] ?? '-') ?></td>
                        <td class="text-center">
                          <div class="d-flex justify-content-center gap-1">
                            <a href="/vetsmart/veterinario/mascotas/<?= $m['id'] ?>/historial" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Historial">
                              <i class="bi bi-file-text"></i>
                            </a>
                            <a href="/vetsmart/veterinario/mascotas/<?= $m['id'] ?>/agendar" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Agendar Cita">
                              <i class="bi bi-calendar-event"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
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

  modalEl.addEventListener('hidden.bs.modal', () => content.innerHTML = '');
  const tipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tipList.forEach(t => new bootstrap.Tooltip(t));
});
</script>
</body>
</html>
