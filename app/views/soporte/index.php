<?php
// app/views/soporte/index.php
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Centro de Soporte</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE ?>/super_admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Soporte</li>
    </ol>

    <div class="mb-4">
        <a href="<?= BASE ?>/soporte/crear" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Crear Nuevo Ticket
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-ticket-alt me-1"></i>
            Listado de Tickets de Soporte
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-striped table-hover" style="white-space: nowrap;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Asunto</th>
                        <th>Rol del Problema</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Creador</th>
                        <th>Asignado a</th>
                        <th>Última Actualización</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay tickets de soporte registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <?php
                                $priorityClass = '';
                                switch ($ticket['prioridad']) {
                                    case 'Urgente':
                                        $priorityClass = 'table-danger';
                                        break;
                                    case 'Alta':
                                        $priorityClass = 'table-warning';
                                        break;
                                    case 'Media':
                                        $priorityClass = 'table-info';
                                        break;
                                    case 'Baja':
                                        $priorityClass = 'table-success';
                                        break;
                                }
                            ?>
                            <tr class="<?php echo $priorityClass; ?>">
                                <td>#<?= htmlspecialchars($ticket['id']) ?></td>
                                <td><?= htmlspecialchars($ticket['asunto']) ?></td>
                                <td><?= htmlspecialchars($ticket['rol_problema']) ?></td>
                                <td>
                                    <span class="badge
                                        <?php
                                            switch ($ticket['estado']) {
                                                case 'Abierto': echo 'bg-success'; break;
                                                case 'En Proceso': echo 'bg-info'; break;
                                                case 'Cerrado': echo 'bg-secondary'; break;
                                                default: echo 'bg-light text-dark';
                                            }
                                        ?>">
                                        <?= htmlspecialchars($ticket['estado']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($ticket['prioridad']) ?></td>
                                <td><?= htmlspecialchars($ticket['creador_nombre']) ?></td>
                                <td><?= htmlspecialchars($ticket['asignado_nombre'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($ticket['actualizado_en'])) ?></td>
                                <td>
                                    <a href="<?= BASE ?>/soporte/ver/<?= $ticket['id'] ?>" class="btn btn-sm btn-info">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>