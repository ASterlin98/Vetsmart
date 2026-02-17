<?php
// Variables esperadas: $mascotas (id, nombre), $uploads [mascota_id => [files]] y $_SESSION['mensaje'] opcional
$mensaje = $_SESSION['mensaje'] ?? null; if (isset($_SESSION['mensaje'])) unset($_SESSION['mensaje']);
?>
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1"><i class="fas fa-file-alt me-2"></i>Reportes</h2>
      <small class="text-muted">Sube documentos y descarga historial clínico por mascota</small>
    </div>
  </div>

  <?php if (!empty($mensaje)): ?>
    <div class="alert alert-<?= htmlspecialchars($mensaje['tipo']) ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($mensaje['texto']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
          <strong><i class="fas fa-upload me-2"></i>Cargar archivo</strong>
        </div>
        <div class="card-body">
          <form action="/vetsmart/recepcionista/reportes/upload" method="POST" enctype="multipart/form-data">
            <?php if (class_exists('CSRF')): ?>
              <?= CSRF::inputField() ?>
            <?php endif; ?>
            <div class="mb-3">
              <label class="form-label">Mascota</label>
              <select name="mascota_id" class="form-select" required>
                <option value="">Seleccione una mascota…</option>
                <?php foreach ($mascotas as $m): ?>
                  <option value="<?= (int)$m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Archivo</label>
              <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp,.txt" required>
              <div class="form-text">Tipos permitidos: PDF, JPG, PNG, WEBP, TXT. Máx 5MB.</div>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary"><i class="fas fa-cloud-upload-alt me-2"></i>Subir</button>
              <a href="/vetsmart/recepcionista/reportes" class="btn btn-outline-secondary">Limpiar</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
          <strong><i class="fas fa-file-pdf me-2"></i>Descargar historial clínico</strong>
        </div>
        <div class="card-body">
          <form action="#" onsubmit="event.preventDefault(); var id = this.mascota_pdf.value; if(id){ window.location='/vetsmart/reportes/mascota/'+id+'/pdf'; }">
            <div class="mb-3">
              <label class="form-label">Mascota</label>
              <select name="mascota_pdf" class="form-select" required>
                <option value="">Seleccione una mascota…</option>
                <?php foreach ($mascotas as $m): ?>
                  <option value="<?= (int)$m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-download me-2"></i>Descargar PDF</button>
            <button type="button" class="btn btn-outline-success" onclick="var id=this.form.mascota_pdf.value; if(id){ window.open('/vetsmart/reportes/mascota/'+id+'/pdf/preview','_blank');}"><i class="fas fa-eye me-2"></i>Vista previa</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
      <strong><i class="fas fa-folder-open me-2"></i>Archivos cargados por mascota</strong>
    </div>
    <div class="card-body">
      <?php if (!empty($uploads)): ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Mascota (ID)</th>
                <th>Archivos</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($uploads as $mid => $files): ?>
              <tr>
                <td><span class="badge bg-secondary">#<?= (int)$mid ?></span></td>
                <td>
                  <?php foreach ($files as $f): 
                      // Ruta relativa al document root del servidor web
                      $url = "/vetsmart/public/assets/uploads/reportes/" . $mascota['id'] . "/" . rawurlencode($f); 
                  ?>
                    <a href="<?= $url ?>" target="_blank" class="me-2"><i class="fas fa-paperclip me-1"></i><?= htmlspecialchars($f) ?></a>
                  <?php endforeach; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted mb-0">Aún no hay archivos cargados.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
