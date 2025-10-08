<h2>Agregar Mascota</h2>
<form method="POST" action="/vetsmart/admin/clientes/<?= $cliente_id ?>/mascotas/guardar">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Especie</label>
        <select name="especie" class="form-control" required>
            <option value="">-- Selecciona una especie --</option>
            <option value="Perro">Perro</option>
            <option value="Gato">Gato</option>
            <option value="Ave">Ave</option>
            <option value="Reptil">Reptil</option>
            <option value="Roedor">Roedor</option>
            <option value="Otro">Otro</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Raza</label>
        <input type="text" name="raza" class="form-control">
    </div>

    <div class="mb-3">
        <label>Edad</label>
        <input type="number" name="edad" class="form-control">
    </div>

    <div class="mb-3">
        <label>Peso (kg)</label>
        <input type="number" step="0.01" name="peso" class="form-control">
    </div>

    <div class="mb-3">
        <label>Notas</label>
        <textarea name="notas" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Guardar Mascota</button>
</form>
