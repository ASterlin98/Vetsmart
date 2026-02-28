<div class="appointment-create-container">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="page-title mb-2">
        <i class="fas fa-calendar-plus me-2 text-success"></i>Nueva Cita
      </h1>
      <p class="text-muted mb-0">Programe una nueva cita para un cliente</p>
    </div>
  </div>

  <!-- Main Form -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
      <?php endif; ?>
<<<<<<< HEAD
      <form action="<?= BASE ?>/recepcionista/citas/store" method="POST" class="row g-4">
=======
      <form action="<?= BASE ?>/citas/store" method="POST" class="row g-4">
>>>>>>> 551a971277bd5d0b94296071273044a14c300280
        <?= CSRF::inputField(); ?>
        
        <!-- Client & Pet Section -->
        <div class="col-12">
          <h5 class="section-title mb-3">
            <i class="fas fa-user-friends me-2 text-primary"></i>Cliente y Mascota
          </h5>
        </div>

        <div class="col-md-6">
          <label for="cliente_id" class="form-label fw-medium">
            Cliente <span class="text-danger">*</span>
          </label>
          <select name="cliente_id" id="cliente_id" class="form-select" required>
            <option value="">Seleccione un cliente...</option>
            <?php foreach ($clientes as $c): ?>
              <option value="<?= $c['id'] ?>" data-foto="<?= htmlspecialchars($c['foto'] ?? '') ?>">
                <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div id="cliente-foto-preview" class="mt-2 text-center" style="display:none;">
             <img src="" alt="Foto Cliente" class="rounded-circle border shadow-sm" style="width:80px;height:80px;object-fit:cover;">
          </div>
        </div>

        <div class="col-md-6">
          <label for="mascota_id" class="form-label fw-medium">
            Mascota <span class="text-danger">*</span>
          </label>
          <select name="mascota_id" id="mascota_id" class="form-select" required disabled>
            <option value="">Seleccione un cliente primero</option>
          </select>
          <div class="form-text">Seleccione un cliente para cargar sus mascotas</div>
        </div>

        <!-- Appointment Details -->
        <div class="col-12">
          <h5 class="section-title mb-3">
            <i class="fas fa-calendar-alt me-2 text-info"></i>Detalles de la Cita
          </h5>
        </div>

        <div class="col-md-6">
          <label for="empleado_id" class="form-label fw-medium">
            Empleado <span class="text-danger">*</span>
          </label>
          <select name="empleado_id" id="empleado_id" class="form-select" required>
            <option value="">Seleccione un empleado...</option>
            <?php foreach ($empleados as $e): ?>
              <option value="<?= $e['id'] ?>" data-role="<?= htmlspecialchars(strtolower((string)($e['rol'] ?? ''))) ?>">
                <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label for="servicio_id" class="form-label fw-medium">
            Servicio <span class="text-danger">*</span>
          </label>
          <select name="servicio_id" id="servicio_id" class="form-select" required>
            <option value="">Seleccione un servicio...</option>
            <?php foreach ($servicios as $s): ?>
              <option value="<?= $s['id'] ?>" data-name="<?= htmlspecialchars(strtolower((string)$s['nombre'])) ?>"><?= htmlspecialchars($s['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Enhanced Date & Time Picker -->
        <div class="col-12">
          <div class="datetime-section">
            <label class="form-label fw-medium mb-3">
              Fecha y Hora de la Cita <span class="text-danger">*</span>
            </label>
            
            <div class="datetime-selector">
              <!-- Enhanced Calendar -->
              <div class="calendar-section mb-4">
                <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
                  <h6 class="mb-0 text-muted">Seleccione la fecha</h6>
                  <div class="selected-date-preview">
                    <span class="badge bg-primary" id="selectedDatePreview">Hoy</span>
                  </div>
                </div>

                <div class="calendar-container">
                  <!-- Calendar Navigation -->
                  <div class="calendar-nav d-flex justify-content-between align-items-center mb-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="prevMonth">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <h6 class="calendar-month mb-0 fw-bold text-dark" id="currentMonth"></h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="nextMonth">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>

                  <!-- Calendar Grid -->
                  <div class="calendar-grid">
                    <div class="calendar-weekdays">
                      <div class="weekday">Do</div>
                      <div class="weekday">Lu</div>
                      <div class="weekday">Ma</div>
                      <div class="weekday">Mi</div>
                      <div class="weekday">Ju</div>
                      <div class="weekday">Vi</div>
                      <div class="weekday">Sá</div>
                    </div>
                    <div class="calendar-days" id="calendarDays"></div>
                  </div>

                  <!-- Calendar Actions -->
                  <div class="calendar-actions mt-3 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger flex-fill" id="clearDate">
                      <i class="fas fa-times me-1"></i>Borrar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary flex-fill" id="todayDate">
                      <i class="fas fa-calendar-day me-1"></i>Hoy
                    </button>
                  </div>
                </div>
              </div>

              <!-- Time Selection (Unchanged) -->
              <div class="time-selection">
                <div class="time-header mb-3">
                  <h6 class="mb-0 text-muted">Seleccione la hora</h6>
                </div>
                
                <!-- Quick Time Buttons -->
                <div class="quick-times mb-3">
                  <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary btn-time" data-time="09:00">
                      9:00 AM
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-time" data-time="10:00">
                      10:00 AM
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-time" data-time="11:00">
                      11:00 AM
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-time" data-time="14:00">
                      2:00 PM
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-time" data-time="15:00">
                      3:00 PM
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-time" data-time="16:00">
                      4:00 PM
                    </button>
                  </div>
                </div>

                <!-- Custom Time Input -->
                <div class="custom-time">
                  <label class="form-label small text-muted mb-2">O ingrese una hora específica</label>
                  <div class="input-group">
                    <input type="time" id="fecha_time" class="form-control time-input" 
                           min="08:00" max="18:00" step="900" value="09:00">
                    <span class="input-group-text">hrs</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Hidden field for form submission -->
            <input type="hidden" name="fecha" id="fecha_hidden" required>

            <!-- Schedule Info -->
            <div class="schedule-info mt-3 p-3 bg-light rounded">
              <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-info me-2"></i>
                <small class="text-muted">
                  <strong>Horario de atención:</strong> Lunes a Viernes 8:00 - 18:00 • Sábados 9:00 - 14:00
                </small>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <label for="notas" class="form-label fw-medium">Notas Adicionales</label>
          <textarea name="notas" id="notas" rows="3" class="form-control" 
                    placeholder="Ingrese cualquier observación o nota adicional para la cita..."></textarea>
        </div>

        <!-- Form Actions -->
        <div class="col-12">
          <div class="border-top pt-4">
            <div class="d-flex gap-2 justify-content-end align-items-center form-actions">
              <a href="<?= BASE ?>/recepcionista/agenda" class="btn btn-outline-secondary btn-cancel" role="button" aria-label="Cancelar y volver a la agenda">
                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                <span class="d-none d-sm-inline">Cancelar</span>
              </a>

              <button type="submit" class="btn btn-primary btn-save px-4" aria-label="Guardar cita" data-original-text="<i class='fas fa-save me-1'></i>Guardar Cita">
                <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                <i class="fas fa-save me-1" aria-hidden="true"></i>
                <span class="btn-text">Guardar Cita</span>
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.appointment-create-container {
  max-width: 100%;
}

.page-title {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.6rem;
}

.section-title {
  color: #495057;
  font-weight: 600;
  font-size: 1.1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e9ecef;
}

/* Enhanced Calendar Styles */
.datetime-section {
  background: #fafafa;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e9ecef;
}

.calendar-container {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e9ecef;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.calendar-nav {
  padding: 0.5rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.calendar-month {
  color: #2c3e50;
  font-size: 1.1rem;
}

.calendar-grid {
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.calendar-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  background: #198754;
  color: white;
  font-weight: 600;
  font-size: 0.85rem;
}

.weekday {
  padding: 0.75rem 0.5rem;
  text-align: center;
  border-right: 1px solid rgba(255,255,255,0.1);
}

.weekday:last-child {
  border-right: none;
}

.calendar-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 1px;
  background: #e9ecef;
}

.calendar-day {
  background: white;
  padding: 0.75rem 0.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  min-height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
}

.calendar-day:hover {
  background: #e3f2fd;
  transform: scale(1.05);
}

.calendar-day.other-month {
  color: #adb5bd;
  background: #f8f9fa;
}

.calendar-day.today {
  background: #fff3cd;
  color: #856404;
  font-weight: 600;
}

.calendar-day.selected {
  background: #198754;
  color: white;
  font-weight: 600;
  transform: scale(1.05);
}

.calendar-day.disabled {
  color: #dee2e6;
  background: #f8f9fa;
  cursor: not-allowed;
}

.calendar-day.disabled:hover {
  background: #f8f9fa;
  transform: none;
}

.calendar-actions {
  border-top: 1px solid #e9ecef;
  padding-top: 1rem;
}

/* Time Selection Styles */
.time-selection {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  border: 1px solid #e9ecef;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.quick-times {
  border-bottom: 1px solid #f1f3f4;
  padding-bottom: 1rem;
}

.btn-time {
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
  border: 1px solid #dee2e6;
  transition: all 0.2s ease;
}

.btn-time:hover {
  background-color: #198754;
  border-color: #198754;
  color: white;
  transform: translateY(-1px);
}

.btn-time.active {
  background-color: #198754;
  border-color: #198754;
  color: white;
}

.custom-time {
  margin-top: 1rem;
}

.time-input {
  border-radius: 6px;
}

.schedule-info {
  border-left: 3px solid #0dcaf0;
}

.selected-date-preview .badge {
  font-size: 0.8rem;
  padding: 0.5rem 0.75rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.4rem;
  }
  
  .datetime-section {
    padding: 1rem;
  }
  
  .calendar-container,
  .time-selection {
    padding: 1rem;
  }
  
  .calendar-day {
    min-height: 40px;
    padding: 0.5rem 0.25rem;
    font-size: 0.8rem;
  }
  
  .quick-times .d-flex {
    gap: 0.5rem;
  }
  
  .btn-time {
    flex: 1;
    min-width: calc(50% - 0.5rem);
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
  }
}

@media (max-width: 576px) {
  .appointment-create-container {
    padding: 0.5rem;
  }
  
  .card-body {
    padding: 1rem !important;
  }
  
  .datetime-section {
    padding: 1rem;
  }
  
  .calendar-day {
    min-height: 35px;
    font-size: 0.75rem;
  }
  
  .btn-time {
    min-width: 100%;
  }
}

/* Buttons: responsive stacking and disabled state */
.form-actions {
  gap: .5rem;
}
@media (max-width: 576px) {
  .form-actions {
    width: 100%;
    display: flex !important;
    flex-direction: column-reverse;
    align-items: stretch;
  }
  .form-actions .btn {
    width: 100%;
  }
}
.btn-save[disabled],
.btn-save.disabled {
  opacity: 0.75;
}
.btn-cancel.disabled {
  opacity: 0.6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Calendar elements
  const calendarDays = document.getElementById('calendarDays');
  const currentMonth = document.getElementById('currentMonth');
  const prevMonthBtn = document.getElementById('prevMonth');
  const nextMonthBtn = document.getElementById('nextMonth');
  const clearDateBtn = document.getElementById('clearDate');
  const todayDateBtn = document.getElementById('todayDate');
  const selectedDatePreview = document.getElementById('selectedDatePreview');
  
  // Time elements
  const fechaTime = document.getElementById('fecha_time');
  const fechaHidden = document.getElementById('fecha_hidden');
  const timeButtons = document.querySelectorAll('.btn-time');

  let currentDate = new Date();
  let selectedDate = new Date();
  let currentViewDate = new Date();

  // Initialize calendar
  function initCalendar() {
    renderCalendar();
    updateSelectedDatePreview();
    updateHiddenDateTime();
    
    // Set first time button as active by default
    if (timeButtons.length > 0) {
      timeButtons[0].classList.add('active');
      fechaTime.value = timeButtons[0].getAttribute('data-time');
      updateHiddenDateTime();
    }
  }

  // Render calendar grid
  function renderCalendar() {
    const year = currentViewDate.getFullYear();
    const month = currentViewDate.getMonth();
    
    // Update month header
    currentMonth.textContent = currentViewDate.toLocaleDateString('es-ES', { 
      month: 'long', 
      year: 'numeric' 
    }).toUpperCase();

    // Get first day of month and last day of month
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startingDay = firstDay.getDay(); // 0 = Sunday
    
    // Clear previous days
    calendarDays.innerHTML = '';

    // Add days from previous month
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    for (let i = startingDay - 1; i >= 0; i--) {
      const day = prevMonthLastDay - i;
      const date = new Date(year, month - 1, day);
      addCalendarDay(date, true);
    }

    // Add days from current month
    for (let day = 1; day <= lastDay.getDate(); day++) {
      const date = new Date(year, month, day);
      addCalendarDay(date, false);
    }

    // Add days from next month to complete grid
    const totalCells = 42; // 6 weeks * 7 days
    const currentCells = calendarDays.children.length;
    for (let day = 1; day <= totalCells - currentCells; day++) {
      const date = new Date(year, month + 1, day);
      addCalendarDay(date, true);
    }
  }

  // Add a day to the calendar
  function addCalendarDay(date, isOtherMonth) {
    const dayElement = document.createElement('button');
    dayElement.type = 'button';
    dayElement.className = 'calendar-day';
    
    if (isOtherMonth) {
      dayElement.classList.add('other-month');
    }
    
    // Check if today
    const today = new Date();
    if (date.toDateString() === today.toDateString()) {
      dayElement.classList.add('today');
    }
    
    // Check if selected
    if (date.toDateString() === selectedDate.toDateString()) {
      dayElement.classList.add('selected');
    }
    
    // Check if disabled (past dates)
    if (date < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
      dayElement.classList.add('disabled');
    } else {
      dayElement.addEventListener('click', () => selectDate(date));
    }
    
    dayElement.textContent = date.getDate();
    calendarDays.appendChild(dayElement);
  }

  // Select a date
  function selectDate(date) {
    selectedDate = date;
    renderCalendar();
    updateSelectedDatePreview();
    updateHiddenDateTime();
  }

  // Update selected date preview
  function updateSelectedDatePreview() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    let displayText = '';
    
    if (selectedDate.toDateString() === today.toDateString()) {
      displayText = 'Hoy';
    } else if (selectedDate.toDateString() === tomorrow.toDateString()) {
      displayText = 'Mañana';
    } else {
      displayText = selectedDate.toLocaleDateString('es-ES', { 
        weekday: 'short', 
        day: 'numeric', 
        month: 'short' 
      });
    }

    selectedDatePreview.textContent = displayText;
  }

  // Navigation handlers
  prevMonthBtn.addEventListener('click', () => {
    currentViewDate.setMonth(currentViewDate.getMonth() - 1);
    renderCalendar();
  });

  nextMonthBtn.addEventListener('click', () => {
    currentViewDate.setMonth(currentViewDate.getMonth() + 1);
    renderCalendar();
  });

  clearDateBtn.addEventListener('click', () => {
    selectedDate = new Date();
    renderCalendar();
    updateSelectedDatePreview();
    updateHiddenDateTime();
  });

  todayDateBtn.addEventListener('click', () => {
    currentViewDate = new Date();
    selectedDate = new Date();
    renderCalendar();
    updateSelectedDatePreview();
    updateHiddenDateTime();
  });

  // Time button handlers
  timeButtons.forEach(button => {
    button.addEventListener('click', function() {
      timeButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      fechaTime.value = this.getAttribute('data-time');
      updateHiddenDateTime();
    });
  });

  // Time input handler
  fechaTime.addEventListener('change', updateHiddenDateTime);

  // Update hidden datetime field
  function updateHiddenDateTime() {
    // Usar componentes locales para evitar problemas de timezone con toISOString()
    const year = selectedDate.getFullYear();
    const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
    const day = String(selectedDate.getDate()).padStart(2, '0');
    const dateString = `${year}-${month}-${day}`;
    
    const timeString = fechaTime.value;
    
    if (dateString && timeString) {
      const datetimeString = `${dateString}T${timeString}`;
      fechaHidden.value = datetimeString;
    }
  }

  // Client-Pet relationship handler (existing functionality)
  const clienteSelect = document.getElementById('cliente_id');
  const mascotaSelect = document.getElementById('mascota_id');

  clienteSelect.addEventListener('change', async () => {
    const clienteId = clienteSelect.value;
    
    // Mostrar foto si existe
    const selectedOption = clienteSelect.options[clienteSelect.selectedIndex];
    const foto = selectedOption.getAttribute('data-foto');
    const previewDiv = document.getElementById('cliente-foto-preview');
    const previewImg = previewDiv.querySelector('img');

    if (foto) {
        previewImg.src = BASE . '/public/assets/uploads/clientes/' + foto;
        previewDiv.style.display = 'block';
    } else {
        previewDiv.style.display = 'none';
        // Podríamos mostrar avatar por defecto si quisiéramos
    }

    mascotaSelect.innerHTML = '<option value="">Cargando mascotas...</option>';
    mascotaSelect.disabled = true;

    if (!clienteId) {
      mascotaSelect.innerHTML = '<option value="">Seleccione un cliente primero</option>';
      mascotaSelect.disabled = true;
      return;
    }

    try {
      const response = await fetch(`<?= BASE ?>/api/clientes/${clienteId}/mascotas`);
      
      if (!response.ok) throw new Error('Error al cargar mascotas');
      
      const mascotas = await response.json();
      mascotaSelect.innerHTML = '<option value="">Seleccione una mascota</option>';
      
      if (mascotas.length === 0) {
        mascotaSelect.innerHTML = '<option value="">El cliente no tiene mascotas registradas</option>';
      } else {
        mascotas.forEach(m => {
          const opt = document.createElement('option');
          opt.value = m.id;
          opt.textContent = m.nombre;
          mascotaSelect.appendChild(opt);
        });
        mascotaSelect.disabled = false;
      }
    } catch (err) {
      console.error('Error:', err);
      mascotaSelect.innerHTML = '<option value="">Error al cargar las mascotas</option>';
    }
  });

  // Form validation
  const form = document.querySelector('form');
  form.addEventListener('submit', function(e) {
    const requiredFields = form.querySelectorAll('select[required]');
    let isValid = true;

    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        field.classList.add('is-invalid');
        isValid = false;
      } else {
        field.classList.remove('is-invalid');
      }
    });

    // Validate datetime
    if (!fechaHidden.value) {
      isValid = false;
    }

    if (mascotaSelect.disabled || !mascotaSelect.value) {
      mascotaSelect.classList.add('is-invalid');
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
      const firstInvalid = form.querySelector('.is-invalid');
      if (firstInvalid) {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstInvalid.focus();
      }
    } else {
      // Mostrar spinner y deshabilitar botones
      const submitBtn = form.querySelector('.btn-save');
      if (submitBtn) {
        const spinner = submitBtn.querySelector('.spinner-border');
        const btnText = submitBtn.querySelector('.btn-text');
        
        // Mostrar estado de carga
        if (spinner) spinner.classList.remove('d-none');
        if (btnText) btnText.textContent = 'Guardando...';
        
        // Deshabilitar visualmente pero PERMITIR que el evento submit continúe
        submitBtn.classList.add('disabled');
        submitBtn.style.pointerEvents = 'none'; // Prevenir clics múltiples
        
        // NO deshabilitar el botón con .disabled = true aquí porque en algunos navegadores cancela el submit
      }
    }
  });

  // Initialize
  initCalendar();

  // Filtrar empleados por servicio (si es peluquería -> solo peluqueros)
  const servicioSelect = document.getElementById('servicio_id');
  const empleadoSelect = document.getElementById('empleado_id');
  const allEmpleadoOptions = Array.from(empleadoSelect.querySelectorAll('option'));

  function isGrooming(nombre) {
    const n = (nombre || '').toLowerCase();
    return n.includes('peluquer') || n.includes('bañ') || n.includes('ban') || n.includes('cort') || n.includes('spa');
  }

  function applyEmployeeFilter() {
    const opt = servicioSelect.options[servicioSelect.selectedIndex];
    const svcName = opt ? (opt.getAttribute('data-name') || opt.textContent) : '';
    const grooming = isGrooming(svcName);

    const current = empleadoSelect.value;
    const orig = allEmpleadoOptions.filter(o => o.value !== '');
    empleadoSelect.innerHTML = '';
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Seleccione un empleado...';
    empleadoSelect.appendChild(placeholder);

    orig.forEach(o => {
      const role = (o.getAttribute('data-role') || '').toLowerCase();
      if (!grooming || role === 'peluquero') {
        const c = o.cloneNode(true);
        empleadoSelect.appendChild(c);
      }
    });

    if (current) {
      const found = Array.from(empleadoSelect.options).some(x => x.value === current);
      empleadoSelect.value = found ? current : '';
    }
  }

  servicioSelect.addEventListener('change', applyEmployeeFilter);
  // inicial
  applyEmployeeFilter();
});
</script>
