<?php
// Partial: admin_head.php
// Incluye enlaces a CSS y estilos compartidos del layout de administrador
?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    /* Copiado del estilo principal de admin para unificar apariencia */
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: #f8f9fa;
      margin: 0;
      transition: background-color 0.3s, color 0.3s;
      font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    }

    .header {
      width: 100%;
      height: 70px;
      background: #198754;
      color: #fff;
      padding: 0 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 30;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .wrapper {
      display: flex;
      flex: 1;
      margin-top: 70px;
      transition: all 0.3s ease;
    }

    .sidebar {
      width: 250px;
      background: #212529;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 1.2rem;
      min-height: calc(100vh - 70px);
      transition: width 0.3s ease;
      overflow: hidden;
    }

    .sidebar.collapsed { width: 80px; }

    .sidebar h2 { font-size: 1.1rem; margin-bottom: 1.2rem; font-weight: bold; }

    .sidebar a { color: #ddd; text-decoration: none; display: flex; align-items: center; gap: 0.6rem; padding: 0.5rem 0.75rem; border-radius: 8px; margin-bottom: 0.4rem; transition: all 0.2s ease; white-space: nowrap; }

    .sidebar a:hover { background: #343a40; color: #fff; transform: translateX(3px); }

    .sidebar .active { background: #0d6efd; color: #fff; font-weight: bold; }

    .sidebar.collapsed a span { display: none; }

    .content { flex: 1; display: flex; flex-direction: column; padding: 25px 20px 0 20px; transition: background-color 0.3s, color 0.3s; }

    footer { text-align: center; font-size: 0.9rem; color: #666; margin-top: auto; padding: 1rem 0; }

    #sidebarToggle, #themeToggle { background: rgba(255,255,255,0.15); color: #fff; border: none; border-radius: 6px; padding: 0.4rem 0.6rem; cursor: pointer; transition: all 0.3s; }

    body.dark { background-color: #121212; color: #e5e5e5; }
    body.dark .header { background: #14532d; }
    body.dark .sidebar { background: #0f172a; color: #e2e8f0; }
    body.dark .sidebar a { color: #9ca3af; }
    body.dark .sidebar a:hover { background: rgba(255,255,255,0.1); color: #fff; }
    body.dark .sidebar .active { background: #166534; }
    body.dark footer { color: #9ca3af; }
  </style>
