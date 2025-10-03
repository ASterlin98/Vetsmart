<?php
// app/views/veterinario/citas/index.php (versión completa y lista para usar)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= htmlspecialchars($csrf, ENT_QUOTES | ENT_SUBSTITUTE) ?>">
  <title>Calendario de Citas</title>

  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

  <style>
    :root{
      --card-bg: #ffffff;
      --muted: #6c757d;
      --accent: #0d6efd;
      --success: #198754;
      --danger: #dc3545;
      --shadow: 0 10px 30px rgba(20,20,30,0.06);
    }

    body { background: linear-gradient(180deg,#f7f9fc 0%, #ffffff 100%); font-family: Inter, 'Segoe UI', system-ui, -apple-system, 'Helvetica Neue', Arial; }

    .page-header { gap: .75rem; }
    .page-title { font-weight: 700; letter-spacing: -0.2px; }

    .card-calendar { border: none; box-shadow: var(--shadow); border-radius: 12px; }

    .legend { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
    .legend .item { display:flex; gap:.5rem; align-items:center; font-size:.9rem; color:var(--muted); }
    .legend .dot { width:12px; height:12px; border-radius:50%; display:inline-block; }

    .fc .fc-event { border: 0; border-radius: 8px; padding:6px 8px; font-size:0.9rem; }

    @media (max-width: 867px) {
      #calendar { height: 500px !important; }
    }

    /* small tweak for modal z-index if fullcalendar popovers overlap */
    .modal { z-index: 1200; }
  </style>
</head>
<body>

<div class="container py-4">
  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success" role="alert">
      <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES | ENT_SUBSTITUTE) ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger" role="alert">
      <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES | ENT_SUBSTITUTE) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-3 page-header">
    <div>
      <h2 class="mb-0 page-title"><i class="bi bi-calendar-event me-2 text-primary"></i> Calendario de Citas</h2>
      <small class="text-muted">Gestiona las citas de tus clientes y sus mascotas</small>
    </div>

    <div class="d-flex gap-2 align-items-center">
      <div class="legend me-2">
        <div class="item"><span class="dot" style="background:var(--accent)"></span> Programada</div>
        <div class="item"><span class="dot" style="background:var(--success)"></span> Confirmada</div>
        <div class="item"><span class="dot" style="background:var(--danger)"></span> Cancelada</div>
      </div>

      <button class="btn btn-primary shadow-sm" id="btnCrearCita">
        <i class="bi bi-plus-circle me-1"></i> Nueva Cita
      </button>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div id="calendar" class="card-calendar"></div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="citaModalBootstrap" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Crear cita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="citaForm">
        <input type="hidden" id="citaId" name="id" value="">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="clienteSelect" class="form-label">Cliente</label>
              <select id="clienteSelect" name="cliente_id" class="form-select">
                <option value="">-- Seleccione cliente --</option>
                <?php foreach (($clientes ?? []) as $cl): ?>
                  <option value="<?= htmlspecialchars($cl['id']) ?>"><?= htmlspecialchars(trim(($cl['nombre'] ?? '') . ' ' . ($cl['apellido'] ?? ''))) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label for="mascotaSelect" class="form-label">Mascota</label>
              <select id="mascotaSelect" name="mascota_id" class="form-select">
                <option value="">-- Seleccione mascota --</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="servicioSelect" class="form-label">Servicio</label>
              <select id="servicioSelect" name="servicio_id" class="form-select">
                <option value="">-- Seleccione servicio --</option>
                <?php foreach (($servicios ?? []) as $s): ?>
                  <option value="<?= htmlspecialchars($s['id']) ?>"><?= htmlspecialchars($s['nombre'] ?? $s['titulo'] ?? '-') ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label for="estadoSelect" class="form-label">Estado</label>
              <select id="estadoSelect" name="estado" class="form-select">
                <option value="programada">Programada</option>
                <option value="confirmada">Confirmada</option>
                <option value="cancelada">Cancelada</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="fechaInput" class="form-label">Fecha</label>
              <input id="fechaInput" name="fecha_date" type="date" class="form-control">
            </div>
            <div class="col-md-6">
              <label for="horaInput" class="form-label">Hora</label>
              <input id="horaInput" name="hora_time" type="time" class="form-control" step="60">
            </div>
            <div class="col-12">
              <label for="notasInput" class="form-label">Notas</label>
              <textarea id="notasInput" name="notas" rows="3" class="form-control"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" id="deleteBtn" class="btn btn-outline-danger me-auto d-none"><i class="bi bi-trash"></i> Eliminar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS & FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
(function(){
  // Ajusta basePath si tu aplicación no está en /vetsmart
  const basePath = '/vetsmart';
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const VET_ID = <?= json_encode($_SESSION['user']['id'] ?? null) ?>;

  // utilidades
  function toastBootstrap(message, type = 'info', delay = 3500){
    const containerId = 'bs-toast-container';
    let container = document.getElementById(containerId);
    if (!container) {
      container = document.createElement('div');
      container.id = containerId;
      container.className = 'position-fixed top-0 end-0 p-3';
      container.style.zIndex = 1100;
      document.body.appendChild(container);
    }

    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-bg-' + (type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary') + ' border-0';
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>`;

    container.appendChild(toastEl);
    const bsToast = new bootstrap.Toast(toastEl, { delay });
    bsToast.show();
  }

  function fetchWithCsrf(url, opts = {}){
    opts = Object.assign({ credentials: 'same-origin', headers: { 'Accept': 'application/json' } }, opts);
    opts.headers = Object.assign({}, opts.headers || {}, { 'X-CSRF-TOKEN': CSRF_TOKEN });
    return fetch(url, opts);
  }

  // referencias DOM
  const calendarEl = document.getElementById('calendar');
  const clienteSelect = document.getElementById('clienteSelect');
  const mascotaSelect = document.getElementById('mascotaSelect');
  const servicioSelect = document.getElementById('servicioSelect');
  const fechaInput = document.getElementById('fechaInput');
  const horaInput = document.getElementById('horaInput');
  const notasInput = document.getElementById('notasInput');
  const citaIdInput = document.getElementById('citaId');
  const modalTitle = document.getElementById('modalTitle');
  const estadoSelect = document.getElementById('estadoSelect');
  const deleteBtn = document.getElementById('deleteBtn');
  const citaForm = document.getElementById('citaForm');
  const citaModalEl = document.getElementById('citaModalBootstrap');
  const bootstrapModal = new bootstrap.Modal(citaModalEl, { backdrop: 'static' });

  document.getElementById('btnCrearCita').addEventListener('click', ()=> openCreateModal(new Date().toISOString().slice(0,10)));
  clienteSelect.addEventListener('change', function(){ loadMascotasByCliente(this.value); });

  function loadMascotasByCliente(clienteId, selectedMascota = null){
    mascotaSelect.innerHTML = '<option value="">Cargando...</option>';
    if (!clienteId) { mascotaSelect.innerHTML = '<option value="">-- Seleccione mascota --</option>'; return; }
    fetch(`${basePath}/api/clientes/${encodeURIComponent(clienteId)}/mascotas`, { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        mascotaSelect.innerHTML = '<option value="">-- Seleccione mascota --</option>';
        data.forEach(m => {
          const opt = document.createElement('option'); opt.value = m.id; opt.textContent = m.nombre;
          if (selectedMascota && String(selectedMascota) === String(m.id)) opt.selected = true;
          mascotaSelect.appendChild(opt);
        });
      })
      .catch(err => { console.error('Error cargando mascotas', err); mascotaSelect.innerHTML = '<option value="">-- Error --</option>'; toastBootstrap('Error cargando mascotas', 'error'); });
  }

  // FullCalendar init
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
    selectable: true, height: 680,
    events: function(fetchInfo, successCallback, failureCallback) {
      const url = `${basePath}/veterinario/citas/listar?start=${encodeURIComponent(fetchInfo.startStr)}&end=${encodeURIComponent(fetchInfo.endStr)}`;
      fetch(url, { credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => successCallback(data))
        .catch(err => { console.error('Error cargando citas', err); failureCallback(err); toastBootstrap('Error cargando citas.', 'error'); });
    },
    dateClick: function(info){ openCreateModal(info.dateStr); },
    eventClick: function(info){ openEditModal(info.event); },
    eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
    eventDidMount: function(info){
      const estado = info.event.extendedProps.estado || 'programada';
      if (estado === 'confirmada') info.el.style.backgroundColor = getComputedStyle(document.documentElement).getPropertyValue('--success') || '#198754';
      if (estado === 'cancelada') info.el.style.backgroundColor = getComputedStyle(document.documentElement).getPropertyValue('--danger') || '#dc3545';
      const servicio = info.event.extendedProps.servicio_nombre || '';
      const mascota = info.event.extendedProps.nombre_mascota || '';
      info.el.setAttribute('title', `${info.event.title}\n${mascota} • ${servicio}`);
    }
  });

  calendar.render();

  function openCreateModal(dateStr){
    modalTitle.textContent = 'Crear cita';
    citaIdInput.value = '';
    deleteBtn.classList.add('d-none');
    fechaInput.value = (new Date(dateStr)).toISOString().slice(0,10);
    horaInput.value = '09:00';
    clienteSelect.value = '';
    mascotaSelect.innerHTML = '<option value="">-- Seleccione mascota --</option>';
    servicioSelect.value = '';
    estadoSelect.value = 'programada';
    notasInput.value = '';
    bootstrapModal.show();
  }

  function openEditModal(event){
    modalTitle.textContent = 'Editar cita';
    deleteBtn.classList.remove('d-none');
    const props = event.extendedProps || {};
    citaIdInput.value = event.id;
    notasInput.value = props.notas || '';
    estadoSelect.value = props.estado || 'programada';
    const start = new Date(event.start);
    fechaInput.value = start.toISOString().slice(0,10);
    horaInput.value = start.toTimeString().slice(0,5);
    if (props.servicio_id) servicioSelect.value = props.servicio_id;
    if (props.cliente_id) {
      clienteSelect.value = props.cliente_id;
      loadMascotasByCliente(props.cliente_id, props.mascota_id || null);
      setTimeout(() => bootstrapModal.show(), 160);
    } else if (props.mascota_id) {
      mascotaSelect.innerHTML = `<option value="${props.mascota_id}" selected>${props.nombre_mascota || 'Mascota'}</option>`;
      bootstrapModal.show();
    } else { bootstrapModal.show(); }
  }

  function resetForm(){ citaForm.reset(); citaIdInput.value = ''; mascotaSelect.innerHTML = '<option value="">-- Seleccione mascota --</option>'; deleteBtn.classList.add('d-none'); }

  citaForm.addEventListener('submit', function(e){
    e.preventDefault();
    const id = citaIdInput.value || null;
    const cliente_id = clienteSelect.value || '';
    const mascota_id = mascotaSelect.value || '';
    const servicio_id = servicioSelect.value || '';
    const fecha_date = fechaInput.value;
    const hora_time = horaInput.value;
    const notas = notasInput.value;
    const estado = estadoSelect.value;

    if (!mascota_id || !servicio_id || !fecha_date || !hora_time){
      toastBootstrap('Por favor completa mascota, servicio, fecha y hora.', 'error');
      return;
    }

    // Verificar disponibilidad con CSRF y enviando servicio_id y vet_id
    const paramsCheck = new URLSearchParams();
    if (VET_ID) paramsCheck.append('veterinario_id', VET_ID);
    paramsCheck.append('fecha', fecha_date);
    paramsCheck.append('hora', hora_time);
    if (servicio_id) paramsCheck.append('servicio_id', servicio_id);

    fetchWithCsrf(`${basePath}/api/disponibilidad-veterinario?${paramsCheck.toString()}`, {
      method: 'GET',
      headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
      if (!data || data.disponible !== true) {
        const why = (data && data.reason) ? (': ' + data.reason) : '.';
        toastBootstrap('❌ No puedes agendar en esta fecha/hora' + why, 'error');
        return;
      }

      // si está disponible, continuamos guardando
      const params = new URLSearchParams();
      if (cliente_id) params.append('cliente_id', cliente_id);
      params.append('mascota_id', mascota_id);
      params.append('servicio_id', servicio_id);
      params.append('fecha_date', fecha_date);
      params.append('hora_time', hora_time);
      params.append('notas', notas);
      params.append('estado', estado);

      const isCreate = !id;
      const url = isCreate ? `${basePath}/veterinario/citas/guardar` : `${basePath}/veterinario/citas/actualizar`;
      if (!isCreate) params.append('id', id);

      fetchWithCsrf(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
      })
      .then(r => r.json())
      .then(resp => {
        if (resp && resp.success){
          toastBootstrap('✅ Cita guardada correctamente.', 'success');
          calendar.refetchEvents();
          bootstrapModal.hide();
          resetForm();
        } else {
          toastBootstrap((resp && resp.message) ? resp.message : 'Error al guardar.', 'error');
        }
      })
      .catch(err => {
        console.error('Error guardando cita', err);
        toastBootstrap('Error interno al guardar cita.', 'error');
      });
    })
    .catch(err => {
      console.error('Error verificando disponibilidad', err);
      toastBootstrap('Error verificando disponibilidad del veterinario.', 'error');
    });
  });

})();
</script>
</body>
</html>
