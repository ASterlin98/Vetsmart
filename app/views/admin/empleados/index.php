<?php
/** @var array $empleados */
/** @var array $roles */
$empleados = $empleados ?? [];
$roles = $roles ?? [];
?>

<div class="container-fluid py-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <h2 class="fw-bold">👨‍⚕️ Gestión de Empleados</h2>

        <div class="d-flex gap-2">
            <!-- Search -->
            <input type="text" id="searchEmpleado" class="form-control" placeholder="🔍 Buscar por nombre o DNI" onkeyup="filtrarEmpleados()">
            <!-- Botón Crear -->
            <a href="/vetsmart/admin/empleados/crear" class="btn btn-primary">
                ➕ Nuevo
            </a>
        </div>
    </div>
    <div class="card p-3">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" id="empleadosTable">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>DNI</th>
                    <th>Especialidad</th>
                    <th>Salario</th>
                    <th>Ingreso</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php if (empty($empleados)): ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted">No hay empleados registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($empleados as $e): ?>
            <tr>
              <td class="fw-bold"><?= htmlspecialchars($e['nombre'].' '.$e['apellido']) ?></td>
              <td><span class="badge bg-info"><?= htmlspecialchars($e['rol'] ?? '-') ?></span></td>
              <td><?= htmlspecialchars($e['email'] ?? '-') ?></td>
              <td><?= htmlspecialchars($e['telefono'] ?? '-') ?></td>
              <td><?= htmlspecialchars($e['docusu'] ?? '-') ?></td>
              <td><?= htmlspecialchars($e['especialidad'] ?? '-') ?></td>
              <td><?= $e['salario'] ? '$'.number_format($e['salario'], 2) : '-' ?></td>
              <td><?= $e['fecha_ingreso'] ?? '-' ?></td>
              <td>
                <?= $e['activo'] 
                  ? '<span class="badge bg-success">Activo</span>' 
                  : '<span class="badge bg-secondary">Inactivo</span>' ?>
                <?php if (!empty($e['is_blocked']) && $e['is_blocked']): ?>
                  <span class="badge bg-danger">Bloqueado</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="/vetsmart/admin/empleados/<?= $e['id'] ?>/editar" class="btn btn-warning btn-sm">✏️</a>
                <a href="/vetsmart/admin/empleados/<?= $e['id'] ?>/eliminar" 
                   onclick="return confirm('¿Seguro de eliminar este empleado?')"
                   class="btn btn-danger btn-sm">
                  🗑️
                </a>
                <?php if (!empty($e['is_blocked']) && $e['is_blocked']): ?>
                  <a href="/vetsmart/admin/desbloquear_usuario/<?= $e['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Desbloquear este usuario?')">🔓 Desbloquear</a>
                <?php endif; ?>
              </td>
            </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
</div>

<script>
// 🔍 Filtro por nombre o documento
function filtrarEmpleados() {
    const input = document.getElementById("searchEmpleado").value.toLowerCase();
    const rows = document.querySelectorAll("#empleadosTable tbody tr");
    rows.forEach(row => {
        const nombre = row.cells[0]?.innerText.toLowerCase();
        const dni = row.cells[4]?.innerText.toLowerCase();
        if (nombre.includes(input) || dni.includes(input)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>
