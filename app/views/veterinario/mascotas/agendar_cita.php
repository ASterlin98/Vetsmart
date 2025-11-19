<?php
// app/views/veterinario/mascotas/agendar_cita.php
// Variables esperadas desde el controller:
//  - $id_mascota (int)
//  - $servicios (array)
//  - opcional: $mascota (array) -> si existe, contiene info del dueño (cliente_id o dueno_id)
//  - opcional: $_SESSION['user']['id'] para empleado actual
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Calcular base dinámico para construir URLs que funcionen tanto en /vetsmart como en root
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($base === '' || $base === '.' ) $base = '';

// Valores seguros para renderizar
$id_mascota_html = htmlspecialchars($id_mascota ?? '', ENT_QUOTES, 'UTF-8');
$empleado_id_html = htmlspecialchars($_SESSION['user']['id'] ?? '', ENT_QUOTES, 'UTF-8');
$csrf_html = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');

// intentar extraer cliente_id desde $mascota si se pasó
$cliente_id_from_mascota = null;
if (!empty($mascota) && is_array($mascota)) {
    $cliente_id_from_mascota = $mascota['cliente_id'] 
                             ?? $mascota['dueno_id'] 
                             ?? $mascota['dueno_id'] 
                             ?? $mascota['owner_id'] 
                             ?? $mascota['owner'] 
                             ?? null;
}
?>
<div class="container">
  <div class="row">
    <div class="col-12">
      <h2 class="mb-4">Agendar cita</h2>

      <!-- Mostrar flash (si el controlador dejó mensajes en sesión) -->
      <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']); ?></div>
        <?php unset($_SESSION['flash_success']); ?>
      <?php endif; ?>
      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']); ?></div>
        <?php unset($_SESSION['flash_error']); ?>
      <?php endif; ?>

      <form method="POST" action="<?= $base ?>/veterinario/citas/guardar" id="formAgendarCita" class="mb-3" autocomplete="off">
        <!-- Datos ocultos -->
        <input type="hidden" name="mascota_id" value="<?= $id_mascota_html ?>">
        <input type="hidden" name="empleado_id" value="<?= $empleado_id_html ?>">
        <input type="hidden" name="_csrf" value="<?= $csrf_html ?>">

        <!-- Enviar cliente_id si lo conocemos desde la mascota -->
        <?php if (!empty($cliente_id_from_mascota)): ?>
          <input type="hidden" name="cliente_id" value="<?= htmlspecialchars($cliente_id_from_mascota, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <!-- Servicio -->
        <div class="mb-3">
          <label for="servicio_id" class="form-label">Servicio</label>
          <select name="servicio_id" id="servicio_id" class="form-control" required>
            <option value="">-- Seleccione servicio --</option>
            <?php if (!empty($servicios) && is_array($servicios)): ?>
              <?php foreach ($servicios as $s): ?>
                <?php
                  $sid = $s['id'] ?? $s['servicio_id'] ?? null;
                  if ($sid === null) continue;
                  $sname = $s['nombre'] ?? $s['titulo'] ?? 'Servicio';
                  $dur = isset($s['duracion_min']) ? ' (' . (int)$s['duracion_min'] . ' min)' : '';
                ?>
                <option value="<?= htmlspecialchars($sid, ENT_QUOTES, 'UTF-8') ?>">
                  <?= htmlspecialchars($sname, ENT_QUOTES, 'UTF-8') . $dur ?>
                </option>
              <?php endforeach; ?>
            <?php else: ?>
              <option value="">-- No hay servicios disponibles --</option>
            <?php endif; ?>
          </select>
        </div>

        <!-- Fecha y hora -->
        <div class="mb-3">
          <label for="fecha" class="form-label">Fecha y hora</label>
          <?php $min_datetime = date('Y-m-d') . 'T00:00'; ?>
          <input id="fecha" name="fecha" type="datetime-local" class="form-control" required min="<?= $min_datetime ?>">
          <div class="form-text">Seleccione la fecha y la hora de la cita (solo hoy en adelante).</div>
        </div>

        <!-- Notas -->
        <div class="mb-3">
          <label for="notas" class="form-label">Notas (opcional)</label>
          <textarea id="notas" name="notas" class="form-control" rows="3"></textarea>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-success">📅 Agendar</button>
          <a href="<?= $base ?>/veterinario/pacientes" class="btn btn-secondary">Cancelar</a>
        </div>
      </form>

      <a href="<?= $base ?>/veterinario/pacientes" class="btn btn-link mt-3">🔙 Volver a Pacientes</a>
    </div>
  </div>
</div>

<!-- JS: validación y prevención de doble envío -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('formAgendarCita');
  if (!form) return;

    form.addEventListener('submit', function (e) {
    // client-side validation: servicio y fecha
    var servicio = document.getElementById('servicio_id').value;
    var fecha = document.getElementById('fecha').value;

    if (!servicio || !fecha) {
      e.preventDefault();
      alert('Seleccione un servicio y una fecha/hora válidos.');
      return false;
    }

    // validar que la fecha seleccionada no sea anterior a hoy (solo fechas previas al día actual)
    try {
      var selected = fecha ? new Date(fecha) : null;
      if (selected) {
        var today = new Date();
        today.setHours(0,0,0,0);
        if (selected < today) {
          e.preventDefault();
          alert('No puedes agendar citas en fechas anteriores a hoy.');
          return false;
        }
      }
    } catch (err) {
      // si hay un fallo al parsear, dejar que el servidor valide
    }

    // disable submit button to avoid double posts
    var btn = form.querySelector('button[type="submit"]');
    if (btn) {
      btn.disabled = true;
      btn.innerText = 'Guardando...';
    }

    // allow normal submit (server will redirect to mis-citas on success)
    return true;
  });
});
</script>
