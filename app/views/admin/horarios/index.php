<?php
// app/views/admin/horarios/index.php
?>
<div class="container-fluid py-3">
  <h2 class="fw-bold">📅 Gestión de Horarios</h2>

  <!-- Nav Tabs -->
  <ul class="nav nav-tabs" id="horariosTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active fw-bold" id="semana-tab" data-bs-toggle="tab" data-bs-target="#semana" type="button" role="tab">
        📆 Horarios Semanales
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link fw-bold" id="turnos-tab" data-bs-toggle="tab" data-bs-target="#turnos" type="button" role="tab">
        ⏰ Turnos Extras
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link fw-bold" id="solicitudes-tab" data-bs-toggle="tab" data-bs-target="#solicitudes" type="button" role="tab">
        📝 Solicitudes
      </button>
    </li>
  </ul>

  <!-- Tab Contents -->
  <div class="tab-content mt-3" id="horariosTabsContent">

    <!-- ================= HORARIOS SEMANALES ================= -->
    <div class="tab-pane fade show active" id="semana" role="tabpanel">
      <button class="btn btn-success mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalHorarioSemana">
        ➕ Nuevo Horario Semanal
      </button>

      <div class="card p-3">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>👨‍⚕️ Empleado</th>
              <th>📅 Día</th>
              <th>⏱ Inicio</th>
              <th>⏱ Fin</th>
              <th>⚙️ Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($horariosSemana as $h): ?>
            <tr>
              <td><?= htmlspecialchars($h['empleado']) ?></td>
              <td><span class="badge bg-info text-dark"><?= htmlspecialchars($h['dia']) ?></span></td>
              <td><?= htmlspecialchars($h['hora_inicio']) ?></td>
              <td><?= htmlspecialchars($h['hora_fin']) ?></td>
            <td>
                <!-- Editar -->
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarSemana<?= $h['id'] ?>">✏️</button>
                <!-- Eliminar -->
                <a href="/vetsmart/admin/horarios/<?= $h['id'] ?>/eliminarSemana" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este horario?')">🗑️</a>
            </td>
            </tr>

            <!-- Modal Editar Semana -->
            <div class="modal fade" id="editarSemana<?= $h['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <form method="POST" action="/vetsmart/admin/horarios/<?= $h['id'] ?>/actualizarHorarioSemana">
                    <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Editar Horario Semanal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <div class="mb-3">
                        <label>Empleado</label>
                        <select name="empleado_id" class="form-select" required>
                            <?php foreach ($empleados as $e): ?>
                                <option value="<?= $e['id'] ?>" <?= ($e['id'] == $h['empleado_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Día</label>
                        <select name="dia" class="form-select" required>
                        <?php foreach (["Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo"] as $dia): ?>
                            <option value="<?= $dia ?>" <?= $h['dia']==$dia ? 'selected' : '' ?>><?= $dia ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label>Hora Inicio</label>
                        <input type="time" name="hora_inicio" value="<?= $h['hora_inicio'] ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                        <label>Hora Fin</label>
                        <input type="time" name="hora_fin" value="<?= $h['hora_fin'] ?>" class="form-control" required>
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
                </div>
            </div>
            </div>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      </div>
    </div>

    <!-- ================= TURNOS EXTRAS ================= -->
    <div class="tab-pane fade" id="turnos" role="tabpanel">
      <button class="btn btn-primary mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTurno">
        ➕ Nuevo Turno Extra
      </button>

      <div class="card p-3">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Empleado</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Tipo</th>
              <th>Notas</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($turnos as $t): ?>
            <tr>
              <td><?= htmlspecialchars($t['empleado']) ?></td>
              <td><?= htmlspecialchars($t['inicio']) ?></td>
              <td><?= htmlspecialchars($t['fin']) ?></td>
              <td>
                <span class="badge <?= $t['tipo']=="Emergencia" ? 'bg-danger' : ($t['tipo']=="Nocturno" ? 'bg-dark' : 'bg-info text-dark') ?>">
                  <?= htmlspecialchars($t['tipo']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($t['notas']) ?></td>
              <td>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarTurno<?= $t['id'] ?>">✏️</button>
                <a href="/vetsmart/admin/horarios/<?= $t['id'] ?>/eliminarTurno" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este turno?')">🗑️</a>
            </td>
            </tr>

            <!-- Modal Editar Turno -->
            <div class="modal fade" id="editarTurno<?= $t['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <form method="POST" action="/vetsmart/admin/horarios/<?= $t['id'] ?>/actualizarTurno">
                    <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Editar Turno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <div class="mb-3">
                        <label>Empleado</label>
                        <select name="empleado_id" class="form-select" required>
                            <?php foreach ($empleados as $e): ?>
                                <option value="<?= $e['id'] ?>" <?= ($e['id'] == $t['empleado_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Inicio</label>
                        <input type="datetime-local" name="inicio" value="<?= date('Y-m-d\TH:i', strtotime($t['inicio'])) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Fin</label>
                        <input type="datetime-local" name="fin" value="<?= date('Y-m-d\TH:i', strtotime($t['fin'])) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Tipo</label>
                        <select name="tipo" class="form-select" required>
                        <?php foreach (["Emergencia","Extras","Nocturno"] as $tipo): ?>
                            <option value="<?= $tipo ?>" <?= $t['tipo']==$tipo ? 'selected' : '' ?>><?= $tipo ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Notas</label>
                        <textarea name="notas" class="form-control"><?= htmlspecialchars($t['notas']) ?></textarea>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
                </div>
            </div>
            </div>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      </div>
    </div>

    <!-- ================= SOLICITUDES ================= -->
    <div class="tab-pane fade" id="solicitudes" role="tabpanel">
      <button class="btn btn-warning mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSolicitud">
        ➕ Nueva Solicitud
      </button>
      <div class="card p-3">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Empleado</th>
              <th>Tipo</th>
              <th>Desde</th>
              <th>Hasta</th>
              <th>Motivo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($solicitudes as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['empleado']) ?></td>
              <td>
                <span class="badge <?= $s['tipo']=="vacaciones" ? 'bg-success' : ($s['tipo']=="incapacidad" ? 'bg-danger' : 'bg-secondary') ?>">
                  <?= ucfirst($s['tipo']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($s['fecha_inicio']) ?></td>
              <td><?= htmlspecialchars($s['fecha_fin']) ?></td>
              <td><?= htmlspecialchars($s['motivo']) ?></td>
<td>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarSolicitud<?= $s['id'] ?>">✏️</button>
                <a href="/vetsmart/admin/horarios/<?= $s['id'] ?>/eliminarSolicitud" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta solicitud?')">🗑️</a>
            </td>
            </tr>

            <!-- Modal Editar Solicitud -->
            <div class="modal fade" id="editarSolicitud<?= $s['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <form method="POST" action="/vetsmart/admin/horarios/<?= $s['id'] ?>/actualizarSolicitud">
                    <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Editar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <div class="mb-3">
                        <label>Empleado</label>
                        <select name="usuario_id" class="form-select" required>
                            <?php foreach ($empleados as $e): ?>
                                <option value="<?= $e['id'] ?>" <?= ($e['id'] == $s['usuario_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Tipo</label>
                        <select name="tipo" class="form-select" required>
                        <?php foreach (["permiso","vacaciones","incapacidad"] as $tipo): ?>
                            <option value="<?= $tipo ?>" <?= $s['tipo']==$tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" value="<?= $s['fecha_inicio'] ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" value="<?= $s['fecha_fin'] ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Motivo</label>
                        <textarea name="motivo" class="form-control"><?= htmlspecialchars($s['motivo']) ?></textarea>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
                </div>
            </div>
            </div>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- ========== MODALES ========== -->

<!-- Modal Horario Semana -->
<div class="modal fade" id="modalHorarioSemana" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="/vetsmart/admin/horarios/guardar-semana">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Nuevo Horario Semanal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Empleado</label>
            <select name="empleado_id" class="form-select" required>
              <?php foreach ($empleados as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Día</label>
            <select name="dia" class="form-select" required>
                <option value="Lunes">Lunes</option>
                <option value="Martes">Martes</option>
                <option value="Miércoles">Miércoles</option>
                <option value="Jueves">Jueves</option>
                <option value="Viernes">Viernes</option>
                <option value="Sábado">Sábado</option>
                <option value="Domingo">Domingo</option>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Hora Inicio</label>
              <input type="time" name="hora_inicio" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Hora Fin</label>
              <input type="time" name="hora_fin" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Turno Extra -->
<div class="modal fade" id="modalTurno" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="/vetsmart/admin/horarios/guardar-turno">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Nuevo Turno Extra</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Empleado</label>
            <select name="empleado_id" class="form-select" required>
              <?php foreach ($empleados as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Inicio</label>
            <input type="datetime-local" name="inicio" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Fin</label>
            <input type="datetime-local" name="fin" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Tipo de turno</label>
            <select name="tipo" class="form-select" required>
                <option value="Emergencia">Emergencia</option>
                <option value="Extras">Extras</option>
                <option value="Nocturno">Nocturno</option>
            </select>
        </div>
          <div class="mb-3">
            <label>Notas</label>
            <textarea name="notas" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Solicitud -->
<div class="modal fade" id="modalSolicitud" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="/vetsmart/admin/horarios/guardar-solicitud">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Nueva Solicitud</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Empleado</label>
            <select name="usuario_id" class="form-select" required>
              <?php foreach ($empleados as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Tipo</label>
            <select name="tipo" class="form-select" required>
              <option value="permiso">Permiso</option>
              <option value="vacaciones">Vacaciones</option>
              <option value="incapacidad">Incapacidad</option>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Fecha Inicio</label>
              <input type="date" name="fecha_inicio" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label>Fecha Fin</label>
              <input type="date" name="fecha_fin" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label>Motivo</label>
            <textarea name="motivo" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
