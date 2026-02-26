<div class="pets-management-container">
  <!-- Header Section -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="mb-3 mb-md-0">
      <h1 class="page-title mb-2">
        <i class="fas fa-paw me-2 text-gray-600"></i>Gestión de Mascotas
      </h1>
      <p class="text-muted mb-0">Administre todas las mascotas registradas en el sistema</p>
    </div>
    
    <div class="d-flex gap-2">
      <div class="input-group input-group-sm" style="width: 200px;">
        <input type="text" class="form-control" placeholder="Buscar mascota..." id="searchInput">
        <button class="btn btn-outline-secondary" type="button">
          <i class="fas fa-search"></i>
        </button>
      </div>
      <a href="/vetsmart/recepcionista/mascotas/create" class="btn btn-success">
        <i class="fas fa-plus-circle me-2"></i>Nueva Mascota
      </a>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if (!empty($mensaje)): ?>
    <div class="alert alert-<?= htmlspecialchars($mensaje['tipo']) ?> alert-dismissible fade show d-flex align-items-center" role="alert">
      <i class="fas fa-<?= $mensaje['tipo'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
      <?= htmlspecialchars($mensaje['texto']) ?>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>

  <!-- Pets Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-4 text-dark">
                <i class="fas fa-paw me-1 text-gray-600"></i>Mascota
              </th>
              <th class="text-dark">
                <i class="fas fa-dna me-1 text-gray-600"></i>Especie
              </th>
              <th class="text-dark">Raza</th>
              <th class="text-dark">
                <i class="fas fa-birthday-cake me-1 text-gray-600"></i>Edad
              </th>
              <th class="text-dark">Sexo</th>
              <th class="text-dark">
                <i class="fas fa-user me-1 text-gray-600"></i>Dueño
              </th>
              <th class="text-center pe-4 text-dark">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($mascotas)): ?>
              <?php foreach ($mascotas as $m): ?>
                <tr class="pet-row" data-species="<?= htmlspecialchars($m['especie']) ?>">
                  <td class="ps-4">
                    <div class="d-flex align-items-center">
                      <?php 
                        $foto = $m['foto'] ?? null;
                        $initial = strtoupper(substr((string)($m['nombre'] ?? ''),0,1)); 
                      ?>
                      <?php if (!empty($foto)): ?>
                        <div class="pet-avatar me-3" title="<?= htmlspecialchars($m['nombre']) ?>">
                            <img src="/vetsmart/public/assets/uploads/mascotas/<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($m['nombre']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        </div>
                      <?php else: ?>
                        <div class="pet-avatar me-3" title="<?= htmlspecialchars($m['nombre']) ?>" style="background-color:#e5e7eb;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:#6b7280;font-weight:600;">
                            <?= htmlspecialchars($initial ?: 'M') ?>
                        </div>
                      <?php endif; ?>
                      <div>
                        <div class="fw-medium text-dark"><?= htmlspecialchars($m['nombre']) ?></div>
                        <?php if (!empty($m['color'])): ?>
                          <small class="text-muted"><?= htmlspecialchars($m['color']) ?></small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td>
                    <?php $spec_class = preg_replace('/[^a-z0-9]+/','-',strtolower((string)($m['especie'] ?? 'otro'))); ?>
                    <span class="badge species-badge species-<?= htmlspecialchars($spec_class) ?>">
                      <?= htmlspecialchars($m['especie'] ?? 'Otro') ?>
                    </span>
                  </td>
                  <td>
                    <?php if (!empty($m['raza'])): ?>
                      <span class="text-dark"><?= htmlspecialchars($m['raza']) ?></span>
                    <?php else: ?>
                      <span class="text-muted">No especificada</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">
                      <?= ($m['edad'] !== null && $m['edad'] !== '') ? htmlspecialchars($m['edad']) . ' años' : '<span class="text-muted">-</span>' ?>
                    </span>
                  </td>
                  <td>
                    <?php $sexoRaw = strtolower(trim((string)($m['sexo'] ?? ''))); ?>
                    <?php if ($sexoRaw === 'macho' || $sexoRaw === 'm'): ?>
                      <span class="badge gender-badge gender-macho"><i class="fas fa-mars me-1"></i>Macho</span>
                    <?php elseif ($sexoRaw === 'hembra' || $sexoRaw === 'h' || $sexoRaw === 'f'): ?>
                      <span class="badge gender-badge gender-hembra"><i class="fas fa-venus me-1"></i>Hembra</span>
                    <?php else: ?>
                      <span class="badge bg-light text-muted">No especificado</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($m['dueno_nombre'])): ?>
                      <div class="d-flex align-items-center">
                        <div class="avatar-circle-sm bg-gray-200 text-gray-700 me-2">
                          <?= strtoupper(substr($m['dueno_nombre'], 0, 1) . substr($m['dueno_apellido'], 0, 1)) ?>
                        </div>
                        <div class="small text-dark">
                          <?= htmlspecialchars($m['dueno_nombre'] . ' ' . $m['dueno_apellido']) ?>
                        </div>
                      </div>
                    <?php else: ?>
                      <span class="text-muted small">Sin dueno</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center pe-4">
                    <div class="btn-group btn-group-sm" role="group">
                      <a href="/vetsmart/recepcionista/mascotas/<?= $m['id'] ?>/historial" 
                         class="btn btn-outline-info btn-action"
                         data-bs-toggle="tooltip" 
                         title="Ver historial clinico">
                        <i class="fas fa-notes-medical"></i>
                      </a>
                      <a href="/vetsmart/recepcionista/mascotas/edit/<?= $m['id'] ?>" 
                         class="btn btn-outline-warning btn-action"
                         data-bs-toggle="tooltip" 
                         title="Editar mascota">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="/vetsmart/recepcionista/mascotas/delete/<?= $m['id'] ?>" 
                         class="btn btn-outline-danger btn-action"
                         onclick="return confirm('Esta seguro de eliminar esta mascota? Se perdera todos sus datos.')"
                         data-bs-toggle="tooltip" 
                         title="Eliminar mascota">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center py-5">
                  <div class="empty-state">
                    <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay mascotas registradas</h5>
                    <p class="text-muted mb-3">Comience agregando la primera mascota al sistema.</p>
                    <a href="/vetsmart/recepcionista/mascotas/create" class="btn btn-success">
                      <i class="fas fa-plus-circle me-2"></i>Registrar Primera Mascota
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
      $perPage = $perPage ?? count($mascotas);
      $total = $total ?? count($mascotas);
      $totalPages = $totalPages ?? 1;
      $start = ($page - 1) * $perPage + 1;
      $end = min($start + count($mascotas) - 1, $total);
    ?>
    <div class="text-muted small">
      Mostrando <strong><?= $start ?></strong> - <strong><?= $end ?></strong> de <strong><?= $total ?></strong>
    </div>
    <?php if ($totalPages > 1): ?>
      <nav aria-label="Paginación mascotas">
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
.pets-management-container {
  max-width: 100%;
}

