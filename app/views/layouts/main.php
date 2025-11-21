<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>VetSmart — Iniciar sesión</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/vetsmart/public/assets/css/dark-mode.css">
  <style>
    body {
      background: linear-gradient(135deg, #f4f6f9 0%, #e8ecf1 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      transition: background 0.3s ease;
    }

    body.dark {
      background: linear-gradient(135deg, #1a1a1a 0%, #242a33 100%);
    }

    .login-box {
      width: 500px;
      padding: 2rem;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0px 0px 12px rgba(0,0,0,0.1);
      transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    body.dark .login-box {
      background: #2a2a2a;
      box-shadow: 0px 0px 12px rgba(0,0,0,0.3);
    }

    body.dark .login-box h2,
    body.dark .login-box label,
    body.dark .login-box p {
      color: #e5e7eb;
    }

    .theme-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      background: rgba(0, 0, 0, 0.1);
      border: none;
      color: #000;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      cursor: pointer;
      font-size: 20px;
      transition: all 0.3s ease;
    }

    body.dark .theme-toggle {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    .theme-toggle:hover {
      transform: scale(1.1);
    }
  </style>
</head>
<body>
  <button class="theme-toggle" id="themeToggle" title="Cambiar tema">🌙</button>
  
  <div class="login-box">
    <?= $content ?? '' ?>
  </div>

  <script src="/vetsmart/public/assets/js/dark-mode.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
