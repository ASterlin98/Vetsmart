

<?php
$totClientes = (isset($data['totalClientes']) ? (int)$data['totalClientes'] : ($totalClientes ?? 0));
$totMascotas = (isset($data['totalMascotas']) ? (int)$data['totalMascotas'] : ($totalMascotas ?? 0));
$totCitas    = (isset($data['totalCitas']) ? (int)$data['totalCitas'] : ($totalCitas ?? 0));
$totIngresos = (isset($data['totalIngresos']) ? (float)$data['totalIngresos'] : ($totalIngresos ?? 0.0));
$totVacunas  = (isset($data['totalVacunas']) ? (int)$data['totalVacunas'] : ($totVacunas ?? 0));
$citasConfirmadas = (isset($data['citasConfirmadas']) ? (int)$data['citasConfirmadas'] : ($citasConfirmadas ?? 0));
$recentActivity = $data['recentActivity'] ?? ($recentActivity ?? []);
$error = $data['error'] ?? ($error ?? null);

function fmtMoney($v) {
    return '$' . number_format((float)$v, 2, '.', ',');
}
?>
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <h1 class="fw-bold">Panel del Super Administrador</h1>
      <p class="text-muted mb-4">Resumen general del sistema</p>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger">
      <strong>Error:</strong> <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-primary fw-semibold">Clientes</div>
          <div class="display-6 fw-bold"><?= number_format($totClientes) ?></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-success fw-semibold">Mascotas</div>
          <div class="display-6 fw-bold"><?= number_format($totMascotas) ?></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-warning fw-semibold">Citas</div>
          <div class="display-6 fw-bold"><?= number_format($totCitas) ?></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-danger fw-semibold">Ingresos</div>
          <div class="display-6 fw-bold"><?= fmtMoney($totIngresos) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Actividad reciente</h5>
          <p class="text-muted small">Aquí podrás ver las acciones recientes del sistema.</p>

          <?php if (empty($recentActivity)): ?>
            <div class="text-muted">No se han detectado actividades recientes.</div>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($recentActivity as $a): 
                $tipo = htmlspecialchars($a['tipo'] ?? '');
                $actor = htmlspecialchars(trim($a['actor'] ?? ''));
                $accion = htmlspecialchars($a['accion'] ?? '');
                $detalle = htmlspecialchars($a['detalle'] ?? '');
                $time = $a['creado_en'] ?? null;
                $when = '';
                if ($time) {
                  $ts = strtotime($time);
                  if ($ts !== false) $when = date('d/m/Y H:i', $ts);
                  else $when = htmlspecialchars($time);
                }
              ?>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="fw-semibold"><?= $accion ?> <?= $tipo ? " — " . $tipo : '' ?></div>
                      <div class="small text-muted"><?= $detalle ?></div>
                      <?php if ($actor): ?><div class="small text-muted">Por: <?= $actor ?></div><?php endif; ?>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block mb-1"><?= $when ?></small>
                        <?php if ($tipo === 'cita'): ?>
                            <button class="btn btn-xs btn-outline-primary ver-detalles-btn" data-id="<?= htmlspecialchars($a['entidad_id']) ?>" data-bs-toggle="modal" data-bs-target="#citaDetallesModal">
                                Ver Detalles
                            </button>
                        <?php endif; ?>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="mb-2">Resumen adicional</h6>
          <div class="row">
            <div class="col-6">
              <div class="small text-muted">Vacunas registradas</div>
              <div class="fw-bold"><?= number_format($totVacunas) ?></div>
            </div>
            <div class="col-6">
              <div class="small text-muted">Citas confirmadas</div>
              <div class="fw-bold"><?= number_format($citasConfirmadas) ?></div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="col-lg-5">
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="card-title">Accesos rápidos</h6>
          <div class="d-grid gap-2">
            <a href="/vetsmart/super_admin/usuarios" class="btn btn-primary">Gestionar usuarios</a>
            <a href="/vetsmart/super_admin/configuracion" class="btn btn-outline-secondary">Configuración global</a>
            <a href="/vetsmart/super_admin/reportes" class="btn btn-outline-info">Ir a reportes</a>
          </div>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Notas</h6>
          <p class="small text-muted mb-0">Usa este panel para monitorear la actividad y acceder a los módulos del sistema.</p>
        </div>
      </div>

    </div>
  </div>

  <div class="row mt-5">
    <div class="col-12 text-center text-muted">
      © <?= date('Y') ?> VetSmart. Todos los derechos reservados.
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = new bootstrap.Modal(document.getElementById('citaDetallesModal'));
    const modalBody = document.getElementById('modal-content-display');
    const loader = document.getElementById('modal-loader');

    document.querySelectorAll('.ver-detalles-btn').forEach(button => {
        button.addEventListener('click', function () {
            const citaId = this.dataset.id;

            // Show loader, hide content
            loader.style.display = 'block';
            modalBody.style.display = 'none';
            modalBody.innerHTML = ''; // Clear previous content

            fetch(`/api/citas/${citaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        modalBody.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                    } else {
                        const formattedDate = new Date(data.fecha).toLocaleString('es-ES', { dateStyle: 'long', timeStyle: 'short' });
                        const Creador = data.creador_nombre ? `${data.creador_nombre} ${data.creador_apellido}` : 'No especificado';
                        modalBody.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID de Cita:</strong> ${data.id}</p>
                                    <p><strong>Fecha y Hora:</strong> ${formattedDate}</p>
                                    <p><strong>Estado:</strong> <span class="badge bg-info text-dark">${data.estado}</span></p>
                                    <p><strong>Creado por:</strong> ${Creador}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Cliente:</strong> ${data.cliente_nombre} ${data.cliente_apellido}</p>
                                    <p><strong>Mascota:</strong> ${data.mascota_nombre} (${data.mascota_especie} - ${data.mascota_raza})</p>
                                    <p><strong>Atendido por:</strong> ${data.empleado_nombre} ${data.empleado_apellido}</p>
                                </div>
                            </div>
                            <hr>
                            <h5>Detalles del Servicio</h5>
                            <p><strong>Servicio:</strong> ${data.servicio_nombre}</p>
                            <p><strong>Precio:</strong> $${Number(data.servicio_precio).toLocaleString('es-ES')}</p>
                            <hr>
                            <h5>Notas de la Cita</h5>
                            <p>${data.notas ? data.notas : 'No hay notas.'}</p>
                        `;
                    }
                    // Hide loader, show content
                    loader.style.display = 'none';
                    modalBody.style.display = 'block';
                })
                .catch(error => {
                    loader.style.display = 'none';
                    modalBody.style.display = 'block';
                    modalBody.innerHTML = `<div class="alert alert-danger">Error al cargar los datos.</div>`;
                    console.error('Error:', error);
                });
        });
    });
});
</script>

<!-- Modal para Detalles de Cita -->
<div class="modal fade" id="citaDetallesModal" tabindex="-1" aria-labelledby="citaDetallesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="citaDetallesModalLabel">Detalles de la Cita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modal-loader" class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
        <div id="modal-content-display" style="display: none;">
            <!-- El contenido se inyectará aquí -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
