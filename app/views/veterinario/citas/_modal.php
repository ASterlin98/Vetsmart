<?php
// app/views/veterinario/citas/_modal.php
// Variables esperadas: $clientes (array), $servicios (array)
// El partial está diseñado para ser resistente: no lanzará warnings si las variables vienen vacías.
?>
<div id="citaModal" class="fixed inset-0 z-50 hidden items-center justify-center">
  <div class="absolute inset-0 bg-black/50" aria-hidden="true"></div>

  <div class="relative z-10 w-full max-w-2xl mx-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b">
        <h3 id="modalTitle" class="text-lg font-semibold">Crear cita</h3>
        <button id="closeModal" class="text-gray-600 hover:text-gray-900 text-2xl leading-none" aria-label="Cerrar">&times;</button>
      </div>

      <form id="citaForm" class="px-6 py-4 space-y-4" autocomplete="off" novalidate>
        <input type="hidden" name="id" id="citaId">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- CLIENTE -->
          <div>
            <label class="block text-sm font-medium mb-1" for="clienteSelect">Cliente</label>

            <select id="clienteSelect" name="cliente_id" class="w-full border rounded px-3 py-2" aria-label="Cliente">
              <?php if (!empty($clientes) && is_array($clientes)): ?>
                <option value="">-- Seleccione cliente --</option>
                <?php foreach ($clientes as $cl): ?>
                  <?php
                    // Aceptamos distintos alias que tu modelo podría devolver: 'id' o 'idusu' o 'ID'
                    $cid = $cl['id'] ?? $cl['idusu'] ?? null;
                    $nombre = trim(($cl['nombre'] ?? '') . ' ' . ($cl['apellido'] ?? ($cl['apellido'] ?? '')));
                    if ($cid === null) continue; // saltar si no tiene id
                  ?>
                  <option value="<?= htmlspecialchars($cid, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($nombre ?: ($cl['nombre_completo'] ?? 'Cliente'), ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="">-- No hay clientes disponibles --</option>
              <?php endif; ?>
            </select>
          </div>

          <!-- MASCOTA -->
          <div>
            <label class="block text-sm font-medium mb-1" for="mascotaSelect">Mascota</label>
            <select id="mascotaSelect" name="mascota_id" class="w-full border rounded px-3 py-2" aria-label="Mascota">
              <option value="">-- Seleccione mascota --</option>
            </select>
          </div>

          <!-- SERVICIO -->
          <div>
            <label class="block text-sm font-medium mb-1" for="servicioSelect">Servicio</label>
            <select id="servicioSelect" name="servicio_id" class="w-full border rounded px-3 py-2" aria-label="Servicio">
              <?php if (!empty($servicios) && is_array($servicios)): ?>
                <option value="">-- Seleccione servicio --</option>
                <?php foreach ($servicios as $s): ?>
                  <?php $sid = $s['id'] ?? null; $sname = $s['nombre'] ?? ($s['titulo'] ?? 'Servicio'); ?>
                  <?php if ($sid === null) continue; ?>
                  <option value="<?= htmlspecialchars($sid, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($sname, ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="">-- No hay servicios disponibles --</option>
              <?php endif; ?>
            </select>
          </div>

          <!-- ESTADO -->
          <div>
            <label class="block text-sm font-medium mb-1" for="estadoSelect">Estado</label>
            <select id="estadoSelect" name="estado" class="w-full border rounded px-3 py-2" aria-label="Estado">
              <option value="programada">Programada</option>
              <option value="confirmada">Confirmada</option>
              <option value="atendida">Atendida</option>
              <option value="cancelada">Cancelada</option>
            </select>
          </div>

          <!-- FECHA -->
          <div>
            <label class="block text-sm font-medium mb-1" for="fechaInput">Fecha</label>
            <input id="fechaInput" name="fecha_date" type="date" class="w-full border rounded px-3 py-2" aria-label="Fecha">
          </div>

          <!-- HORA -->
          <div>
            <label class="block text-sm font-medium mb-1" for="horaInput">Hora</label>
            <input id="horaInput" name="hora_time" type="time" class="w-full border rounded px-3 py-2" aria-label="Hora">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1" for="notasInput">Notas</label>
          <textarea id="notasInput" name="notas" rows="3" class="w-full border rounded px-3 py-2" aria-label="Notas"></textarea>
        </div>

        <div class="flex justify-end gap-3">
          <button type="button" id="deleteBtn" class="hidden bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar</button>
          <button type="button" id="cancelBtn" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Cancelar</button>
          <button type="submit" id="saveBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
