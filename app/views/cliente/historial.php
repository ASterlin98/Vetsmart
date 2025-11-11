<?php
// $registros
?>
<div class="historial-clinico">
  <!-- Header Section -->
  <div class="header-section mb-4">
    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <div class="section-icon">
          <i class="fas fa-file-medical"></i>
        </div>
        <div>
          <h2 class="h4 mb-1">Historial Clínico</h2>
          <p class="text-muted mb-0">Registro completo de atenciones veterinarias</p>
        </div>
      </div>
      <div class="header-actions">
        <a class="btn btn-primary" href="/vetsmart/cliente/historial/exportar<?= ($qs ?? '') ? ('?' . $qs) : '' ?>">
          <i class="fas fa-download me-2"></i>Exportar
        </a>
      </div>
    </div>
  </div>

  <!-- Filtros Section -->
  <div class="filters-section mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="card-title mb-3">
          <i class="fas fa-filter me-2 text-primary"></i>
          Filtros de Búsqueda
        </h6>
        <form method="get" action="/vetsmart/cliente/historial" class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label small">Desde</label>
            <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($filtros['desde'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label small">Hasta</label>
            <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($filtros['hasta'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label small">Mascota</label>
            <select name="mascota_id" class="form-select">
              <option value="0">Todas las mascotas</option>
              <?php foreach (($mascotas ?? []) as $m): ?>
                <option value="<?= (int)$m['id'] ?>" <?= ((int)($filtros['mascota_id'] ?? 0) === (int)$m['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <div class="d-flex gap-2">
              <button class="btn btn-primary w-100" type="submit">
                <i class="fas fa-search me-1"></i>Filtrar
              </button>
              <a class="btn btn-outline-secondary" href="/vetsmart/cliente/historial" title="Limpiar filtros">
                <i class="fas fa-undo"></i>
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Registros Section -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-container">
        <table class="table table-hover mb-0">
          <thead class="table-header">
            <tr>
              <th class="ps-4">
                <div class="d-flex align-items-center">
                  <i class="fas fa-calendar me-2"></i>
                  <span>Fecha</span>
                </div>
              </th>
              <th>
                <div class="d-flex align-items-center">
                  <i class="fas fa-paw me-2"></i>
                  <span>Mascota</span>
                </div>
              </th>
              <th>
                <div class="d-flex align-items-center">
                  <i class="fas fa-user-md me-2"></i>
                  <span>Veterinario</span>
                </div>
              </th>
              <th>
                <div class="d-flex align-items-center">
                  <i class="fas fa-stethoscope me-2"></i>
                  <span>Diagnóstico</span>
                </div>
              </th>
              <th class="pe-4">
                <div class="d-flex align-items-center">
                  <i class="fas fa-pills me-2"></i>
                  <span>Tratamiento</span>
                </div>
              </th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($registros as $r): ?>
            <tr class="table-row">
              <td class="ps-4">
                <div class="date-cell">
                  <div class="date-main"><?= htmlspecialchars($r['creado_en'] ?? '') ?></div>
                  <?php if (isset($r['creado_en'])): ?>
                    <div class="date-time text-muted"><?= date('H:i', strtotime($r['creado_en'])) ?></div>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div class="pet-cell">
                  <div class="pet-name"><?= htmlspecialchars($r['mascota'] ?? '-') ?></div>
                  <?php if (isset($r['especie'])): ?>
                    <div class="pet-species text-muted"><?= htmlspecialchars($r['especie']) ?></div>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div class="vet-cell">
                  <div class="vet-name"><?= htmlspecialchars($r['veterinario'] ?? '-') ?></div>
                  <?php if (isset($r['especialidad'])): ?>
                    <div class="vet-specialty text-muted"><?= htmlspecialchars($r['especialidad']) ?></div>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div class="diagnosis-cell">
                  <?= htmlspecialchars($r['diagnostico'] ?? '-') ?>
                </div>
              </td>
              <td class="pe-4">
                <div class="treatment-cell">
                  <?= htmlspecialchars($r['tratamiento'] ?? '-') ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        
        <?php if (empty($registros)): ?>
          <div class="empty-state text-center py-5">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay registros clínicos</h5>
            <p class="text-muted">No se encontraron historiales médicos para mostrar.</p>
            <?php if (isset($filtros) && array_filter($filtros)): ?>
              <a href="/vetsmart/cliente/historial" class="btn btn-primary mt-2">
                <i class="fas fa-undo me-2"></i>Limpiar filtros
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
.historial-clinico {
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

.header-section h2 {
  color: #1e293b;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.header-actions .btn {
  border-radius: 8px;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
}

/* Filters Section */
.filters-section .card {
  border-radius: 12px;
  border-left: 4px solid #06b6d4;
}

.filters-section .card-title {
  color: #1e293b;
  font-weight: 600;
  font-size: 1rem;
}

.filters-section .form-label {
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.5rem;
}

.filters-section .form-control,
.filters-section .form-select {
  border-radius: 8px;
  border: 1px solid #d1d5db;
}

/* Table Styles */
.card {
  border-radius: 16px;
  overflow: hidden;
}

.table-container {
  border-radius: 16px;
  overflow: hidden;
}

.table-header {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 2px solid #e2e8f0;
}

.table-header th {
  border: none;
  padding: 1.25rem 0.75rem;
  font-weight: 600;
  color: #475569;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.table-header th:first-child {
  padding-left: 1.5rem;
}

.table-header th:last-child {
  padding-right: 1.5rem;
}

.table-header i {
  color: #06b6d4;
  font-size: 0.8rem;
}

.table-row {
  transition: all 0.2s ease;
  border-bottom: 1px solid #f1f5f9;
}

.table-row:hover {
  background-color: #f8fafc;
}

.table-row td {
  padding: 1.25rem 0.75rem;
  vertical-align: middle;
  border: none;
  font-size: 0.9rem;
}

.table-row td:first-child {
  padding-left: 1.5rem;
}

.table-row td:last-child {
  padding-right: 1.5rem;
}

/* Cell Styles */
.date-cell .date-main {
  font-weight: 500;
  color: #1e293b;
  font-size: 0.95rem;
}

.date-cell .date-time {
  font-size: 0.8rem;
  color: #64748b;
  margin-top: 2px;
}

.pet-cell .pet-name {
  font-weight: 500;
  color: #1e293b;
  font-size: 0.95rem;
}

.pet-cell .pet-species {
  font-size: 0.8rem;
  color: #64748b;
  margin-top: 2px;
}

.vet-cell .vet-name {
  font-weight: 500;
  color: #1e293b;
  font-size: 0.95rem;
}

.vet-cell .vet-specialty {
  font-size: 0.8rem;
  color: #64748b;
  margin-top: 2px;
}

.diagnosis-cell {
  color: #475569;
  line-height: 1.4;
  max-width: 250px;
}

.treatment-cell {
  color: #475569;
  line-height: 1.4;
  max-width: 250px;
}

/* Empty State */
.empty-state {
  background: #f8fafc;
  border-radius: 0 0 16px 16px;
  padding: 3rem 2rem;
}

.empty-state i {
  opacity: 0.5;
}

.empty-state h5 {
  margin-bottom: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .historial-clinico {
    padding: 1rem 0;
  }
  
  .header-section .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 1rem;
  }
  
  .header-actions {
    width: 100%;
  }
  
  .header-actions .btn {
    width: 100%;
  }
  
  .filters-section .row {
    gap: 1rem;
  }
  
  .filters-section .col-md-3,
  .filters-section .col-md-4 {
    width: 100%;
  }
  
  .table-header th {
    padding: 1rem 0.5rem;
    font-size: 0.8rem;
  }
  
  .table-row td {
    padding: 1rem 0.5rem;
    font-size: 0.85rem;
  }
  
  .diagnosis-cell,
  .treatment-cell {
    max-width: 150px;
  }
}

@media (max-width: 576px) {
  .section-icon {
    width: 40px;
    height: 40px;
    font-size: 1rem;
    margin-right: 0.75rem;
  }
  
  .header-section h2 {
    font-size: 1.25rem;
  }
  
  .table-header th span {
    display: none;
  }
  
  .table-header th i {
    margin-right: 0 !important;
    font-size: 1rem;
  }
  
  .date-cell .date-time,
  .pet-cell .pet-species,
  .vet-cell .vet-specialty {
    display: none;
  }
  
  .empty-state {
    padding: 2rem 1rem;
  }
  
  .diagnosis-cell,
  .treatment-cell {
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
}

/* Print Styles */
@media print {
  .header-actions,
  .filters-section,
  .btn {
    display: none !important;
  }
  
  .historial-clinico {
    padding: 0 !important;
  }
  
  .card {
    box-shadow: none !important;
    border: 1px solid #dee2e6 !important;
  }
  
  .table-row:hover {
    background-color: transparent !important;
  }
}
</style>

<script>
// Mejoras de interactividad
document.addEventListener('DOMContentLoaded', function() {
  // Auto-submit form cuando cambian las fechas (opcional)
  const dateInputs = document.querySelectorAll('input[type="date"]');
  dateInputs.forEach(input => {
    input.addEventListener('change', function() {
      // Opcional: auto-submit después de 1 segundo
      setTimeout(() => {
        this.form.submit();
      }, 1000);
    });
  });
  
  // Mejorar experiencia en móviles
  if (window.innerWidth <= 768) {
    const tableCells = document.querySelectorAll('.diagnosis-cell, .treatment-cell');
    tableCells.forEach(cell => {
      cell.title = cell.textContent;
    });
  }
});
</script>