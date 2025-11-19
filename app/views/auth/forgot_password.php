<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Recuperar contraseña - VetSmart</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center"
      style="background-image: url('/vetsmart/public/assets/css/img4.jpg'); background-size: cover; background-position: center;">

  <div>
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
      ¿Olvidaste tu contraseña?
    </h2>

    <!-- Mensajes de error y éxito -->
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm font-medium">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm font-medium">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form method="POST" action="/vetsmart/auth/sendResetLink" class="space-y-4">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
        <input type="email" name="email" id="email" required 
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
      </div>

      <button type="submit" 
              class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
        Enviar enlace
      </button>
    </form>

    <p class="mt-6 text-center text-sm">
      <a href="/vetsmart/auth/login" class="text-blue-600 hover:underline">
        Volver al login
      </a>
    </p>
  </div>

</body>
</html>
