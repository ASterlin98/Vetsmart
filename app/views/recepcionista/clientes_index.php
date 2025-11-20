<div class="client-management-container">
  <!-- Header Section -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="mb-3 mb-md-0">
      <h1 class="page-title mb-2">
        <i class="fas fa-users me-2 text-success"></i>Gestión de Clientes
      </h1>
      <p class="text-muted mb-0">Administre la información de todos los clientes registrados</p>
    </div>
    
    <div class="d-flex gap-2">
      <div class="input-group input-group-sm" style="width: 200px;">
        <input type="text" class="form-control" placeholder="Buscar cliente...">
        <button class="btn btn-outline-secondary" type="button">
          <i class="fas fa-search"></i>
        </button>
      </div>
      <a href="/vetsmart/recepcionista/clientes/create" class="btn btn-success">
        <i class="fas fa-user-plus me-1"></i>Nuevo Cliente
      </a>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
      <i class="fas fa-check-circle me-2"></i>
      <?= htmlspecialchars($_SESSION['success']) ?>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php elseif (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <?= htmlspecialchars($_SESSION['error']) ?>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <!-- Clients Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-4">Cliente</th>
              <th>Contacto</th>
              <th>Dirección</th>
              <th>Registro</th>
              <th class="text-center pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($clientes)): ?>
              <?php foreach ($clientes as $cli): ?>
                <tr class="client-row">
                  <td class="ps-4">
                    <div class="d-flex align-items-center">
                      <?php
                        $foto = $cli['foto'] ?? '';
                        $rutaRel = 'assets/uploads/clientes/' . $foto;
                        $tieneFoto = !empty($foto) && file_exists(__DIR__ . '/../../../public/' . $rutaRel);  
                        $initials = strtoupper(substr((string)($cli['nombre'] ?? ''),0,1) . substr((string)($cli['apellido'] ?? ''),0,1));
                      ?>
                      <?php
                        // Mostrar SIEMPRE avatar de iniciales como foto de perfil
                        $ini = $initials ?: 'U';
                      ?>
                      <div class="avatar-circle bg-primary text-white me-3" style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;">
                        <?= htmlspecialchars($ini) ?>
                      </div>
                      <div>
                        <div class="fw-medium"><?= htmlspecialchars($cli['nombre'] ?? '-') ?> <?= htmlspecialchars($cli['apellido'] ?? '-') ?></div>
                        <small class="text-muted"><?= htmlspecialchars($cli['email'] ?? '-') ?></small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <?php if (!empty($cli['telefono'])): ?>
                      <div class="d-flex align-items-center text-nowrap">
                        <i class="fas fa-phone text-muted me-2"></i>
                        <span class="small"><?= htmlspecialchars($cli['telefono']) ?></span>
                      </div>
                    <?php else: ?>
                      <span class="text-muted small">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($cli['direccion'])): ?>
                      <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        <span class="small text-truncate" style="max-width: 150px;"><?= htmlspecialchars($cli['direccion']) ?></span>
                      </div>
                    <?php else: ?>
                      <span class="text-muted small">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark small">
                      <?= htmlspecialchars($cli['created_at'] ?? '-') ?>
                    </span>
                  </td>
                  <td class="text-center pe-4">
                    <div class="btn-group btn-group-sm" role="group">
                      <a href="/vetsmart/recepcionista/clientes/ver/<?= $cli['id'] ?>" 
                         class="btn btn-outline-secondary btn-action" title="Ver perfil">
                        <i class="fas fa-id-badge"></i>
                      </a>
                      <a href="/vetsmart/recepcionista/clientes/edit/<?= $cli['id'] ?>" 
                         class="btn btn-outline-primary btn-action"
                         data-bs-toggle="tooltip" 
                         title="Editar cliente">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="/vetsmart/recepcionista/clientes/<?= $cli['id'] ?>/eliminar" 
                         class="btn btn-outline-danger btn-action"
                         onclick="return confirm('¿Está seguro de eliminar este cliente y todos sus datos asociados?')"
                         data-bs-toggle="tooltip" 
                         title="Eliminar cliente">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center py-5">
                  <div class="empty-state">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay clientes registrados</h5>
                    <p class="text-muted mb-3">Comience agregando el primer cliente al sistema.</p>
                    <a href="/vetsmart/recepcionista/clientes/create" class="btn btn-success">
                      <i class="fas fa-user-plus me-2"></i>Registrar Primer Cliente
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

  <!-- Summary Footer / Pagination -->
  <div class="d-flex justify-content-between align-items-center mt-3">
    <?php
      $page = $page ?? 1;
      $perPage = $perPage ?? count($clientes);
      $total = $total ?? count($clientes);
      $totalPages = $totalPages ?? 1;
      $start = ($page - 1) * $perPage + 1;
      $end = min($start + count($clientes) - 1, $total);
    ?>
    <div class="text-muted small">
      Mostrando <strong><?= $start ?></strong> - <strong><?= $end ?></strong> de <strong><?= $total ?></strong>
    </div>

    <?php if ($totalPages > 1): ?>
      <nav aria-label="Paginación clientes">
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">Anterior</a>
          </li>
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= min($totalPages, $page + 1) ?>">Siguiente</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</div>

