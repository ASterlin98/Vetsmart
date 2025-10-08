<?php
// app/views/super_admin/roles_index.php
// Este archivo es responsable de renderizar la interfaz de usuario para la gestión de roles y permisos.
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Roles y Permisos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Roles y Permisos</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-shield me-1"></i>
            Seleccione un Rol para Administrar sus Permisos
        </div>
        <div class="card-body">
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php else : ?>
                <div class="row">
                    <div class="col-md-4">
                        <label for="role-select" class="form-label">Rol:</label>
                        <select id="role-select" class="form-select">
                            <option value="">-- Seleccione un rol --</option>
                            <?php foreach ($roles as $role) : ?>
                                <?php if ($role['id'] != 1) : // Ocultar super_admin de la lista ?>
                                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['nombre']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted"><?= htmlspecialchars($roles[array_search('super_admin', array_column($roles, 'nombre'))]['descripcion']) ?></small>
                    </div>
                </div>

                <hr>

                <div id="permissions-container" class="mt-3" style="display: none;">
                    <h4 id="permissions-header">Permisos para el Rol: <span id="selected-role-name" class="text-primary"></span></h4>
                    <div id="permissions-content" class="mt-3">
                        <!-- Los permisos se cargarán aquí dinámicamente -->
                    </div>
                    <div id="loading-spinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                    <div id="feedback-message" class="mt-3"></div>
                    <button id="save-permissions-btn" class="btn btn-primary mt-3" style="display: none;">Guardar Cambios</button>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .permission-module {
        margin-bottom: 1.5rem;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 1rem;
    }
    .permission-module h5 {
        border-bottom: 2px solid #007bff;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        text-transform: capitalize;
    }
    .permission-group {
        display: flex;
        flex-wrap: wrap;
    }
    .permission-item {
        flex: 1 1 300px; /* Flex-grow, flex-shrink, flex-basis */
        margin-right: 1rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role-select');
    const permissionsContainer = document.getElementById('permissions-container');
    const permissionsContent = document.getElementById('permissions-content');
    const selectedRoleName = document.getElementById('selected-role-name');
    const saveBtn = document.getElementById('save-permissions-btn');
    const loadingSpinner = document.getElementById('loading-spinner');
    const feedbackMessage = document.getElementById('feedback-message');

    roleSelect.addEventListener('change', function() {
        const roleId = this.value;
        const roleName = this.options[this.selectedIndex].text;

        // Limpiar y ocultar el contenedor de permisos
        permissionsContent.innerHTML = '';
        saveBtn.style.display = 'none';
        feedbackMessage.innerHTML = '';

        if (!roleId) {
            permissionsContainer.style.display = 'none';
            return;
        }

        permissionsContainer.style.display = 'block';
        selectedRoleName.textContent = roleName;
        loadingSpinner.style.display = 'block';

        // Construir la URL de forma segura
        const baseUrl = window.location.origin;
        const fetchUrl = `${baseUrl}/super_admin/getPermisosPorRol/${roleId}`;

        fetch(fetchUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la red: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                loadingSpinner.style.display = 'none';
                if (data.error) {
                    throw new Error(data.error);
                }

                renderPermissions(data.all_permissions, data.assigned_permissions);
                saveBtn.style.display = 'block';
            })
            .catch(error => {
                loadingSpinner.style.display = 'none';
                permissionsContent.innerHTML = `<div class="alert alert-danger">Error al cargar los permisos: ${error.message}</div>`;
            });
    });

    function renderPermissions(allPermissions, assignedPermissions) {
        permissionsContent.innerHTML = ''; // Limpiar antes de renderizar
        for (const module in allPermissions) {
            const moduleContainer = document.createElement('div');
            moduleContainer.className = 'permission-module';

            const moduleTitle = document.createElement('h5');
            moduleTitle.textContent = module.replace(/_/g, ' ');
            moduleContainer.appendChild(moduleTitle);

            const permissionGroup = document.createElement('div');
            permissionGroup.className = 'permission-group';

            allPermissions[module].forEach(permission => {
                const isChecked = assignedPermissions.includes(String(permission.id)) || assignedPermissions.includes(Number(permission.id));

                const itemDiv = document.createElement('div');
                itemDiv.className = 'form-check form-switch permission-item';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'form-check-input';
                checkbox.value = permission.id;
                checkbox.id = `perm-${permission.id}`;
                checkbox.checked = isChecked;

                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.htmlFor = `perm-${permission.id}`;
                label.textContent = permission.descripcion || permission.nombre;

                itemDiv.appendChild(checkbox);
                itemDiv.appendChild(label);
                permissionGroup.appendChild(itemDiv);
            });

            moduleContainer.appendChild(permissionGroup);
            permissionsContent.appendChild(moduleContainer);
        }
    }

    saveBtn.addEventListener('click', function() {
        const roleId = roleSelect.value;
        if (!roleId) {
            alert('Por favor, seleccione un rol primero.');
            return;
        }

        const checkedPermissions = [];
        permissionsContent.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            checkedPermissions.push(checkbox.value);
        });

        feedbackMessage.innerHTML = '';
        feedbackMessage.className = 'mt-3'; // Reset class
        loadingSpinner.style.display = 'block';
        saveBtn.disabled = true;

        const baseUrl = window.location.origin;
        const postUrl = `${baseUrl}/super_admin/actualizarPermisos`;

        fetch(postUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                role_id: roleId,
                permission_ids: checkedPermissions
            })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (status >= 200 && status < 300 && body.success) {
                feedbackMessage.textContent = body.message || 'Permisos actualizados con éxito.';
                feedbackMessage.classList.add('alert', 'alert-success');
            } else {
                throw new Error(body.message || 'Ocurrió un error desconocido.');
            }
        })
        .catch(error => {
            feedbackMessage.textContent = `Error: ${error.message}`;
            feedbackMessage.classList.add('alert', 'alert-danger');
        })
        .finally(() => {
            loadingSpinner.style.display = 'none';
            saveBtn.disabled = false;
        });
    });
});
</script>