<?php
// app/views/veterinario/pacientes/index.php (versión visual mejorada)
$mascotas = $mascotas ?? [];
$q = $q ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pacientes - VetSmart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background: #f4f6fb; font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
    .page-title { font-weight:700; letter-spacing:-0.2px; }
    .card-patient { border:0; border-radius:12px; box-shadow: 0 8px 24px rgba(20,24,40,0.06); }
    .patient-avatar { width:56px; height:56px; border-radius:10px; background:#eef2ff; display:inline-flex; align-items:center; justify-content:center; font-weight:700; color:#5b21b6; }
    .badge-species { background: linear-gradient(90deg,#eef2ff,#f8fafc); color:#0f172a; border-radius:8px; padding:4px 8px; font-size:0.8rem; }
    .table thead th { border-bottom: 0; }
    .empty-state { padding:48px; }
    @media (max-width: 991px) {
      .d-desktop-only { display:none !important; }
    }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h2 class="page-title mb-1">🐾 Pacientes</h2>
      <small class="text-muted">Lista de mascotas registradas en la clínica</small>
    </div>

    <div class="d-flex gap-2">
      <form method="GET" action="/vetsmart/veterinario/pacientes" class="d-flex align-items-center">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
          <input id="searchInput" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" type="search" class="form-control" placeholder="Buscar por nombre, especie, raza o dueño...">
          <button id="resetBtn" type="button" class="btn btn-outline-secondary" title="Limpiar búsqueda"><i class="bi bi-x-lg"></i></button>
        </div>
      </form>

      <a href="/vetsmart/veterinario/mis-citas" class="btn btn-primary d-flex align-items-center">
        <i class="bi bi-calendar-event me-2"></i> Ir a mi calendario
      </a>
    </div>
  </div>

  <div class="card card-patient">
    <div class="card-body p-3 p-lg-4">
      <?php if (empty($mascotas)): ?>
        <div class="empty-state text-center text-muted">
          <div class="mb-3"><i class="bi bi-emoji-frown" style="font-size:36px"></i></div>
          <h5>No hay pacientes aún</h5>
          <p class="mb-0">Aún no se han registrado mascotas. Usa el botón "Nueva mascota" para añadir un paciente.</p>
        </div>
      <?php else: ?>
        <!-- Desktop: tabla; Mobile: lista cards -->
        <div class="d-none d-lg-block">
          <div class="table-responsive">
            <table id="tablaMascotas" class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Paciente</th>
                  <th>Tipo / Raza</th>
                  <th>Edad</th>
                  <th>Dueño</th>
                  <th>Teléfono</th>
                  <th class="text-end">Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($mascotas as $m): ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <div class="patient-avatar"><?= htmlspecialchars(substr($m['nombre'] ?? '-',0,1)) ?></div>
                        <div>
                          <div class="fw-semibold"><?= htmlspecialchars($m['nombre'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                          <div class="text-muted small d-desktop-only"><?= htmlspecialchars($m['microchip'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="badge-species me-2"><?= htmlspecialchars($m['especie'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                      <div class="text-muted small mt-1"><?= htmlspecialchars($m['raza'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                    </td>
                    <td><?= ($m['edad'] === null || $m['edad'] === '') ? '-' : (int)$m['edad'] . ' años' ?></td>
                    <td><?= htmlspecialchars(trim(($m['nombre_dueno'] ?? '') . ' ' . ($m['apellido_dueno'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['telefono_dueno'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                      <a class="btn btn-sm btn-outline-primary" href="/vetsmart/veterinario/mascotas/<?= htmlspecialchars($m['id'], ENT_QUOTES, 'UTF-8') ?>/historial">
                        <i class="bi bi-journal-text me-1"></i> Ver historial
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Mobile list -->
        <div class="d-lg-none">
          <div class="row g-3">
            <?php foreach ($mascotas as $m): ?>
              <div class="col-12">
                <div class="card">
                  <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                      <div class="patient-avatar"><?= htmlspecialchars(substr($m['nombre'] ?? '-',0,1)) ?></div>
                      <div>
                        <div class="fw-semibold"><?= htmlspecialchars($m['nombre'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($m['especie'] ?? '-', ENT_QUOTES, 'UTF-8') ?> • <?= htmlspecialchars($m['raza'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                      </div>
                    </div>

                    <div class="text-end">
                      <a class="btn btn-sm btn-outline-primary" href="/vetsmart/veterinario/mascotas/<?= htmlspecialchars($m['id'], ENT_QUOTES, 'UTF-8') ?>/historial">
                        <i class="bi bi-journal-text"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
  const searchInput = document.getElementById('searchInput');
  const resetBtn = document.getElementById('resetBtn');
  const tabla = document.getElementById('tablaMascotas');

  // cliente-side filter para la tabla (desktop) y para cards (mobile)
  function filterRows(q) {
    const query = q.trim().toLowerCase();
    if (!tabla) return;
    const rows = tabla.tBodies[0].rows;
    for (let r of rows) {
      const text = (r.textContent || r.innerText || '').toLowerCase();
      r.style.display = query === '' ? '' : (text.indexOf(query) !== -1 ? '' : 'none');
    }
  }

  function filterCards(q) {
    const cards = document.querySelectorAll('.d-lg-none .card');
    const query = q.trim().toLowerCase();
    cards.forEach(card => {
      const text = (card.textContent || card.innerText || '').toLowerCase();
      card.parentElement.style.display = query === '' ? '' : (text.indexOf(query) !== -1 ? '' : 'none');
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const q = this.value;
      filterRows(q);
      filterCards(q);
    });
  }

  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      if (searchInput) searchInput.value = '';
      filterRows('');
      filterCards('');
      const url = window.location.pathname;
      window.history.replaceState({}, '', url);
    });
  }
})();
</script>
</body>
</html>