<style>
.client-management-container {
  max-width: 100%;
}

.page-title {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.6rem;
}

.avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.9rem;
}

.table th {
  border-top: none;
  font-weight: 600;
  font-size: 0.8rem;
  padding: 1rem 0.75rem;
  background-color: #f8f9fa;
}

.table td {
  padding: 1rem 0.75rem;
  vertical-align: middle;
}

.client-row {
  transition: background-color 0.2s ease;
  border-bottom: 1px solid #dee2e6;
}

.client-row:hover {
  background-color: #f8f9fa;
}

.client-row:last-child {
  border-bottom: none;
}

.btn-action {
  border-radius: 6px;
  padding: 0.375rem 0.75rem;
  transition: all 0.2s ease;
  border: 1px solid #dee2e6;
}

.btn-action:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group .btn-action {
  border-radius: 0;
  margin: 0 -1px;
}

.btn-group .btn-action:first-child {
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

.btn-group .btn-action:last-child {
  border-top-right-radius: 4px;
  border-bottom-right-radius: 4px;
}

.empty-state {
  padding: 3rem 1rem;
}

.alert {
  border: none;
  border-radius: 8px;
}

.input-group {
  border-radius: 6px;
}

.input-group .form-control {
  border-radius: 6px 0 0 6px;
}

.input-group .btn {
  border-radius: 0 6px 6px 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.4rem;
  }
  
  .table {
    font-size: 0.85rem;
  }
  
  .avatar-circle {
    width: 32px;
    height: 32px;
    font-size: 0.8rem;
  }
  
  .btn-group .btn-action {
    padding: 0.25rem 0.5rem;
  }
  
  .d-flex.flex-md-row {
    flex-direction: column;
  }
  
  .d-flex.gap-2 {
    width: 100%;
    justify-content: space-between;
  }
  
  .input-group {
    width: 100% !important;
    margin-bottom: 0.5rem;
  }
}

@media (max-width: 576px) {
  .client-management-container {
    padding: 0.5rem;
  }
  
  .btn-group {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .btn-group .btn-action {
    border-radius: 4px !important;
    margin: 0;
  }
  
  .table-responsive {
    font-size: 0.8rem;
  }
  
  .avatar-circle {
    width: 28px;
    height: 28px;
    font-size: 0.7rem;
    margin-right: 0.5rem !important;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });

  // Enhanced delete confirmation
  const deleteButtons = document.querySelectorAll('a[href*="/eliminar"]');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      if (!confirm('¿Está completamente seguro de eliminar este cliente?\n\nEsta acción eliminará:\n• Información del cliente\n• Mascotas asociadas\n• Historial de citas\n\nEsta acción no se puede deshacer.')) {
        e.preventDefault();
      }
    });
  });

  // Simple search functionality
  const searchInput = document.querySelector('input[type="text"]');
  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('.client-row');
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }
});
</script>
