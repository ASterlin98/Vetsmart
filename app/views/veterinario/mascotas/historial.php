<?php
// app/views/veterinario/mascotas/historial.php
$mascota = $mascota ?? [];
$citas = $citas ?? [];
$notasRapidas = $notasRapidas ?? [];

$ownerNombre = $mascota['nombre_dueno'] ?? '';
$ownerApellido = $mascota['apellido_dueno'] ?? '';
$ownerFull = trim("$ownerNombre $ownerApellido");
$ownerTelefono = $mascota['telefono_dueno'] ?? '';
$ownerEmail = $mascota['email_dueno'] ?? '';
$foto = $mascota['foto'] ?? null;
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Historial clínico — <?= htmlspecialchars($mascota['nombre'] ?? 'Mascota') ?></h2>
    <div>
      <a href="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/vacunas" class="btn btn-warning">💉 Ver Vacunas</a> 
      <a href="/vetsmart/veterinario/pacientes" class="btn btn-secondary">🔙 Volver a Pacientes</a>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-4">
      <!-- Tarjeta de la mascota -->
      <div class="card">
        <div class="card-body text-center">
          <?php if ($foto): ?>
            <img src="<?= htmlspecialchars($foto) ?>" alt="Foto mascota" class="img-fluid rounded mb-2" style="max-height:180px;">
          <?php else: ?>
            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:180px;">
              <span class="text-muted">Sin foto</span>
            </div>
          <?php endif; ?>
          <h4 class="mt-3 mb-0"><?= htmlspecialchars($mascota['nombre'] ?? '-') ?></h4>
          <p class="mb-1 text-muted"><?= htmlspecialchars($mascota['especie'] ?? '-') ?> — <?= htmlspecialchars($mascota['raza'] ?? '-') ?></p>
          <p class="small text-muted mb-0">Edad: <?= htmlspecialchars($mascota['edad'] ?? '-') ?> años</p>
          <p class="small text-muted">Peso: <?= htmlspecialchars($mascota['peso'] ?? '-') ?> kg</p>
        </div>
      </div>

      <!-- Tarjeta de dueño -->
      <div class="card mt-3">
        <div class="card-header"><strong>Dueño</strong></div>
        <div class="card-body">
          <p class="mb-1"><?= $ownerFull ? htmlspecialchars($ownerFull) : '<span class="text-muted">No disponible</span>' ?></p>
          <p class="mb-1 small"><strong>Tel:</strong> <?= $ownerTelefono ?: '<span class="text-muted">-</span>' ?></p>
          <p class="mb-0 small"><strong>Email:</strong> <?= $ownerEmail ?: '<span class="text-muted">-</span>' ?></p>
        </div>
      </div>

      <!-- Notas generales -->
      <?php if (!empty($mascota['notas'])): ?>
      <div class="card mt-3">
        <div class="card-header"><strong>Notas de la mascota</strong></div>
        <div class="card-body">
          <p><?= nl2br(htmlspecialchars($mascota['notas'])) ?></p>
        </div>
      </div>
      <?php endif; ?>

      <!-- Notas rápidas -->
      <?php if (!empty($notasRapidas)): ?>
      <div class="card mt-3">
        <div class="card-header"><strong>Notas rápidas</strong></div>
        <div class="card-body">
          <?php foreach ($notasRapidas as $n): ?>
            <div class="border rounded p-2 mb-2">
              <div class="d-flex justify-content-between align-items-start">
                <small class="text-muted"><?= date('Y-m-d H:i', strtotime($n['creado_en'])) ?></small>
                <div class="ms-auto">
                  <a href="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/notas/<?= $n['id'] ?>/editar" class="btn btn-sm btn-warning">Editar</a>
                  <form action="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/notas/<?= $n['id'] ?>/eliminar" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta nota?');">
                    <button class="btn btn-sm btn-danger">Eliminar</button>
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

    <!-- Columna de historial y agregar notas -->
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Historial de citas / consultas</strong>
          <small class="text-muted"><?= count($citas) ?> registros</small>
        </div>
        <div class="card-body p-0">
          <?php if (empty($citas)): ?>
            <div class="p-3">No hay citas registradas para esta mascota.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Servicio</th>
                    <th>Veterinario</th>
                    <th>Estado</th>
                    <th>Notas</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($citas as $c): ?>
                    <tr>
                      <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($c['fecha'] ?? ''))) ?></td>
                      <td><?= htmlspecialchars($c['servicio'] ?? '-') ?></td>
                      <td><?= htmlspecialchars($c['veterinario'] ?? '-') ?></td>
                      <td><?= htmlspecialchars($c['estado'] ?? '-') ?></td>
                      <td style="max-width:280px; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($c['notas'] ?? '')) ?></td>
                      <td>
                        <a href="/vetsmart/veterinario/consultas/crear/<?= $mascota['id'] ?>" class="btn btn-success">🩺 Crear Consulta</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Agregar nota rápida -->
      <div class="card mt-3">
        <div class="card-header"><strong>Agregar nota rápida</strong></div>
        <div class="card-body">
          <form action="/vetsmart/veterinario/mascotas/<?= $mascota['id'] ?>/notas/guardar" method="POST">
            <div class="mb-3">
              <textarea name="nota" class="form-control" rows="4" placeholder="Escribe una nota clínica breve..."></textarea>
            </div>
            <button class="btn btn-primary">Guardar nota</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
