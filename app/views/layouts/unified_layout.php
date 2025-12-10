
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'VetSmart' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/vetsmart/public/assets/css/dark-mode.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f1f5f9;
            transition: background-color 0.3s, color 0.3s;
        }

        .header {
            background: linear-gradient(90deg, #0d6efd, #0a58ca);
            color: #fff;
            height: 70px;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 30;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .wrapper {
            display: flex;
            flex: 1;
            margin-top: 70px;
            transition: all 0.3s ease;
        }

        .sidebar {
            width: 260px;
            background: #212529;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 1.5rem 1rem;
            min-height: calc(100vh - 70px);
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.15);
            transition: width 0.3s ease;
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar h2 {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
            color: #e5e7eb;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed h2 {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar a {
            color: #d1d5db;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sidebar a i {
            font-size: 1.1rem;
            width: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            color: rgba(255, 255, 255, 0.7);
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            transform: translateX(4px);
        }

        .sidebar a:hover i {
            color: #fff;
            transform: scale(1.1);
        }

        .sidebar .active {
            background: #198754;
            color: #fff;
            font-weight: 600;
            box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.3);
        }

        .sidebar .active i {
            color: #fff;
            transform: scale(1.15);
        }

        .sidebar.collapsed a span {
            display: none;
        }

        .sidebar.collapsed a {
            justify-content: center;
        }

        .sidebar.collapsed a i {
            margin: 0;
            width: auto;
        }

        .content {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            background: #f9fafb;
            transition: background-color 0.3s, color 0.3s;
        }

        main {
            animation: fadeIn 0.3s ease-in-out;
        }

        footer {
            text-align: center;
            font-size: 0.9rem;
            color: #6b7280;
            margin-top: auto;
            padding: 1rem 0;
            border-top: 1px solid #e5e7eb;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #themeToggle, #sidebarToggle {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.4rem 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        #themeToggle:hover, #sidebarToggle:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        #sidebarToggle {
            font-size: 1.3rem;
            margin-right: 0.75rem;
        }

        body.dark {
            background-color: #1a1a1a;
            color: #e5e7eb;
        }

        body.dark .header {
            background: linear-gradient(90deg, #1e3a8a, #1e40af);
            color: #f9fafb;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
        }

        body.dark .sidebar {
            background: #242a33;
            color: #e2e8f0;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        body.dark .sidebar h2 {
            color: #e5e7eb;
            border-bottom-color: rgba(255, 255, 255, 0.15);
        }

        body.dark .sidebar a {
            color: #b3bcc9;
        }

        body.dark .sidebar a:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        body.dark .sidebar a:hover i {
            color: #fff;
            transform: scale(1.1);
        }

        body.dark .sidebar .active {
            background: #2d5a3d;
            color: #fff;
            box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.4);
        }

        body.dark .sidebar .active i {
            color: #fff;
            transform: scale(1.15);
        }

        body.dark .sidebar a i {
            color: rgba(255, 255, 255, 0.7);
        }

        body.dark .content {
            background: #1a1a1a;
        }

        body.dark main {
            background: #2a2a2a !important;
            color: #e5e7eb;
            border-color: #404040 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        body.dark footer {
            border-top-color: #404040;
            color: #9ca3af;
        }

        /* Estilos oscuros para elementos adicionales */
        body.dark input, 
        body.dark select, 
        body.dark textarea {
            background-color: #3a3a3a;
            border-color: #505050;
            color: #e5e7eb;
        }

        body.dark input:focus, 
        body.dark select:focus, 
        body.dark textarea:focus {
            background-color: #454545;
            border-color: #0d6efd;
            color: #e5e7eb;
        }

        body.dark input::placeholder,
        body.dark textarea::placeholder {
            color: #909090;
        }

        body.dark .btn-light {
            background-color: #404040 !important;
            border-color: #505050 !important;
            color: #e5e7eb !important;
        }

        body.dark .btn-light:hover {
            background-color: #505050 !important;
            color: #fff !important;
        }

        body.dark .table {
            background-color: #2a2a2a;
            border-color: #404040;
        }

        body.dark .table th {
            background-color: #3a3a3a;
            color: #e5e7eb;
            border-color: #404040;
        }

        body.dark .table td {
            border-color: #404040;
            color: #e5e7eb;
        }

        body.dark .table tbody tr:hover {
            background-color: #353535;
        }

        body.dark .btn {
            border-color: rgba(255, 255, 255, 0.1);
        }

        body.dark .card {
            background-color: #2a2a2a;
            border-color: #404040;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        body.dark .card h5,
        body.dark .card h6 {
            color: #e5e7eb;
        }

        body.dark .badge {
            background-color: #c82333 !important;
            color: #fff !important;
        }

        body.dark .form-control {
            background-color: #3a3a3a;
            border-color: #505050;
            color: #e5e7eb;
        }

        body.dark .form-control:focus {
            background-color: #454545;
            border-color: #0d6efd;
            color: #e5e7eb;
        }

        body.dark label {
            color: #d1d5db;
        }

        body.dark .modal-content {
            background-color: #2a2a2a;
            border-color: #404040;
        }

        body.dark .modal-header {
            background-color: #3a3a3a;
            border-color: #404040;
        }

        body.dark .modal-header .btn-close {
            filter: invert(1);
        }

        body.dark .modal-title {
            color: #e5e7eb;
        }

        body.dark .alert {
            background-color: #3a3a3a;
            border-color: #505050;
            color: #e5e7eb;
        }

        body.dark .nav-tabs {
            border-bottom-color: #404040;
        }

        body.dark .nav-tabs .nav-link {
            color: #b3bcc9;
            border-color: transparent;
        }

        body.dark .nav-tabs .nav-link.active {
            background-color: #3a3a3a;
            color: #e5e7eb;
            border-color: #404040 #404040 transparent #404040;
        }

        body.dark .dropdown-menu {
            background-color: #3a3a3a;
            border-color: #505050;
        }

        body.dark .dropdown-menu .dropdown-item {
            color: #d1d5db;
        }

        body.dark .dropdown-menu .dropdown-item:hover {
            background-color: #454545;
            color: #e5e7eb;
        }

        /* ===== MEDIA QUERIES - RESPONSIVE DESIGN ===== */

        /* Tablets (768px and up) */
        @media (max-width: 1024px) {
            .sidebar {
                width: 220px;
            }

            .sidebar.collapsed {
                width: 70px;
            }

            .content {
                padding: 1.5rem;
            }

            main {
                padding: 1.2rem !important;
            }

            .sidebar a {
                padding: 0.5rem 0.6rem;
            }
        }

        /* Mobile (768px and down) */
        @media (max-width: 768px) {
            .header {
                height: 60px;
                padding: 0 1rem;
            }

            .header h1 {
                font-size: 1rem !important;
            }

            .wrapper {
                margin-top: 60px;
                flex-direction: row;
                position: relative;
                width: 100%;
            }

            .sidebar {
                position: fixed;
                left: 0;
                top: 60px;
                width: 260px;
                height: calc(100vh - 60px);
                z-index: 999;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.15);
                overflow-y: auto;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar.collapsed {
                width: 260px;
                transform: translateX(-100%);
            }

            .sidebar.collapsed.open {
                transform: translateX(0);
            }

            .sidebar a span {
                display: inline !important;
            }

            .sidebar.collapsed a {
                justify-content: flex-start;
            }

            .sidebar.collapsed a i {
                margin: 0;
                width: auto;
            }

            .content {
                flex: 1;
                width: 100%;
                padding: 1rem;
                margin-left: 0;
                z-index: 1;
            }

            main {
                padding: 1rem !important;
                font-size: 0.95rem;
            }

            .flex.items-center.gap-2 {
                gap: 0.5rem !important;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            #sidebarToggle {
                font-size: 1.2rem;
                margin-right: 0.5rem;
            }

            footer {
                margin-left: 0;
                font-size: 0.85rem;
                padding: 0.75rem 0;
            }

            /* Overlay cuando sidebar está abierto */
            .sidebar-overlay {
                position: fixed;
                top: 60px;
                left: 0;
                width: 100%;
                height: calc(100vh - 60px);
                background: rgba(0, 0, 0, 0.5);
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
                z-index: 10;
            }

            .sidebar-overlay.active {
                opacity: 1;
                pointer-events: auto;
            }

            /* Tablas responsive */
            .table {
                font-size: 0.85rem;
                padding: 0.5rem !important;
            }

            .table th, .table td {
                padding: 0.6rem !important;
            }

            /* Cards responsive */
            .card {
                margin-bottom: 1rem;
            }

            /* Inputs responsive */
            input, select, textarea {
                font-size: 16px; /* Evita zoom en iOS */
            }
        }

        /* Small phones (480px and down) */
        @media (max-width: 480px) {
            .header {
                height: 55px;
                padding: 0 0.75rem;
            }

            .header h1 {
                font-size: 0.85rem !important;
            }

            .wrapper {
                margin-top: 55px;
            }

            .sidebar {
                top: 55px;
                height: calc(100vh - 55px);
            }

            .sidebar-overlay {
                top: 55px;
                height: calc(100vh - 55px);
            }

            .content {
                padding: 0.75rem;
            }

            main {
                padding: 0.75rem !important;
                border-radius: 0.5rem;
            }

            .sidebar h2 {
                font-size: 0.95rem;
                margin-bottom: 1rem;
            }

            .sidebar a {
                padding: 0.5rem 0.6rem;
                gap: 0.5rem;
                font-size: 0.9rem;
            }

            .sidebar a i {
                font-size: 0.95rem;
                width: 1.3rem;
            }

            .btn-light {
                padding: 0.25rem 0.5rem !important;
                font-size: 0.7rem !important;
            }

            #sidebarToggle {
                font-size: 1rem;
                margin-right: 0.25rem;
            }

            #themeToggle {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }

            .table {
                font-size: 0.75rem;
            }

            .table th, .table td {
                padding: 0.4rem !important;
            }

            footer {
                font-size: 0.75rem;
                padding: 0.5rem 0;
            }

            /* Stack elements vertically */
            .row-cards {
                flex-direction: column;
                gap: 0.75rem !important;
            }
        }

        /* Extra small devices (320px and up) */
        @media (max-width: 320px) {
            .header h1 {
                font-size: 0.75rem !important;
            }

            .sidebar a {
                font-size: 0.8rem;
            }

            .btn-sm {
                font-size: 0.65rem;
            }

            main {
                padding: 0.5rem !important;
            }
        }

        /* Landscape mode adjustments */
        @media (max-height: 500px) and (orientation: landscape) {
            .sidebar {
                overflow-y: auto;
                max-height: calc(100vh - 60px);
            }

            .header {
                height: 50px;
            }

            .wrapper {
                margin-top: 50px;
            }

            .content {
                padding: 0.75rem;
            }

            main {
                padding: 0.75rem !important;
            }
        }

        /* Print styles */
        @media print {
            .header, .sidebar, #sidebarToggle, #themeToggle, footer {
                display: none;
            }

            .wrapper {
                margin-top: 0;
            }

            .content {
                padding: 0;
                margin: 0;
            }

            main {
                box-shadow: none;
                border: none;
            }
        }
        /* Global responsive helpers */
        /* Make images fluid by default */
        img, .img-fluid {
            max-width: 100%;
            height: auto;
        }

        /* Modals full width on small screens */
        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 100% !important;
                margin: 0.5rem;
            }
            .modal-content {
                border-radius: 0.5rem;
            }
            .btn-group {
                flex-wrap: wrap;
            }
            .btn-group .btn {
                margin-bottom: 0.35rem;
            }
            /* Ensure forms and cards are full width */
            .card, form, .table-responsive {
                width: 100% !important;
            }
            /* Make long table cells wrap text */
            .table td, .table th {
                white-space: normal !important;
                word-break: break-word;
            }
        }
    </style>
