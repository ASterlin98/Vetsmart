<?php
// Espera: $mascota, $citas, $consultas, $notas, $logoB64
$nombre = htmlspecialchars($mascota['nombre'] ?? 'Mascota');
$dueno  = trim(($mascota['dueno_nombre'] ?? '') . ' ' . ($mascota['dueno_apellido'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#111; }
    .header { display:flex; align-items:center; gap:16px; margin-bottom:8px; }
    .title { font-size: 20px; font-weight: 700; }
    .subtitle { color:#666; }
    .section { margin-top:16px; }
    .section h3 { margin:0 0 8px 0; border-bottom:1px solid #ccc; padding-bottom:4px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #e0e0e0; padding:6px; }
    th { background:#f7f7f7; text-align:left; }
    .muted { color:#666; }
  </style>
  <title>Historial Clínico - <?= $nombre ?></title>
  </head>
  <body>
    <div class="header">
      <?php if (!empty($logoB64)): ?>
        <img src="<?= $logoB64 ?>" alt="Logo" width="60" height="60"/>
      <?php endif; ?>
      <div>
        <div class="title">Historial Clínico</div>
        <div class="subtitle">Mascota: <strong><?= $nombre ?></strong> <?= $dueno ? ' | Dueño: ' . htmlspecialchars($dueno) : '' ?></div>
      </div>
    </div>

    <div class="section">
      <h3>Datos de la Mascota</h3>
      <table>
        <tr>
          <th>Nombre</th><td><?= $nombre ?></td>
          <th>Especie</th><td><?= htmlspecialchars($mascota['especie'] ?? '-') ?></td>
        </tr>
        <tr>
          <th>Raza</th><td><?= htmlspecialchars($mascota['raza'] ?? '-') ?></td>
          <th>Sexo</th><td><?= htmlspecialchars($mascota['sexo'] ?? '-') ?></td>
        </tr>
        <tr>
          <th>Edad</th><td><?= htmlspecialchars($mascota['edad'] ?? '-') ?></td>
          <th>Notas</th><td><?= htmlspecialchars($mascota['notas'] ?? '-') ?></td>
        </tr>
      </table>
    </div>

    <div class="section">
      <h3>Citas</h3>
      <?php if (!empty($citas)): ?>
        <table>
          <thead><tr><th>Fecha</th><th>Hora</th><th>Estado</th></tr></thead>
          <tbody>
            <?php foreach ($citas as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['fecha'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['hora'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['estado'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="muted">Sin registros.</div>
      <?php endif; ?>
    </div>

    <div class="section">
      <h3>Consultas Clínicas</h3>
      <?php if (!empty($consultas)): ?>
        <table>
          <thead><tr><th>Fecha</th><th>Motivo</th><th>Diagnóstico</th></tr></thead>
          <tbody>
            <?php foreach ($consultas as $q): ?>
              <tr>
                <td><?= htmlspecialchars($q['fecha'] ?? '-') ?></td>
                <td><?= htmlspecialchars($q['motivo'] ?? '-') ?></td>
                <td><?= htmlspecialchars($q['diagnostico'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="muted">Sin registros.</div>
      <?php endif; ?>
    </div>

    <div class="section">
      <h3>Notas de la Mascota</h3>
      <?php if (!empty($notas)): ?>
        <table>
          <thead><tr><th>Fecha</th><th>Nota</th></tr></thead>
          <tbody>
            <?php foreach ($notas as $n): ?>
              <tr>
                <td><?= htmlspecialchars($n['fecha'] ?? '-') ?></td>
                <td><?= htmlspecialchars($n['nota'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="muted">Sin registros.</div>
      <?php endif; ?>
    </div>
  </body>
 </html>

