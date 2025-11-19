<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>VetSmart — Iniciar sesión</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background: #f4f6f9;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .login-box {
      width: 500px;
      padding: 2rem;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0px 0px 12px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="login-box">
    <?= $content ?? '' ?>
  </div>
</body>
</html>
