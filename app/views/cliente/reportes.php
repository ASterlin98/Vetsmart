<?php
// $cliente, $mascotas, $citas, $historial
?>
<div class="reporte-container">
  <!-- Header Section -->
  <div class="header-section mb-4">
    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <div class="section-icon">
          <i class="fas fa-chart-bar"></i>
        </div>
        <div>
          <h2 class="h4 mb-1">Reporte General</h2>
          <p class="text-muted mb-0">Resumen completo de tu información en VetSmart</p>
        </div>
      </div>
      <div class="header-actions d-flex gap-2">
        <a class="btn btn-primary" href="/vetsmart/reportes/cliente/<?= (int)($cliente['id'] ?? 0) ?>/pdf">
          <i class="fas fa-download me-2"></i>Descargar PDF
        </a>
        <a class="btn btn-outline-primary" href="/vetsmart/reportes/cliente/<?= (int)($cliente['id'] ?? 0) ?>/pdf/preview" target="_blank" rel="noopener">
          <i class="fas fa-print me-2"></i>Imprimir
        </a>
      </div>
    </div>
  </div>

  <!-- Client Information -->
  <div class="card reporte-card border-0 shadow-sm mb-4">
    <div class="card-header">
      <div class="d-flex align-items-center">
        <div class="card-icon">
          <i class="fas fa-user"></i>
        </div>
        <div>
          <h5 class="mb-0">Datos del Cliente</h5>
          <small class="text-muted">Información personal registrada</small>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="info-item">
            <div class="info-label">
              <i class="fas fa-signature me-2"></i>Nombre completo
            </div>
            <div class="info-value"><?= htmlspecialchars(($cliente['nombre'] ?? '').' '.($cliente['apellido'] ?? '')) ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">
              <i class="fas fa-envelope me-2"></i>Correo electrónico
            </div>
            <div class="info-value"><?= htmlspecialchars($cliente['email'] ?? '') ?></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-item">
            <div class="info-label">
              <i class="fas fa-phone me-2"></i>Teléfono
            </div>
            <div class="info-value"><?= htmlspecialchars($cliente['telefono'] ?? 'No registrado') ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">
              <i class="fas fa-map-marker-alt me-2"></i>Dirección
            </div>
            <div class="info-value"><?= htmlspecialchars($cliente['direccion'] ?? 'No registrada') ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mascotas Section -->
  <div class="card reporte-card border-0 shadow-sm mb-4">
    <div class="card-header">
      <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="fas fa-paw"></i>
          </div>
          <div>
            <h5 class="mb-0">Mis Mascotas</h5>
            <small class="text-muted">Compañeros registrados en el sistema</small>
          </div>
        </div>
        <span class="badge bg-primary"><?= count($mascotas) ?></span>
      </div>
    </div>
    <div class="card-body">
      <?php if (empty($mascotas)): ?>
        <div class="empty-state text-center py-4">
          <i class="fas fa-paw fa-2x text-muted mb-3"></i>
          <h6 class="text-muted">No hay mascotas registradas</h6>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-header">
              <tr>
                <th class="ps-4">Nombre</th>
                <th>Especie</th>
                <th>Raza</th>
                <th>Edad</th>
                <th class="pe-4">Sexo</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($mascotas as $m): ?>
              <tr class="table-row">
                <td class="ps-4">
                  <div class="pet-name"><?= htmlspecialchars($m['nombre'] ?? '') ?></div>
                </td>
                <td>
                  <span class="species-badge"><?= htmlspecialchars($m['especie'] ?? '') ?></span>
                </td>
                <td><?= htmlspecialchars($m['raza'] ?? 'No especificada') ?></td>
                <td>
                  <div class="age-display">
                    <span class="age-number"><?= htmlspecialchars((string)($m['edad'] ?? '')) ?></span>
                    <span class="age-unit">años</span>
                  </div>
                </td>
                <td class="pe-4">
                  <span class="gender-badge <?= ($m['sexo'] ?? '') === 'Macho' ? 'gender-male' : 'gender-female' ?>">
                    <?= htmlspecialchars($m['sexo'] ?? 'No especificado') ?>
                  </span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Citas Section -->
  <div class="card reporte-card border-0 shadow-sm mb-4">
    <div class="card-header">
      <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div>
            <h5 class="mb-0">Historial de Citas</h5>
            <small class="text-muted">Registro de visitas programadas</small>
          </div>
        </div>
        <span class="badge bg-info"><?= count($citas) ?></span>
      </div>
    </div>
    <div class="card-body">
      <?php if (empty($citas)): ?>
        <div class="empty-state text-center py-4">
          <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
          <h6 class="text-muted">No hay citas registradas</h6>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-header">
              <tr>
                <th class="ps-4">Fecha</th>
                <th>Mascota</th>
                <th>Servicio</th>
                <th class="pe-4">Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($citas as $c): ?>
              <tr class="table-row">
                <td class="ps-4">
                  <div class="date-cell">
                    <div class="date-main"><?= htmlspecialchars($c['fecha'] ?? '') ?></div>
                  </div>
                </td>
                <td><?= htmlspecialchars($c['mascota'] ?? 'No especificada') ?></td>
                <td><?= htmlspecialchars($c['servicio'] ?? 'No especificado') ?></td>
                <td class="pe-4">
                  <span class="status-badge status-<?= strtolower($c['estado'] ?? 'pendiente') ?>">
                    <?= htmlspecialchars($c['estado'] ?? 'Pendiente') ?>
                  </span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Historial Clínico Section -->
  <div class="card reporte-card border-0 shadow-sm mb-4">
    <div class="card-header">
      <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="fas fa-file-medical"></i>
          </div>
          <div>
            <h5 class="mb-0">Historial Clínico</h5>
            <small class="text-muted">Registro médico de tus mascotas</small>
          </div>
        </div>
        <span class="badge bg-success"><?= count($historial) ?></span>
      </div>
    </div>
    <div class="card-body">
      <?php if (empty($historial)): ?>
        <div class="empty-state text-center py-4">
          <i class="fas fa-file-medical fa-2x text-muted mb-3"></i>
          <h6 class="text-muted">No hay registros médicos</h6>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-header">
              <tr>
                <th class="ps-4">Fecha</th>
                <th>Mascota</th>
                <th>Diagnóstico</th>
                <th class="pe-4">Tratamiento</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($historial as $h): ?>
              <tr class="table-row">
                <td class="ps-4">
                  <div class="date-cell">
                    <div class="date-main"><?= htmlspecialchars($h['creado_en'] ?? '') ?></div>
                  </div>
                </td>
                <td><?= htmlspecialchars($h['mascota'] ?? 'No especificada') ?></td>
                <td>
                  <div class="diagnosis-text"><?= htmlspecialchars($h['diagnostico'] ?? 'No registrado') ?></div>
                </td>
                <td class="pe-4">
                  <div class="treatment-text"><?= htmlspecialchars($h['tratamiento'] ?? 'No registrado') ?></div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer -->
  <div class="report-footer text-center text-muted mt-4 pt-4 border-top">
    <p class="mb-1">Reporte generado el <?= date('d/m/Y \a \l\a\s H:i') ?></p>
    <small>VetSmart - Sistema de Gestión Veterinaria</small>
  </div>
