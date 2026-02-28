<h2 class="mb-4">Listado de Clientes</h2>
<a href="<?= BASE ?>/admin/clientes/crear" class="btn btn-primary mb-3">➕ Nuevo Cliente</a>

<?php
    $page = $page ?? 1;
    $perPage = $perPage ?? count($clientes);
    $total = $total ?? count($clientes);
    $totalPages = $totalPages ?? 1;
    $start = ($page - 1) * $perPage + 1;
?>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th style="width:70px;">#</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Ciudad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($clientes)): foreach ($clientes as $i => $c): ?>
                <tr>
                    <td class="fw-medium text-muted"><?= $start + $i ?></td>
                    <td><?= htmlspecialchars(($c['nombre'] ?? '') . ' ' . ($c['apellido'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($c['docusu'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['telefono'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['ciudad'] ?? '-') ?></td>
                    <td>
                        <a href="<?= BASE ?>/admin/clientes/<?= $c['id'] ?>" class="btn btn-sm btn-info">👁️ Ver</a>
                        <a href="<?= BASE ?>/admin/clientes/<?= $c['id'] ?>/editar" class="btn btn-sm btn-warning">✏️ Editar</a>
                        <a href="<?= BASE ?>/admin/clientes/<?= $c['id'] ?>/eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar cliente?')">🗑️ Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No hay clientes registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1):
        // Construir base URL sin querystring para enlaces de paginación
        $currentPath = strtok($_SERVER['REQUEST_URI'], '?');
        $baseUrl = $currentPath ?: BASE . '/admin/clientes';
?>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">Mostrando <strong><?= $start ?></strong> - <strong><?= min($start + count($clientes) - 1, $total) ?></strong> de <strong><?= $total ?></strong></div>
        <nav aria-label="Paginación clientes">
            <ul class="pagination mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars($baseUrl . '?page=' . max(1, $page - 1)) ?>" aria-label="Anterior">&laquo; Anterior</a>
                </li>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>" aria-current="<?= $p === $page ? 'page' : '' ?>">
                        <a class="page-link" href="<?= htmlspecialchars($baseUrl . '?page=' . $p) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars($baseUrl . '?page=' . min($totalPages, $page + 1)) ?>" aria-label="Siguiente">Siguiente &raquo;</a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>
