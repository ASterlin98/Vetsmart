<?php
// app/views/veterinario/consultas/crear.php
$mascota = $mascota ?? null;
$cita = $cita ?? null;
?>
<div class="container py-4">
  <div class="d-flex align-items-center mb-3">
    <div class="me-3 bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
      <i class="bi bi-journal-medical" style="font-size:1.25rem;"></i>
    </div>
    <div>
      <h2 class="h4 fw-bold mb-0">Nueva consulta</h2>
      <small class="text-muted">Registra el examen, diagnóstico y tratamiento</small>
    </div>
  </div>

  <form action="/vetsmart/veterinario/consultas/guardar" method="POST">
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-3 mb-3">
          <div class="mb-3">
            <label for="mascota_id" class="form-label">Mascota <span class="text-danger">*</span></label>
            <select id="mascota_id" name="mascota_id" class="form-select" required aria-required="true">
              <?php if ($mascota): ?>
                <option value="<?= $mascota['id'] ?>" selected><?= htmlspecialchars($mascota['nombre']) ?> — ID: <?= htmlspecialchars($mascota['id']) ?></option>
              <?php else: ?>
                <option value="">Seleccionar mascota</option>
              <?php endif; ?>
            </select>
            <?php if (!$mascota): ?>
              <div class="form-text">Puedes iniciar la creación desde la ficha de la mascota para autoseleccionarla.</div>
            <?php endif; ?>
          </div>

          <?php if ($cita): ?>
            <input type="hidden" name="cita_id" value="<?= htmlspecialchars($cita['id']) ?>">
            <div class="mb-3">
              <label class="form-label">Cita relacionada</label>
              <div class="p-2 rounded-2 bg-light">
                <div class="d-flex justify-content-between">
                  <div>
                    <div class="fw-semibold">ID <?= htmlspecialchars($cita['id']) ?> — <?= htmlspecialchars($cita['servicio_nombre'] ?? ($cita['servicio_id'] ?? '')) ?></div>
                    <div class="small text-muted">Fecha: <?= htmlspecialchars($cita['fecha'] ?? ($cita['creado_en'] ?? '')) ?></div>
                    <?php if (!empty($cita['notas'])): ?>
                      <div class="small mt-1">Nota cita: <?= nl2br(htmlspecialchars($cita['notas'])) ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="text-end small">
                    <span class="badge bg-secondary"><?= htmlspecialchars($cita['estado'] ?? '-') ?></span>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <div class="mb-3">
            <label for="motivo" class="form-label">Motivo</label>
            <textarea id="motivo" name="motivo" class="form-control" rows="3" placeholder="Describa brevemente el motivo de la consulta"><?= htmlspecialchars($cita['notas'] ?? '') ?></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="examen" class="form-label">Examen físico / hallazgos</label>
              <textarea id="examen" name="examen" class="form-control" rows="4" placeholder="Observaciones del examen físico"></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label for="diagnostico" class="form-label">Diagnóstico</label>
              <textarea id="diagnostico" name="diagnostico" class="form-control" rows="4" placeholder="Conclusión / diagnóstico presuntivo"></textarea>
            </div>
          </div>

          <div class="mb-3">
            <label for="tratamiento" class="form-label">Tratamiento</label>
            <textarea id="tratamiento" name="tratamiento" class="form-control" rows="3" placeholder="Medicamentos, dosis y duración"></textarea>
          </div>

          <div class="mb-3">
            <label for="recomendaciones" class="form-label">Recomendaciones</label>
            <textarea id="recomendaciones" name="recomendaciones" class="form-control" rows="2" placeholder="Cuidados y recomendaciones para el propietario"></textarea>
          </div>

          <div class="mb-3">
            <label for="notas" class="form-label">Notas internas</label>
            <textarea id="notas" name="notas" class="form-control" rows="2" placeholder="Notas internas que no verá el cliente"><?= htmlspecialchars($cita['notas'] ?? '') ?></textarea>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar consulta</button>
            <a href="/vetsmart/veterinario/consultas" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" name="guardar_y_nuevo" value="1" class="btn btn-outline-primary">Guardar y nueva</button>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-3 mb-3">
          <div class="text-center mb-3">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;">
              <i class="bi bi-paw" style="font-size:1.5rem;color:#0d6efd;"></i>
            </div>
          </div>
          <h6 class="fw-semibold">Resumen mascota</h6>
          <?php if ($mascota): ?>
            <div class="small text-muted">Nombre</div>
            <div class="mb-2 fw-semibold"><?= htmlspecialchars($mascota['nombre']) ?> <span class="text-muted">#<?= htmlspecialchars($mascota['id']) ?></span></div>
            <div class="small text-muted">Especie / Raza</div>
            <div class="mb-2"><?= htmlspecialchars($mascota['especie'] ?? '-') ?> / <?= htmlspecialchars($mascota['raza'] ?? '-') ?></div>
            <div class="small text-muted">Dueño</div>
            <div class="mb-2"><?= htmlspecialchars(($mascota['nombre_dueno'] ?? '') . ' ' . ($mascota['apellido_dueno'] ?? '')) ?></div>
            <div class="small text-muted">Teléfono</div>
            <div class="mb-2"><?= htmlspecialchars($mascota['telefono_dueno'] ?? '-') ?></div>
            <a href="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/historial" class="btn btn-sm btn-outline-info w-100">Ver historial</a>
          <?php else: ?>
            <div class="text-muted">No hay mascota seleccionada. Puedes seleccionar una en el campo "Mascota" o crear la consulta desde la ficha de la mascota.</div>
          <?php endif; ?>
        </div>

        <div class="card border-0 shadow-sm p-3">
          <h6 class="fw-semibold">Atajos</h6>
          <div class="d-grid gap-2">
            <a href="/vetsmart/veterinario/mascotas" class="btn btn-sm btn-outline-primary">Buscar mascota</a>
            <a href="/vetsmart/veterinario/consultas" class="btn btn-sm btn-outline-secondary">Volver a consultas</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  // Auto-resize textareas to content
  (function(){
    function autosize(el){
      el.style.height = 'auto';
      el.style.height = (el.scrollHeight) + 'px';
    }
    document.querySelectorAll('textarea').forEach(function(t){
      autosize(t);
      t.addEventListener('input', function(){ autosize(t); });
    });
  })();
</script>