</head>
<body>

<header class="header">
    <div class="flex items-center">
        <button id="sidebarToggle" title="Mostrar/Ocultar menú">☰</button>
        <h1 class="text-lg font-bold tracking-wide"><?= $header_title ?? 'VetSmart' ?></h1>
    </div>

    <div class="flex items-center gap-2">
        <button id="themeToggle" title="Cambiar tema">🌙</button>
        <a href="/vetsmart/logout" class="btn btn-light btn-sm shadow-sm hover:bg-gray-200 transition">
            Cerrar Sesión
        </a>
    </div>
</header>

<div class="wrapper">
    <aside class="sidebar" id="sidebar">
        <h2>
            Bienvenido<br>
            <span class="text-blue-300">
                <?= htmlspecialchars($_SESSION['user']['nombre'] ?? '') ?>
                <?= htmlspecialchars($_SESSION['user']['apellido'] ?? '') ?>
            </span>
        </h2>

        <?php if (isset($nav_links)): ?>
            <?php foreach ($nav_links as $link): ?>
                <a href="<?= $link['url'] ?>" class="<?= strpos($_SERVER['REQUEST_URI'], $link['url']) !== false ? 'active' : '' ?>">
                    <i class="<?= $link['icon'] ?>"></i> <span><?= $link['text'] ?></span>
                    <?php if (isset($link['badge'])): ?>
                        <span class="badge bg-danger ms-auto"><?= $link['badge'] ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </aside>

    <div class="content">
        <main class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
            <?= $content ?? '' ?>
        </main>

        <footer>
            © <?= date("Y") ?> VetSmart. Todos los derechos reservados.
        </footer>
    </div>
