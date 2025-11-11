<?php
declare(strict_types=1);
/** @var array $cliente */
/** @var array $mascotas */
/** @var array $citas */
/** @var array $clinico */
/** @var string $logoB64 */
$hoy = (new DateTime('now'))->format('Y-m-d H:i');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Cliente</title>
<style>
  @page { margin: 28mm 18mm 25mm 18mm; }
  body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color:#0f172a; }
  .header { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
  .header img { height: 48px; }
  .title { margin: 10px 0 18px; font-size:18px; font-weight:700; color:#0ea5e9; }
  .meta { font-size:11px; color:#64748b; }
  .card { border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; margin-bottom:14px; }
  .grid-2 { display:grid; grid-template-columns: 1fr 1fr; gap:10px; }
  h3 { font-size:14px; margin:0 0 8px; color:#0f172a; }
  table { width:100%; border-collapse: collapse; font-size:11.5px; }
  th, td { border:1px solid #e5e7eb; padding:6px 8px; }
  th { background:#f8fafc; text-align:left; font-weight:600; }
  tr:nth-child(even) td { background:#fcfdff; }
  .badge { display:inline-block; padding:2px 8px; border-radius:9999px; background:#e0f2fe; color:#0369a1; font-size:10.5px; }
  .muted { color:#64748b; }
  .section { margin-top:14px; page-break-inside: avoid; }
  .small { font-size:10.5px; }
  .right { text-align:right; }
</style>
</head>
<body>

  <div class="header">
    <?php if (!empty($logoB64)): ?>
      <img src="<?= $logoB64 ?>" alt="Logo">
    <?php endif; ?>
    <div>
      <div style="font-weight:700">VETSMART</div>
      <div class="small muted">Reporte profesional de cliente</div>
      <div class="meta">Generado: <?= htmlspecialchars($hoy) ?></div>
    </div>
  </div>

  <div class="title">Ficha del Cliente</div>

  <div class="card">
    <div class="grid-2">
      <div>
        <strong>Nombre:</strong> <?= htmlspecialchars((($cliente['nombre'] ?? '') . ' ' . ($cliente['apellido'] ?? ''))) ?><br>
        <strong>Documento:</strong> <?= htmlspecialchars($cliente['documento'] ?? '-') ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($cliente['email'] ?? '-') ?>
      </div>
      <div>
        <strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono'] ?? '-') ?><br>
        <strong>Dirección:</strong> <?= htmlspecialchars($cliente['direccion'] ?? '-') ?><br>
        <strong>Desde:</strong> <?= htmlspecialchars(substr((string)($cliente['created_at'] ?? ''),0,10)) ?>
      </div>
    </div>
  </div>

  <div class="section">
    <h3>Mascotas (<?= count($mascotas) ?>)</h3>
    <table>
      <thead>
        <tr>
          <th>Nombre</th><th>Especie</th><th>Raza</th><th>Edad</th><th>Sexo</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$mascotas): ?>
          <tr><td colspan="5" class="muted">Sin mascotas registradas.</td></tr>
        <?php else: foreach ($mascotas as $m): ?>
          <tr>
            <td><?= htmlspecialchars($m['nombre'] ?? '') ?></td>
            <td><span class="badge"><?= htmlspecialchars($m['especie'] ?? '') ?></span></td>
            <td><?= htmlspecialchars($m['raza'] ?? '') ?></td>
            <td class="right"><?= htmlspecialchars((string)($m['edad'] ?? '')) ?> años</td>
            <td><?= htmlspecialchars($m['sexo'] ?? '') ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <div class="section">
    <h3>Últimas Citas</h3>
    <table>
      <thead>
        <tr><th>Fecha</th><th>Hora</th><th>Servicio</th><th>Estado</th></tr>
      </thead>
      <tbody>
        <?php if (!$citas): ?>
          <tr><td colspan="4" class="muted">No hay citas registradas.</td></tr>
        <?php else: foreach ($citas as $c): ?>
          <tr>
            <?php $dt = (string)($c['fecha'] ?? ''); $fec = substr($dt,0,10); $hor = substr($dt,11,5); ?>
            <td><?= htmlspecialchars($fec) ?></td>
            <td><?= htmlspecialchars($hor) ?></td>
            <td><?= htmlspecialchars($c['servicio'] ?? '-') ?></td>
            <td><span class="badge"><?= htmlspecialchars($c['estado'] ?? '-') ?></span></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <div class="section">
    <h3>Resumen Historial Clínico</h3>
    <table>
      <thead>
        <tr><th>Mascota</th><th>Fecha</th><th>Motivo</th><th>Diagnóstico</th></tr>
      </thead>
      <tbody>
        <?php if (!$clinico): ?>
          <tr><td colspan="4" class="muted">No hay registros médicos.</td></tr>
        <?php else: foreach ($clinico as $h): ?>
          <tr>
            <td><?= htmlspecialchars($h['mascota_nombre'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['fecha'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['motivo'] ?? '-') ?></td>
            <td><?= htmlspecialchars($h['diagnostico'] ?? '-') ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>

