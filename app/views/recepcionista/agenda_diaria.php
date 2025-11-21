<div class="agenda-container">
  <!-- Header Section -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="mb-3 mb-md-0">
      <h1 class="page-title mb-2">
        <i class="fas fa-calendar-day me-2 text-success"></i>Agenda Diaria
      </h1>
      <p class="text-muted mb-0">
        Citas programadas para:
        <?php if (!empty($fecha)): ?>
          <strong class="text-dark"><?= date('d/m/Y', strtotime($fecha)) ?></strong>
        <?php else: ?>
          <strong class="text-dark">Todas las fechas</strong>
        <?php endif; ?>
      </p>
    </div>
    
    <div class="d-flex gap-2 flex-column flex-md-row align-items-stretch align-items-md-center w-100 w-md-auto">
      <form class="d-flex gap-2 align-items-center" action="/vetsmart/recepcionista/agenda" method="get">
        <input type="date" class="form-control" name="fecha" value="<?= htmlspecialchars($fecha) ?>" 
               title="Seleccionar fecha">
        <select class="form-select" name="estado">
          <?php $estSel = $estado ?? ''; ?>
          <option value="">Todos los estados</option>
          <option value="pendiente" <?= ($estSel === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
          <option value="completada" <?= ($estSel === 'completada') ? 'selected' : '' ?>>Completada</option>
        </select>
        <button type="submit" class="btn btn-outline-success">
          <i class="fas fa-filter me-1"></i>Filtrar
        </button>
        <a href="/vetsmart/recepcionista/agenda" class="btn btn-outline-secondary" title="Limpiar filtros">
          <i class="fas fa-rotate-left"></i>
        </a>
      </form>
      <a href="/vetsmart/recepcionista/citas/create" class="btn btn-success">
        <i class="fas fa-plus-circle me-2"></i>Nueva Cita
      </a>
    </div>
  </div>

  <!-- Agenda Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-4">
                <i class="fas fa-calendar me-1 text-muted"></i>Fecha
              </th>
              <th>
                <i class="fas fa-clock me-1 text-muted"></i>Hora
              </th>
              <th>
                <i class="fas fa-user me-1 text-muted"></i>Cliente
              </th>
              <th>
                <i class="fas fa-paw me-1 text-muted"></i>Mascota
              </th>
              <th>
                <i class="fas fa-stethoscope me-1 text-muted"></i>Servicio
              </th>
              <th>
                <i class="fas fa-user-md me-1 text-muted"></i>Empleado
              </th>
              <th>Estado</th>
              <th class="text-end pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($citas)): ?>
              <?php foreach ($citas as $c): ?>
                <tr class="cita-row">
                  <td class="ps-4 fw-bold text-dark">
                    <?= date('d/m/Y', strtotime($c['fecha'])) ?>
                  </td>
                  <td class="ps-4 fw-bold text-dark">
                    <?= htmlspecialchars($c['hora']) ?>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar-circle-sm bg-gray-200 text-gray-700 me-2">
                        <?= strtoupper(substr($c['cliente_nombre'], 0, 1) . substr($c['cliente_apellido'], 0, 1)) ?>
                      </div>
                      <div class="small">
                        <div class="fw-medium"><?= htmlspecialchars($c['cliente_nombre'] . ' ' . $c['cliente_apellido']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">
                      <i class="fas fa-paw me-1"></i>
                      <?= htmlspecialchars($c['mascota_nombre'] ?? '-') ?>
                    </span>
                  </td>
                  <td>
                    <span class="text-dark"><?= htmlspecialchars($c['servicio'] ?? '-') ?></span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar-circle-sm bg-gray-200 text-gray-700 me-2">
                        <?= strtoupper(substr($c['empleado_nombre'], 0, 1) . substr($c['empleado_apellido'], 0, 1)) ?>
                      </div>
                      <div class="small text-muted">
                        <?= htmlspecialchars($c['empleado_nombre'] . ' ' . $c['empleado_apellido']) ?>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge estado-badge estado-<?= str_replace(' ', '-', $c['estado']) ?>">
                      <?= ucfirst($c['estado']) ?>
                    </span>
                  </td>
                  <td class="text-end pe-4">
                    <button type="button" class="btn btn-sm btn-outline-info btn-action"
                            onclick="verCita(<?= $c['id'] ?>)"
                            data-bs-toggle="tooltip" 
                            title="Ver detalles de la cita">
                      <i class="fas fa-eye"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="text-center py-5">
                  <div class="empty-state">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay citas programadas</h5>
                    <p class="text-muted mb-3">No se encontraron citas para la fecha seleccionada.</p>
                    <a href="/vetsmart/recepcionista/citas/create" class="btn btn-success">
                      <i class="fas fa-plus-circle me-2"></i>Programar Nueva Cita
                    </a>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Summary Footer -->
  <div class="d-flex justify-content-between align-items-center mt-3">
    <?php
      $page = $page ?? 1;
      $perPage = $perPage ?? count($citas);
      $total = $total ?? count($citas);
      $start = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
      $end = $total > 0 ? min($total, $page * $perPage) : 0;
    ?>
    <div class="text-muted small">
      Mostrando <strong><?= $start ?> - <?= $end ?></strong> de <strong><?= $total ?></strong> cita<?= $total !== 1 ? 's' : '' ?>
    </div>
    <?php if (!empty($estado)): ?>
      <span class="badge bg-success-subtle text-success">
        <i class="fas fa-filter me-1"></i>Filtrado: <?= ucfirst($estado) ?>
      </span>
    <?php endif; ?>
  </div>

  <!-- Paginación -->
  <?php
    $totalPages = $totalPages ?? 1;
    $qsBase = [];
    if (!empty($fecha)) { $qsBase['fecha'] = $fecha; }
    if (!empty($estado)) { $qsBase['estado'] = $estado; }
  ?>
  <?php if ($totalPages > 1): ?>
    <nav aria-label="Paginación agenda" class="mt-3">
      <ul class="pagination justify-content-end">
        <?php $prevDisabled = $page <= 1 ? ' disabled' : ''; ?>
        <li class="page-item<?= $prevDisabled ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($qsBase, ['page' => max(1, $page - 1)])) ?>" aria-label="Anterior">&laquo;</a>
        </li>

        <?php
          $startPage = max(1, $page - 3);
          $endPage = min($totalPages, $page + 3);
          if ($startPage > 1) {
        ?>
          <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($qsBase, ['page' => 1])) ?>">1</a></li>
          <?php if ($startPage > 2): ?>
            <li class="page-item disabled"><span class="page-link">…</span></li>
          <?php endif; ?>
        <?php } ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
          <li class="page-item<?= $i === $page ? ' active' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($qsBase, ['page' => $i])) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($endPage < $totalPages): ?>
          <?php if ($endPage < $totalPages - 1): ?>
            <li class="page-item disabled"><span class="page-link">…</span></li>
          <?php endif; ?>
          <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($qsBase, ['page' => $totalPages])) ?>"><?= $totalPages ?></a></li>
        <?php endif; ?>

        <?php $nextDisabled = $page >= $totalPages ? ' disabled' : ''; ?>
        <li class="page-item<?= $nextDisabled ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($qsBase, ['page' => min($totalPages, $page + 1)])) ?>" aria-label="Siguiente">&raquo;</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<!-- Modal Detalle Cita -->
