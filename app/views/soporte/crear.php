<?php
// app/views/soporte/crear.php
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Crear Nuevo Ticket de Soporte</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE ?>/super_admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE ?>/soporte">Soporte</a></li>
        <li class="breadcrumb-item active">Crear Ticket</li>
    </ol>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_error'] ?>
            <?php unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Detalles del Nuevo Ticket
        </div>
        <div class="card-body">
            <form action="<?= BASE ?>/soporte/guardar" method="POST">
                <div class="mb-3">
                    <label for="asunto" class="form-label">Asunto</label>
                    <input type="text" class="form-control" id="asunto" name="asunto" required>
                    <small class="form-text text-muted">Un resumen breve y claro del problema.</small>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción del Problema</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="6" required></textarea>
                    <small class="form-text text-muted">Por favor, detalla el problema lo máximo posible.</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prioridad" class="form-label">Prioridad</label>
                        <select class="form-select" id="prioridad" name="prioridad">
                            <option value="Baja">Baja</option>
                            <option value="Media" selected>Media</option>
                            <option value="Alta">Alta</option>
                            <option value="Urgente">Urgente</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="asignado_a" class="form-label">Asignar a (Opcional)</label>
                        <select class="form-select" id="asignado_a" name="asignado_a">
                            <option value="">-- Sin asignar --</option>
                            <?php if (!empty($staff)): ?>
                                <?php foreach ($staff as $member): ?>
                                    <option value="<?= $member['id'] ?>"><?= htmlspecialchars($member['nombre'] . ' ' . $member['apellido']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Puedes asignar este ticket a un miembro del equipo directamente.</small>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary">Crear Ticket</button>
                <a href="<?= BASE ?>/soporte" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>