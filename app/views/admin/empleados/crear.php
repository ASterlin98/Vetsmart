<?php
// app/views/admin/empleados/crear.php
/** @var array $roles */
$roles = $roles ?? [];
?>

<div class="container py-4">
    <h2 class="fw-bold">➕ Nuevo Empleado</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card p-3 mt-4">
      <form id="empleadoForm" method="POST" action="<?= BASE ?>/admin/empleados/guardar">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">DNI</label>
            <input type="text" class="form-control" name="docusu">
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono">
          </div>

          <div class="col-md-6">
            <label class="form-label">Rol</label>
            <select class="form-select" name="role_id" required>
              <option value="">Seleccione...</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Especialidad</label>
            <input type="text" class="form-control" name="especialidad">
          </div>

          <div class="col-md-6">
            <label class="form-label">Salario</label>
            <input type="number" step="0.01" class="form-control" name="salario">
          </div>

          <div class="col-md-6">
            <label class="form-label">Fecha Ingreso</label>
            <input type="date" class="form-control" name="fecha_ingreso">
          </div>

          <div class="col-md-6 d-flex align-items-center">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" id="empleado_activo" checked>
                <label class="form-check-label" for="empleado_activo">Activo</label>
            </div>
          </div>

          <div class="col-md-12">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">💾 Guardar</button>
            <a href="<?= BASE ?>/admin/empleados" class="btn btn-secondary">❌ Cancelar</a>
        </div>
      </form>
    </div>
</div>
