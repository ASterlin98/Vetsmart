<?php
// app/views/super_admin/reportes.php

// Formatear números para una mejor visualización
function format_currency($number) {
    return '$' . number_format($number, 0, ',', '.');
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Reportes Generales</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Reportes</li>
    </ol>

    <!-- Filtros de Fecha -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filtrar por Rango de Fechas
        </div>
        <div class="card-body">
            <form method="GET" action="<?= BASE ?>/super_admin/reportes" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="desde" class="form-label">Desde:</label>
                    <input type="date" id="desde" name="desde" value="<?= htmlspecialchars($desde) ?>" class="form-control">
                </div>
                <div class="col-auto">
                    <label for="hasta" class="form-label">Hasta:</label>
                    <input type="date" id="hasta" name="hasta" value="<?= htmlspecialchars($hasta) ?>" class="form-control">
                </div>
                <div class="col-auto mt-4">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="<?= BASE ?>/super_admin/exportarReportes?desde=<?= htmlspecialchars($desde) ?>&hasta=<?= htmlspecialchars($hasta) ?>" class="btn btn-success" target="_blank">
                        <i class="fas fa-file-excel me-1"></i>
                        Exportar a Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPIs Principales -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="fs-4"><?= format_currency($stats['ingresos_totales'] ?? 0) ?></div>
                    <div class="small">Ingresos Totales (Completadas)</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="fs-4"><?= $stats['total_citas'] ?? 0 ?></div>
                    <div class="small">Citas Completadas</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="fs-4"><?= $citasPorEstado['pendiente'] ?? 0 ?></div>
                    <div class="small">Citas Pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="fs-4"><?= $citasPorEstado['cancelada'] ?? 0 ?></div>
                    <div class="small">Citas Canceladas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas y Gráficos -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Top 5 Servicios más Rentables
                </div>
                <div class="card-body">
                    <?php if (empty($topServicios)): ?>
                        <div class="alert alert-info">No hay datos suficientes para mostrar.</div>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th>Ingresos Generados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topServicios as $servicio): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($servicio['nombre']) ?></td>
                                        <td><?= format_currency($servicio['total_ingresos']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-clock me-1"></i>
                    Top 5 Empleados con más Citas
                </div>
                <div class="card-body">
                     <?php if (empty($topEmpleados)): ?>
                        <div class="alert alert-info">No hay datos suficientes para mostrar.</div>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Citas Atendidas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topEmpleados as $empleado): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($empleado['empleado']) ?></td>
                                        <td><?= $empleado['total_citas'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>