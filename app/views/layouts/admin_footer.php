<?php
// Partial: admin_footer.php
// JS para comportamiento del layout administrativo (sidebar toggle y tema)
?>
  <script>
    (function(){
      const sidebar = document.getElementById('sidebar');
      const sidebarToggle = document.getElementById('sidebarToggle');
      const themeToggle = document.getElementById('themeToggle');
      const body = document.body;

      try {
        if (sidebar && localStorage.getItem('sidebar') === 'collapsed') {
          sidebar.classList.add('collapsed');
        }

        if (sidebarToggle) {
          sidebarToggle.addEventListener('click', () => {
            sidebar && sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar', sidebar && sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
          });
        }

        if (themeToggle) {
          if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark');
            themeToggle.textContent = '🌞';
          }
          themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark');
            const isDark = body.classList.contains('dark');
            themeToggle.textContent = isDark ? '🌞' : '🌙';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
          });
        }
      } catch (e) {
        // No romper layout si algún elemento no existe
        console.warn('admin_footer script:', e.message);
      }
    })();
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
