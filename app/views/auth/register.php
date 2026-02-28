<?php // app/views/auth/register.php ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro - VetSmart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= BASE ?>/public/assets/css/responsive.css">
  <?php $siteKey = $siteKey ?? ($recaptcha_site_key ?? ''); ?>
  <?php if (!empty($siteKey)): ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($siteKey) ?>"></script>
  <?php endif; ?>
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      width: 100%;
      background: none !important;
    }

    .auth-page {
      background-image: url('<?= BASE ?>/public/assets/css/img5.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      width: 100%;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 1;
    }

    .auth-container {
      width: 100%;
      max-width: 520px;
      position: relative;
      z-index: 2;
      padding: 1rem;
    }

    .auth-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 2rem 1.5rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      animation: slideUp 0.4s ease-out;
    }

    .auth-form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .form-group label {
      font-size: 0.9375rem;
      font-weight: 600;
      color: #374151;
      letter-spacing: 0.3px;
    }

    .form-group input {
      padding: 0.875rem 1rem;
      font-size: 16px !important;
      border: 1.5px solid #e5e7eb;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--accent, #3b82f6);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .auth-heading {
      font-size: 1.75rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 0.5rem;
      text-align: center;
      letter-spacing: -0.5px;
    }

    .auth-subtext {
      font-size: 0.9375rem;
      color: #6b7280;
      text-align: center;
      margin-bottom: 1.75rem;
      line-height: 1.5;
    }

    .error-message {
      background: #fee2e2;
      border-left: 4px solid #dc2626;
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      font-size: 0.9375rem;
      color: #7f1d1d;
    }

    .auth-button {
      padding: 0.875rem 1rem;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      background-color: var(--success, #10b981);
      color: white;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-top: 0.5rem;
    }

    .auth-button:hover {
      background-color: #059669;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .auth-button:active {
      transform: translateY(0);
    }

    .auth-link {
      text-align: center;
      margin-top: 1.25rem;
      font-size: 0.9375rem;
    }

    .auth-link a {
      color: var(--accent, #3b82f6);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .auth-link a:hover {
      color: #1d4ed8;
      text-decoration: underline;
    }

    .recaptcha-note {
      font-size: 0.75rem;
      color: #9ca3af;
      text-align: center;
      margin-top: 1rem;
    }

    /* Mostrar badge de reCAPTCHA v3 */
    .grecaptcha-badge {
      position: fixed !important;
      bottom: 20px !important;
      right: 20px !important;
      z-index: 9999 !important;
    }

    @media (max-width: 480px) {
      .grecaptcha-badge {
        bottom: 10px !important;
        right: 10px !important;
      }
    }

    @media (max-width: 480px) {
      .auth-page {
        padding: 0.75rem;
      }

      .auth-card {
        padding: 1.5rem 1.25rem;
      }

      .auth-heading {
        font-size: 1.5rem;
        margin-bottom: 0.375rem;
      }

      .auth-subtext {
        font-size: 0.875rem;
        margin-bottom: 1.25rem;
      }

      .form-row {
        grid-template-columns: 1fr;
        gap: 0.75rem;
      }

      .form-group input {
        padding: 0.75rem 0.875rem;
      }

      .auth-form {
        gap: 0.75rem;
      }

      .auth-button {
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
      }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card">
    <h1 class="text-2xl sm:text-3xl font-bold text-center text-blue-600 mb-4 sm:mb-6">Crear cuenta</h1>
    <p class="text-center text-gray-600 mb-5 sm:mb-6 text-sm sm:text-base">Es rápido y fácil.</p>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 mb-4 rounded-lg text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= BASE ?>/auth/storeClient" class="space-y-3 sm:space-y-4">
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
      <a href="<?= BASE ?>/auth/login" class="text-blue-600 hover:underline font-medium">
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
