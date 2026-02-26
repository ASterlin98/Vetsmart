<?php
// $mascotas, $servicios, $empleados
?>
<div class="agendar-cita-container">
  <!-- Header Section -->
  <div class="header-section mb-4">
    <div class="d-flex align-items-center">
      <div class="section-icon">
        <i class="fas fa-calendar-plus"></i>
      </div>
      <div>
        <h2 class="h4 mb-1">Agendar Nueva Cita</h2>
        <p class="text-muted mb-0">Programa una atención para tu mascota - Atención 7 días a la semana</p>
      </div>
    </div>
  </div>

  <div class="card agendar-card border-0 shadow-sm">
    <div class="card-body p-4">
      <form method="post" action="/vetsmart/cliente/citas/guardar" class="agendar-form" onsubmit="return validarReagendar(this)">
        <?= CSRF::inputField(); ?>
        
        <div class="row g-4">
          <!-- Mascota Selection -->
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">
                <i class="fas fa-paw me-2 text-primary"></i>
                Mascota
                <span class="text-danger">*</span>
              </label>
              <select name="mascota_id" class="form-select form-select-lg" required>
                <option value="">Selecciona una mascota...</option>
                <?php foreach ($mascotas as $m): ?>
                  <option value="<?= (int)$m['id'] ?>">
                    <?= htmlspecialchars($m['nombre']) ?> 
                    <?php if (isset($m['especie'])): ?>
                      <small class="text-muted">(<?= htmlspecialchars($m['especie']) ?>)</small>
                    <?php endif; ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Selecciona la mascota que necesita atención</div>
            </div>
          </div>

          <!-- Servicio Selection -->
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">
                <i class="fas fa-stethoscope me-2 text-primary"></i>
                Servicio
                <span class="text-danger">*</span>
              </label>
              <select name="servicio_id" class="form-select form-select-lg" required>
                <option value="">Selecciona un servicio...</option>
                <?php foreach ($servicios as $s): ?>
                  <option value="<?= (int)$s['id'] ?>">
                    <?= htmlspecialchars($s['nombre']) ?>
                    <?php if (isset($s['duracion'])): ?>
                      <small class="text-muted">(<?= htmlspecialchars($s['duracion']) ?> min)</small>
                    <?php endif; ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Elige el tipo de atención requerida</div>
            </div>
          </div>

          <!-- Profesional Selection -->
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">
                <i class="fas fa-user-md me-2 text-primary"></i>
                Profesional Preferido
              </label>
              <select name="empleado_id" class="form-select form-select-lg">
                <option value="">Selecciona un profesional...</option>
                <option value="">Cualquier profesional disponible</option>
                <?php foreach ($empleados as $e): ?>
                  <option value="<?= (int)$e['id'] ?>">
                    <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?>
                    <?php if (isset($e['especialidad'])): ?>
                      <small class="text-muted">(<?= htmlspecialchars($e['especialidad']) ?>)</small>
                    <?php endif; ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Opcional - Se asignará automáticamente si no eliges</div>
            </div>
          </div>

          <!-- Date and Time Selection with Mini Calendar -->
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">
                <i class="fas fa-clock me-2 text-primary"></i>
                Fecha y Hora de la Cita
                <span class="text-danger">*</span>
              </label>
              
              <div class="datetime-selection">
                <!-- Mini Calendar -->
                <div class="calendar-section mb-4">
                  <label class="sub-label">Selecciona el día</label>
                  <div class="mini-calendar-container">
                    <div class="calendar-header">
                      <button type="button" class="calendar-nav prev-month" onclick="changeMonth(-1)">
                        <i class="fas fa-chevron-left"></i>
                      </button>
                      <div class="calendar-title" id="calendarTitle"></div>
                      <button type="button" class="calendar-nav next-month" onclick="changeMonth(1)">
                        <i class="fas fa-chevron-right"></i>
                      </button>
                    </div>
                    <div class="calendar-weekdays">
                      <div>Lun</div>
                      <div>Mar</div>
                      <div>Mié</div>
                      <div>Jue</div>
                      <div>Vie</div>
                      <div class="weekend">Sáb</div>
                      <div class="weekend">Dom</div>
                    </div>
                    <div class="calendar-days" id="calendarDays"></div>
                  </div>
                </div>

                <!-- Time Selection -->
                <div class="time-section">
                  <label class="sub-label">Selecciona la hora</label>
                  <div class="time-slots-container">
                    <div class="time-slots-grid" id="timeSlots">
                      <!-- Los horarios se generarán dinámicamente -->
                    </div>
                    <div class="selected-date-info mt-3 p-3 bg-light rounded">
                      <i class="fas fa-calendar-day text-primary me-2"></i>
                      <strong>Día seleccionado:</strong>
                      <span id="selectedDateDisplay">Ningún día seleccionado</span>
                    </div>
                  </div>
                </div>
              </div>

              <input type="hidden" name="fecha" id="fechaCompleta" required />
              <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>
                Horario de atención: Lunes a Domingo 8:00 - 18:00
              </div>
            </div>
          </div>

          <!-- Additional Notes -->
          <div class="col-12">
            <div class="form-group">
              <label class="form-label">
                <i class="fas fa-notes-medical me-2 text-primary"></i>
                Notas Adicionales
              </label>
              <textarea name="notas" class="form-control form-control-lg" 
                        rows="3" 
                        placeholder="Describe los síntomas, comportamientos inusuales o cualquier información relevante para la cita..."></textarea>
              <div class="form-text">Información que ayudará al veterinario a prepararse para la consulta</div>
            </div>
          </div>

          <!-- Selected Appointment Summary -->
          <div class="col-12">
            <div class="appointment-summary card bg-light border-0" id="appointmentSummary" style="display: none;">
              <div class="card-body">
                <h6 class="card-title mb-3">
                  <i class="fas fa-calendar-check text-primary me-2"></i>
                  Resumen de la Cita
                </h6>
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-1"><strong>Fecha:</strong> <span id="summaryDate">-</span></p>
                    <p class="mb-1"><strong>Hora:</strong> <span id="summaryTime">-</span></p>
                  </div>
                  <div class="col-md-6">
                    <p class="mb-1"><strong>Duración estimada:</strong> <span id="summaryDuration">-</span></p>
                    <p class="mb-0">
                      <strong>Día:</strong> 
                      <span id="summaryDay">-</span>
                      <span id="weekendBadge" class="badge bg-warning ms-1" style="display: none;">Fin de semana</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="col-12">
            <div class="form-actions border-top pt-4 mt-2">
              <div class="d-flex gap-3 flex-wrap">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="fas fa-calendar-check me-2"></i>
                  Confirmar Cita
                </button>
                <a href="/vetsmart/cliente/citas" class="btn btn-outline-secondary btn-lg">
                  <i class="fas fa-arrow-left me-2"></i>
                  Volver al Listado
                </a>
                <button type="reset" class="btn btn-outline-primary btn-lg" onclick="resetDateTime()">
                  <i class="fas fa-undo me-2"></i>
                  Limpiar Selección
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Help Section -->
  <div class="row mt-4">
    <div class="col-md-4">
      <div class="help-card text-center p-4">
        <div class="help-icon mb-3">
          <i class="fas fa-clock text-primary"></i>
        </div>
        <h6>Horarios Disponibles</h6>
        <p class="text-muted small">Lunes a Domingo de 8:00 a 18:00 hrs</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="help-card text-center p-4">
        <div class="help-icon mb-3">
          <i class="fas fa-calendar-week text-primary"></i>
        </div>
        <h6>Atención Completa</h6>
        <p class="text-muted small">Servicio los 7 días de la semana</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="help-card text-center p-4">
        <div class="help-icon mb-3">
          <i class="fas fa-phone text-primary"></i>
        </div>
        <h6>Soporte</h6>
        <p class="text-muted small">¿Necesitas ayuda? Escribenos: soporte.vetmart@gmail.com</p>
      </div>
    </div>
  </div>
