<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>VetSmart — Iniciar sesión</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= BASE ?>/public/assets/css/responsive.css">
  <style>
    :root {
      --bg-light: #f4f6f9;
      --bg-light-alt: #e8ecf1;
      --card-light: #fff;
      --text-light: #000;
      --accent: #0d6efd;
    }

    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, var(--bg-light) 0%, var(--bg-light-alt) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .auth-container {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      width: 100%;
      padding: 20px;
    }

    .login-box {
      width: 100%;
      max-width: 500px;
      padding: 2rem;
      background: var(--card-light);
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      animation: slideUp 0.4s ease-out;
    }

    .login-box h2 {
      margin-bottom: 1.5rem;
      color: var(--text-light);
      font-weight: 700;
      text-align: center;
    }

    .login-box .form-group {
      margin-bottom: 1rem;
    }

    .login-box label {
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: var(--text-light);
    }

    .login-box input,
    .login-box select {
      padding: 0.75rem;
      border: 1px solid rgba(0, 0, 0, 0.1);
      border-radius: 6px;
      font-size: 14px;
      transition: all 0.2s ease;
    }

    .login-box input:focus,
    .login-box select:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .login-box button {
      width: 100%;
      padding: 0.75rem;
      margin-top: 1rem;
      background: var(--accent);
      color: #fff;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 16px;
    }

    .login-box button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }

    .login-box button:active {
      transform: translateY(0);
    }

    .login-box p {
      text-align: center;
      color: var(--text-light);
      margin-top: 1.5rem;
      font-size: 14px;
    }

    .login-box a {
      color: var(--accent);
      text-decoration: none;
      font-weight: 600;
    }

    .login-box a:hover {
      text-decoration: underline;
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

    /* Error messages */
    .error-message {
      color: #dc2626;
      font-size: 13px;
      margin-top: 0.25rem;
      display: block;
    }

    .success-message {
      color: #10b981;
      font-size: 13px;
      margin-top: 0.25rem;
      display: block;
    }

    /* Responsive adjustments */
    @media (max-width: 480px) {
      .login-box {
        padding: 1.5rem;
      }

      .login-box h2 {
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
      }

      .login-box input,
      .login-box select {
        font-size: 16px; /* Previene zoom en iOS */
      }

      .login-box button {
        padding: 0.875rem;
        font-size: 16px;
      }


    }

    @media (max-width: 320px) {
      .login-box {
        padding: 1.25rem;
      }

      .login-box h2 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
      }

      .login-box p {
        font-size: 13px;
      }
    }
  </style>
</head>
<body>
  
  <div class="auth-container">
    <div class="login-box">
      <?= $content ?? '' ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE ?>/public/assets/js/responsive.js"></script>
</body>
</html>
