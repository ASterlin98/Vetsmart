<?php
// $mascotas: array
?>
<div class="mascotas-container">
  <!-- Header Section -->
  <div class="header-section mb-4">
    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <div class="section-icon">
          <i class="fas fa-paw"></i>
        </div>
        <div>
          <h2 class="h4 mb-1">Mis Mascotas <span class="badge bg-primary-subtle text-dark ms-2">Total: <strong><?= (int)count($mascotas) ?></strong></span></h2>
          <p class="text-muted mb-0">Gestiona la información de tus compañeros</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if (!empty($mensaje)): ?>
    <div class="alert alert-<?= htmlspecialchars($mensaje['tipo'] ?? 'info') ?> alert-dismissible fade show" role="alert">
      <i class="fas fa-<?= ($mensaje['tipo'] ?? '') === 'success' ? 'check-circle' : 'info-circle' ?> me-2"></i>
      <?= htmlspecialchars($mensaje['texto'] ?? '') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Add Pet Card -->
  <div class="card add-pet-card border-0 shadow-sm mb-4">
    <div class="card-body" id="formNuevaMascota">
      <div class="d-flex align-items-center mb-3">
        <i class="fas fa-plus-circle text-primary me-2"></i>
        <h6 class="mb-0">Registrar nueva mascota</h6>
      </div>
      <form method="post" action="/vetsmart/cliente/mascotas/guardar" enctype="multipart/form-data" class="row g-3">
        <?= CSRF::inputField(); ?>
        <div class="col-md-3">
          <label class="form-label small">Nombre</label>
          <input class="form-control" name="nombre" placeholder="Ej: Max" required>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Especie</label>
          <input class="form-control" name="especie" placeholder="Ej: Perro">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Raza</label>
          <input class="form-control" name="raza" placeholder="Ej: Labrador">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Edad</label>
          <input class="form-control" name="edad" type="number" min="0" placeholder="Años">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Sexo</label>
          <select class="form-select" name="sexo">
            <option value="">Seleccionar</option>
            <option>Macho</option>
            <option>Hembra</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label small">Foto</label>
          <input class="form-control" type="file" name="foto" accept="image/*">
        </div>
        <div class="col-12">
          <button class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Guardar Mascota
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Pets List -->
  <div class="card pets-list-card border-0 shadow-sm">
    <div class="card-body" id="formNuevaMascota">
      <div class="table-container">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-header">
            <tr>
              <th class="ps-4">Mascota</th>
              <th>Nombre</th>
              <th>Especie</th>
              <th>Raza</th>
              <th>Edad</th>
              <th class="pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($mascotas as $m): ?>
            <tr class="pet-row">
              <td class="ps-4">
                <div class="pet-avatar">
                  <?php if (!empty($m['foto'])): ?>
                    <img src="/vetsmart/assets/uploads/mascotas/<?= htmlspecialchars($m['foto']) ?>" 
                         class="pet-image" 
                         alt="<?= htmlspecialchars($m['nombre'] ?? '') ?>">
                  <?php else: ?>
                    <div class="pet-placeholder">
                      <i class="fas fa-paw"></i>
                    </div>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div class="pet-name"><?= htmlspecialchars($m['nombre'] ?? '') ?></div>
                <?php if (isset($m['sexo'])): ?>
                  <div class="pet-sex text-muted small"><?= htmlspecialchars($m['sexo']) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <span class="species-badge"><?= htmlspecialchars($m['especie'] ?? '') ?></span>
              </td>
              <td><?= htmlspecialchars($m['raza'] ?? '') ?></td>
              <td>
                <div class="age-display">
                  <span class="age-number"><?= htmlspecialchars((string)($m['edad'] ?? '')) ?></span>
                  <span class="age-unit">años</span>
                </div>
              </td>
              <td class="pe-4">
                <div class="actions-container">
                  <!-- Edit Button opens modal -->
                  <button class="btn btn-sm btn-outline-primary" type="button" onclick="openEditModal(<?= (int)$m['id'] ?>)">
                    <i class="fas fa-edit"></i>
                  </button>

                  <!-- Delete Form -->
                  <form method="post" action="/vetsmart/cliente/mascotas/eliminar" class="d-inline" 
                        onsubmit="return confirm('¿Esta seguro de que desea eliminar a <?= htmlspecialchars(addslashes($m['nombre'] ?? '')) ?>?')">
                    <?= CSRF::inputField(); ?>
                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>

                <!-- Modal Edit Mascota -->
                <div class="pet-modal" id="editModal-<?= (int)$m['id'] ?>">
                  <div class="pet-modal__overlay" onclick="closeEditModal(<?= (int)$m['id'] ?>)"></div>
                  <div class="pet-modal__dialog">
                    <div class="pet-modal__header">
                      <h5 class="m-0"><i class="fas fa-paw me-2"></i>Editar Mascota</h5>
                      <button type="button" class="btn btn-sm btn-light" onclick="closeEditModal(<?= (int)$m['id'] ?>)">×</button>
                    </div>
                    <div class="pet-modal__body">
                      <form method="post" action="/vetsmart/cliente/mascotas/editar" enctype="multipart/form-data">
                        <?= CSRF::inputField(); ?>
                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                        <div class="row g-3">
                          <div class="col-md-4 text-center">
                            <div class="pet-photo-preview mb-2">
                              <?php if (!empty($m['foto'])): ?>
                                <img src="/vetsmart/assets/uploads/mascotas/<?= htmlspecialchars($m['foto']) ?>" alt="<?= htmlspecialchars($m['nombre'] ?? '') ?>">
                              <?php else: ?>
                                <div class="pet-placeholder large"><i class="fas fa-paw"></i></div>
                              <?php endif; ?>
                            </div>
                            <input class="form-control form-control-sm" type="file" name="foto" accept="image/*">
                          </div>
                          <div class="col-md-8">
                            <div class="row g-2">
                              <div class="col-md-6">
                                <label class="form-label small">Nombre</label>
                                <input class="form-control form-control-sm" name="nombre" value="<?= htmlspecialchars($m['nombre'] ?? '') ?>" required>
                              </div>
                              <div class="col-md-6">
                                <label class="form-label small">Especie</label>
                                <input class="form-control form-control-sm" name="especie" value="<?= htmlspecialchars($m['especie'] ?? '') ?>">
                              </div>
                              <div class="col-md-6">
                                <label class="form-label small">Raza</label>
                                <input class="form-control form-control-sm" name="raza" value="<?= htmlspecialchars($m['raza'] ?? '') ?>">
                              </div>
                              <div class="col-md-3">
                                <label class="form-label small">Edad</label>
                                <input class="form-control form-control-sm" type="number" name="edad" value="<?= htmlspecialchars((string)($m['edad'] ?? '')) ?>">
                              </div>
                              <div class="col-md-3">
                                <label class="form-label small">Sexo</label>
                                <select class="form-select form-select-sm" name="sexo">
                                  <option value="">Sexo</option>
                                  <option <?= (($m['sexo'] ?? '')==='Macho')?'selected':'' ?>>Macho</option>
                                  <option <?= (($m['sexo'] ?? '')==='Hembra')?'selected':'' ?>>Hembra</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-end gap-2">
                          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="closeEditModal(<?= (int)$m['id'] ?>)">Cancelar</button>
                          <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>

        <?php if (empty($mascotas)): ?>
          <div class="empty-state text-center py-5">
            <i class="fas fa-paw fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay mascotas registradas</h5>
            <p class="text-muted">Comienza agregando tu primera mascota.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
