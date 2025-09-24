<?php
// app/views/veterinario/citas/index.php
// Variables esperadas desde el controller: $clientes (array), $servicios (array)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf_token'];
?>
<!------------------ META CSRF ------------------>
<meta name="csrf-token" content="<?=htmlspecialchars($csrf)?>">

<?php if (!empty($_SESSION['flash_success'])): ?>
  <div class="alert alert-success" role="alert">
    <?= htmlspecialchars($_SESSION['flash_success']) ?>
  </div>
  <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="alert alert-danger" role="alert">
    <?= htmlspecialchars($_SESSION['flash_error']) ?>
  </div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<style>
/* Reglas pequeñas para asegurar que el calendario se vea bien */
#calendar { width: 100%; max-width: 900px; margin: 0 auto; }
.cita-modal-backdrop.hidden { display: none; }
.cita-modal-backdrop.flex { display: flex; align-items: center; justify-content: center; }
.cita-modal { width: 720px; max-width: calc(100% - 32px); background:#fff; border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,.12); padding:18px; }
</style>

<div class="p-6">
  <h1 class="text-2xl font-bold mb-4">Calendario de Citas</h1>

  <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-2" style="position:fixed; right:20px; top:20px; z-index:9999;"></div>

  <div class="bg-white rounded-lg shadow p-4">
    <div id="calendar" class="w-full"></div>
  </div>
</div>

<?php
// INCLUIMOS EL MODAL DIRECTAMENTE AQUÍ (evita incluir partials con debug)
?>
<div id="citaModal" class="cita-modal-backdrop hidden" style="position:fixed; inset:0; z-index:1050; background: rgba(0,0,0,0.35);">
  <div class="cita-modal" role="dialog" aria-modal="true">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
      <h4 id="modalTitle" style="margin:0">Crear cita</h4>
      <button id="closeModal" class="btn btn-link" aria-label="Cerrar">✕</button>
    </div>

    <form id="citaForm">
      <input type="hidden" id="citaId" name="id" value="">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div>
          <label>Cliente</label>
          <select id="clienteSelect" name="cliente_id" class="form-control">
            <option value="">-- Seleccione cliente --</option>
            <?php foreach (($clientes ?? []) as $cl): ?>
              <option value="<?=htmlspecialchars($cl['id'])?>"><?=htmlspecialchars(trim(($cl['nombre'] ?? '') . ' ' . ($cl['apellido'] ?? ''))) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label>Mascota</label>
          <select id="mascotaSelect" name="mascota_id" class="form-control">
            <option value="">-- Seleccione mascota --</option>
          </select>
        </div>

        <div>
          <label>Servicio</label>
          <select id="servicioSelect" name="servicio_id" class="form-control">
            <option value="">-- Seleccione servicio --</option>
            <?php foreach (($servicios ?? []) as $s): ?>
              <option value="<?=htmlspecialchars($s['id'])?>"><?=htmlspecialchars($s['nombre'] ?? $s['titulo'] ?? '-')?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label>Estado</label>
          <select id="estadoSelect" name="estado" class="form-control">
            <option value="programada">Programada</option>
            <option value="confirmada">Confirmada</option>
            <option value="cancelada">Cancelada</option>
          </select>
        </div>

        <div>
          <label>Fecha</label>
          <input id="fechaInput" name="fecha_date" type="date" class="form-control">
        </div>

        <div>
          <label>Hora</label>
          <input id="horaInput" name="hora_time" type="time" class="form-control">
        </div>

        <div style="grid-column:1 / -1;">
          <label>Notas</label>
          <textarea id="notasInput" name="notas" rows="4" class="form-control"></textarea>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:12px;">
        <button type="button" id="deleteBtn" class="btn btn-danger hidden">Eliminar</button>
        <button type="button" id="cancelBtn" class="btn btn-secondary">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- FullCalendar CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
(function(){
  const basePath = '/vetsmart';
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function toast(msg, type = 'info', duration = 3500) {
    const container = document.getElementById('toast-container');
    const el = document.createElement('div');
    el.className = 'max-w-xs p-3 rounded shadow-md text-white';
    el.style.opacity = '0';
    el.style.transition = 'opacity .18s';
    el.style.marginBottom = '8px';
    el.style.padding = '8px 12px';
    el.style.borderRadius = '6px';
    el.style.color = '#fff';
    if (type === 'success') el.style.background = '#16a34a';
    else if (type === 'error') el.style.background = '#dc2626';
    else el.style.background = '#0ea5e9';
    el.textContent = msg;
    container.appendChild(el);
    requestAnimationFrame(()=> el.style.opacity = '1');
    setTimeout(()=> {
      el.style.opacity = '0';
      setTimeout(()=> el.remove(), 220);
    }, duration);
  }

  // envia cookies; pide JSON
  function fetchWithCsrf(url, opts = {}) {
    opts = Object.assign({
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json'
      }
    }, opts);

    opts.headers = Object.assign({}, opts.headers || {}, {
      'X-CSRF-TOKEN': CSRF_TOKEN
    });

    return fetch(url, opts);
  }

  /* DOM */
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
  const closeModalBtn = document.getElementById('closeModal');
  const cancelBtn = document.getElementById('cancelBtn');
  const citaForm = document.getElementById('citaForm');
  const citaModal = document.getElementById('citaModal');

  function openModalCentered() {
    citaModal.classList.remove('hidden');
    citaModal.classList.add('flex');
    document.body.style.overflow = 'hidden';
  }
  function closeModalCentered() {
    citaModal.classList.add('hidden');
    citaModal.classList.remove('flex');
    document.body.style.overflow = '';
    resetForm();
  }

  closeModalBtn.addEventListener('click', closeModalCentered);
  cancelBtn.addEventListener('click', closeModalCentered);

  clienteSelect.addEventListener('change', function() {
    loadMascotasByCliente(this.value);
  });

  function loadMascotasByCliente(clienteId, selectedMascota = null) {
    mascotaSelect.innerHTML = '<option value="">Cargando...</option>';
    if (!clienteId) {
      mascotaSelect.innerHTML = '<option value="">-- Seleccione mascota --</option>';
      return;
    }
    fetch(`${basePath}/api/clientes/${clienteId}/mascotas`, { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        mascotaSelect.innerHTML = '<option value="">-- Seleccione mascota --</option>';
        data.forEach(m => {
          const opt = document.createElement('option');
          opt.value = m.id;
          opt.textContent = m.nombre;
          if (selectedMascota && String(selectedMascota) === String(m.id)) opt.selected = true;
          mascotaSelect.appendChild(opt);
        });
      })
      .catch(err => {
        console.error('Error cargando mascotas', err);
        mascotaSelect.innerHTML = '<option value="">-- Error cargando mascotas --</option>';
        toast('Error cargando mascotas', 'error');
      });
  }

  /* FullCalendar init */
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    selectable: true,
    height: 700,
    events: function(fetchInfo, successCallback, failureCallback) {
      const url = `${basePath}/veterinario/citas/listar?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`;
      fetch(url, { credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => successCallback(data))
        .catch(err => {
          console.error('Error cargando citas', err);
          failureCallback(err);
          toast('Error cargando citas.', 'error');
        });
    },
    dateClick: function(info) {
      openCreateModal(info.dateStr);
    },
    eventClick: function(info) {
      openEditModal(info.event);
    },
    eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false }
  });

  calendar.render();

  function openCreateModal(dateStr) {
    modalTitle.textContent = 'Crear cita';
    citaIdInput.value = '';
    deleteBtn.classList.add('hidden');
    fechaInput.value = (new Date(dateStr)).toISOString().slice(0,10);
    horaInput.value = '09:00';
    clienteSelect.value = '';
    mascotaSelect.innerHTML = '<option value="">-- Seleccione mascota --</option>';
    servicioSelect.value = '';
    estadoSelect.value = 'programada';
    notasInput.value = '';
    setTimeout(openModalCentered, 60);
  }

  function openEditModal(event) {
    modalTitle.textContent = 'Editar cita';
    deleteBtn.classList.remove('hidden');
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
      setTimeout(openModalCentered, 160);
    } else if (props.mascota_id) {
      mascotaSelect.innerHTML = `<option value="${props.mascota_id}" selected>${props.nombre_mascota || 'Mascota'}</option>`;
      setTimeout(openModalCentered, 80);
    } else {
      setTimeout(openModalCentered, 80);
    }
  }

  function resetForm() {
    citaForm.reset();
    citaIdInput.value = '';
    mascotaSelect.innerHTML = '<option value="">-- Seleccione mascota --</option>';
    deleteBtn.classList.add('hidden');
  }

  /* Submit: create / update */
  citaForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const id = citaIdInput.value || null;
    const cliente_id = clienteSelect.value || '';
    const mascota_id = mascotaSelect.value || '';
    const servicio_id = servicioSelect.value || '';
    const fecha_date = fechaInput.value;
    const hora_time = horaInput.value;
    const notas = notasInput.value;
    const estado = estadoSelect.value;

    if (!mascota_id || !servicio_id || !fecha_date || !hora_time) {
      toast('Por favor completa mascota, servicio, fecha y hora.', 'error');
      return;
    }

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
    .then(data => {
      if (data.success) {
        toast('Cita guardada correctamente.', 'success');
        calendar.refetchEvents();
        closeModalCentered();
      } else {
        toast(data.message || 'Error al guardar.', 'error');
      }
    })
    .catch(err => {
      console.error('Error guardando cita', err);
      toast('Error interno al guardar cita.', 'error');
    });
  });

  /* Delete: ahora con credentials y manejo robusto de respuesta */
  deleteBtn.addEventListener('click', async function () {
    if (!confirm('¿Deseas eliminar esta cita?')) return;
    const id = citaIdInput.value;
    if (!id) return toast('ID de cita no encontrado.', 'error');

    try {
      const resp = await fetchWithCsrf(`${basePath}/veterinario/citas/${id}/eliminar`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({ id }).toString()
      });

      const text = await resp.text();
      console.log('[DELETE] status', resp.status, 'body:', text);

      // intentar parsear JSON
      try {
        const data = JSON.parse(text);
        if (data.success) {
          toast('Cita eliminada', 'success');
          calendar.refetchEvents();
          closeModalCentered();
        } else {
          toast(data.message || 'No se pudo eliminar', 'error');
        }
      } catch (err) {
        console.error('Respuesta del servidor no es JSON al eliminar:', err);
        toast('Error interno al eliminar (ver consola).', 'error', 6000);
      }
    } catch (err) {
      console.error('Error fetch eliminar:', err);
      toast('Error interno al eliminar', 'error');
    }
  });

})();
</script>
