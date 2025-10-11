<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid py-4">
    <h1 class="fw-bold">Dashboard del Super Administrador</h1>
    <p class="text-muted mb-4">Visión general del sistema y actividad reciente.</p>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary fw-semibold">Usuarios Totales</div>
                    <div class="display-6 fw-bold"><?= $totalUsuarios ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-danger fw-semibold">Tickets Abiertos</div>
                    <div class="display-6 fw-bold"><?= $ticketsAbiertos ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success fw-semibold">Citas para Hoy</div>
                    <div class="display-6 fw-bold"><?= $citasHoy ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Usuarios por Rol</h5>
                    <canvas id="userRoleChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Tickets por Prioridad</h5>
                    <canvas id="ticketPriorityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Tickets -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Actividad Reciente</h5>
                    <?php if (empty($recentActivity)): ?>
                        <p class="text-muted">No hay actividad reciente.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recentActivity as $activity): ?>
                                <li class="list-group-item">
                                    <div class="fw-semibold"><?= htmlspecialchars($activity['accion']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($activity['detalle']) ?></div>
                                    <div class="small text-muted">Por: <?= htmlspecialchars($activity['actor']) ?> - <?= date('d/m/Y H:i', strtotime($activity['creado_en'])) ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <nav class="mt-3">
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Tickets Recientes</h5>
                    <?php if (empty($recentTickets)): ?>
                        <p class="text-muted">No hay tickets recientes.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recentTickets as $ticket): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <a href="/vetsmart/soporte/ver/<?= $ticket['id'] ?>" class="fw-semibold"><?= htmlspecialchars($ticket['asunto']) ?></a>
                                            <div class="small text-muted">Por: <?= htmlspecialchars($ticket['creador_nombre']) ?></div>
                                        </div>
                                        <span class="badge bg-info"><?= htmlspecialchars($ticket['prioridad']) ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart: Users by Role
    const userRoleCtx = document.getElementById('userRoleChart').getContext('2d');
    const userRoleData = JSON.parse('<?= $usuariosPorRol ?>');
    new Chart(userRoleCtx, {
        type: 'bar',
        data: {
            labels: userRoleData.map(d => d.nombre),
            datasets: [{
                label: 'Número de Usuarios',
                data: userRoleData.map(d => d.total),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart: Tickets by Priority
    const ticketPriorityCtx = document.getElementById('ticketPriorityChart').getContext('2d');
    const ticketPriorityData = JSON.parse('<?= $ticketsPorPrioridad ?>');
    new Chart(ticketPriorityCtx, {
        type: 'pie',
        data: {
            labels: ticketPriorityData.map(d => d.prioridad),
            datasets: [{
                label: 'Tickets',
                data: ticketPriorityData.map(d => d.total),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        }
    });
});
</script>