.mascotas-container {
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

/* Add Pet Card */
.add-pet-card {
  border-radius: 16px;
  border-left: 4px solid #06b6d4;
}

.add-pet-card .form-label {
  font-weight: 500;
  color: #475569;
}

/* Pets List Card */
.pets-list-card {
  border-radius: 16px;
}

.table-header {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 2px solid #e2e8f0;
}

.table-header th {
  border: none;
  padding: 1rem 0.75rem;
  font-weight: 600;
  color: #475569;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.pet-row {
  transition: all 0.2s ease;
  border-bottom: 1px solid #f1f5f9;
}

.pet-row:hover {
  background-color: #f8fafc;
}

.pet-avatar {
  width: 50px;
  height: 50px;
}

.pet-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
  border: 2px solid #e2e8f0;
}

.pet-placeholder {
  width: 100%;
  height: 100%;
  border-radius: 12px;
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  border: 2px dashed #cbd5e1;
}

.pet-name {
  font-weight: 600;
  color: #1e293b;
}

.pet-sex {
  font-size: 0.8rem;
  margin-top: 2px;
}

.species-badge {
  background: #f0f9ff;
  color: #0369a1;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  border: 1px solid #bae6fd;
}

.age-display {
  text-align: center;
}

.age-number {
  font-size: 1.25rem;
  font-weight: 700;
  color: #06b6d4;
  display: block;
}

