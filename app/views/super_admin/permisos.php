<?php
// app/views/super_admin/permisos.php
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Permisos del Sistema</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/vetsmart/super_admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Permisos</li>
    </ol>

    <!-- Notificaciones Flash -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Asignar Permisos por Rol -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-shield me-1"></i>
            Asignar Permisos por Rol
        </div>
        <div class="card-body">
            <?php if (empty($roles)) : ?>
                <div class="alert alert-warning">No hay roles en el sistema. Debe crearlos en la sección correspondiente.</div>
            <?php else : ?>
                <div class="row">
                    <div class="col-md-4">
                        <label for="role-select" class="form-label">Seleccione un Rol:</label>
                        <select id="role-select" class="form-select">
                            <option value="">-- Seleccione un rol --</option>
                            <?php foreach ($roles as $role) : ?>
                                <?php if ($role['id'] != 1) : // Super Admin has all permissions ?>
                                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['nombre']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr>

                <div id="permissions-container" class="mt-3" style="display: none;">
                    <h4 id="permissions-header">Permisos para el Rol: <span id="selected-role-name" class="text-primary"></span></h4>
                    <div id="permissions-content" class="mt-3">
                        <!-- Permissions will be loaded here dynamically -->
                    </div>
                    <div id="loading-spinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                    <div id="feedback-message" class="mt-3"></div>
                    <button id="save-permissions-btn" class="btn btn-success mt-3" style="display: none;"><i class="fas fa-save me-1"></i> Guardar Permisos del Rol</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Listado de Permisos del Sistema -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-key me-1"></i>
                Listado de Permisos
            </span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                <i class="fas fa-plus me-1"></i>
                Crear Permiso
            </button>
        </div>
        <div class="card-body">
            <?php if (empty($permisosAgrupados)): ?>
                <div class="alert alert-info">No hay permisos definidos en el sistema. ¡Crea el primero!</div>
            <?php else: ?>
                <?php foreach ($permisosAgrupados as $modulo => $permisos): ?>
                    <div class="mb-4">
                        <h4 class="text-capitalize border-bottom pb-2 mb-3"><?= htmlspecialchars($modulo) ?></h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre (Clave)</th>
                                        <th>Descripción</th>
                                        <th>Acción</th>
                                        <th>Orden</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($permisos as $permiso): ?>
                                        <tr>
                                            <td><?= $permiso['id'] ?></td>
                                            <td><code><?= htmlspecialchars($permiso['nombre']) ?></code></td>
                                            <td><?= htmlspecialchars($permiso['descripcion']) ?></td>
                                            <td><?= htmlspecialchars($permiso['accion']) ?></td>
                                            <td><?= $permiso['orden'] ?></td>
                                            <td>
                                                <span class="badge <?= $permiso['activo'] ? 'bg-success' : 'bg-secondary' ?>">
                                                    <?= $permiso['activo'] ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning edit-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editPermissionModal"
                                                        data-id="<?= $permiso['id'] ?>"
                                                        data-modulo="<?= htmlspecialchars($permiso['modulo']) ?>"
                                                        data-nombre="<?= htmlspecialchars($permiso['nombre']) ?>"
                                                        data-descripcion="<?= htmlspecialchars($permiso['descripcion']) ?>"
                                                        data-accion="<?= htmlspecialchars($permiso['accion']) ?>"
                                                        data-orden="<?= $permiso['orden'] ?>"
                                                        data-activo="<?= $permiso['activo'] ?>">
                                                    Editar
                                                </button>
                                                <a href="/vetsmart/super_admin/eliminarPermiso/<?= $permiso['id'] ?>"
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('¿Estás seguro de que deseas eliminar este permiso? Esta acción no se puede deshacer.');">
                                                    Eliminar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para Crear Permiso -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPermissionModalLabel">Crear Nuevo Permiso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/vetsmart/super_admin/guardarPermiso" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_modulo" class="form-label">Módulo</label>
                        <select class="form-select" id="create_modulo" name="modulo">
                            <?php foreach($modulos as $modulo): ?>
                                <option value="<?= htmlspecialchars($modulo) ?>"><?= htmlspecialchars(ucfirst($modulo)) ?></option>
                            <?php endforeach; ?>
                        </select>
                         <input type="text" class="form-control mt-2" id="create_nuevo_modulo" name="nuevo_modulo" placeholder="O escribe un nuevo módulo aquí">
                    </div>
                    <div class="mb-3">
                        <label for="create_nombre" class="form-label">Nombre (Clave)</label>
                        <input type="text" class="form-control" id="create_nombre" name="nombre" placeholder="ej: modulo.accion" required>
                    </div>
                     <div class="mb-3">
                        <label for="create_accion" class="form-label">Acción</label>
                        <input type="text" class="form-control" id="create_accion" name="accion" placeholder="ej: ver, crear, editar" required>
                    </div>
                    <div class="mb-3">
                        <label for="create_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="create_descripcion" name="descripcion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="create_orden" class="form-label">Orden</label>
                        <input type="number" class="form-control" id="create_orden" name="orden" value="0">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="create_activo" name="activo" checked>
                        <label class="form-check-label" for="create_activo">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Permiso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Permiso -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPermissionModalLabel">Editar Permiso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/vetsmart/super_admin/actualizarPermiso" method="POST">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_modulo" class="form-label">Módulo</label>
                        <select class="form-select" id="edit_modulo" name="modulo">
                             <?php foreach($modulos as $modulo): ?>
                                <option value="<?= htmlspecialchars($modulo) ?>"><?= htmlspecialchars(ucfirst($modulo)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre (Clave)</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                     <div class="mb-3">
                        <label for="edit_accion" class="form-label">Acción</label>
                        <input type="text" class="form-control" id="edit_accion" name="accion" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_orden" class="form-label">Orden</label>
                        <input type="number" class="form-control" id="edit_orden" name="orden">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="edit_activo" name="activo">
                        <label class="form-check-label" for="edit_activo">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Permiso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editPermissionModal = document.getElementById('editPermissionModal');
    editPermissionModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const modulo = button.getAttribute('data-modulo');
        const nombre = button.getAttribute('data-nombre');
        const descripcion = button.getAttribute('data-descripcion');
        const accion = button.getAttribute('data-accion');
        const orden = button.getAttribute('data-orden');
        const activo = button.getAttribute('data-activo');

        const modalTitle = editPermissionModal.querySelector('.modal-title');
        const modalBodyInputId = editPermissionModal.querySelector('#edit_id');
        const modalBodyInputModulo = editPermissionModal.querySelector('#edit_modulo');
        const modalBodyInputNombre = editPermissionModal.querySelector('#edit_nombre');
        const modalBodyInputDescripcion = editPermissionModal.querySelector('#edit_descripcion');
        const modalBodyInputAccion = editPermissionModal.querySelector('#edit_accion');
        const modalBodyInputOrden = editPermissionModal.querySelector('#edit_orden');
        const modalBodyInputActivo = editPermissionModal.querySelector('#edit_activo');

        modalTitle.textContent = 'Editar Permiso #' + id;
        modalBodyInputId.value = id;
        modalBodyInputModulo.value = modulo;
        modalBodyInputNombre.value = nombre;
        modalBodyInputDescripcion.value = descripcion;
        modalBodyInputAccion.value = accion;
        modalBodyInputOrden.value = orden;
        modalBodyInputActivo.checked = activo == '1';
    });
});
</script>

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
    if (!roleSelect) return; // Si no existe el selector de rol, no hacer nada.

    const permissionsContainer = document.getElementById('permissions-container');
    const permissionsContent = document.getElementById('permissions-content');
    const selectedRoleName = document.getElementById('selected-role-name');
    const saveBtn = document.getElementById('save-permissions-btn');
    const loadingSpinner = document.getElementById('loading-spinner');
    const feedbackMessage = document.getElementById('feedback-message');

    roleSelect.addEventListener('change', function() {
        const roleId = this.value;
        const roleName = this.options[this.selectedIndex].text;

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

        const baseUrl = window.location.origin;
        const basePath = '/vetsmart';
        const fetchUrl = `${baseUrl}${basePath}/app/api/permissions_api.php?role_id=${roleId}`;

        fetch(fetchUrl)
            .then(response => {
                if (!response.ok) throw new Error(`Error en la red: ${response.statusText}`);
                return response.json();
            })
            .then(data => {
                loadingSpinner.style.display = 'none';
                if (data.error) throw new Error(data.error);

                renderPermissions(data.all_permissions, data.assigned_permissions);
                saveBtn.style.display = 'block';
            })
            .catch(error => {
                loadingSpinner.style.display = 'none';
                permissionsContent.innerHTML = `<div class="alert alert-danger">Error al cargar los permisos: ${error.message}</div>`;
            });
    });

    function renderPermissions(allPermissions, assignedPermissions) {
        permissionsContent.innerHTML = '';
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

        const checkedPermissions = Array.from(permissionsContent.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value);

        feedbackMessage.innerHTML = '';
        feedbackMessage.className = 'mt-3';
        loadingSpinner.style.display = 'block';
        saveBtn.disabled = true;

        const baseUrl = window.location.origin;
        const basePath = '/vetsmart';
        const postUrl = `${baseUrl}${basePath}/app/api/permissions_api.php`;

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