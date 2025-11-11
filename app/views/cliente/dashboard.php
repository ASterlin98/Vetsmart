<?php
// Variables de estadísticas
$mascotasTotal = $mascotasTotal ?? 0;
$proximasCitas = $proximasCitas ?? 0;
$proximaCitaFecha = $proximaCitaFecha ?? null;
$perfilCompleto = $perfilCompleto ?? 0;
$consultasTotales = $consultasTotales ?? 0;
$reportesDisponibles = $reportesDisponibles ?? 1;
?>

<style>
    .dashboard-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title i {
        color: #06b6d4;
        font-size: 1.8rem;
    }

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .dashboard-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #06b6d4 0%, #0ea5e9 100%);
    }

    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(6, 182, 212, 0.15);
        border-color: #06b6d4;
    }

    .card-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid rgba(6, 182, 212, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #06b6d4;
        font-size: 1.5rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .dashboard-card:hover .card-icon {
        background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
        color: white;
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(6, 182, 212, 0.3);
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .card-description {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.5;
    }

    .card-stat {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid rgba(6, 182, 212, 0.2);
        border-radius: 12px;
        padding: 1rem;
        margin: 1.5rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #06b6d4;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    .card-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        margin-top: auto;
    }

    .card-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(6, 182, 212, 0.4);
        color: white;
        text-decoration: none;
    }

    .card-action i {
        transition: transform 0.3s ease;
    }

    .card-action:hover i {
        transform: translateX(3px);
    }

    .welcome-banner {
        background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2.5rem;
        box-shadow: 0 8px 30px rgba(6, 182, 212, 0.3);
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .welcome-icon {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        flex-shrink: 0;
    }

    .welcome-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .welcome-content p {
        font-size: 1rem;
        opacity: 0.95;
        margin: 0;
    }

    @media (max-width: 768px) {
        .dashboard-cards {
            grid-template-columns: 1fr;
        }

        .welcome-banner {
            flex-direction: column;
            text-align: center;
        }

        .card-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dashboard-card {
        animation: fadeInUp 0.6s ease-out both;
    }

    .dashboard-section:nth-child(1) { animation-delay: 0s; }
    .dashboard-section:nth-child(2) { animation-delay: 0.1s; }
    .dashboard-section:nth-child(3) { animation-delay: 0.2s; }
</style>

<!-- Bienvenida -->
<div class="welcome-banner">
    <div class="welcome-icon">
        <i class="fas fa-user-circle"></i>
    </div>
    <div class="welcome-content">
        <h2>Bienvenido, <?= htmlspecialchars($nombre ?? 'Usuario') ?> <?= htmlspecialchars($apellido ?? '') ?></h2>
        <p>Este es tu panel de control personal. Aquí puedes gestionar tus mascotas, citas y más.</p>
    </div>
</div>

<!-- Mascotas -->
<div class="dashboard-section">
    <div class="section-title">
        <i class="fas fa-paw"></i>
        Mis Mascotas
    </div>
    <div class="dashboard-cards">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-paw"></i>
                </div>
                <div>
                    <div class="card-title">Gestión de Mascotas</div>
                    <div class="card-description">Consulta y gestiona la información de tus mascotas registradas</div>
                </div>
            </div>
            <div class="card-stat">
                <div>
                    <div class="stat-number"><?= (int)($mascotasTotal ?? 0) ?></div>
                    <div class="stat-label">mascotas activas</div>
                </div>
            </div>
            <a href="/vetsmart/cliente/mascotas" class="card-action">
                <span>Gestionar mascotas</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Citas y Consultas -->
<div class="dashboard-section">
    <div class="section-title">
        <i class="fas fa-calendar-check"></i>
        Citas y Consultas
    </div>
    <div class="dashboard-cards">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <div class="card-title">Mis Citas</div>
                    <div class="card-description">Revisa el estado de tus citas programadas</div>
                </div>
            </div>
            <div class="card-stat">
                <div>
                    <div class="stat-number"><?= (int)($proximasCitas ?? 0) ?></div>
                    <div class="stat-label">
                        <?php if (isset($proximaCitaFecha) && $proximaCitaFecha): ?>
                            próxima: <?= htmlspecialchars($proximaCitaFecha) ?>
                        <?php else: ?>
                            sin próximas
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <a href="/vetsmart/cliente/citas" class="card-action">
                <span>Ver citas</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div>
                    <div class="card-title">Historial Clínico</div>
                    <div class="card-description">Accede al historial médico completo</div>
                </div>
            </div>
            <div class="card-stat">
                <div>
                    <div class="stat-number"><?= (int)($consultasTotales ?? 0) ?></div>
                    <div class="stat-label">consultas totales</div>
                </div>
            </div>
            <a href="/vetsmart/cliente/historial" class="card-action">
                <span>Ver historial</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Información Personal y Reportes -->
<div class="dashboard-section">
    <div class="section-title">
        <i class="fas fa-chart-bar"></i>
        Mi Información
    </div>
    <div class="dashboard-cards">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-id-badge"></i>
                </div>
                <div>
                    <div class="card-title">Mi Perfil</div>
                    <div class="card-description">Consulta y actualiza tus datos personales</div>
                </div>
            </div>
            <div class="card-stat">
                <div>
                    <div class="stat-number"><?= (int)($perfilCompleto ?? 0) ?>%</div>
                    <div class="stat-label">perfil completo</div>
                </div>
            </div>
            <a href="/vetsmart/cliente/perfil" class="card-action">
                <span>Ver perfil</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <div class="card-title">Reportes</div>
                    <div class="card-description">Visualiza estadísticas del cuidado de tus mascotas</div>
                </div>
            </div>
            <div class="card-stat">
                <div>
                    <div class="stat-number"><?= (int)($reportesDisponibles ?? 1) ?></div>
                    <div class="stat-label">reportes disponibles</div>
                </div>
            </div>
            <a href="/vetsmart/cliente/reportes" class="card-action">
                <span>Ver reportes</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>