.page-title {
  color: #374151;
  font-weight: 700;
  font-size: 1.6rem;
}

/* Neutral Color Scheme */
.bg-gray-200 {
  background-color: #e9ecef !important;
}

.text-gray-600 {
  color: #6b7280 !important;
}

.text-gray-700 {
  color: #374151 !important;
}

/* Pet Avatar */
.pet-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  font-size: 1.1rem;
}

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

/* Species Badges - Neutral Colors */
.species-badge {
  font-size: 0.75rem;
  padding: 0.35rem 0.7rem;
  border-radius: 6px;
  font-weight: 500;
  border: 1px solid;
}

.species-canino {
  background-color: #fef3c7;
  border-color: #f59e0b;
  color: #92400e;
}

.species-felino {
  background-color: #dbeafe;
  border-color: #3b82f6;
  color: #1e40af;
}

.species-ave,
.species-roedor,
.species-reptil,
.species-otro {
  background-color: #f3e8ff;
  border-color: #8b5cf6;
  color: #6b21a8;
}

/* Gender Badges - Neutral Colors */
.gender-badge {
  font-size: 0.75rem;
  padding: 0.35rem 0.7rem;
  border-radius: 6px;
  font-weight: 500;
  border: 1px solid;
}

.gender-macho {
  background-color: #dbeafe;
  border-color: #3b82f6;
  color: #1e40af;
}

.gender-hembra {
  background-color: #fce7f3;
  border-color: #ec4899;
  color: #be185d;
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
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background-color: #f8f9fa;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.table td {
  padding: 1rem 0.75rem;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
}

.pet-row {
  transition: background-color 0.2s ease;
}

.pet-row:hover {
  background-color: #f8fafc;
}

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
  
  .pet-avatar {
    width: 32px;
    height: 32px;
    font-size: 1rem;
    margin-right: 0.75rem;
  }
  
  .avatar-circle-sm {
    width: 28px;
    height: 28px;
    font-size: 0.7rem;
  }
  
  .btn-group .btn-action {
    padding: 0.25rem 0.5rem;
  }
}

@media (max-width: 576px) {
  .pets-management-container {
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
  
  .d-flex.flex-md-row {
    flex-direction: column;
  }
  
  .input-group {
    width: 100% !important;
    margin-bottom: 0.5rem;
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

  // Search functionality
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('.pet-row');
      
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

  // Enhanced delete confirmation
  const deleteButtons = document.querySelectorAll('a[href*="/mascotas/delete/"]');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      const petName = this.closest('.pet-row').querySelector('.fw-medium').textContent;
      if (!confirm(`??Esta completamente seguro de eliminar a "${petName}"?\n\nEsta accion eliminara:\n La Informaci??n de la mascota\nHistorial medico\nCitas asociadas\n\nEsta acci??n no se puede deshacer.`)) {
        e.preventDefault();
      }
    });
  });
});
</script>
