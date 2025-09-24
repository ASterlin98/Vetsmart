<div class="container">
    <h2 class="mb-4">Gestión de Permisos por Rol</h2>

    <div class="mb-3">
        <label for="roleSelect" class="form-label">Selecciona un Rol:</label>
        <select id="roleSelect" class="form-select">
            <option disabled selected>-- Selecciona un rol --</option>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="permisosContainer" class="d-none mt-4">
        <form id="permisosForm">
            <?php foreach ($permisosPorModulo as $modulo => $permisos): ?>
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white fw-bold">
                        <?= htmlspecialchars($modulo) ?>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($permisos as $permiso): ?>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input permiso-checkbox"
                                            type="checkbox"
                                            name="permisos[]"
                                            value="<?= $permiso['id'] ?>"
                                            id="permiso<?= $permiso['id'] ?>">
                                        <label class="form-check-label" for="permiso<?= $permiso['id'] ?>">
                                            <?= htmlspecialchars($permiso['accion']) ?> (<?= htmlspecialchars($permiso['nombre']) ?>)
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-success">Guardar Permisos</button>
        </form>
    </div>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function () {
    const roleId = this.value;
    if (!roleId) return;

    fetch(`/vetsmart/superadmin/permisos/rol/${roleId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('permisosContainer').classList.remove('d-none');
            document.querySelectorAll('.permiso-checkbox').forEach(cb => {
                cb.checked = data.permisosAsignados.includes(cb.value);
            });
        });
});

document.getElementById('permisosForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const roleId = document.getElementById('roleSelect').value;
    const permisosSeleccionados = Array.from(document.querySelectorAll('.permiso-checkbox:checked')).map(cb => cb.value);

    fetch('/vetsmart/superadmin/permisos/actualizar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            role_id: roleId,
            permisos: permisosSeleccionados
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Permisos actualizados correctamente.');
        } else {
            alert('Error al actualizar permisos.');
        }
    });
});
</script>