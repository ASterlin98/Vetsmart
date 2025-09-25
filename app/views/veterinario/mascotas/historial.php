<?php
// app/views/veterinario/mascotas/historial.php (versión visual mejorada)
$mascota = $mascota ?? [];
$citas = $citas ?? [];
$notasRapidas = $notasRapidas ?? [];

$ownerNombre = $mascota['nombre_dueno'] ?? '';
$ownerApellido = $mascota['apellido_dueno'] ?? '';
$ownerFull = trim("$ownerNombre $ownerApellido");
$ownerTelefono = $mascota['telefono_dueno'] ?? '';
$ownerEmail = $mascota['email_dueno'] ?? '';
$foto = $mascota['foto'] ?? null;

$basePublic = '/vetsmart';
$fotoDb = $foto ?? null;
$fotoUrl = null;
$fotoFsExists = false;
if (!empty($fotoDb)) {
    if (strpos($fotoDb, $basePublic) === 0) {
        $fotoUrl = $fotoDb;
        $relPath = substr($fotoDb, strlen($basePublic));
    } else {
        $fotoUrl = $basePublic . '/' . ltrim($fotoDb, '/');
        $relPath = '/' . ltrim($fotoDb, '/');
    }

    $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
    $fsPath = realpath($docRoot . $relPath);
    if ($fsPath && is_file($fsPath)) {
        $fotoFsExists = true;
    } else {
        $try = realpath(__DIR__ . '/../../../public' . $relPath);
        if ($try && is_file($try)) {
            $fotoFsExists = true;
        } else {
            $fotoFsExists = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Historial - <?= htmlspecialchars($mascota['nombre'] ?? 'Mascota') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background:#f6f8fb; font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; }
    .card-hero { border-radius:12px; box-shadow: 0 10px 30px rgba(20,24,40,0.06); }
    .avatar { width:140px; height:140px; border-radius:12px; object-fit:cover; }
    .meta-item { font-size:0.95rem; color:#6b7280; }
    .note-card { border-left:4px solid #0d6efd; }
    .small-muted { color:#6b7280; }
    .table-fixed { table-layout: fixed; }
    .table-fixed td { overflow-wrap: anywhere; }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-0">Historial clínico</h3>
      <small class="text-muted">Paciente: <strong><?= htmlspecialchars($mascota['nombre'] ?? '-') ?></strong></small>
    </div>
    <div class="d-flex gap-2">
      <a href="/vetsmart/veterinario/mascotas/<?= (int)$mascota['id'] ?>/vacunas" class="btn btn-outline-warning">💉 Vacunas</a>
      <a href="/vetsmart/veterinario/pacientes" class="btn btn-outline-secondary">🔙 Volver</a>
      <a href="/vetsmart/veterinario/consultas/crear/<?= (int)$mascota['id'] ?>" class="btn btn-success">🩺 Nueva consulta</a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card card-hero p-3 text-center">
        <?php if (!empty($fotoUrl) && $fotoFsExists): ?>
          <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto mascota" class="avatar mx-auto mb-3">
        <?php else: ?>
          <div class="avatar bg-light d-flex align-items-center justify-content-center mx-auto mb-3">
            <span class="fs-1 text-secondary"><?= htmlspecialchars(substr($mascota['nombre'] ?? 'M', 0, 1)) ?></span>
          </div>
        <?php endif; ?>

        <h4 class="mb-0"><?= htmlspecialchars($mascota['nombre'] ?? '-') ?></h4>
        <div class="small-muted mb-2"><?= htmlspecialchars($mascota['especie'] ?? '-') ?> • <?= htmlspecialchars($mascota['raza'] ?? '-') ?></div>

        <div class="d-flex justify-content-center gap-3 mb-3">
          <div class="text-center">
            <div class="fw-semibold"><?= ($mascota['edad'] === null || $mascota['edad'] === '') ? '-' : (int)$mascota['edad'] . ' años' ?></div>
            <div class="meta-item">Edad</div>
          </div>
          <div class="text-center">
            <div class="fw-semibold"><?= htmlspecialchars($mascota['peso'] ?? '-') ?> kg</div>
            <div class="meta-item">Peso</div>
          </div>
        </div>

        <hr>

        <div class="text-start">
          <h6 class="mb-1">Dueño</h6>
          <p class="mb-1"><strong><?= $ownerFull ? htmlspecialchars($ownerFull) : '<span class="text-muted">No disponible</span>' ?></strong></p>
          <p class="mb-1 small"><i class="bi bi-telephone me-1"></i> <?= $ownerTelefono ?: '<span class="text-muted">-</span>' ?></p>
          <p class="mb-0 small"><i class="bi bi-envelope me-1"></i> <?= $ownerEmail ?: '<span class="text-muted">-</span>' ?></p>
        </div>

        <div class="mt-3">
          <form action="/vetsmart/veterinario/mascotas/<?= (int)$mascota['id'] ?>/actualizar-foto" method="POST" enctype="multipart/form-data">
            <div class="mb-2">
              <input type="file" name="foto" accept="image/*" class="form-control form-control-sm">
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-sm">📸 <?= $fotoFsExists ? 'Cambiar foto' : 'Subir foto' ?></button>
            </div>
          </form>

          <?php if (!empty($fotoUrl) && $fotoFsExists): ?>
            <form action="/vetsmart/veterinario/mascotas/<?= (int)$mascota['id'] ?>/eliminar-foto" method="POST" onsubmit="return confirm('¿Eliminar la foto actual?');" class="mt-2">
              <button type="submit" class="btn btn-outline-danger btn-sm w-100">🗑️ Eliminar foto</button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <?php if (!empty($mascota['notas'])): ?>
        <div class="card mt-3">
          <div class="card-header"><strong>Notas generales</strong></div>
          <div class="card-body small-muted"><?= nl2br(htmlspecialchars($mascota['notas'])) ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($notasRapidas)): ?>
        <div class="card mt-3">
          <div class="card-header"><strong>Notas rápidas</strong></div>
          <div class="card-body">
            <?php foreach ($notasRapidas as $n): ?>
              <div class="p-2 mb-2 note-card rounded">
                <div class="d-flex justify-content-between align-items-start">
                  <small class="text-muted"><?= date('Y-m-d H:i', strtotime($n['creado_en'])) ?></small>
                  <div class="ms-auto">
                    <a href="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/notas/<?= $n['id'] ?>/editar" class="btn btn-sm btn-outline-warning me-1">Editar</a>
                    <form action="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/notas/<?= $n['id'] ?>/eliminar" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta nota?');">
                      <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                  </div>
                </div>
                <div class="mt-2"><?= nl2br(htmlspecialchars($n['nota'])) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Historial de citas</strong>
          <small class="text-muted"><?= count($citas) ?> registros</small>
        </div>
        <div class="card-body p-0">
          <?php if (empty($citas)): ?>
            <div class="p-3 text-center text-muted">No hay citas registradas para esta mascota.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover table-fixed mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width:14%">Fecha</th>
                    <th style="width:20%">Servicio</th>
                    <th style="width:18%">Veterinario</th>
                    <th style="width:12%">Estado</th>
                    <th>Notas</th>
                    <th style="width:12%">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($citas as $c): ?>
                    <tr>
                      <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($c['fecha'] ?? ''))) ?></td>
                      <td><?= htmlspecialchars($c['servicio'] ?? '-') ?></td>
                      <td><?= htmlspecialchars($c['veterinario'] ?? '-') ?></td>
                      <td>
                        <?php $est = $c['estado'] ?? 'programada'; ?>
                        <?php if ($est === 'confirmada'): ?>
                          <span class="badge bg-success">Confirmada</span>
                        <?php elseif ($est === 'cancelada'): ?>
                          <span class="badge bg-danger">Cancelada</span>
                        <?php else: ?>
                          <span class="badge bg-secondary">Programada</span>
                        <?php endif; ?>
                      </td>
                      <td style="max-width:320px; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($c['notas'] ?? '')) ?></td>
                      <td>
                        <a href="/vetsmart/veterinario/consultas/crear/<?= $mascota['id'] ?>?cita=<?= $c['id'] ?>" class="btn btn-sm btn-outline-success mb-1">🩺 Crear Consulta</a>
                        <a href="/vetsmart/veterinario/citas/<?= $c['id'] ?>/ver" class="btn btn-sm btn-outline-primary">Detalles</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header"><strong>Agregar nota rápida</strong></div>
        <div class="card-body">
          <form action="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/notas/guardar" method="POST">
            <div class="mb-3">
              <textarea name="nota" class="form-control" rows="4" placeholder="Escribe una nota clínica breve..."></textarea>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-primary">Guardar nota</button>
              <a href="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/notas" class="btn btn-outline-secondary">Gestionar notas</a>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>