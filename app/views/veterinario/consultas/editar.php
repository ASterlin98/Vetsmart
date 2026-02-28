<?php
// app/views/veterinario/consultas/editar.php
$consulta = $consulta ?? null;
if (!$consulta) {
  echo "<div class='p-3 text-danger'>Consulta no encontrada.</div>";
  return;
}
?>

<div class="modal-header">
  <h5 class="modal-title">
    ✏️ Editar Consulta <small class="text-muted">#<?= htmlspecialchars($consulta['id']) ?></small>
  </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
</div>

<form id="formEditarConsulta" action="<?= BASE ?>/veterinario/consultas/actualizar/<?= (int)$consulta['id'] ?>" method="POST">
  <div class="modal-body">
    <div id="editarAlert" class="alert d-none small py-2 px-3 mb-3"></div>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">🐾 Mascota</label>
        <input class="form-control-plaintext" readonly value="<?= htmlspecialchars($consulta['nombre_mascota'] ?? '-') ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Motivo</label>
        <textarea name="motivo" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($consulta['motivo'] ?? '') ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">Examen</label>
        <textarea name="examen" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($consulta['examen'] ?? '') ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">Diagnóstico</label>
        <textarea name="diagnostico" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($consulta['diagnostico'] ?? '') ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">Tratamiento</label>
        <textarea name="tratamiento" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($consulta['tratamiento'] ?? '') ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">Recomendaciones</label>
        <textarea name="recomendaciones" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($consulta['recomendaciones'] ?? '') ?></textarea>
      </div>

      <div class="col-12">
        <label class="form-label">Notas adicionales</label>
        <textarea name="notas" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($consulta['notas'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <div class="modal-footer d-flex justify-content-between">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
    <button id="btnGuardarConsulta" type="button" class="btn btn-primary btn-sm">
      💾 Guardar cambios
    </button>
  </div>
</form>

<script>
(function () {
  const form = document.getElementById('formEditarConsulta');
  const btn = document.getElementById('btnGuardarConsulta');
  const alertBox = document.getElementById('editarAlert');

  function showAlert(type, message) {
    alertBox.className = 'alert alert-' + type + ' small py-2 px-3';
    alertBox.innerText = message;
    alertBox.classList.remove('d-none');
  }

  btn.addEventListener('click', function (e) {
    e.preventDefault(); // ✅ <-- evita envío normal del formulario

    btn.disabled = true;
    const originalText = btn.innerText;
    btn.innerText = 'Guardando...';

    const fd = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(res => res.json().then(json => ({ ok: res.ok, status: res.status, json })))
    .then(data => {
      if (data.ok && data.json?.success) {
        showAlert('success', data.json.message || 'Guardado correctamente.');
        setTimeout(() => {
          const modal = bootstrap.Modal.getInstance(document.getElementById('consultaModal'));
          if (modal) modal.hide();
          location.reload();
        }, 600);
      } else {
        showAlert('danger', data.json?.message || 'Error al guardar.');
        btn.disabled = false;
        btn.innerText = originalText;
      }
    })
    .catch(err => {
      console.error(err);
      showAlert('danger', 'Error de conexión o respuesta inválida.');
      btn.disabled = false;
      btn.innerText = originalText;
    });
  });
})();
</script>