</div>

<style>
.agendar-cita-container {
  padding: 1.5rem 0;
}

.header-section {
  padding: 0 0.5rem;
}

.section-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  color: white;
  font-size: 1.25rem;
}

/* Agendar Card */
.agendar-card {
  border-radius: 20px;
  border-left: 4px solid #06b6d4;
}

/* DateTime Selection Styles */
.datetime-selection {
  background: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
}

.sub-label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  display: block;
  font-size: 0.9rem;
}

/* Mini Calendar Styles */
.mini-calendar-container {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.calendar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
  color: white;
}

.calendar-nav {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.calendar-nav:hover {
  background: rgba(255, 255, 255, 0.3);
}

.calendar-title {
  font-weight: 600;
  font-size: 1rem;
  text-align: center;
  flex: 1;
}

.calendar-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.calendar-weekdays > div {
  padding: 0.75rem 0.5rem;
  text-align: center;
  font-size: 0.8rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
}

.calendar-weekdays .weekend {
  color: #059669; /* Verde para fines de semana disponibles */
}

.calendar-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 1px;
  background: #e5e7eb;
}

.calendar-day {
  background: white;
  padding: 0.75rem 0.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  font-size: 0.9rem;
  min-height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.calendar-day:hover:not(.disabled):not(.selected) {
  background: #f0f9ff;
  color: #06b6d4;
}

.calendar-day.selected {
  background: #06b6d4;
  color: white;
  font-weight: 600;
}

.calendar-day.disabled {
  background: #f9fafb;
  color: #d1d5db;
  cursor: not-allowed;
}

.calendar-day.weekend {
  background: #f0fdf4; /* Fondo verde claro para fines de semana */
  color: #059669; /* Texto verde para fines de semana */
  font-weight: 500;
}

.calendar-day.weekend.selected {
  background: #059669; /* Verde más oscuro para fin de semana seleccionado */
  color: white;
}

.calendar-day.weekend:hover:not(.disabled):not(.selected) {
  background: #dcfce7;
  color: #059669;
}

.calendar-day.today {
  background: #fffbeb;
  color: #d97706;
  font-weight: 600;
  border: 2px solid #f59e0b;
}

.calendar-day.today.selected {
  background: #06b6d4;
  color: white;
  border-color: #06b6d4;
}

.calendar-day.today.weekend {
  background: #fef7cd;
  color: #ca8a04;
  border-color: #eab308;
}

/* Time Slots */
.time-slots-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.time-slot {
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  padding: 0.75rem 0.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.85rem;
  font-weight: 500;
}

.time-slot:hover {
  border-color: #06b6d4;
  background: #f0fdff;
}

.time-slot.selected {
  background: #06b6d4;
  color: white;
  border-color: #06b6d4;
}

.time-slot.disabled {
  background: #f3f4f6;
  color: #9ca3af;
  cursor: not-allowed;
  border-color: #d1d5db;
}

/* Selected Date Info */
.selected-date-info {
  background: #f0f9ff !important;
  border: 1px solid #bae6fd;
  color: #0369a1;
}

/* Weekend Badge */
.weekend-badge {
  background: #f59e0b;
  color: white;
  font-size: 0.7rem;
  padding: 0.2rem 0.5rem;
  border-radius: 12px;
  margin-left: 0.5rem;
}

/* Appointment Summary */
.appointment-summary {
  border-radius: 12px;
  border-left: 4px solid #10b981;
}

.appointment-summary .card-title {
  color: #065f46;
  font-weight: 600;
}

/* Help Cards */
.help-card {
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  transition: transform 0.3s ease;
}

.help-card:hover {
  transform: translateY(-2px);
}

.help-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  background: rgba(6, 182, 212, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  color: #06b6d4;
  font-size: 1.25rem;
}

.help-card h6 {
  color: #374151;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .agendar-cita-container {
    padding: 1rem 0;
  }
  
  .time-slots-grid {
    grid-template-columns: repeat(3, 1fr);
  }
  
  .datetime-selection {
    padding: 1rem;
  }
  
  .calendar-day {
    padding: 0.5rem 0.25rem;
    min-height: 40px;
    font-size: 0.8rem;
  }
}

