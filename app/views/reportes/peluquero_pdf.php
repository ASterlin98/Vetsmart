<?php
declare(strict_types=1);
/** @var array $empleado */
/** @var array $kpis */
/** @var array $topServicios */
/** @var array $ultimas */
/** @var string $logoB64 */
$hoy = (new DateTime('now'))->format('Y-m-d H:i');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Peluquero</title>
<style>
  @page { margin: 28mm 18mm 25mm 18mm; }
  body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color:#0f172a; }
  .header { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
  .header img { height: 48px; }
  .title { margin: 10px 0 18px; font-size:18px; font-weight:700; color:#0ea5e9; }
  .meta { font-size:11px; color:#64748b; }
  .card { border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; margin-bottom:14px; }
  .grid-2 { display:grid; grid-template-columns: 1fr 1fr; gap:10px; }
  .grid-4 { display:grid; grid-template-columns: repeat(4, 1fr); gap:10px; }
  h3 { font-size:14px; margin:0 0 8px; color:#0f172a; }
  table { width:100%; border-collapse: collapse; font-size:11.5px; }
  th, td { border:1px solid #e5e7eb; padding:6px 8px; }
  th { background:#f8fafc; text-align:left; font-weight:600; }
  tr:nth-child(even) td { background:#fcfdff; }
  .badge { display:inline-block; padding:2px 8px; border-radius:9999px; background:#e0f2fe; color:#0369a1; font-size:10.5px; }
  .muted { color:#64748b; }
  .small { font-size:10.5px; }
</style>
</head>
<body>

  <div class="header">
    <?php if (!empty($logoB64)): ?>
      <img src="<?= $logoB64 ?>" alt="Logo">
    <?php endif; ?>
    <div>
      <div style="font-weight:700">VETSMART</div>
      <div class="small muted">Reporte profesional de desempeño - Peluquería</div>
      <div class="meta">Generado: <?= htmlspecialchars($hoy) ?></div>
    </div>
  </div>

  <div class="title">Perfil del Empleado</div>
  <div class="card">
    <div class="grid-2">
      <div>
        <strong>Nombre:</strong> <?= htmlspecialchars((string)($empleado['nombre_completo'] ?? '')) ?><br>
        <strong>Documento:</strong> <?= htmlspecialchars((string)($empleado['documento'] ?? '-')) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars((string)($empleado['email'] ?? '-')) ?>
      </div>
      <div>
        <strong>Teléfono:</strong> <?= htmlspecialchars((string)($empleado['telefono'] ?? '-')) ?><br>
        <strong>Cargo:</strong> <?= htmlspecialchars((string)($empleado['cargo'] ?? 'Peluquero')) ?><br>
        <strong>Ingreso:</strong> <?= htmlspecialchars(substr((string)($empleado['fecha_ingreso'] ?? ''),0,10)) ?>
      </div>
    </div>
  </div>

  <div class="card">
    <h3>Métricas del mes actual</h3>
    <div class="grid-4">
      <div>
        <div class="muted small">Citas atendidas</div>
        <div style="font-size:18px; font-weight:700;"><?= (int)($kpis['citas_atendidas'] ?? 0) ?></div>
      </div>
      <div>
        <div class="muted small">Tasa de finalización</div>
        <div style="font-size:18px; font-weight:700;"><?= number_format((float)($kpis['tasa_finalizacion'] ?? 0), 1) ?>%</div>
      </div>
      <div>
        <div class="muted small">Ticket promedio</div>
        <div style="font-size:18px; font-weight:700;">$ <?= number_format((float)($kpis['ticket_promedio'] ?? 0), 2) ?></div>
      </div>
      <div>
        <div class="muted small">Ingresos generados</div>
        <div style="font-size:18px; font-weight:700;">$ <?= number_format((float)($kpis['ingresos'] ?? 0), 2) ?></div>
      </div>
    </div>
  </div>

  <div class="card">
    <h3>Top 5 servicios</h3>
    <table>
      <thead><tr><th>Servicio</th><th>Cantidad</th></tr></thead>
      <tbody>
        <?php if (!$topServicios): ?>
          <tr><td colspan="2" class="muted">Sin datos en el período.</td></tr>
        <?php else: foreach ($topServicios as $t): ?>
          <tr>
            <td><?= htmlspecialchars((string)($t['nombre'] ?? '')) ?></td>
            <td><?= (int)($t['cnt'] ?? 0) ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h3>Últimas atenciones</h3>
    <table>
      <thead>
        <tr><th>Fecha</th><th>Hora</th><th>Mascota</th><th>Cliente</th><th>Servicio</th><th class="right">Precio</th><th>Notas</th></tr>
      </thead>
      <tbody>
        <?php if (!$ultimas): ?>
          <tr><td colspan="7" class="muted">Sin registros recientes.</td></tr>
        <?php else: foreach ($ultimas as $u): ?>
          <tr>
            <td><?= htmlspecialchars((string)($u['fecha'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($u['hora'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($u['mascota'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($u['cliente'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($u['servicio'] ?? '')) ?></td>
            <td class="right">$ <?= number_format((float)($u['precio_final'] ?? 0), 2) ?></td>
            <td><?= htmlspecialchars((string)($u['notas'] ?? '')) ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>

