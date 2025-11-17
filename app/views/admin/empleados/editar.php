
<?php
/** @var array $empleado */
/** @var array $roles */

$empleado = $empleado ?? [];
$roles = $roles ?? [];
?>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">✏️ Editar Empleado</h2>
        <a href="/vetsmart/admin/empleados" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la Lista
        </a>
    </div>

    <div class="card p-4 shadow-sm">
        <form id="empleadoForm" method="POST" action="/vetsmart/admin/empleados/<?= htmlspecialchars($empleado['id']) ?>/actualizar">
            <div class="row g-3">
                <input type="hidden" name="id" id="empleado_id" value="<?= htmlspecialchars($empleado['id']) ?>">

                <div class="col-md-6">
                    <label for="empleado_nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" id="empleado_nombre" value="<?= htmlspecialchars($empleado['nombre']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="empleado_apellido" class="form-label">Apellido</label>
                    <input type="text" class="form-control" name="apellido" id="empleado_apellido" value="<?= htmlspecialchars($empleado['apellido']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="empleado_dni" class="form-label">DNI</label>
                    <input type="text" class="form-control" name="docusu" id="empleado_dni" value="<?= htmlspecialchars($empleado['docusu'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="empleado_email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="empleado_email" value="<?= htmlspecialchars($empleado['email']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="empleado_telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" id="empleado_telefono" value="<?= htmlspecialchars($empleado['telefono'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="empleado_role_id" class="form-label">Rol</label>
                    <select class="form-select" name="role_id" id="empleado_role_id" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($roles as $r) : ?>
                            <option value="<?= $r['id'] ?>" <?= ($r['id'] == $empleado['role_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="empleado_especialidad" class="form-label">Especialidad</label>
                    <input type="text" class="form-control" name="especialidad" id="empleado_especialidad" value="<?= htmlspecialchars($empleado['especialidad'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="empleado_salario" class="form-label">Salario</label>
                    <input type="number" step="0.01" class="form-control" name="salario" id="empleado_salario" value="<?= htmlspecialchars($empleado['salario'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="empleado_fecha_ingreso" class="form-label">Fecha Ingreso</label>
                    <input type="date" class="form-control" name="fecha_ingreso" id="empleado_fecha_ingreso" value="<?= htmlspecialchars($empleado['fecha_ingreso'] ?? '') ?>">
                </div>

                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" id="empleado_activo" <?= ($empleado['activo'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="empleado_activo">
                            Activo
                        </label>
                    </div>
                </div>

                <div class="col-md-12">
                    <p class="text-muted">La contraseña no se puede editar desde aquí. Si es necesario un cambio, contacte al soporte.</p>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
                <a href="/vetsmart/admin/empleados" class="btn btn-secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>
</div>
