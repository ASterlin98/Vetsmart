<?php
// app/views/super_admin/config.php
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Configuración Global</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Configuración</li>
    </ol>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Configuración actualizada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Error al actualizar la configuración. Motivo: <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cogs me-1"></i>
            Ajustes Generales del Sistema
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php elseif (empty($settings)): ?>
                 <div class="alert alert-info">No hay ajustes de configuración definidos en la base de datos. Se pueden añadir nuevos desde el formulario.</div>
                 <form action="<?= BASE ?>/super_admin/actualizarConfiguracion" method="POST">
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Nombre del Sitio</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="">
                    </div>
                     <div class="mb-3">
                        <label for="contact_email" class="form-label">Email de Contacto Principal</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" value="">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            <?php else: ?>
                <form action="<?= BASE ?>/super_admin/actualizarConfiguracion" method="POST">

                    <div class="mb-3">
                        <label for="site_name" class="form-label">Nombre del Sitio</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>">
                        <small class="form-text text-muted">El nombre que aparece en el título de la página y en los correos.</small>
                    </div>

                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Email de Contacto Principal</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
                        <small class="form-text text-muted">La dirección de correo para notificaciones y contacto general.</small>
                    </div>

                    <div class="mb-3">
                        <label for="company_address" class="form-label">Dirección de la Empresa</label>
                        <input type="text" class="form-control" id="company_address" name="company_address" value="<?= htmlspecialchars($settings['company_address'] ?? '') ?>">
                        <small class="form-text text-muted">La dirección física de la clínica.</small>
                    </div>

                    <div class="mb-3">
                        <label for="company_phone" class="form-label">Teléfono de Contacto</label>
                        <input type="text" class="form-control" id="company_phone" name="company_phone" value="<?= htmlspecialchars($settings['company_phone'] ?? '') ?>">
                        <small class="form-text text-muted">El número de teléfono principal de la clínica.</small>
                    </div>

                    <div class="mb-3">
                        <label for="company_logo_url" class="form-label">URL del Logo</label>
                        <input type="text" class="form-control" id="company_logo_url" name="company_logo_url" value="<?= htmlspecialchars($settings['company_logo_url'] ?? '') ?>">
                        <small class="form-text text-muted">La ruta al archivo del logo (ej. /assets/img/logo.png).</small>
                    </div>

                    <div class="mb-3">
                        <label for="maintenance_mode" class="form-label">Modo Mantenimiento</label>
                        <select class="form-select" id="maintenance_mode" name="maintenance_mode">
                            <option value="0" <?= (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '0') ? 'selected' : '' ?>>Inactivo</option>
                            <option value="1" <?= (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'selected' : '' ?>>Activo</option>
                        </select>
                        <small class="form-text text-muted">Si está activo, solo los administradores podrán acceder al sitio.</small>
                    </div>

                    <div class="mb-3">
                        <label for="records_per_page" class="form-label">Registros por Página</label>
                        <input type="number" class="form-control" id="records_per_page" name="records_per_page" value="<?= htmlspecialchars($settings['records_per_page'] ?? '15') ?>">
                        <small class="form-text text-muted">Número de items a mostrar en las listas paginadas (ej. clientes, mascotas).</small>
                    </div>

                    <hr>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Pequeño script para cerrar las alertas
document.addEventListener('DOMContentLoaded', (event) => {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        new bootstrap.Alert(alert);
    });
});
</script>