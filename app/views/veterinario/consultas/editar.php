<?php
// app/views/veterinario/consultas/editar.php
$consulta = $consulta ?? null;
if (!$consulta) {
  echo "<div class='p-3'>Consulta no encontrada.</div>";
  return;
}
?>
<div class="modal-header">
  <h5 class="modal-title">Editar Consulta #<?= htmlspecialchars($consulta['id']) ?></h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
</div>

<form id="formEditarConsulta" action="/vetsmart/veterinario/consultas/actualizar/<?= (int)$consulta['id'] ?>" method="POST">
  <div class="modal-body">
    <div id="editarAlert" class="alert d-none"></div>

    <div class="mb-3">
      <label class="form-label">Mascota</label>
      <input class="form-control" value="<?= htmlspecialchars($consulta['nombre_mascota'] ?? '-') ?>" disabled>
    </div>

    <div class="mb-3">
      <label class="form-label">Motivo</label>
      <textarea name="motivo" class="form-control" rows="2"><?= htmlspecialchars($consulta['motivo'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Examen</label>
      <textarea name="examen" class="form-control" rows="3"><?= htmlspecialchars($consulta['examen'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Diagnóstico</label>
      <textarea name="diagnostico" class="form-control" rows="2"><?= htmlspecialchars($consulta['diagnostico'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Tratamiento</label>
      <textarea name="tratamiento" class="form-control" rows="2"><?= htmlspecialchars($consulta['tratamiento'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Recomendaciones</label>
      <textarea name="recomendaciones" class="form-control" rows="2"><?= htmlspecialchars($consulta['recomendaciones'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Notas</label>
      <textarea name="notas" class="form-control" rows="2"><?= htmlspecialchars($consulta['notas'] ?? '') ?></textarea>
    </div>
  </div>

  <div class="modal-footer">
    <button id="btnGuardarConsulta" type="button" class="btn btn-primary">Guardar cambios</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
  </div>
</form>

<script>
(function () {
  const form = document.getElementById('formEditarConsulta');
  const btn = document.getElementById('btnGuardarConsulta');
  const alertBox = document.getElementById('editarAlert');

  function showAlert(type, message) {
    alertBox.className = 'alert alert-' + type;
    alertBox.innerText = message;
    alertBox.classList.remove('d-none');
  }

  btn.addEventListener('click', function (e) {
    // simple client-side disable + UX
    btn.disabled = true;
    const originalText = btn.innerText;
    btn.innerText = 'Guardando...';

    // build FormData
    const fd = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    }).then(function (res) {
      // si server devuelve JSON con error de status, aún parsear
      return res.json().then(function (json) {
        return { ok: res.ok, status: res.status, json: json };
      }).catch(function () {
        throw new Error('Respuesta inválida del servidor');
      });
    }).then(function (data) {
      if (data.ok && data.json && data.json.success) {
        showAlert('success', data.json.message || 'Guardado correctamente.');
        // cerrar modal y refrescar lista / fila
        setTimeout(function () {
          const bsModal = bootstrap.Modal.getInstance(document.getElementById('consultaModal'));
          if (bsModal) bsModal.hide();
          // por ahora recargamos la página para ver cambios (puedes cambiar por actualización parcial)
          location.reload();
        }, 600);
      } else {
        const msg = (data.json && data.json.message) ? data.json.message : 'Error al guardar.';
        showAlert('danger', msg);
        btn.disabled = false;
        btn.innerText = originalText;
      }
    }).catch(function (err) {
      console.error(err);
      showAlert('danger', 'Error de conexión o respuesta inválida.');
      btn.disabled = false;
      btn.innerText = originalText;
    });
  });
})();
</script>
