<?php
// plantilla_pdf.php - plantilla mejorada para Dompdf (A4, vertical)
// Variables esperadas: $desde, $hasta, $consultas (array), $citas (array)

$totalConsultas = count($consultas);
$totalCitas = count($citas);

// calcular rango de días para promedio (si viene $desde/$hasta)
$days = 1;
if (!empty($desde) && !empty($hasta)) {
    $d1 = strtotime($desde);
    $d2 = strtotime($hasta);
    if ($d1 !== false && $d2 !== false && $d2 >= $d1) {
        $days = max(1, (int)floor(($d2 - $d1) / 86400) + 1);
    }
}
$avgConsultasPorDia = round($totalConsultas / $days, 2);

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte Veterinario <?= esc($desde) ?> - <?= esc($hasta) ?></title>

  <style>
    /* Tip: Dompdf soporta la mayoría de CSS básicos; evitar selectores modernos complejos */
    @page { margin: 40px 36px; }
    body { font-family: "DejaVu Sans", Arial, sans-serif; font-size:12px; color:#111; margin:0; }
    header { position: fixed; top: -10px; left: 0; right: 0; height: 90px; }
    .brand {
      display:flex; align-items:center; gap:12px;
    }
    .logo {
      width:72px; height:72px; border-radius:8px; object-fit:cover; background:#eee; display:inline-block;
      text-align:center; line-height:72px; color:#666; font-weight:700;
    }
    .title {
      font-size:18px; font-weight:700; margin:0;
    }
    .subtitle { font-size:11px; color:#555; margin-top:4px; }

    main { margin-top:90px; }

    .summary { display:flex; gap:12px; margin-bottom:12px; }
    .card {
      flex:1; background:#fff; border:1px solid #e6e6e6; padding:10px 12px; border-radius:6px;
      box-shadow: none;
    }
    .card .label { font-size:11px; color:#666; }
    .card .value { font-size:18px; font-weight:700; margin-top:6px; }

    h3.section { margin:18px 0 8px 0; font-size:14px; border-bottom:1px solid #e9ecef; padding-bottom:6px; color:#222; }

    table { width:100%; border-collapse:collapse; margin-bottom:10px; }
    thead th { background:#f4f6f8; text-align:left; padding:8px; font-size:11px; border:1px solid #e0e0e0; }
    tbody td { padding:8px; border:1px solid #eee; vertical-align:top; font-size:11px; }
    tbody tr:nth-child(even) td { background:#fbfdff; }

    .col-date { width:14%; max-width:120px; white-space:nowrap; }
    .col-mascota { width:18%; max-width:160px; }
    .col-vet { width:16%; max-width:140px; }
    .col-serv { width:14%; max-width:120px; }
    .col-notes { width:auto; }

    .small-muted { font-size:10px; color:#666; }

    footer {
      position: fixed; bottom: 8px; left: 36px; right: 36px; height:20px; font-size:10px; color:#666;
      border-top:1px solid #eee; padding-top:6px; text-align:right;
    }

    /* Manejo de textos largos en celdas */
    td { word-wrap: break-word; word-break: break-word; white-space: pre-wrap; }

    /* Si se necesita forzar salto de página entre secciones */
    .page-break { page-break-after: always; }
  </style>
</head>
<body>

<header>
  <div class="brand">
    <!-- Si tienes un logo público, reemplaza src por su ruta absoluta o relativa dentro del proyecto público -->
    <!-- Dompdf debe tener 'enable_remote' => true si usas URLs externas -->
    <div class="logo"><?= /* fallback: iniciales */ strtoupper(substr($_SERVER['HTTP_HOST'] ?? 'V', 0, 1)) ?></div>
    <div>
      <div class="title">Veterinaria VetSmart</div>
      <div class="subtitle">Reporte generado: <?= date('Y-m-d H:i') ?> &nbsp; &middot; Rango: <?= esc($desde) ?> — <?= esc($hasta) ?></div>
    </div>
  </div>
</header>

<main>
  <!-- resumen -->
  <div class="summary">
    <div class="card">
      <div class="label">Consultas (total)</div>
      <div class="value"><?= $totalConsultas ?></div>
      <div class="small-muted">Promedio por día: <?= $avgConsultasPorDia ?> (<?= $days ?> días)</div>
    </div>

    <div class="card">
      <div class="label">Citas atendidas (total)</div>
      <div class="value"><?= $totalCitas ?></div>
      <div class="small-muted">Período: <?= esc($desde ?: 'inicio') ?> → <?= esc($hasta ?: 'hoy') ?></div>
    </div>

    <div class="card">
      <div class="label">Notas</div>
      <div class="value"><?= $totalConsultas + $totalCitas ?></div>
      <div class="small-muted">Registros totales (consultas + citas)</div>
    </div>
  </div>

  <!-- Consultas -->
  <h3 class="section">Consultas (<?= $totalConsultas ?>)</h3>
  <table>
    <thead>
      <tr>
        <th class="col-date">Fecha</th>
        <th class="col-mascota">Mascota</th>
        <th>Motivo</th>
        <th class="col-vet">Veterinario</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($consultas)): ?>
        <tr><td colspan="4" class="small-muted">No hay consultas en el rango seleccionado.</td></tr>
      <?php else: foreach ($consultas as $c): ?>
        <tr>
          <td class="col-date"><?= esc(isset($c['creado_en']) ? date('d/m/Y H:i', strtotime($c['creado_en'])) : '') ?></td>
          <td class="col-mascota"><?= esc($c['nombre_mascota'] ?? '-') ?></td>
          <td><?= esc($c['motivo'] ?? '-') ?></td>
          <td class="col-vet"><?= esc(trim(($c['nombre_veterinario'] ?? '') . ' ' . ($c['apellido_veterinario'] ?? ''))) ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

  <!-- Citas -->
  <h3 class="section">Citas atendidas (<?= $totalCitas ?>)</h3>
  <table>
    <thead>
      <tr>
        <th class="col-date">Fecha</th>
        <th class="col-mascota">Mascota</th>
        <th>Cliente</th>
        <th class="col-serv">Servicio</th>
        <th class="col-notes">Notas</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($citas)): ?>
        <tr><td colspan="5" class="small-muted">No hay citas en el rango seleccionado.</td></tr>
      <?php else: foreach ($citas as $c): ?>
        <tr>
          <td class="col-date"><?= esc(isset($c['fecha']) ? date('d/m/Y H:i', strtotime($c['fecha'])) : '') ?></td>
          <td class="col-mascota"><?= esc($c['nombre_mascota'] ?? '-') ?></td>
          <td><?= esc(trim(($c['cliente_nombre'] ?? '') . ' ' . ($c['cliente_apellido'] ?? ''))) ?></td>
          <td class="col-serv"><?= esc($c['nombre_servicio'] ?? '-') ?></td>
          <td class="col-notes"><?= esc($c['notas'] ?? '-') ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

  <div class="page-break"></div>

  <!-- Opcional: añadir gráfico pequeño (si generas imagen y la inyectas) -->
  <?php /* if (isset($chartBase64) && $chartBase64): ?>
    <h3 class="section">Resumen gráfico</h3>
    <div style="text-align:center;">
      <img src="<?= $chartBase64 ?>" style="max-width:100%; height:auto; border:1px solid #ddd; padding:6px; background:#fff">
    </div>
  <?php endif; */ ?>

</main>

<footer>
  VetSmart — Reporte veterinario · Página <!-- page number via Dompdf script --> 
</footer>

<?php
// Dompdf: insertar número de página en el footer (usa API de Dompdf: page_text)
if (isset($pdf) && is_object($pdf)) {
    // ubicaciones y fuente
    $font = $pdf->getFontMetrics()->get_font("DejaVu Sans", "normal");
    $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
    // coordenadas: x,y en puntos desde la izquierda/arriba de la página (A4 height ~ 842)
    $pdf->page_text(520, 820, $text, $font, 10, array(0,0,0));
}
?>

</body>
</html>
