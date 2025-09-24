<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reporte Veterinario</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
    h2 { text-align:center; margin-bottom:4px; }
    p.meta { text-align:center; margin-top:0; margin-bottom:12px; font-size:12px; }
    table { width:100%; border-collapse: collapse; margin-bottom: 12px; }
    th, td { border: 1px solid #333; padding: 6px; vertical-align: top; }
    th { background: #eee; }
    footer { font-size: 10px; text-align:center; margin-top:6px; position: fixed; bottom: 10px; width: 100%; }
  </style>
</head>
<body>
  <h2>Reporte Veterinario</h2>
  <p class="meta">Desde: <?= htmlspecialchars($desde) ?> &nbsp; | &nbsp; Hasta: <?= htmlspecialchars($hasta) ?></p>

  <h3>Consultas (<?= count($consultas) ?>)</h3>
  <table>
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Mascota</th>
        <th>Motivo</th>
        <th>Veterinario</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($consultas)): ?>
        <tr><td colspan="4">No hay consultas en el rango seleccionado.</td></tr>
      <?php else: foreach ($consultas as $c): ?>
        <tr>
          <td><?= htmlspecialchars($c['creado_en'] ?? '') ?></td>
          <td><?= htmlspecialchars($c['nombre_mascota'] ?? '') ?></td>
          <td><?= htmlspecialchars($c['motivo'] ?? '') ?></td>
          <td><?= htmlspecialchars($c['nombre_veterinario'] ?? '') ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

  <h3>Citas (<?= count($citas) ?>)</h3>
  <table>
    <thead>
      <tr>
        <th>Fecha</th><th>Mascota</th><th>Cliente</th><th>Servicio</th><th>Notas</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($citas)): ?>
        <tr><td colspan="5">No hay citas en el rango seleccionado.</td></tr>
      <?php else: foreach ($citas as $c): ?>
        <tr>
          <td><?= htmlspecialchars($c['fecha'] ?? '') ?></td>
          <td><?= htmlspecialchars($c['nombre_mascota'] ?? '') ?></td>
          <td><?= htmlspecialchars(trim(($c['cliente_nombre'] ?? '') . ' ' . ($c['cliente_apellido'] ?? ''))) ?></td>
          <td><?= htmlspecialchars($c['nombre_servicio'] ?? '') ?></td>
          <td><?= htmlspecialchars($c['notas'] ?? '') ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

  <footer>Reporte generado: <?= date('Y-m-d H:i') ?></footer>
</body>
</html>
