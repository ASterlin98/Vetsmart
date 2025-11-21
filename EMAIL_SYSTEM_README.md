# 📧 Sistema de Envío de Correos - VetSmart

## ¿Qué hace este sistema?

Cuando se agenda una cita (desde recepcionista, veterinario o peluquero), el cliente recibe automáticamente un correo de confirmación con los detalles de su cita.

## Archivos Creados/Modificados

### 1. **EmailHelper.php** (Nuevo)
   - **Ubicación:** `/app/helpers/EmailHelper.php`
   - **Función:** Centraliza la lógica de envío de correos
   - **Método principal:** `EmailHelper::enviarConfirmacionCita()`
   - **Maneja:** PHPMailer con fallback a mail() nativo del sistema

### 2. **CitaController.php** (Modificado)
   - **Cambios en `store()`:** Se envía correo después de crear la cita
   - **Cambios en `guardarCitaPeluqueria()`:** Se envía correo después de crear la cita de peluquería
   - **Datos incluidos:** Fecha, hora, mascota, servicio, empleado, tipo de profesional

## Configuración Requerida

### Opción 1: Gmail con Contraseña de Aplicación (Recomendado)

1. **Habilita 2FA en tu cuenta Gmail**
2. **Genera una contraseña de aplicación:**
   - Ve a: https://myaccount.google.com/apppasswords
   - Selecciona "Correo" y "Windows"
   - Copia la contraseña de 16 caracteres

3. **Actualiza tu `.env`:**
   ```env
   MAIL_HOST=smtp.gmail.com
   MAIL_USERNAME=tu.email@gmail.com
   MAIL_PASSWORD=xxxx xxxx xxxx xxxx
   MAIL_PORT=465
   MAIL_SECURE=smtps
   MAIL_FROM=tu.email@gmail.com
   MAIL_FROM_NAME=VetSmart
   ```

### Opción 2: Outlook/Hotmail

```env
MAIL_HOST=smtp-mail.outlook.com
MAIL_USERNAME=tu.email@outlook.com
MAIL_PASSWORD=tu_contraseña
MAIL_PORT=587
MAIL_SECURE=tls
MAIL_FROM=tu.email@outlook.com
MAIL_FROM_NAME=VetSmart
```

### Opción 3: Servidor SMTP Personalizado

```env
MAIL_HOST=tu.servidor.com
MAIL_USERNAME=usuario@ejemplo.com
MAIL_PASSWORD=tu_contraseña
MAIL_PORT=465  # o 587
MAIL_SECURE=smtps  # o tls
MAIL_FROM=noreply@tu.dominio.com
MAIL_FROM_NAME=VetSmart
```

### Opción 4: Usar mail() del Sistema

Si no configuras las credenciales SMTP, el sistema automáticamente intenta usar `mail()` del sistema (requiere que PHP esté configurado correctamente en php.ini).

## Campos del Correo

El correo incluye:

- ✅ **Nombre del cliente**
- ✅ **Fecha de la cita** (formato dd/m/yyyy)
- ✅ **Hora**
- ✅ **Nombre de la mascota**
- ✅ **Servicio a realizar**
- ✅ **Profesional asignado**
- ✅ **Tipo de profesional** (Veterinario / Peluquero)
- ✅ **Recordatorio:** Llegar 10 minutos antes
- ✅ **Datos de contacto** para cambios

## Pruebas

### Test Básico en Producción

1. Agenda una cita desde recepcionista/veterinario/peluquero
2. Verifica el correo del cliente en su bandeja de entrada
3. Revisa `/var/log/php_errors.log` o los logs de PHP si hay problemas

### Verificar Configuración

```php
// En tu terminal PHP o dentro de un archivo test:
require_once 'app/helpers/EmailHelper.php';

$testData = [
    'fecha' => date('Y-m-d H:i', strtotime('+1 day')),
    'hora' => date('H:i', strtotime('+1 day')),
    'mascota' => 'Max',
    'servicio' => 'Consulta General',
    'empleado' => 'Dr. García',
    'tipo_empleado' => 'Veterinario'
];

$result = EmailHelper::enviarConfirmacionCita(
    'cliente@ejemplo.com',
    'Juan Pérez',
    $testData
);

echo $result ? "✓ Correo enviado" : "✗ Error al enviar";
```

## Solución de Problemas

### "No se envía el correo"

1. **Verifica las credenciales en `.env`**
   - ¿El usuario es correcto?
   - ¿La contraseña está bien (sin espacios extra)?

2. **Revisa los logs:**
   - PHP error log
   - Apache/Nginx error log
   - Busca mensajes de error de PHPMailer

3. **Prueba la conexión SMTP:**
   ```bash
   telnet smtp.gmail.com 465
   ```

4. **Verifica que el cliente tiene email:**
   - La cita se crea aunque no se envíe el correo
   - Si `cli.email` es NULL, el correo no se envía

### "El correo se ve extraño"

1. Es normal que Gmail/Outlook reformatee el HTML
2. El correo tiene versión texto plano de respaldo
3. Todos los navegadores de correo lo deben mostrar bien

### "Quiero deshabilitar el envío temporal"

Comentariza estas líneas en `CitaController.php`:

```php
// require_once APP_ROOT . '/helpers/EmailHelper.php';
// $mailSent = EmailHelper::enviarConfirmacionCita(...)
```

## Caracteristicas del Correo

- 📱 **Responsive:** Se ve bien en desktop, tablet y móvil
- 🎨 **Profesional:** Diseño moderno con colores de marca
- 📧 **Multipart:** Incluye HTML y texto plano
- 🔒 **Seguro:** No incluye datos sensibles (contraseñas, etc.)
- ⚡ **Rápido:** Se envía de forma asincrónica sin bloquear la UI

## Variables de Entorno Soportadas

```env
# SMTP Configuration
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=tu.email@gmail.com
MAIL_PASSWORD=tu_contraseña
MAIL_PORT=465
MAIL_SECURE=smtps
MAIL_FROM=tu.email@gmail.com
MAIL_FROM_NAME=VetSmart

# Base URL (para links en el correo)
APP_BASE_URL=http://localhost/vetsmart
```

## Próximas Mejoras

- [ ] Envío de correos de recordatorio 24h antes
- [ ] Notificación al empleado cuando se le asigna una cita
- [ ] Correos de cancelación/reprogramación
- [ ] Templates personalizables
- [ ] Historial de correos enviados en DB
