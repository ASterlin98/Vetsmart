

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro - VetSmart</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center"
      style="background-image: url('/vetsmart/public/assets/css/img5.jpg'); background-size: cover; background-position: center;">

  <div >
    <h1 class="text-3xl font-bold text-center text-blue-600 mb-6">Crear cuenta</h1>
    <p class="text-center text-gray-600 mb-6">Es rápido y fácil.</p>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 mb-4 rounded-md">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="/vetsmart/auth/storeClient" class="space-y-4">
      <?= \CSRF::inputField() ?> 

      <div class="grid grid-cols-2 gap-3">
        <input id="nombre" name="nombre" type="text" placeholder="Nombre" required
          class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <input id="apellido" name="apellido" type="text" placeholder="Apellido" required
          class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
      </div>

      <input id="docusu" name="docusu" type="text" placeholder="Documento" required
        class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">

      <input id="email" name="email" type="email" placeholder="Correo electrónico" required
        class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">

      <input id="telefono" name="telefono" type="text" placeholder="Número de celular" required
        class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">

      <input id="password" name="password" type="password" placeholder="Contraseña nueva" required
        class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">

      <button type="submit"
        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition">
        Registrarme
      </button>
    </form>

    <p class="text-center mt-6">
      <a href="/vetsmart/auth/login" class="text-blue-600 hover:underline font-medium">
        Ya tengo una cuenta
      </a>
    </p>
  </div>
</body>
</html>
