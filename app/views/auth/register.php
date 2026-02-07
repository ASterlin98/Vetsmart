<?php // app/views/auth/register.php ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro - VetSmart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <?php $siteKey = $siteKey ?? ($recaptcha_site_key ?? ''); ?>
  <?php if (!empty($siteKey)): ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($siteKey) ?>"></script>
  <?php endif; ?>
  <style>
    .auth-page {
      background-image: url('/vetsmart/public/assets/css/img5.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: scroll;
      min-height: 100vh;
      position: relative;
    }
    .auth-page::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,0.88);
      z-index: 0;
    }
    @media (min-width: 768px) {
      .auth-page::before { background: rgba(255,255,255,0.8); }
    }
    .auth-card {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 440px;
      padding: 1.5rem;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.1);
    }
    @media (max-width: 480px) {
      .auth-card {
        margin: 0.75rem;
        padding: 1.25rem;
        border-radius: 10px;
      }
    }
    .auth-card input { font-size: 16px !important; }
  </style>
</head>
<body class="auth-page min-h-screen flex items-center justify-center p-4 sm:p-6 py-8">

  <div class="auth-card">
    <h1 class="text-2xl sm:text-3xl font-bold text-center text-blue-600 mb-4 sm:mb-6">Crear cuenta</h1>
    <p class="text-center text-gray-600 mb-5 sm:mb-6 text-sm sm:text-base">Es rápido y fácil.</p>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 mb-4 rounded-lg text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="/vetsmart/auth/storeClient" class="space-y-3 sm:space-y-4">
      <?= \CSRF::inputField() ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input id="nombre" name="nombre" type="text" placeholder="Nombre" required
          class="border border-gray-300 rounded-lg px-3 py-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500">
        <input id="apellido" name="apellido" type="text" placeholder="Apellido" required
          class="border border-gray-300 rounded-lg px-3 py-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500">
      </div>

      <input id="docusu" name="docusu" type="text" placeholder="Documento" required
        class="border border-gray-300 rounded-lg px-3 py-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500">

      <input id="email" name="email" type="email" placeholder="Correo electrónico" required
        class="border border-gray-300 rounded-lg px-3 py-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500">

      <input id="telefono" name="telefono" type="text" placeholder="Número de celular" required
        class="border border-gray-300 rounded-lg px-3 py-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500">

      <input id="password" name="password" type="password" placeholder="Contraseña nueva" required
        class="border border-gray-300 rounded-lg px-3 py-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500">

      <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" value="">

      <button type="submit"
        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 sm:py-3 rounded-lg transition mt-2 sm:mt-3 text-base">
        Registrarme
      </button>
    </form>

    <p class="text-center mt-5 sm:mt-6 text-sm">
      <a href="/vetsmart/auth/login" class="text-blue-600 hover:underline font-medium">
        Ya tengo una cuenta
      </a>
    </p>
  </div>

  <?php if (!empty($siteKey)): ?>
  <script>
    // reCAPTCHA v3: ejecutar cuando el formulario se envía
    document.querySelector('form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Obtener el site key desde el script de reCAPTCHA
      const siteKey = '<?= htmlspecialchars($siteKey) ?>';
      
      grecaptcha.execute(siteKey, { action: 'register' }).then(function(token) {
        // Guardar el token en el input oculto
        document.getElementById('g-recaptcha-response').value = token;
        
        // Enviar el formulario
        document.querySelector('form').submit();
      }).catch(function(err) {
        console.error('reCAPTCHA error:', err);
        alert('Error al verificar reCAPTCHA. Por favor intenta de nuevo.');
      });
    });
  </script>
  <?php endif; ?>
</body>
</html>