@media (max-width: 576px) {
  .time-slots-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .calendar-days {
    gap: 0.5px;
  }
  
  .calendar-day {
    padding: 0.4rem 0.2rem;
    min-height: 35px;
    font-size: 0.75rem;
  }
  
  .form-actions .d-flex {
    flex-direction: column;
    gap: 1rem !important;
  }
  
  .form-actions .btn {
    width: 100%;
  }
}

/* Form Elements */
.form-select-lg,
.form-control-lg {
  border-radius: 12px;
  border: 2px solid #e5e7eb;
  padding: 0.75rem 1rem;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-select-lg:focus,
.form-control-lg:focus {
  border-color: #06b6d4;
  box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
  outline: none;
}

.btn-lg {
  border-radius: 12px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
  border: none;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
}
</style>

<script>
// Available time slots (every 30 minutes from 8:00 to 17:30)
const availableTimeSlots = [
  '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
  '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
  '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
  '17:00', '17:30'
];

let selectedDate = null;
let selectedTime = null;
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

document.addEventListener('DOMContentLoaded', function() {
  initializeCalendar();
  generateTimeSlots();
});

function initializeCalendar() {
  updateCalendarTitle();
  generateCalendarDays();
  
  // Set default selection to tomorrow
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  selectDate(tomorrow);
}

function updateCalendarTitle() {
  const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
  ];
  document.getElementById('calendarTitle').textContent = 
    `${monthNames[currentMonth]} ${currentYear}`;
}

