<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h4 mb-0"><i class="fas fa-id-card text-success me-2"></i>Editar Mascota</h1><a href="/vetsmart/recepcionista/mascotas" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>

<div class="row g-3 mb-3">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <div class="mb-2">
          <div style="width:160px;height:160px;border-radius:12px;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-size:48px;color:#6c757d;" class="mx-auto">
            <?= strtoupper(substr($mascota['nombre'],0,1)) ?>
          </div>
        </div>
        <div class="fw-bold fs-5 mb-1"><?= htmlspecialchars($mascota['nombre']) ?></div>
        <div class="text-muted small mb-1"><?= htmlspecialchars(($mascota['especie'] ?? '-') . ' · ' . ($mascota['raza'] ?? '-')) ?></div>
        <div class="text-muted small">Edad: <?= htmlspecialchars($mascota['edad'] ?? '-') ?> · Sexo: <?= htmlspecialchars($mascota['sexo'] ?? '-') ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
<form action="/vetsmart/recepcionista/mascotas/update" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
  <?= CSRF::inputField(); ?>
  <input type="hidden" name="id" value="<?= htmlspecialchars($mascota['id']) ?>">

  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Nombre *</label>
      <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($mascota['nombre']) ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Especie</label>
      <input type="text" name="especie" class="form-control" value="<?= htmlspecialchars($mascota['especie']) ?>">
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Raza</label>
      <input type="text" name="raza" class="form-control" value="<?= htmlspecialchars($mascota['raza']) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Edad (años)</label>
      <input type="number" name="edad" class="form-control" min="0" value="<?= htmlspecialchars($mascota['edad']) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Sexo</label>
      <select name="sexo" class="form-select">
        <option value="">Seleccionar</option>
        <option value="Macho" <?= $mascota['sexo'] === 'Macho' ? 'selected' : '' ?>>Macho</option>
        <option value="Hembra" <?= $mascota['sexo'] === 'Hembra' ? 'selected' : '' ?>>Hembra</option>
      </select>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Dueño</label>
    <select name="dueno_id" class="form-select" required>
      <option value="">Seleccione un cliente</option>
      <?php foreach ($clientes as $cli): ?>
        <option value="<?= htmlspecialchars($cli['id']) ?>" <?= $cli['id'] == $mascota['dueno_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($cli['nombre'] . ' ' . $cli['apellido']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="alert alert-info py-2 px-3"> <i class="fas fa-info-circle me-1"></i> La foto se muestra con la inicial de la mascota.</div>

  <div class="text-end">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="/vetsmart/recepcionista/mascotas" class="btn btn-secondary">Volver</a>
  </div>
  </form>
  </div>
</div>
<script>
// Asegura envío de 'dueno_id' (ASCII) junto con el select del dueño
document.addEventListener('DOMContentLoaded', function(){
  const form = document.querySelector('form[action$="/mascotas/update"]');
  if (!form) return;
  const sel = form.querySelector('select[name$="o_id"]');
  if (!sel) return;
  let hid = form.querySelector('input[name="dueno_id"]');
  if (!hid) {
    hid = document.createElement('input');
    hid.type = 'hidden';
    hid.name = 'dueno_id';
    form.appendChild(hid);
  }
  const sync = ()=> hid.value = sel.value || '';
  sel.addEventListener('change', sync);
  sync();
});
</script>
