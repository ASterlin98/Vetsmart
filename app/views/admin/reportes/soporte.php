<?php
// app/views/admin/reportes/soporte.php
?>

<div class="container">
    <h2>Crear Ticket de Soporte</h2>
    <form action="/vetsmart/admin/guardarTicket" method="post">
        <div class="mb-3">
            <label for="asunto" class="form-label">Asunto</label>
            <input type="text" class="form-control" id="asunto" name="asunto" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label for="rol_problema" class="form-label">Rol con el Problema</label>
            <select class="form-select" id="rol_problema" name="rol_problema" required>
                <option value="">Seleccione un rol</option>
                <option value="admin">Admin</option>
                <option value="veterinario">Veterinario</option>
                <option value="recepcionista">Recepcionista</option>
                <option value="peluquero">Peluquero</option>
                <option value="cliente">Cliente</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="prioridad" class="form-label">Prioridad</label>
            <select class="form-select" id="prioridad" name="prioridad" required>
                <option value="Baja">Baja</option>
                <option value="Media">Media</option>
                <option value="Alta">Alta</option>
                <option value="Urgente">Urgente</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Ticket</button>
    </form>
</div>