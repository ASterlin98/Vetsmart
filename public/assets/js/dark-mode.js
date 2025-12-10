/**
 * Dark Mode Manager - Gestiona el modo oscuro en toda la aplicación
 * Soporta persistencia en localStorage y observación de cambios del sistema
 */

class DarkModeManager {
    constructor() {
        this.STORAGE_KEY = 'vetsmart-theme';
        this.DARK_CLASS = 'dark';
        this.init();
    }

    init() {
        // Restaurar tema guardado o detectar preferencia del sistema
        const savedTheme = this.getSavedTheme();
        const prefersDark = this.prefersReducedMotion() ? false : this.getSystemPreference();
        
        const isDark = savedTheme !== null ? savedTheme : prefersDark;
        
        if (isDark) {
            this.enable();
        } else {
            this.disable();
        }

        // Escuchar cambios de preferencia del sistema
        this.watchSystemPreference();
        
        // Inicializar botón si existe
        this.initToggleButton();
    }

    /**
     * Obtener tema guardado del localStorage
     */
    getSavedTheme() {
        const saved = localStorage.getItem(this.STORAGE_KEY);
        return saved !== null ? saved === 'dark' : null;
    }

    /**
     * Obtener preferencia del sistema
     */
    getSystemPreference() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    /**
     * Reducir movimiento para transiciones suaves
     */
    prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    /**
     * Habilitar modo oscuro
     */
    enable() {
        document.body.classList.add(this.DARK_CLASS);
        document.documentElement.classList.add(this.DARK_CLASS);
        localStorage.setItem(this.STORAGE_KEY, 'dark');
        this.updateToggleButton(true);
        this.applyDarkModeToIframes();
    }

    /**
     * Deshabilitar modo oscuro
     */
    disable() {
        document.body.classList.remove(this.DARK_CLASS);
        document.documentElement.classList.remove(this.DARK_CLASS);
        localStorage.setItem(this.STORAGE_KEY, 'light');
        this.updateToggleButton(false);
        this.removeDarkModeFromIframes();
    }

    /**
     * Alternar modo oscuro
     */
    toggle() {
        if (document.body.classList.contains(this.DARK_CLASS)) {
            this.disable();
        } else {
            this.enable();
        }
    }

    /**
     * Actualizar botón de alternancia
     */
    updateToggleButton(isDark) {
        const buttons = document.querySelectorAll('#themeToggle, [data-theme-toggle]');
        buttons.forEach(btn => {
            btn.textContent = isDark ? '🌞' : '🌙';
            btn.setAttribute('aria-pressed', isDark);
        });
    }

    /**
     * Inicializar botón de alternancia
     */
    initToggleButton() {
        const toggleBtn = document.getElementById('themeToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggle();
            });
        }

        // Soporte para múltiples botones con atributo data-theme-toggle
        document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggle();
            });
        });
    }

    /**
     * Escuchar cambios de preferencia del sistema
     */
    watchSystemPreference() {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Solo aplicar si no hay preferencia guardada
            if (this.getSavedTheme() === null) {
                e.matches ? this.enable() : this.disable();
            }
        });
    }

    /**
     * Aplicar dark mode a iframes
     */
    applyDarkModeToIframes() {
        document.querySelectorAll('iframe').forEach(iframe => {
            try {
                if (iframe.contentDocument) {
                    iframe.contentDocument.body.classList.add(this.DARK_CLASS);
                }
            } catch (e) {
                // Cross-origin iframes no se pueden modificar
            }
        });
    }

    /**
     * Remover dark mode de iframes
     */
    removeDarkModeFromIframes() {
        document.querySelectorAll('iframe').forEach(iframe => {
            try {
                if (iframe.contentDocument) {
                    iframe.contentDocument.body.classList.remove(this.DARK_CLASS);
                }
            } catch (e) {
                // Cross-origin iframes no se pueden modificar
            }
        });
    }

    /**
     * Observar cambios en el DOM para aplicar dark mode a elementos nuevos
     */
    observeNewElements() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length > 0) {
                    // Re-aplicar estilos si es necesario
                    this.applyStylesToNewElements();
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    /**
     * Aplicar estilos a elementos nuevos (si es necesario)
     */
    applyStylesToNewElements() {
        // Este método puede ser extendido según necesidades específicas
    }

    /**
     * Obtener estado actual
     */
    isDarkMode() {
        return document.body.classList.contains(this.DARK_CLASS);
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.darkModeManager = new DarkModeManager();
        window.darkModeManager.observeNewElements();
    });
} else {
    window.darkModeManager = new DarkModeManager();
    window.darkModeManager.observeNewElements();
}

// Exponer métodos globales para fácil acceso
window.toggleDarkMode = () => window.darkModeManager.toggle();
window.enableDarkMode = () => window.darkModeManager.enable();
window.disableDarkMode = () => window.darkModeManager.disable();
window.isDarkModeEnabled = () => window.darkModeManager.isDarkMode();
