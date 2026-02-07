<?php // app/views/auth/login.php ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login - VetSmart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .auth-page {
      background-image: url('/vetsmart/public/assets/css/img2.jpg');
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
      background: rgba(255,255,255,0.85);
      z-index: 0;
    }
    @media (min-width: 768px) {
      .auth-page::before { background: rgba(255,255,255,0.75); }
    }
    .auth-card {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 420px;
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
    input { font-size: 16px !important; } /* Evita zoom en iOS */
  </style>
</head>
<body class="auth-page min-h-screen flex items-center justify-center p-4 sm:p-6">

  <div class="auth-card">
    <h2 class="text-xl sm:text-2xl font-bold text-center text-gray-800 mb-5 sm:mb-6">
      VetSmart — Iniciar sesión
    </h2>

    <?php if (!empty($error)): ?>
      <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm font-medium">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="/vetsmart/login" autocomplete="off" class="space-y-4">
      <?= \CSRF::inputField() ?>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
        <input id="email" name="email" type="email" required
               class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500">
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input id="password" name="password" type="password" required
               class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500">
      </div>

      <button type="submit" 
              class="w-full bg-blue-600 text-white py-2.5 rounded-lg hover:bg-blue-700 transition font-semibold text-base">
        Ingresar
      </button>
    </form>

    <p class="mt-5 sm:mt-6 text-center text-sm">
      <a href="/vetsmart/auth/forgot" class="text-blue-600 hover:underline">
        ¿Olvidaste tu contraseña?
      </a>
    </p>

    <p class="mt-2 text-center text-sm">
      ¿No tienes cuenta?
      <a href="/vetsmart/auth/register" class="text-green-600 font-semibold hover:underline">
        Regístrate como cliente
      </a>
    </p>
  </div>

</body>
</html>
