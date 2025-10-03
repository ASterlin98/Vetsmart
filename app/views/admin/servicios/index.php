<div class="container py-4">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
    <h2 class="fw-bold text-primary">🛠️ Gestión de Servicios</h2>
    <div class="d-flex gap-2">
      <!-- Buscador -->
      <input type="text" id="searchServicio" class="form-control shadow-sm" placeholder="🔍 Buscar servicio...">
      <!-- Botón que abre modal Crear -->
      <button class="btn btn-gradient shadow-sm"
              data-bs-toggle="modal"
              data-bs-target="#servicioModal"
              onclick="openCrearServicio()">
        ➕ Nuevo Servicio
      </button>
    </div>
  </div>

  <?php if (empty($servicios)): ?>
    <div class="alert alert-info text-center p-4 rounded shadow-sm">
      <i class="bi bi-info-circle"></i> No hay servicios registrados.
    </div>
  <?php else: ?>
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle mb-0" id="tablaServicios">
            <thead class="table-dark text-white">
              <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio (COP)</th>
                <th>Duración</th>
                <th>Estado</th>
                <th style="width:150px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($servicios as $s): ?>
              <tr>
                <td class="fw-semibold"><?= htmlspecialchars($s['nombre']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($s['descripcion']) ?></td>
                <td><span class="fw-bold text-success">$<?= number_format($s['precio'], 0, ',', '.') ?></span></td>
                <td><?= (int)$s['duracion_min'] ?> min</td>
                <td>
                  <?= $s['activo'] 
                        ? '<span class="badge bg-success px-3 py-2">Activo</span>' 
                        : '<span class="badge bg-secondary px-3 py-2">Inactivo</span>' ?>
                </td>
                <td>
                  <button class="btn btn-sm btn-warning shadow-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#servicioModal"
                          onclick='openEditarServicio(<?= json_encode($s) ?>)'>
                    ✏️
                  </button>
                  <a href="/vetsmart/admin/servicios/<?= $s['id'] ?>/eliminar" 
                     class="btn btn-sm btn-danger shadow-sm" 
                     onclick="return confirm('¿Eliminar este servicio?')" 
                     title="Eliminar">🗑️</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- ========== MODAL CREAR/EDITAR SERVICIO ========== -->
<div class="modal fade" id="servicioModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form id="servicioForm" method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalServicioTitle">Nuevo Servicio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body row g-3">
          <input type="hidden" name="id" id="servicio_id">

          <div class="col-md-6">
            <label class="form-label fw-semibold">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="servicio_nombre" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Duración (min)</label>
            <input type="number" class="form-control" name="duracion_min" id="servicio_duracion" required>
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea class="form-control" name="descripcion" id="servicio_descripcion"></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Precio (COP)</label>
            <input type="number" class="form-control" name="precio" id="servicio_precio" required>
          </div>

          <div class="col-md-6 d-flex align-items-center">
            <div class="form-check mt-4">
              <input type="checkbox" class="form-check-input" name="activo" id="servicio_activo" checked>
              <label class="form-check-label fw-semibold" for="servicio_activo">Activo</label>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success shadow-sm">Guardar</button>
          <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* Botón con gradiente */
.btn-gradient {
  background: linear-gradient(45deg, #007bff, #00c6ff);
  color: #fff;
  border: none;
}
.btn-gradient:hover {
  background: linear-gradient(45deg, #0056b3, #0099cc);
  color: #fff;
}
</style>

<script>
function openCrearServicio() {
    document.getElementById("modalServicioTitle").innerText = "Nuevo Servicio";
    document.getElementById("servicioForm").action = "/vetsmart/admin/servicios/guardar";
    document.getElementById("servicio_id").value = "";
    document.getElementById("servicio_nombre").value = "";
    document.getElementById("servicio_duracion").value = "";
    document.getElementById("servicio_descripcion").value = "";
    document.getElementById("servicio_precio").value = "";
    document.getElementById("servicio_activo").checked = true;
}

function openEditarServicio(s) {
    document.getElementById("modalServicioTitle").innerText = "Editar Servicio";
    document.getElementById("servicioForm").action = "/vetsmart/admin/servicios/" + s.id + "/actualizar";
    document.getElementById("servicio_id").value = s.id;
    document.getElementById("servicio_nombre").value = s.nombre;
    document.getElementById("servicio_duracion").value = s.duracion_min;
    document.getElementById("servicio_descripcion").value = s.descripcion;
    document.getElementById("servicio_precio").value = s.precio;
    document.getElementById("servicio_activo").checked = s.activo == 1;
}

/* 🔍 Buscador dinámico */
document.getElementById("searchServicio").addEventListener("keyup", function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#tablaServicios tbody tr");

    rows.forEach(function(row) {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(value) ? "" : "none";
    });
});
</script>
