<div class="container-fluid py-3">
    <h2 class="fw-bold">✏️ Editar Servicio</h2>

    <div class="card p-3 mt-4">
        <div class="card-body">
            <form id="servicioForm" method="POST" action="/vetsmart/admin/servicios/<?= htmlspecialchars($servicio['id']) ?>/actualizar">
                <input type="hidden" name="id" value="<?= htmlspecialchars($servicio['id']) ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($servicio['nombre']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Duración (min)</label>
                        <input type="number" class="form-control" name="duracion_min" value="<?= htmlspecialchars($servicio['duracion_min']) ?>" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"><?= htmlspecialchars($servicio['descripcion']) ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Precio (COP)</label>
                        <input type="number" class="form-control" name="precio" value="<?= htmlspecialchars($servicio['precio']) ?>" required>
                    </div>

                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" name="activo" id="servicio_activo" value="1" <?= $servicio['activo'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="servicio_activo">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success shadow-sm">Actualizar Servicio</button>
                    <a href="/vetsmart/admin/servicios" class="btn btn-secondary shadow-sm">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>