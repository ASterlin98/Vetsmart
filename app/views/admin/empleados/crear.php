<?php
// Si en empleadosIndex pasas $roles, úsalo. Aquí asumimos que $roles pudo ser pasado.
$roles = $roles ?? [
  ['id'=>2,'nombre'=>'admin'],
  ['id'=>3,'nombre'=>'recepcionista'],
  ['id'=>4,'nombre'=>'veterinario'],
  ['id'=>5,'nombre'=>'peluquero'],
];
?>
<h2>Crear Empleado</h2>
<form method="POST" action="/vetsmart/admin/empleados/guardar">
  <div>
    <label>Nombre</label>
    <input type="text" name="nombre" required>
  </div>
  <div>
    <label>Apellido</label>
    <input type="text" name="apellido" required>
  </div>
  <div>
    <label>Email</label>
    <input type="email" name="email" required>
  </div>
  <div>
    <label>Password</label>
    <input type="password" name="password" required>
  </div>

  <div>
    <label>Rol</label>
    <select name="role_id" required>
      <?php foreach($roles as $r): ?>
        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label>Especialidad</label>
    <input type="text" name="especialidad">
  </div>
  <div>
    <label>Salario</label>
    <input type="number" name="salario" step="0.01">
  </div>
  <div>
    <label>Fecha de ingreso</label>
    <input type="date" name="fecha_ingreso">
  </div>
  <div>
    <label>Activo</label>
    <input type="checkbox" name="activo" checked>
  </div>

  <button type="submit">Crear</button>
</form>