</div>

<!-- Overlay para móvil -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
    // === VARIABLES GLOBALES ===
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    let isMobileView = window.innerWidth <= 768;

    // === INICIALIZACIÓN ===
    function initSidebar() {
        if (!isMobileView && localStorage.getItem('sidebar') === 'collapsed') {
            sidebar.classList.add('collapsed');
        }
    }

    // === TOGGLE SIDEBAR ===
    sidebarToggle.addEventListener('click', (e) => {
        e.preventDefault();
        if (isMobileView) {
            // En móvil, abrir/cerrar drawer
            sidebar.classList.toggle('open');
            updateSidebarState();
        } else {
            // En desktop, colapsar/expandir
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
        }
    });

    // === CERRAR SIDEBAR AL HACER CLIC EN UN ENLACE (MÓVIL) ===
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', () => {
            if (isMobileView) {
                closeSidebar();
            }
        });
    });

    // === CERRAR SIDEBAR ===
    function closeSidebar() {
        sidebar.classList.remove('open');
        updateSidebarState();
    }

    // === ABRIR SIDEBAR ===
    function openSidebar() {
        sidebar.classList.add('open');
        updateSidebarState();
    }

    // === OVERLAY CLICK ===
    if (overlay) {
        overlay.addEventListener('click', () => {
            closeSidebar();
        });
    }

    // === DETECTAR CAMBIOS DE TAMAÑO DE PANTALLA ===
    window.addEventListener('resize', () => {
        const wasPhone = isMobileView;
        isMobileView = window.innerWidth <= 768;

        if (wasPhone !== isMobileView) {
            // Cambió entre móvil y desktop
            if (!isMobileView) {
                // De móvil a desktop
                closeSidebar();
                if (localStorage.getItem('sidebar') === 'collapsed') {
                    sidebar.classList.add('collapsed');
                }
            } else {
                // De desktop a móvil
                sidebar.classList.remove('collapsed');
                closeSidebar();
            }
        }
    });

    // === ACTUALIZAR ESTADO DEL SIDEBAR ===
    function updateSidebarState() {
        if (overlay) {
            if (sidebar.classList.contains('open')) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        }
    }

    // === INICIALIZACIÓN EN CARGA ===
    initSidebar();
    updateSidebarState();

    // === DETECTAR ORIENTATION CHANGE ===
    window.addEventListener('orientationchange', () => {
        setTimeout(() => {
            isMobileView = window.innerWidth <= 768;
            if (isMobileView) {
                sidebar.classList.remove('collapsed');
                closeSidebar();
            }
        }, 100);
    });

    // === CERRAR SIDEBAR CON ESC ===
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isMobileView && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/vetsmart/public/assets/js/dark-mode.js"></script>
</body>
</html>
