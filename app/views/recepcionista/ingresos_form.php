<div class="container-fluid">
  <h1 class="h4 mb-3">
    <i class="fas fa-plus-circle text-success me-2"></i>
    <?= $accion === 'editar' ? 'Editar Movimiento' : 'Nuevo Movimiento' ?>
  </h1>

  <form action="<?= $accion==='editar' ? BASE . '/recepcionista/ingresos/' . (int)$item['id'] . '/update' : BASE . '/recepcionista/ingresos/store' ?>" method="post" class="card p-3 shadow-sm" id="movForm">
    <?= CSRF::inputField(); ?>
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Tipo</label>
        <select name="tipo" class="form-select" id="tipoSelect">
          <?php $t = $item['tipo'] ?? 'ingreso'; ?>
          <option value="ingreso" <?= $t==='ingreso'?'selected':'' ?>>Ingreso</option>
          <option value="egreso" <?= $t==='egreso'?'selected':'' ?>>Egreso</option>
        </select>
      </div>
      <div class="col-md-5">
        <label class="form-label">Concepto</label>
        <?php 
          $conceptosIngreso = $conceptosIngreso ?? [];
          $conceptosEgreso  = $conceptosEgreso ?? [];
        ?>
        <select name="concepto_predef" id="conceptoPredef" class="form-select">
          <option value="">Seleccione...</option>
          <optgroup label="Ingresos">
          <?php foreach ($conceptosIngreso as $c): ?>
            <option value="<?= htmlspecialchars($c['concepto']) ?>" data-monto="<?= htmlspecialchars($c['monto']) ?>" <?= (!empty($item['concepto']) && $item['concepto']===$c['concepto'] && ($item['tipo']??'')==='ingreso')?'selected':'' ?>>
              <?= htmlspecialchars($c['concepto']) ?> (<?= number_format((float)$c['monto'],2) ?>)
            </option>
          <?php endforeach; ?>
          </optgroup>
          <optgroup label="Egresos">
          <?php foreach ($conceptosEgreso as $c): ?>
            <option value="<?= htmlspecialchars($c['concepto']) ?>" data-monto="<?= htmlspecialchars($c['monto']) ?>" <?= (!empty($item['concepto']) && $item['concepto']===$c['concepto'] && ($item['tipo']??'')==='egreso')?'selected':'' ?>>
              <?= htmlspecialchars($c['concepto']) ?> (<?= number_format((float)$c['monto'],2) ?>)
            </option>
          <?php endforeach; ?>
          </optgroup>
          <option value="otro">Otro (especificar)</option>
        </select>
        <input type="text" name="concepto" id="conceptoTexto" class="form-control mt-2" placeholder="Especificar concepto" value="<?= htmlspecialchars($item['concepto'] ?? '') ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label">Monto</label>
        <input type="number" step="0.01" min="0" name="monto" id="montoInput" class="form-control" value="<?= htmlspecialchars($item['monto'] ?? '') ?>" required>
      </div>
      <div class="col-md-4" id="grupoProducto" style="display:none;">
        <label class="form-label">Producto (para Medicamento)</label>
        <?php $productos = $productos ?? []; ?>
        <select name="producto_id" id="productoSelect" class="form-select">
          <option value="">Seleccione producto...</option>
          <?php foreach ($productos as $p): ?>
            <option value="<?= (int)$p['id'] ?>" data-precio="<?= htmlspecialchars($p['precio'] ?? 0) ?>">
              <?= htmlspecialchars($p['nombre']) ?> (<?= number_format((float)($p['precio'] ?? 0), 2) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Fecha</label>
        <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($item['fecha'] ?? date('Y-m-d')) ?>" required>
      </div>
      <div class="col-12">
        <label class="form-label">Notas</label>
        <textarea name="notas" class="form-control" rows="3"><?= htmlspecialchars($item['notas'] ?? '') ?></textarea>
      </div>
    </div>

    <div class="text-end mt-3">
      <a class="btn btn-outline-secondary" href="<?= BASE ?>/recepcionista/ingresos">Cancelar</a>
      <button class="btn btn-success" type="submit"><i class="fas fa-save me-1"></i>Guardar</button>
    </div>
  </form>
</div>

  <script>
  document.addEventListener('DOMContentLoaded', function(){
    const tipo = document.getElementById('tipoSelect');
    const sel = document.getElementById('conceptoPredef');
    const txt = document.getElementById('conceptoTexto');
    const monto = document.getElementById('montoInput');
    const grupoProducto = document.getElementById('grupoProducto');
    const productoSelect = document.getElementById('productoSelect');
    function applyState(){
      const v = sel.value;
      if (v && v !== 'otro') {
        // Si selecciona un concepto, fijar monto y deshabilitar edición de monto/concepto texto
        const opt = sel.selectedOptions[0];
        const m = opt ? opt.getAttribute('data-monto') : '';
        txt.disabled = true; txt.required = false; txt.value='';
        monto.value = m || '';
        monto.readOnly = true;
        // Mostrar selector de producto sólo si el concepto es Medicamento (y tipo ingreso)
        const isMedicamento = v.toLowerCase() === 'medicamento' && (tipo.value || '').toLowerCase() === 'ingreso';
        grupoProducto.style.display = isMedicamento ? '' : 'none';
        if (!isMedicamento) {
          productoSelect.value = '';
        }
      } else if (v === 'otro') {
        txt.disabled = false; txt.required = true;
        monto.readOnly = false;
        grupoProducto.style.display = 'none';
        productoSelect.value = '';
      } else {
        // nada seleccionado
        txt.disabled = false; txt.required = false;
        monto.readOnly = false;
        grupoProducto.style.display = 'none';
        productoSelect.value = '';
      }
    }
    sel.addEventListener('change', applyState);
    (tipo||{}).addEventListener && tipo.addEventListener('change', ()=>{ sel.value=''; applyState(); });
    productoSelect && productoSelect.addEventListener('change', function(){
      const opt = this.selectedOptions[0];
      if (!opt) return;
      const precio = opt.getAttribute('data-precio');
      if (precio) {
        monto.value = precio;
        monto.readOnly = true;
      }
    });
    applyState();
  });
  </script>
