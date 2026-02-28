<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Restablecer contraseña - VetSmart</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div>
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
      Restablecer contraseña
    </h2>

    <form method="POST" action="<?= BASE ?>/auth/updatePassword" class="space-y-4">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Nueva contraseña</label>
            <input id="password" name="password" type="password" required
                  class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
            <input id="confirm_password" name="confirm_password" type="password" required
                  class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <button type="submit" 
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-semibold">
            Actualizar contraseña
        </button>
    </form>

    <p class="mt-6 text-center text-sm">
      <a href="<?= BASE ?>/auth/login" class="text-blue-600 hover:underline">
        Volver al login
      </a>
    </p>
  </div>

</body>
</html>
