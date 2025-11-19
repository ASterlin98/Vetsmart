<div class="container py-3">
  <div class="card perfil-card shadow mx-auto" style="max-width:420px;">
    <div class="card-body text-center">
      <?php
        $foto = $mascota['foto'] ?? '';
        $rutaFoto = 'assets/uploads/mascotas/' . $foto;
        if (empty($foto) || !file_exists(__DIR__ . '/../../../public/' . $rutaFoto)) {
            $rutaFoto = 'assets/img/default_pet.png';
        }
      ?>
      <img src="/vetsmart/<?= htmlspecialchars($rutaFoto) ?>" class="rounded-circle mb-3 perfil-foto" width="130" height="130" alt="Foto de <?= htmlspecialchars($mascota['nombre']) ?>">
      <h4 class="mb-0"><?= htmlspecialchars($mascota['nombre'] ?? '-') ?></h4>
      <p class="text-muted"><?= htmlspecialchars($mascota['especie'] ?? '-') ?> · <?= htmlspecialchars($mascota['raza'] ?? '-') ?></p>
      <p>Edad: <?= htmlspecialchars($mascota['edad'] ?? '-') ?> años — Sexo: <?= htmlspecialchars($mascota['sexo'] ?? '-') ?></p>
      <hr>
      <p class="text-muted small">ID Interno: #<?= htmlspecialchars($mascota['id'] ?? '-') ?></p>
    </div>
  </div>
  <div class="text-center mt-3">
    <a href="/vetsmart/recepcionista/mascotas" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Volver</a>
    <a href="/vetsmart/recepcionista/mascotas/edit/<?= (int)($mascota['id'] ?? 0) ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
  </div>
</div>

<style>
.perfil-foto { object-fit: cover; }
.perfil-card { border-radius: 16px; }
</style>