<div class="modal fade" id="modalCita" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light border-bottom">
        <h5 class="modal-title text-dark">
          <i class="fas fa-calendar-check me-2 text-success"></i>Detalle de la Cita
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="detail-item">
              <label class="detail-label text-muted small">Cliente</label>
              <p class="detail-value fw-medium" id="detalleCliente">-</p>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="detail-item">
              <label class="detail-label text-muted small">Mascota</label>
              <p class="detail-value fw-medium" id="detalleMascota">-</p>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="detail-item">
              <label class="detail-label text-muted small">Servicio</label>
              <p class="detail-value fw-medium" id="detalleServicio">-</p>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="detail-item">
              <label class="detail-label text-muted small">Empleado</label>
              <p class="detail-value fw-medium" id="detalleEmpleado">-</p>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="detail-item">
              <label class="detail-label text-muted small">Fecha y Hora</label>
              <p class="detail-value fw-medium" id="detalleFecha">-</p>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="detail-item">
              <label class="detail-label text-muted small">Duracion</label>
              <p class="detail-value fw-medium" id="detalleDuracion">-</p>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="detail-item">
              <label class="detail-label text-muted small">Estado</label>
              <p class="detail-value">
                <span id="detalleEstado" class="badge estado-badge">-</span>
              </p>
            </div>
          </div>
        </div>
        <div class="detail-item">
          <label class="detail-label text-muted small">Notas Adicionales</label>
          <div class="detail-value bg-light rounded p-3" id="detalleNotas">
            <span class="text-muted">No hay notas adicionales</span>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<style>
.agenda-container {
  max-width: 100%;
}

.page-title {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.6rem;
}

/* Neutral Color Scheme */
.bg-gray-200 {
  background-color: #e9ecef !important;
}

.text-gray-700 {
  color: #374151 !important;
}

.bg-success-subtle {
  background-color: #d1e7dd !important;
}