.age-unit {
  font-size: 0.8rem;
  color: #64748b;
}

/* Actions */
.actions-container {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.edit-form-container {
  position: relative;
  display: inline-block;
}

.edit-form-dropdown {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  width: 400px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  padding: 1rem;
  z-index: 1000;
  margin-top: 0.5rem;
}

.edit-form-container.active .edit-form-dropdown {
  display: block;
}

/* Modal styles */
.pet-modal { display: none; }
.pet-modal.active { display: block; }
.pet-modal__overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 1060;
}
.pet-modal__dialog {
  position: fixed; z-index: 1070; top: 50%; left: 50%; transform: translate(-50%, -50%);
  width: min(720px, 92vw); background: #fff; border-radius: 12px; box-shadow: 0 12px 30px rgba(0,0,0,.2);
}
.pet-modal__header { display:flex; justify-content: space-between; align-items:center; padding: .75rem 1rem; border-bottom: 1px solid #e2e8f0; }
.pet-modal__body { padding: 1rem; }
.pet-photo-preview { width: 160px; height: 160px; border-radius: 12px; background: #f8fafc; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; overflow:hidden; margin: 0 auto; }
.pet-photo-preview img { width:100%; height:100%; object-fit: cover; }
.pet-placeholder.large { width: 120px; height: 120px; border-radius: 12px; background: #f1f5f9; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size: 2rem; }

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
}

.empty-state {
  background: #f8fafc;
  border-radius: 0 0 16px 16px;
}

/* Responsive */
@media (max-width: 768px) {
  .mascotas-container {
    padding: 1rem 0;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .edit-form-dropdown {
    width: 300px;
    right: -50px;
  }
  
  .header-section .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .section-icon {
    margin-bottom: 1rem;
  }
}

@media (max-width: 576px) {
  .edit-form-dropdown {
    width: 280px;
    right: -80px;
  }
  
  .actions-container {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .species-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Toggle edit forms
  document.querySelectorAll('.edit-toggle').forEach(button => {
    button.addEventListener('click', function() {
      const container = this.closest('.edit-form-container');
      container.classList.toggle('active');
    });
  });

  // Close edit forms when clicking cancel
  document.querySelectorAll('.cancel-edit').forEach(button => {
    button.addEventListener('click', function() {
      const container = this.closest('.edit-form-container');
      container.classList.remove('active');
    });
  });

  // Close edit forms when clicking outside
  document.addEventListener('click', function(event) {
    if (!event.target.closest('.edit-form-container')) {
      document.querySelectorAll('.edit-form-container').forEach(container => {
        container.classList.remove('active');
      });
    }
  });
});

function openEditModal(id){
  const el = document.getElementById('editModal-' + id);
  if(el){ el.classList.add('active'); }
}
function closeEditModal(id){
  const el = document.getElementById('editModal-' + id);
  if(el){ el.classList.remove('active'); }
}
</script>
