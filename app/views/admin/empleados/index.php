<?php
/** @var array $empleados */
/** @var array $roles */
$empleados = $empleados ?? [];
$roles = $roles ?? [];
?>

<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <h2 class="mb-0">👨‍⚕️ Gestión de Empleados</h2>

        <div class="d-flex gap-2">
            <!-- Search -->
            <input type="text" id="searchEmpleado" class="form-control" placeholder="🔍 Buscar por nombre o DNI" onkeyup="filtrarEmpleados()">

            <!-- Botón Crear -->
            <button class="btn btn-primary" 
                    data-bs-toggle="modal" 
                    data-bs-target="#empleadoModal" 
                    onclick="openCrearEmpleado()">
                ➕ Nuevo
            </button>
        </div>
    </div>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle" id="empleadosTable">
            <thead class="table-dark text-center">
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
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" 
                                    data-bs-toggle="modal" data-bs-target="#empleadoModal"
                                    onclick='openEditarEmpleado(<?= json_encode($e) ?>)'>
                                    ✏️
                                </button>
                                <a href="/vetsmart/admin/empleados/<?= $e['id'] ?>/eliminar" 
                                onclick="return confirm('¿Seguro de eliminar este empleado?')" 
                                class="btn btn-danger btn-sm">
                                    🗑
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ========== MODAL Crear/Editar ========== -->
<div class="modal fade" id="empleadoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content shadow-lg">
      <form id="empleadoForm" method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalTitle">Nuevo Empleado</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">

          <input type="hidden" name="id" id="empleado_id">

          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="empleado_nombre" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" id="empleado_apellido" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">DNI</label>
            <input type="text" class="form-control" name="docusu" id="empleado_dni">
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="empleado_email" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="empleado_telefono">
          </div>

          <div class="col-md-6">
            <label class="form-label">Rol</label>
            <select class="form-select" name="role_id" id="empleado_role_id" required>
              <option value="">Seleccione...</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Especialidad</label>
            <input type="text" class="form-control" name="especialidad" id="empleado_especialidad">
          </div>

          <div class="col-md-6">
            <label class="form-label">Salario</label>
            <input type="number" step="0.01" class="form-control" name="salario" id="empleado_salario">
          </div>

          <div class="col-md-6">
            <label class="form-label">Fecha Ingreso</label>
            <input type="date" class="form-control" name="fecha_ingreso" id="empleado_fecha_ingreso">
          </div>

          <div class="col-md-12">
            <label class="form-label">Activo</label>
            <input type="checkbox" name="activo" id="empleado_activo" checked>
          </div>

          <div class="col-md-12" id="passwordDiv">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="empleado_password">
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">💾 Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancelar</button>
        </div>
      </form>
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

function openCrearEmpleado() {
    document.getElementById("modalTitle").innerText = "Nuevo Empleado";
    document.getElementById("empleadoForm").action = "/vetsmart/admin/empleados/guardar";
    document.getElementById("empleadoForm").reset();
    document.getElementById("passwordDiv").style.display = "block";
}

function openEditarEmpleado(e) {
    document.getElementById("modalTitle").innerText = "Editar Empleado";
    document.getElementById("empleadoForm").action = "/vetsmart/admin/empleados/" + e.id + "/actualizar";
    document.getElementById("empleado_id").value = e.id;
    document.getElementById("empleado_nombre").value = e.nombre;
    document.getElementById("empleado_apellido").value = e.apellido;
    document.getElementById("empleado_email").value = e.email;
    document.getElementById("empleado_dni").value = e.docusu || "";
    document.getElementById("empleado_telefono").value = e.telefono || "";
    document.getElementById("empleado_role_id").value = e.role_id;
    document.getElementById("empleado_especialidad").value = e.especialidad || "";
    document.getElementById("empleado_salario").value = e.salario || "";
    document.getElementById("empleado_fecha_ingreso").value = e.fecha_ingreso || "";
    document.getElementById("empleado_activo").checked = e.activo == 1;
    document.getElementById("empleado_password").value = "";
    document.getElementById("passwordDiv").style.display = "none"; 
}
</script>