function generateCalendarDays() {
  const calendarDays = document.getElementById('calendarDays');
  calendarDays.innerHTML = '';

  const firstDay = new Date(currentYear, currentMonth, 1);
  const lastDay = new Date(currentYear, currentMonth + 1, 0);
  const today = new Date();
  
  // Get day of week for first day (0 = Sunday, 1 = Monday, ...)
  let firstDayOfWeek = firstDay.getDay();
  // Convert to Monday-based week (0 = Monday, 6 = Sunday)
  firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;

  // Add empty cells for days before the first day of the month
  for (let i = 0; i < firstDayOfWeek; i++) {
    const emptyDay = document.createElement('div');
    emptyDay.className = 'calendar-day disabled';
    calendarDays.appendChild(emptyDay);
  }

  // Add days of the month
  for (let day = 1; day <= lastDay.getDate(); day++) {
    const date = new Date(currentYear, currentMonth, day);
    const dayElement = document.createElement('button');
    dayElement.type = 'button';
    dayElement.className = 'calendar-day';
    dayElement.textContent = day;
    
    // Check if it's today
    if (date.toDateString() === today.toDateString()) {
      dayElement.classList.add('today');
    }
    
    // Check if it's weekend
    const dayOfWeek = date.getDay();
    if (dayOfWeek === 0 || dayOfWeek === 6) {
      dayElement.classList.add('weekend');
    }
    
    // Check if date is in the past
    if (date < today && date.toDateString() !== today.toDateString()) {
      dayElement.classList.add('disabled');
    } else {
      dayElement.addEventListener('click', () => selectDate(date));
    }
    
    // Check if this date is selected
    if (selectedDate && date.toDateString() === selectedDate.toDateString()) {
      dayElement.classList.add('selected');
    }
    
    calendarDays.appendChild(dayElement);
  }
}

function changeMonth(direction) {
  currentMonth += direction;
  
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  } else if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  
  updateCalendarTitle();
  generateCalendarDays();
}

function selectDate(date) {
  selectedDate = date;
  selectedTime = null;
  
  // Update calendar display
  generateCalendarDays();
  
  // Update selected date display
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  const formattedDate = date.toLocaleDateString('es-ES', options);
  document.getElementById('selectedDateDisplay').textContent = formattedDate;
  
  // Generate time slots for selected date
  generateTimeSlots();
  updateAppointmentSummary();
}

