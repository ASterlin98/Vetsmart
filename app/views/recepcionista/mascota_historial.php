<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h1 class="h4 mb-1">
        <i class="fas fa-file-medical text-success me-2"></i>
        Historial clinico de <?= htmlspecialchars($mascota['nombre']) ?>
      </h1>
      <div class="text-muted">
        Dueñoo: <?= htmlspecialchars(($mascota['nombre_dueno'] ?? '-') . ' ' . ($mascota['apellido_dueno'] ?? '')) ?>
        <?php if (!empty($mascota['email_dueno'])): ?>
          · Email: <?= htmlspecialchars($mascota['email_dueno']) ?>
        <?php endif; ?>
        <?php if (!empty($mascota['telefono_dueno'])): ?>
          · Tel: <?= htmlspecialchars($mascota['telefono_dueno']) ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE ?>/reportes/mascota/<?= $mascota['id'] ?>/pdf" target="_blank" class="btn btn-outline-danger">
            <i class="fas fa-file-pdf me-1"></i> Historial PDF
        </a>
        <a href="<?= BASE ?>/recepcionista/mascotas" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-4">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white">
          <i class="fas fa-info-circle me-2"></i>Ficha de la mascota
        </div>
        <div class="card-body text-center">
             <div class="mb-3">
              <?php if (!empty($mascota['foto']) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/assets/uploads/mascotas/' . $mascota['foto'])): ?>
                 <div style="width:120px;height:120px;border-radius:50%;overflow:hidden;margin:0 auto;border: 3px solid #198754;">
                    <img src="<?= BASE ?>/public/assets/uploads/mascotas/<?= htmlspecialchars($mascota['foto']) ?>" alt="Foto Mascota" style="width:100%;height:100%;object-fit:cover;">
                 </div>
              <?php else: ?>
                <div style="width:120px;height:120px;border-radius:50%;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-size:40px;color:#6c757d;margin:0 auto;border: 3px solid #dee2e6;">
                  <?= strtoupper(substr($mascota['nombre'] ?? 'M',0,1)) ?>
                </div>
              <?php endif; ?>
             </div>
             <div class="text-start">
          <div class="mb-2"><strong>Nombre:</strong> <?= htmlspecialchars($mascota['nombre'] ?? '-') ?></div>
          <div class="mb-2"><strong>Especie:</strong> <?= htmlspecialchars($mascota['especie'] ?? '-') ?></div>
          <div class="mb-2"><strong>Raza:</strong> <?= htmlspecialchars($mascota['raza'] ?? '-') ?></div>
          <div class="mb-2"><strong>Edad:</strong> <?= htmlspecialchars($mascota['edad'] ?? '-') ?></div>
          <div class="mb-2"><strong>Peso:</strong> <?= htmlspecialchars($mascota['peso'] ?? '-') ?></div>
          <div class="mb-2"><strong>Notas:</strong> <?= htmlspecialchars($mascota['notas'] ?? '-') ?></div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0 mt-3">
        <div class="card-header bg-light">
          <i class="fas fa-sticky-note me-2 text-success"></i>Notas rapidas
        </div>
        <div class="card-body p-0">
          <?php if (!empty($notas)): ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($notas as $n): ?>
                <li class="list-group-item">
                  <div class="small text-muted mb-1"><?= htmlspecialchars($n['creado_en'] ?? '') ?></div>
                  <div><?= nl2br(htmlspecialchars($n['nota'] ?? '')) ?></div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <div class="p-3 text-muted">Sin notas registradas.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-8">
      
      <!-- Archivos Adjuntos -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-folder-open me-2 text-primary"></i>Archivos Adjuntos
            </div>
<<<<<<< HEAD
            <!-- Botón para subir archivo (redirige a reportes con preselección si fuera posible, por ahora solo link) -->
            <a href="<?= BASE ?>/recepcionista/reportes" class="btn btn-sm btn-outline-primary"><i class="fas fa-upload me-1"></i>Subir</a>
=======
            <div>
                <a href="<?= BASE ?>/recepcionista/reportes" class="btn btn-sm btn-outline-primary"><i class="fas fa-upload me-1"></i>Subir</a>
            </div>
>>>>>>> 551a971277bd5d0b94296071273044a14c300280
        </div>
        <div class="card-body p-0">
          <?php if (!empty($archivos)): ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($archivos as $f): 
                  // Usar ruta relativa absoluta desde la raíz del servidor web
                  $url = BASE . "/public/assets/uploads/reportes/" . $mascota['id'] . "/" . rawurlencode($f);
              ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <a href="<?= $url ?>" target="_blank" class="text-decoration-none text-dark">
                    <i class="fas fa-paperclip me-2 text-secondary"></i><?= htmlspecialchars($f) ?>
                  </a>
                  <form action="<?= BASE ?>/recepcionista/reportes/delete" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este archivo?');">
                    <?php if (class_exists('CSRF')): ?><?= CSRF::inputField() ?><?php endif; ?>
                    <input type="hidden" name="mascota_id" value="<?= $mascota['id'] ?>">
                    <input type="hidden" name="filename" value="<?= htmlspecialchars($f) ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <div class="p-3 text-muted text-center">No hay archivos adjuntos.</div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light">
          <i class="fas fa-user-md me-2 text-success"></i>Consultas medicas
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
              <thead class="table-success">
                <tr>
                  <th>Fecha</th>
                  <th>Motivo</th>
                  <th>Diagnostico</th>
                  <th>Tratamiento</th>
                  <th>Atendido por</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($consultas)): ?>
                  <?php foreach ($consultas as $c): ?>
                    <tr>
                      <td data-label="Fecha"><?= htmlspecialchars($c['creado_en'] ?? '-') ?></td>
                      <td data-label="Motivo"><?= htmlspecialchars($c['motivo'] ?? '-') ?></td>
                      <td data-label="Diagnóstico"><?= htmlspecialchars($c['diagnostico'] ?? '-') ?></td>
                      <td data-label="Tratamiento"><?= htmlspecialchars($c['tratamiento'] ?? '-') ?></td>
                      <td data-label="Atendido por"><?= htmlspecialchars(trim(($c['nombre_empleado'] ?? $c['nombre_veterinario'] ?? '') . ' ' . ($c['apellido_empleado'] ?? $c['apellido_veterinario'] ?? ''))) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted py-4">Sin consultas registradas.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
          <i class="fas fa-calendar-check me-2 text-success"></i>Citas e intervenciones
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
              <thead class="table-light">
                <tr>
                  <th>Fecha</th>
                  <th>Servicio</th>
                  <th>Estado</th>
                  <th>Veterinario</th>
                  <th>Notas</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($citas)): ?>
                  <?php foreach ($citas as $c): ?>
                    <tr>
                      <td data-label="Fecha"><?= htmlspecialchars($c['fecha'] ?? '-') ?></td>
                      <td data-label="Servicio"><?= htmlspecialchars($c['servicio'] ?? '-') ?></td>
                      <td data-label="Estado">
                        <span class="badge estado-badge <?= 'estado-' . str_replace(' ', '-', strtolower($c['estado'] ?? 'pendiente')) ?>">
                          <?= htmlspecialchars(ucfirst($c['estado'] ?? 'pendiente')) ?>
                        </span>
                      </td>
                      <td data-label="Veterinario"><?= htmlspecialchars($c['veterinario'] ?? '-') ?></td>
                      <td data-label="Notas"><?= htmlspecialchars($c['notas'] ?? '-') ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted py-4">Sin citas previas.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.estado-badge {
  font-size: 0.8rem;
}
.estado-en-curso { background-color: #fff3cd; color: #856404; }
.estado-completada { background-color: #d4edda; color: #155724; }
.estado-pendiente { background-color: #e2e3e5; color: #383d41; }
</style>

