<?php
/** @var array $empleados */
$empleados = $empleados ?? [];
?>
<div class="container py-4">
    <h2 class="fw-bold mb-4">🔒 Usuarios Bloqueados</h2>
    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>DNI</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php if (empty($empleados)): ?>
                        <tr>
                            <td colspan="6" class="text-muted">No hay usuarios bloqueados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($empleados as $e): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($e['nombre'].' '.$e['apellido']) ?></td>
                                <td><span class="badge bg-info"><?= htmlspecialchars($e['rol'] ?? '-') ?></span></td>
                                <td><?= htmlspecialchars($e['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($e['telefono'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($e['docusu'] ?? '-') ?></td>
                                <td>
                                    <a href="/vetsmart/admin/desbloquear_usuario/<?= $e['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Desbloquear este usuario?')">🔓 Desbloquear</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>