function generateTimeSlots() {
  const timeSlotsContainer = document.getElementById('timeSlots');
  timeSlotsContainer.innerHTML = '';
  
  if (!selectedDate) return;
  
  const today = new Date();
  const isToday = selectedDate.toDateString() === today.toDateString();
  
  availableTimeSlots.forEach(time => {
    const timeSlot = document.createElement('button');
    timeSlot.type = 'button';
    timeSlot.className = 'time-slot';
    timeSlot.textContent = time;
    timeSlot.dataset.time = time;
    
    // Check if time slot is in the past for today
    if (isToday) {
      const [hours, minutes] = time.split(':').map(Number);
      const slotTime = new Date();
      slotTime.setHours(hours, minutes, 0, 0);
      
      if (slotTime < today) {
        timeSlot.classList.add('disabled');
        timeSlot.title = 'Este horario ya pasó para hoy';
      }
    }
    
    timeSlot.addEventListener('click', function() {
      if (!this.classList.contains('disabled')) {
        selectTimeSlot(this);
      }
    });
    
    timeSlotsContainer.appendChild(timeSlot);
  });
}

function selectTimeSlot(element) {
  // Remove selected class from all time slots
  document.querySelectorAll('.time-slot').forEach(slot => {
    slot.classList.remove('selected');
  });
  
  // Add selected class to clicked time slot
  element.classList.add('selected');
  selectedTime = element.dataset.time;
  
  updateAppointmentSummary();
}

function updateAppointmentSummary() {
  const summaryElement = document.getElementById('appointmentSummary');
  const summaryDate = document.getElementById('summaryDate');
  const summaryTime = document.getElementById('summaryTime');
  const summaryDay = document.getElementById('summaryDay');
  const summaryDuration = document.getElementById('summaryDuration');
  const weekendBadge = document.getElementById('weekendBadge');
  const fechaCompleta = document.getElementById('fechaCompleta');
  
  if (selectedDate && selectedTime) {
    // Format date
    const formattedDate = selectedDate.toLocaleDateString('es-ES', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    
    // Format time
    const [hours, minutes] = selectedTime.split(':');
    const formattedTime = `${hours}:${minutes}`;
    
    // Check if it's weekend
    const dayOfWeek = selectedDate.getDay();
    const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
    
    // Set summary values
    summaryDate.textContent = formattedDate;
    summaryTime.textContent = formattedTime;
    summaryDay.textContent = selectedDate.toLocaleDateString('es-ES', { weekday: 'long' });
    summaryDuration.textContent = '30 minutos'; // Default duration
    
    // Show/hide weekend badge
    if (isWeekend) {
      weekendBadge.style.display = 'inline-block';
    } else {
      weekendBadge.style.display = 'none';
    }
    
    // Set hidden input value for form submission
    const formattedDateISO = selectedDate.toISOString().split('T')[0];
    fechaCompleta.value = `${formattedDateISO}T${selectedTime}`;
    
    // Show summary
    summaryElement.style.display = 'block';
  } else {
    summaryElement.style.display = 'none';
    fechaCompleta.value = '';
    weekendBadge.style.display = 'none';
  }
}

function resetDateTime() {
  selectedDate = null;
  selectedTime = null;
  document.querySelectorAll('.calendar-day').forEach(day => {
    day.classList.remove('selected');
  });
  document.querySelectorAll('.time-slot').forEach(slot => {
    slot.classList.remove('selected');
  });
  document.getElementById('selectedDateDisplay').textContent = 'Ningún día seleccionado';
  document.getElementById('weekendBadge').style.display = 'none';
  updateAppointmentSummary();
}

function validarReagendar(form) {
  if (!selectedDate || !selectedTime) {
    alert('Por favor, selecciona una fecha y hora para la cita.');
    return false;
  }
  
  const fechaCompleta = new Date(selectedDate);
  const [hours, minutes] = selectedTime.split(':');
  fechaCompleta.setHours(parseInt(hours), parseInt(minutes), 0, 0);
  
  const ahora = new Date();
  
  if (fechaCompleta <= ahora) {
    alert('Por favor, selecciona una fecha y hora futura.');
    return false;
  }
  
  // Validación de horario laboral (ahora incluye fines de semana)
  const hora = fechaCompleta.getHours();
  
  if (hora < 8 || hora >= 18) {
    alert('El horario de atención es de 8:00 a 18:00 hrs.');
    return false;
  }
  
  return true;
}
</script>