</div>

<style>
.reporte-container {
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

/* Report Cards */
.reporte-card {
  border-radius: 16px;
  border-left: 4px solid #06b6d4;
}

.card-header {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
  border-bottom: 1px solid #e2e8f0;
  padding: 1.25rem 1.5rem;
}

.card-header .card-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: rgba(6, 182, 212, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  color: #06b6d4;
  font-size: 1rem;
}

.card-body {
  padding: 1.5rem;
}

/* Client Information */
.info-item {
  margin-bottom: 1rem;
}

.info-label {
  font-weight: 500;
  color: #64748b;
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.info-value {
  color: #1e293b;
  font-weight: 500;
  font-size: 1rem;
}

/* Tables */
.table-container {
  border-radius: 12px;
  overflow: hidden;
}

.table-header {
  background: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
}

.table-header th {
  border: none;
  padding: 1rem 0.75rem;
  font-weight: 600;
  color: #475569;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.table-row {
  transition: background-color 0.2s ease;
  border-bottom: 1px solid #f1f5f9;
}

.table-row:hover {
  background-color: #f8fafc;
}

.table td {
  padding: 1rem 0.75rem;
  border: none;
  vertical-align: middle;
}

/* Badges and Status */
.species-badge {
  background: #f0f9ff;
  color: #0369a1;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  border: 1px solid #bae6fd;
}

.gender-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

.gender-male {
  background: #dbeafe;
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.gender-female {
  background: #fce7f3;
  color: #be185d;
  border: 1px solid #f9a8d4;
}

.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-completada {
  background: #dcfce7;
  color: #166534;
  border: 1px solid #bbf7d0;
}

.status-pendiente {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
}

.status-cancelada {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

/* Age Display */
.age-display {
  text-align: center;
}

.age-number {
  font-size: 1.1rem;
  font-weight: 700;
  color: #06b6d4;
  display: block;
}

.age-unit {
  font-size: 0.75rem;
  color: #64748b;
}

/* Text Display */
.diagnosis-text,
.treatment-text {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Empty States */
.empty-state {
  padding: 2rem 1rem;
}

.empty-state i {
  opacity: 0.5;
}

/* Report Footer */
.report-footer {
  padding: 1rem;
}

/* Print Styles */
@media print {
  .sidebar, .header, footer, .header-actions, .btn {
    display: none !important;
  }
  
  .reporte-container {
    padding: 0 !important;
    margin: 0 !important;
  }
  
  .card {
    box-shadow: none !important;
    border: 1px solid #dee2e6 !important;
    margin-bottom: 1rem !important;
    page-break-inside: avoid;
  }
  
  .table-row:hover {
    background-color: transparent !important;
  }
}

/* Responsive */
@media (max-width: 768px) {
  .reporte-container {
    padding: 1rem 0;
  }
  
  .header-section .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .header-actions {
    margin-top: 1rem;
    width: 100%;
  }
  
  .header-actions .btn {
    width: 100%;
    margin-bottom: 0.5rem;
  }
  
  .table td {
    padding: 0.75rem 0.5rem;
  }
}

@media (max-width: 576px) {
  .card-body {
    padding: 1rem;
  }
  
  .diagnosis-text,
  .treatment-text {
    max-width: 120px;
  }
}
</style>

<!-- Estilos de impresión para reporte profesional -->
<style>
@media print {
  .header, .sidebar, footer, .header-actions, .btn, a.btn { display:none !important; }
  .content { padding: 0 !important; }
  body { background:#fff; }
  .reporte-container { padding: 0 !important; }
  .reporte-card { border: none; page-break-inside: avoid; }
  .reporte-card .card-header { background: #fff !important; border-bottom: 1px solid #e5e7eb; }
  .table-header { background: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  table { border-collapse: collapse; width: 100%; }
  table thead th { border-bottom: 1px solid #e5e7eb; }
  table tbody td { border-bottom: 1px solid #eef2f7; }
}
</style>
