<div class="container-fluid py-3">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
    <h2 class="fw-bold">🛠️ Gestión de Servicios</h2>

    <div class="d-flex gap-2">
      <input type="text" id="searchServicio" class="form-control" placeholder="🔍 Buscar servicio...">
      <!-- Botón que abre modal Crear -->
      <a href="/vetsmart/admin/servicios/crear" class="btn btn-primary">
        ➕ Nuevo Servicio
      </a>
    </div>
  </div>

  <?php if (empty($servicios)): ?>
    <div class="alert alert-info text-center p-4 rounded shadow-sm">
      <i class="bi bi-info-circle"></i> No hay servicios registrados.
    </div>
  <?php else: ?>
      <div class="card p-3">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle" id="tablaServicios">
            <thead class="table-light">
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
                  <a href="/vetsmart/admin/servicios/<?= $s['id'] ?>/editar"
                     class="btn btn-sm btn-warning shadow-sm"
                     title="Editar">✏️</a>
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

<script>
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
