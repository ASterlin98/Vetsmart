<?php
// Vista de reportes del peluquero
$totalCitasMes = $totalCitasMes ?? 0;
$citasCompletadasMes = $citasCompletadasMes ?? 0;
$ingresosMes = $ingresosMes ?? 0;
$clientesAtendidos = $clientesAtendidos ?? 0;
$serviciosMasUsados = $serviciosMasUsados ?? [];
$ultimasCitas = $ultimasCitas ?? [];
$mesActual = $mesActual ?? date('Y-m');

$porcentajeCompletadas = $totalCitasMes > 0 ? round(($citasCompletadasMes / $totalCitasMes) * 100, 1) : 0;
$fechaMes = new DateTime($mesActual . '-01');
$mesNombre = $fechaMes->format('F Y');
?>

<style>
    .reportes-container {
        padding: 1.5rem 0;
    }

    .header-reportes {
        display: flex;
        align-items: center;
        justify-content: between;
        margin-bottom: 2rem;
        gap: 1rem;
    }

    .header-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .header-title h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .header-title p {
        color: #64748b;
        margin: 0.25rem 0 0 0;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .stat-card .icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .stat-card .value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-card .label {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .stat-card .subtitle {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.5rem;
    }

    /* Card Styles */
    .report-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .report-card-header {
        background: #f8fafc;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .report-card-header h6 {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .report-card-body {
        padding: 1.5rem;
    }

    /* Table */
    .table-reportes {
        width: 100%;
        border-collapse: collapse;
    }

    .table-reportes thead {
        background: #f8fafc;
    }

    .table-reportes th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-reportes td {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-reportes tbody tr:hover {
        background: #f8fafc;
    }

    .badge-estado {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-completada {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .badge-pendiente {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .badge-confirmada {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #64748b;
        margin: 0;
    }

    /* Grid Layout */
    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    @media (max-width: 768px) {
        .header-reportes {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-title h1 {
            font-size: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .content-grid {
            grid-template-columns: 1fr;
        }

        .table-reportes {
            font-size: 0.9rem;
        }

        .table-reportes th,
        .table-reportes td {
            padding: 0.75rem;
        }
    }
</style>

<div class="reportes-container container-fluid">
    <!-- Header -->
    <div class="header-reportes">
        <div class="header-icon">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="header-title">
            <h1>Reportes de Peluquería</h1>
            <p>Estadísticas del mes de <?= $mesNombre ?></p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="value"><?= $totalCitasMes ?></div>
            <div class="label">Citas Agendadas</div>
            <div class="subtitle">Total del mes</div>
        </div>

        <div class="stat-card">
            <div class="icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="value"><?= $citasCompletadasMes ?></div>
            <div class="label">Citas Completadas</div>
            <div class="subtitle"><?= $porcentajeCompletadas ?>% de éxito</div>
        </div>

        <div class="stat-card">
            <div class="icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="value">$<?= number_format($ingresosMes, 0) ?></div>
            <div class="label">Ingresos del Mes</div>
            <div class="subtitle">Por servicios realizados</div>
        </div>

        <div class="stat-card">
            <div class="icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="value"><?= $clientesAtendidos ?></div>
            <div class="label">Clientes Atendidos</div>
            <div class="subtitle">Únicos en el mes</div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Servicios Más Solicitados -->
        <div class="report-card">
            <div class="report-card-header">
                <h6>
                    <i class="fas fa-chart-pie" style="color: #06b6d4;"></i>
                    Servicios Más Solicitados
                </h6>
            </div>
            <div class="report-card-body">
                <?php if (!empty($serviciosMasUsados)): ?>
                    <table class="table-reportes">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th style="text-align: right;">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($serviciosMasUsados as $servicio): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($servicio['nombre']) ?></strong>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="badge bg-primary"><?= $servicio['cantidad'] ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No hay datos disponibles</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Últimas Citas -->
        <div class="report-card">
            <div class="report-card-header">
                <h6>
                    <i class="fas fa-list" style="color: #10b981;"></i>
                    Últimas Citas Realizadas
                </h6>
            </div>
            <div class="report-card-body">
                <?php if (!empty($ultimasCitas)): ?>
                    <div style="overflow-x: auto;">
                        <table class="table-reportes">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimasCitas as $cita): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($cita['cliente']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($cita['mascota']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($cita['servicio']) ?></td>
                                        <td>
                                            <small><?= $cita['fecha'] ?> <?= $cita['hora'] ?></small>
                                        </td>
                                        <td>
                                            <span class="badge-estado badge-<?= strtolower($cita['estado']) ?>">
                                                <?= ucfirst($cita['estado']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No hay citas en este mes</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
