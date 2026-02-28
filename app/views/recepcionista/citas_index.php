<div class="citas-management-container">
  <!-- Header Section -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="mb-3 mb-md-0">
      <h1 class="page-title mb-2">
        <i class="fas fa-calendar-alt me-2 text-success"></i>Gestion de Citas
      </h1>
      <p class="text-muted mb-0">Administra y organiza todas las citas del sistema</p>
    </div>
    
    <a href="<?= BASE ?>/recepcionista/citas/create" class="btn btn-success btn-lg px-4">
      <i class="fas fa-plus-circle me-2"></i>Nueva Cita
    </a>
  </div>

  <!-- Stats Overview -->
  <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card border-0 bg-primary text-white">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-0"><?= count($citas) ?></h4>
              <small class="opacity-75">Total Citas</small>
            </div>
            <i class="fas fa-calendar fa-xl opacity-50"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card border-0 bg-warning text-dark">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-0">
                <?= array_reduce($citas, function($count, $cita) {
                  $e = strtolower(trim((string)($cita['estado'] ?? '')));
                  return $count + ($e === 'pendiente' ? 1 : 0);
                }, 0) ?>
              </h4>
              <small class="opacity-75">Pendientes</small>
            </div>
            <i class="fas fa-clock fa-xl opacity-50"></i>
          </div>
        </div>
      </div>
    </div>
    <!-- Tarjeta 'En curso' eliminada por no ser funcional -->
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card border-0 bg-success text-white">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-0">
                <?= array_reduce($citas, function($count, $cita) {
                  $e = strtolower(trim((string)($cita['estado'] ?? '')));
                  return $count + ($e === 'completada' ? 1 : 0);
                }, 0) ?>
              </h4>
              <small class="opacity-75">Completadas</small>
            </div>
            <i class="fas fa-check-circle fa-xl opacity-50"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Citas Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-light py-3">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
          <i class="fas fa-list me-2 text-success"></i>Lista de Citas
        </h5>
        <form class="d-flex gap-2" method="get" action="<?= BASE ?>/recepcionista/citas">
          <?php $estSel = $estado ?? ''; ?>
          <select name="estado" class="form-select form-select-sm" style="width:auto">
            <option value="">Todos</option>
            <option value="pendiente" <?= $estSel==='pendiente'?'selected':'' ?>>Pendiente</option>
            <option value="completada" <?= $estSel==='completada'?'selected':'' ?>>Completada</option>
          </select>
          <button class="btn btn-outline-secondary btn-sm" type="submit">
            <i class="fas fa-filter me-1"></i>Filtrar
          </button>
          <a class="btn btn-outline-secondary btn-sm" href="<?= BASE ?>/recepcionista/citas">Limpiar</a>
        </form>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-success">
            <tr>
              <th class="ps-3">Fecha</th>
              <th>Hora</th>
              <th>Cliente</th>
              <th>Mascota</th>
              <th>Servicio</th>
              <th>Estado</th>
              <th class="text-center pe-3">Acciones</th>
          </tr>
        </thead>
        <tbody>
            <?php if (!empty($citas)): ?>
              <?php foreach ($citas as $c): ?>
                <tr>
                  <td class="ps-3 fw-medium"><?= htmlspecialchars($c['fecha'] ?? '-') ?></td>
                  <td>
                    <span class="badge bg-light text-dark"><?= htmlspecialchars($c['hora'] ?? '-') ?></span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <?php if (!empty($c['cliente_foto']) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/assets/uploads/clientes/' . $c['cliente_foto'])): ?>
                        <div class="avatar-sm me-2 overflow-hidden border">
                           <img src="<?= BASE ?>/public/assets/uploads/clientes/<?= htmlspecialchars($c['cliente_foto']) ?>" alt="Foto" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                      <?php else: ?>
                        <div class="avatar-sm bg-primary text-white me-2">
                          <?= strtoupper(substr($c['cliente_nombre'], 0, 1) . substr($c['cliente_apellido'], 0, 1)) ?>
                        </div>
                      <?php endif; ?>
                      <div class="fw-medium small"><?= htmlspecialchars($c['cliente_nombre'] . ' ' . $c['cliente_apellido']) ?></div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                        <?php if (!empty($c['mascota_foto']) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/assets/uploads/mascotas/' . $c['mascota_foto'])): ?>
                            <div class="avatar-sm me-2 overflow-hidden border rounded-circle">
                               <img src="<?= BASE ?>/public/assets/uploads/mascotas/<?= htmlspecialchars($c['mascota_foto']) ?>" alt="Mascota" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        <?php else: ?>
                             <span class="badge bg-light text-dark me-1"><i class="fas fa-paw"></i></span>
                        <?php endif; ?>
                        <span class="small fw-medium"><?= htmlspecialchars($c['mascota_nombre'] ?? '-') ?></span>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($c['servicio'] ?? '-') ?></td>
                  <td>
                    <?php $estadoNorm = strtolower(trim((string)($c['estado'] ?? 'pendiente'))); ?>
                    <form action="<?= BASE ?>/recepcionista/agenda/update" method="post" class="d-inline-flex align-items-center gap-1">
                      <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                      <input type="hidden" name="redirect" value=BASE . "/recepcionista/citas">
                      <select name="estado" class="form-select form-select-sm">
                        <?php $opts = ['pendiente','completada']; foreach ($opts as $opt): ?>
                          <option value="<?= $opt ?>" <?= $estadoNorm===$opt?'selected':'' ?>><?= ucfirst($opt) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button class="btn btn-sm btn-outline-primary" title="Actualizar estado"><i class="fas fa-save"></i></button>
                    </form>
                  </td>
                  <td class="text-center pe-3">
                    <div class="btn-group">
                      <a href="<?= BASE ?>/citas/edit/<?= $c['id'] ?>" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="<?= BASE ?>/citas/delete/<?= $c['id'] ?>" 
                         class="btn btn-sm btn-outline-danger"
                         onclick="return confirm('¿Seguro que deseas eliminar esta cita?')">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center py-4">
                  <div class="empty-state">
                    <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-2">No hay citas registradas</p>
                    <a href="<?= BASE ?>/recepcionista/citas/create" class="btn btn-success btn-sm">
                      <i class="fas fa-plus-circle me-1"></i>Crear Primera Cita
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
</div>

<style>
.citas-management-container {
  max-width: 100%;
}

.page-title {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.6rem;
}

.stat-card {
  border-radius: 8px;
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.avatar-sm {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 600;
}

.table {
  margin-bottom: 0;
}

.table th {
  border-top: none;
  font-weight: 600;
  font-size: 0.8rem;
  padding: 0.75rem 0.5rem;
  white-space: nowrap;
}

.table td {
  padding: 0.75rem 0.5rem;
  vertical-align: middle;
}

.badge.estado-completada {
  background-color: #d4edda;
  color: #155724;
}

.badge.estado-en-curso {
  background-color: #fff3cd;
  color: #856404;
}

.badge.estado-pendiente {
  background-color: #e2e3e5;
  color: #383d41;
}

.btn-group .btn {
  border-radius: 4px;
  padding: 0.25rem 0.5rem;
}

.empty-state {
  padding: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.4rem;
  }
  
  .stat-card .card-body {
    padding: 1rem;
  }
  
  .table {
    font-size: 0.85rem;
  }
  
  .avatar-sm {
    width: 24px;
    height: 24px;
    font-size: 0.65rem;
  }
}

@media (max-width: 576px) {
  .card-header {
    padding: 1rem;
  }
  
  .btn-group {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .btn-group .btn {
    border-radius: 4px !important;
  }
}
</style>
