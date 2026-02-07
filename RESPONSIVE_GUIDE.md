# 📱 Guía de Diseño Responsive - VetSmart

## 🎯 Principios Clave

1. **Mobile-First**: Diseña para móvil primero, luego expande a desktop
2. **Viewport Meta Tag**: Siempre incluir en layouts
3. **Bootstrap 5**: Classes como `col-sm-`, `col-md-`, `col-lg-`
4. **Responsive CSS**: Archivo `responsive.css` integrado

---

## 🔧 CSS y JavaScript Base

### Incluir en todos los layouts:

```html
<!-- Estilos -->
<link rel="stylesheet" href="/vetsmart/public/assets/css/responsive.css">

<!-- Scripts -->
<script src="/vetsmart/public/assets/js/responsive.js"></script>
```

---

## 📐 Patrones Responsive Recomendados

### 1. **Tablas**
```html
<!-- Siempre envolver en un contenedor responsive -->
<div class="table-responsive">
    <table class="table">
        <!-- contenido -->
    </table>
</div>
```

### 2. **Grillas de Tarjetas**
```html
<!-- Usar Bootstrap Grid -->
<div class="row g-3">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card">Contenido</div>
    </div>
</div>

<!-- O CSS Grid (preferido) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
    <div class="card">Contenido</div>
</div>
```

### 3. **Botones en Móvil**
```html
<!-- Usar flex-wrap para móviles -->
<div class="d-flex flex-wrap gap-2">
    <button class="btn btn-primary">Acción 1</button>
    <button class="btn btn-secondary">Acción 2</button>
</div>
```

### 4. **Formularios**
```html
<!-- Inputs completos en móvil -->
<div class="form-group mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" class="form-control" />
</div>

<!-- En tablas, usar col-12 col-md-6 -->
<div class="row">
    <div class="col-12 col-md-6">
        <div class="form-group mb-3">
            <label>Campo 1</label>
            <input type="text" class="form-control" />
        </div>
    </div>
</div>
```

### 5. **Headers y Navegación**
```html
<!-- El header usa el layout template -->
<!-- Para móvil: hamburger button visible, sidebar off-screen -->
<!-- El responsive.js maneja el toggle automático -->
```

---

## 📱 Breakpoints

| Dispositivo | Ancho | Breakpoint |
|---|---|---|
| Móvil pequeño | < 320px | Extra small |
| Móvil | 320px - 480px | `@media (max-width: 480px)` |
| Tablet | 481px - 768px | `@media (max-width: 768px)` |
| Laptop | 769px - 1024px | Desktop |
| Desktop grande | > 1024px | Desktop |

---

## ✅ Checklist para Cada Vista

Antes de completar una vista, verifica:

- [ ] Meta viewport presente: `<meta name="viewport" content="width=device-width, initial-scale=1.0">`
- [ ] Tablas envueltas en `.table-responsive`
- [ ] Grillas usan `col-sm-`, `col-md-` o CSS Grid
- [ ] Formularios tienen inputs a ancho completo en móvil
- [ ] Botones no están muy ajustados (usar `gap`, no `mr-2` etc)
- [ ] Imágenes usan `max-width: 100%`
- [ ] No hay overflow horizontal sin `.table-responsive`
- [ ] Fonts no son menores a 14px en inputs (iOS)
- [ ] Modal dialogs caben en pantalla pequeña

---

## 🎨 Clases Utilidad Disponibles

### Spacing
```css
.mt-1 .mb-1 .p-1 /* margin/padding: 4px */
.mt-2 .mb-2 .p-2 /* margin/padding: 8px */
.mt-3 .mb-3 .p-3 /* margin/padding: 12px */
.mt-4 .mb-4 .p-4 /* margin/padding: 16px */
.mt-5 .mb-5 .p-5 /* margin/padding: 20px */
```

### Display
```css
.d-none   /* display: none */
.d-block  /* display: block */
.d-flex   /* display: flex */
.d-grid   /* display: grid */
.gap-1, .gap-2, .gap-3, .gap-4 /* flex/grid gap */
```

### Text
```css
.text-center .text-left .text-right
.text-muted .text-primary .text-success .text-danger
.fw-bold .fw-semibold .fw-normal
```

### Dark Mode
Las clases funcionan automáticamente con dark mode via CSS custom properties.

---

## 🛠️ Mejoras Realizadas

### ✅ Completadas:
1. **responsive.css** - Base CSS responsive
2. **responsive.js** - Manejo de sidebar en móvil
3. **main.php** - Login responsive
4. **unified_layout.php** - Layouts principales mejorados
5. **main_cliente.php, main_peluquero.php, main_recepcionista.php** - Heredan mejoras

### 🔄 En Progreso:
- [ ] Revisar y mejorar dashboards específicos
- [ ] Optimizar formularios
- [ ] Mejorar tablas complejas
- [ ] Pruebas en dispositivos reales

---

## 🧪 Cómo Probar

### Desde PC:
1. Abre DevTools (F12)
2. Presiona Ctrl+Shift+M para mobile mode
3. Prueba diferentes tamaños: 320px, 480px, 768px, 1024px

### Desde Móvil:
1. Accede a la URL del servidor
2. Verifica en orientación portrait y landscape
3. Prueba los botones, formularios, tablas

---

## 📝 Notas Importantes

- **Fuente mínima en inputs**: 16px (previene auto-zoom en iOS)
- **Touch targets**: Mínimo 44x44px (usabilidad táctil)
- **Viewport máximo**: `maximum-scale=5.0` (permite zoom sin excesos)
- **Dark mode**: Toggle automático vía localStorage
- **Sidebar en móvil**: Off-canvas, se abre/cierra con hamburger

---

## 📧 Contacto y Soporte

Para recibir ayuda detallada de una vista específica, proporciona:
1. Ruta de la vista (ej: `/app/views/admin/dashboard.php`)
2. Problemas observados en móvil
3. Tamaño de pantalla donde se ve mal

---

**Última actualización**: 2026-02-06  
**Versión**: 1.0
