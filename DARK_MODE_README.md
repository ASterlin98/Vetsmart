# 🌙 Modo Oscuro - VetSmart

## Descripción General

Se ha implementado un sistema completo de modo oscuro para todos los perfiles de la aplicación VetSmart. El modo oscuro proporciona:

- ✅ **Mejor contraste**: Texto claro sobre fondos oscuros para facilitar la lectura
- ✅ **Reducción de fatiga visual**: Especialmente útil en ambientes de poca luz
- ✅ **Persistencia**: La preferencia se guarda en localStorage
- ✅ **Compatibilidad**: Funciona en todos los navegadores modernos
- ✅ **Cobertura completa**: Se aplica a todos los elementos (formularios, tablas, modales, etc.)

## Archivos Modificados

### 1. **Hojas de Estilo**
- `/public/assets/css/dark-mode.css` - CSS dedicado al modo oscuro (nuevo)
- `/public/assets/css/app.css` - CSS base actualizado con variables de tema
- `/app/views/layouts/unified_layout.php` - Estilos integrados

### 2. **Scripts JavaScript**
- `/public/assets/js/dark-mode.js` - Gestor de modo oscuro (nuevo)

### 3. **Layouts PHP**
- `/app/views/layouts/unified_layout.php` - Layout principal para perfiles
- `/app/views/layouts/main.php` - Layout de login

## Características Principales

### Gestión Automática de Tema

El sistema de modo oscuro automáticamente:
- Detecta la preferencia del sistema operativo (`prefers-color-scheme`)
- Guarda la preferencia del usuario en localStorage
- Restaura el tema al recargar la página
- Observa cambios en el DOM para aplicar estilos a nuevos elementos

### Elementos Soportados

El modo oscuro se aplica a:
- ✓ Header y Navbar
- ✓ Sidebar y Navegación
- ✓ Tarjetas (Cards)
- ✓ Formularios (inputs, select, textarea)
- ✓ Botones
- ✓ Tablas
- ✓ Modales
- ✓ Alertas y Badges
- ✓ Tabs y Dropdowns
- ✓ Scrollbars personalizadas
- ✓ Tooltips
- ✓ Listas y Paginación

## Uso

### Para Usuarios

1. **Alternar Modo Oscuro**: Haz clic en el botón 🌙 en la esquina superior derecha
2. **Preferencia Guardada**: El sistema recuerda tu preferencia
3. **Sincronizar con Sistema**: Si no estableciste preferencia, usa la del SO

### Para Desarrolladores

#### Aplicar Modo Oscuro a Nuevos Elementos

Todos los elementos heredan automáticamente los estilos del modo oscuro. Solo necesitas seguir estas convenciones:

```html
<!-- Bootstrap Classes se adaptan automáticamente -->
<button class="btn btn-light">Mi Botón</button>

<!-- Elements con fondo blanco explícito se convierten automáticamente -->
<div style="background: white; color: black;">
  Este div se verá bien en modo oscuro
</div>
```

#### Acceder al Estado del Modo Oscuro

```javascript
// Alternar modo oscuro
window.toggleDarkMode();

// Habilitar modo oscuro
window.enableDarkMode();

// Deshabilitar modo oscuro
window.disableDarkMode();

// Verificar si está habilitado
if (window.isDarkModeEnabled()) {
  console.log('Modo oscuro activo');
}

// Acceso directo al gestor
window.darkModeManager.toggle();
```

#### Agregar Estilos Personalizados

En `dark-mode.css`, agrega tus reglas personalizadas:

```css
body.dark .mi-elemento-personalizado {
    background-color: #2a2a2a;
    color: #e5e7eb;
    border-color: #404040;
}
```

## Paleta de Colores

### Modo Claro
- Fondo: `#f4f6f9`
- Texto Principal: `#0f172a`
- Tarjetas: `#ffffff`
- Bordes: `#efefef`

### Modo Oscuro
- Fondo: `#1a1a1a`
- Texto Principal: `#e5e7eb`
- Tarjetas: `#2a2a2a`
- Bordes: `#404040`
- Header: Gradiente `#1e3a8a` → `#1e40af`
- Sidebar: `#242a33`

## Solución de Problemas

### El modo oscuro no se aplica

1. Asegúrate de que el archivo CSS está cargado:
   ```html
   <link rel="stylesheet" href="/vetsmart/public/assets/css/dark-mode.css">
   ```

2. Verifica que el script está cargado:
   ```html
   <script src="/vetsmart/public/assets/js/dark-mode.js"></script>
   ```

3. Limpia el caché del navegador (Ctrl+Shift+Supr)

### Elementos específicos no cambian de color

1. Revisa si tienen `!important` en estilos inline
2. Agrega la regla correspondiente a `dark-mode.css`
3. Usa selectores CSS más específicos

### El botón de tema no funciona

1. Verifica que existe un elemento con id `themeToggle`
2. Abre la consola del navegador (F12) para ver errores
3. Asegúrate de que el script `dark-mode.js` se está ejecutando

## Personalización

### Cambiar Colores

En `dark-mode.css`, modifica los valores de color:

```css
body.dark {
    background-color: #tu-color-aqui;
    color: #tu-texto-aqui;
}
```

### Agregar Nuevas Reglas

Agrega nuevas secciones según sea necesario:

```css
/* === MI SECCIÓN PERSONALIZADA === */
body.dark .mi-componente {
    background: #2a2a2a;
    color: #e5e7eb;
}
```

### Deshabilitar para Ciertos Elementos

Usa `!important` para sobrescribir:

```css
body.dark .elemento-siempre-claro {
    background: white !important;
    color: black !important;
}
```

## Rendimiento

- **Sin Impacto**: El modo oscuro usa CSS puro, sin JavaScript innecesario
- **Transiciones Suaves**: Las transiciones se desactivan si el usuario prefiere movimiento reducido
- **Almacenamiento Local**: Usa localStorage para persistencia (sin carga adicional de servidor)

## Navegadores Soportados

- ✓ Chrome/Chromium 76+
- ✓ Firefox 67+
- ✓ Safari 13+
- ✓ Edge 79+
- ✓ Opera 63+

## Próximas Mejoras (Futuro)

- [ ] Tema personalizable (múltiples esquemas de color)
- [ ] Exportar/Importar configuración de tema
- [ ] Modo oscuro automático según hora del día
- [ ] Animaciones al cambiar tema
- [ ] Soporte para temas adicionales (sepia, alto contraste, etc.)

## Contacto

Para reportar problemas o sugerir mejoras al sistema de modo oscuro, contacta al equipo de desarrollo.

---

**Última actualización**: Noviembre 21, 2025
