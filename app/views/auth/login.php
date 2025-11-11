<?php // app/views/auth/login.php ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login - VetSmart</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center"
      style="background-image: url('/vetsmart/public/assets/css/img2.jpg'); background-size: cover; background-position: center;">

  <div>
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
      VetSmart — Iniciar sesión
    </h2>

    <!-- Mensaje de error -->
    <?php if (!empty($error)): ?>
      <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm font-medium">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Formulario de login -->
    <form method="post" action="/vetsmart/login" autocomplete="off" class="space-y-4">
      <?= \CSRF::inputField() ?>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
        <input id="email" name="email" type="email" required
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input id="password" name="password" type="password" required
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
      </div>

      <button type="submit" 
              class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
        Ingresar
      </button>
    </form>

    <!-- Enlaces -->
    <p class="mt-6 text-center text-sm">
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
