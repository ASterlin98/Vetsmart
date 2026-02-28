<?php
// Vista para agendar citas de peluquería
$servicios = $servicios ?? [];
$clientes = $clientes ?? [];
$empleado_id = $empleado_id ?? 0;
?>

<style>
    .form-section {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title i {
        color: #06b6d4;
        font-size: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        outline: none;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }

    .btn-primary {
        background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(6, 182, 212, 0.4);
    }

    .btn-secondary {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .alert {
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        border: 1px solid;
    }

    .alert-success {
        background: #f0fdf4;
        border-color: #86efac;
        color: #166534;
    }

    .alert-error {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #991b1b;
    }

    .required {
        color: #ef4444;
        font-weight: 600;
    }

    .helper-text {
        font-size: 0.9rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        .form-section {
            padding: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .form-actions button {
            width: 100%;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Mostrar mensajes de sesión -->
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['flash_success']) ?>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['flash_error']) ?>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <!-- Formulario de agendamiento -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-plus-circle"></i>
                    Agendar Nueva Cita de Peluquería
                </div>

                <form method="POST" action="<?= BASE ?>/peluquero/guardar-cita-peluqueria" id="agendarForm">
                    <!-- Cliente -->
                    <div class="form-group">
                        <label for="cliente_id">Cliente <span class="required">*</span></label>
                        <select class="form-select" id="cliente_id" name="cliente_id" required onchange="cargarMascotas()">
                            <option value="">Selecciona un cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id'] ?>">
                                    <?= htmlspecialchars($cliente['nombre_completo']) ?> (<?= htmlspecialchars($cliente['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="helper-text">Selecciona el cliente propietario de la mascota</div>
                    </div>

                    <!-- Mascota -->
                    <div class="form-group">
                        <label for="mascota_id">Mascota <span class="required">*</span></label>
                        <select class="form-select" id="mascota_id" name="mascota_id" required>
                            <option value="">Primero selecciona un cliente</option>
                        </select>
                        <div class="helper-text">Las mascotas del cliente seleccionado aparecerán aquí</div>
                    </div>

                    <!-- Servicio y Fecha -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="servicio_id">Servicio <span class="required">*</span></label>
                            <select class="form-select" id="servicio_id" name="servicio_id" required>
                                <option value="">Selecciona un servicio</option>
                                <?php foreach ($servicios as $servicio): ?>
                                    <option value="<?= $servicio['id'] ?>">
                                        <?= htmlspecialchars($servicio['nombre']) ?> (<?= $servicio['duracion_min'] ?? 60 ?> min)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha">Fecha <span class="required">*</span></label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <!-- Hora -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hora">Hora <span class="required">*</span></label>
                            <input type="time" class="form-control" id="hora" name="hora" required>
                            <div class="helper-text">Horario disponible de 8:00 a 18:00</div>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="form-group">
                        <label for="notas">Notas Adicionales</label>
                        <textarea class="form-control" id="notas" name="notas" rows="4" placeholder="Ej: Instrucciones especiales, alergias, observaciones..."></textarea>
                    </div>

                    <!-- Botones de acción -->
                    <div class="form-actions">
                        <a href="<?= BASE ?>/peluquero/agenda" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Cargar mascotas del cliente seleccionado
    function cargarMascotas() {
        const clienteId = document.getElementById('cliente_id').value;
        const mascotaSelect = document.getElementById('mascota_id');

        if (!clienteId) {
            mascotaSelect.innerHTML = '<option value="">Primero selecciona un cliente</option>';
            return;
        }

        // Realizar solicitud AJAX para obtener mascotas
        fetch(`<?= BASE ?>/api/clientes/${clienteId}/mascotas`)
            .then(response => response.json())
            .then(mascotas => {
                if (!Array.isArray(mascotas) || mascotas.length === 0) {
                    mascotaSelect.innerHTML = '<option value="">No hay mascotas para este cliente</option>';
                    return;
                }

                let html = '<option value="">Selecciona una mascota</option>';
                mascotas.forEach(mascota => {
                    html += `<option value="${mascota.id}">${mascota.nombre || 'Sin nombre'}</option>`;
                });
                mascotaSelect.innerHTML = html;
            })
            .catch(error => {
                console.error('Error al cargar mascotas:', error);
                mascotaSelect.innerHTML = '<option value="">Error al cargar mascotas</option>';
            });
    }

    // Validar formulario antes de enviar
    document.getElementById('agendarForm').addEventListener('submit', function(e) {
        const cliente = document.getElementById('cliente_id').value;
        const mascota = document.getElementById('mascota_id').value;
        const servicio = document.getElementById('servicio_id').value;
        const fecha = document.getElementById('fecha').value;
        const hora = document.getElementById('hora').value;

        if (!cliente || !mascota || !servicio || !fecha || !hora) {
            e.preventDefault();
            alert('Por favor completa todos los campos obligatorios.');
        }
    });
</script>