/* Avatar Circles */
.avatar-circle-sm {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Table Styles */
.table {
  margin-bottom: 0;
}

.table th {
  border-top: none;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 1rem 0.75rem;
  color: #374151;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e5e7eb;
}

.table td {
  padding: 1rem 0.75rem;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
}

.cita-row {
  transition: background-color 0.2s ease;
}

.cita-row:hover {
  background-color: #f8fafc;
}

/* Estado Badges */
.estado-badge {
  font-size: 0.75rem;
  padding: 0.35rem 0.7rem;
  border-radius: 6px;
  font-weight: 500;
  border: 1px solid;
}

.estado-completada {
  background-color: #d1e7dd;
  border-color: #198754;
  color: #0f5132;
}

.estado-en-curso {
  background-color: #fff3cd;
  border-color: #ffc107;
  color: #856404;
}

.estado-pendiente {
  background-color: #e2e3e5;
  border-color: #6c757d;
  color: #383d41;
}

/* Action Buttons */
.btn-action {
  border-radius: 6px;
  padding: 0.375rem 0.75rem;
  transition: all 0.2s ease;
  border: 1px solid #d1d5db;
}

.btn-action:hover {
  background-color: #f9fafb;
  transform: translateY(-1px);
}

.empty-state {
  padding: 3rem 1rem;
}

/* Detail Items */
.detail-item {
  margin-bottom: 1rem;
}

.detail-label {
  font-weight: 500;
  margin-bottom: 0.25rem;
  display: block;
}

.detail-value {
  color: #212529;
  margin: 0;
  font-size: 0.95rem;
}

/* Card Styles */
.card {
  border: 1px solid #e5e7eb;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.4rem;
  }
  
  .table-responsive {
    font-size: 0.85rem;
  }
  
  .avatar-circle-sm {
    width: 28px;
    height: 28px;
    font-size: 0.7rem;
  }
  
  .btn-action {
    padding: 0.25rem 0.5rem;
  }
  
  .d-flex.flex-md-row {
    flex-direction: column;
  }
  
  .d-flex.gap-2 {
    width: 100%;
  }
  
  form.d-flex {
    flex-direction: column;
    width: 100%;
    gap: 0.5rem !important;
  }
  
  form.d-flex .form-control,
  form.d-flex .form-select,
  form.d-flex .btn {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .agenda-container {
    padding: 0.5rem;
  }
  
  .table th,
  .table td {
    padding: 0.75rem 0.5rem;
  }
  
  .modal-body .row {
    margin: 0 -0.25rem;
  }
  
  .modal-body .col-md-6 {
    padding: 0 0.25rem;
  }
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });
});

async function verCita(id) {
  try {
    const res = await fetch(`/vetsmart/recepcionista/citas/ver/${id}`, { cache: 'no-store' });
    const text = await res.text();

    let data;
    try {
      data = JSON.parse(text);
    } catch (parseErr) {
      console.error('Respuesta no JSON:', text);
      alert('Error al cargar los datos de la cita.');
      return;
    }

    if (!res.ok) {
      console.error('Error servidor:', data);
      alert('Error al cargar la cita: ' + (data.msg || 'Error del servidor'));
      return;
    }

    if (!data.id) {
      alert("No se pudo cargar el detalle de la cita.");
      return;
    }

    // Update modal content
    document.getElementById('detalleCliente').textContent = `${data.cliente_nombre} ${data.cliente_apellido}`;
    document.getElementById('detalleMascota').textContent = data.mascota_nombre || 'No especificada';
    document.getElementById('detalleServicio').textContent = data.servicio || 'No especificado';
    document.getElementById('detalleEmpleado').textContent = data.empleado_nombre ? 
      `${data.empleado_nombre} ${data.empleado_apellido}` : 'No asignado';
    
    const fecha = data.fecha ? new Date(data.fecha).toLocaleDateString('es-ES', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }) : 'No especificada';
    document.getElementById('detalleFecha').textContent = fecha;
    
    document.getElementById('detalleDuracion').textContent = data.duracion_min ? `${data.duracion_min} min` : 'No especificada';
    
    const estadoBadge = document.getElementById('detalleEstado');
    estadoBadge.textContent = data.estado ? data.estado.charAt(0).toUpperCase() + data.estado.slice(1) : 'No especificado';
    estadoBadge.className = 'badge estado-badge estado-' + ((data.estado || 'pendiente').toString().toLowerCase().replace(/\s+/g, '-'));
    
    const notasElement = document.getElementById('detalleNotas');
    if (data.notas && data.notas.trim() !== '') {
      notasElement.innerHTML = data.notas;
      notasElement.classList.remove('text-muted');
    } else {
      notasElement.innerHTML = '<span class="text-muted">No hay notas adicionales</span>';
    }

    const modal = new bootstrap.Modal(document.getElementById('modalCita'));
    modal.show();

  } catch (err) {
    console.error('Fetch error:', err);
    alert('Error de conexion al cargar los datos de la cita.');
  }
}
</script>
