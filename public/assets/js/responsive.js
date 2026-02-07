// public/assets/js/responsive.js - Manejo del sidebar en móviles

document.addEventListener('DOMContentLoaded', function() {
  const hamburgerBtn = document.querySelector('.hamburger-btn');
  const sidebar = document.querySelector('.app-sidebar');
  const overlay = document.querySelector('.sidebar-overlay');

  // Toggle sidebar on hamburger click
  if (hamburgerBtn) {
    hamburgerBtn.addEventListener('click', function() {
      sidebar?.classList.toggle('active');
      overlay?.classList.toggle('active');
    });
  }

  // Close sidebar when clicking overlay
  if (overlay) {
    overlay.addEventListener('click', function() {
      sidebar?.classList.remove('active');
      overlay?.classList.remove('active');
    });
  }

  // Close sidebar when clicking a link (opcional)
  const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function() {
      // Solo cerrar en pantallas pequeñas
      if (window.innerWidth <= 768) {
        sidebar?.classList.remove('active');
        overlay?.classList.remove('active');
      }
    });
  });

  // Close sidebar on window resize
  window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
      sidebar?.classList.remove('active');
      overlay?.classList.remove('active');
    }
  });
});
