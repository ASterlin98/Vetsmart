# 📧 Verificar que los Correos Funcionen

Ahora que has configurado la contraseña de aplicación, vamos a verificar que todo funcione.

## Opción 1: Prueba Rápida desde Terminal (RECOMENDADO)

```bash
# Abre PowerShell en la carpeta del proyecto
cd C:\xampp\htdocs\Vetsmart

# Ejecuta el test simple
php tests/test_envio_simple.php
```

Sigue estos pasos:
1. Ingresa un email (puede ser el tuyo)
2. Espera a que se envíe
3. Verifica tu bandeja de entrada (o spam)

**Resultado esperado:**
```
✅ ÉXITO: Correo enviado correctamente
📧 Destinatario: tu_email@gmail.com
📅 Fecha de cita: 2025-11-23 14:30
🐾 Mascota: Max (Prueba)
```

---

## Opción 2: Prueba a través de la Aplicación

1. **Inicia sesión** como recepcionista/veterinario
2. **Agenda una nueva cita** para un cliente
3. **Verifica la bandeja** del cliente:
   - Si el cliente tiene email guardado en la BD → debería recibir el correo
   - Si NO tiene email → no recibirá (pero sin error)

---

## Opción 3: Diagnóstico Completo

```bash
cd C:\xampp\htdocs\Vetsmart
php tests/diagnostico_email.php
```

Este script verificará:
- ✓ Archivo .env
- ✓ Configuración SMTP
- ✓ Conexión a Gmail
- ✓ Base de datos
- ✓ Últimos logs

---

## ¿Dónde Ver los Logs?

Si hay problemas, los logs estarán en:

### Windows con XAMPP:
- `C:\xampp\apache\logs\error.log`
- O: `C:\xampp\php\logs\php_errors.log`

### Comandos útiles:
```bash
# Ver últimas 20 líneas
Get-Content C:\xampp\apache\logs\error.log -Tail 20

# Ver solo errores de correo
Get-Content C:\xampp\apache\logs\error.log | Select-String "[EMAIL]|[CITA]"
```

---

## Checklist Final

- [x] Contraseña de aplicación generada en Gmail
- [ ] Contraseña guardada en `.env`
- [ ] Ejecuté `php tests/test_envio_simple.php`
- [ ] Correo de prueba enviado exitosamente
- [ ] Agendé una cita de prueba
- [ ] Cliente recibió el correo de confirmación

---

## ¿Qué Hacer Si No Funciona?

### El correo no se envía:
1. Verifica que MAIL_USERNAME y MAIL_FROM sean iguales
2. Revisa que la contraseña sea la de **aplicación** (16 caracteres sin espacios o con espacios separados)
3. Ejecuta: `php tests/diagnostico_email.php`
4. Busca errores en los logs

### El correo llega a SPAM:
1. Abre el correo
2. Marca como "No es spam"
3. El próximo correo debería ir a bandeja principal

### El cliente no recibe nada:
1. Verifica que el cliente tenga email en la BD:
   ```sql
   SELECT id, nombre, email FROM usuarios WHERE id = 49;
   ```
2. Si el email es NULL, actualiza:
   ```sql
   UPDATE usuarios SET email = 'cliente@example.com' WHERE id = 49;
   ```

---

## Información de Configuración

Tu configuración actual:
- **SMTP Host:** smtp.gmail.com
- **Puerto:** 465 (SSL/TLS)
- **Autenticación:** andres.rojast98@gmail.com
- **Remitente:** andres.rojast98@gmail.com

---

## Próximas Mejoras Opcionales

- [ ] Agregar reminders 24h antes de la cita
- [ ] Enviar confirmación también al veterinario/peluquero
- [ ] Permitir cancelación de cita por email
- [ ] Agregar firma personalizada al correo
- [ ] Internacionalizar el correo (español/inglés)

---

## Soporte

Si aún tienes problemas:
1. Ejecuta el diagnóstico
2. Comparte los errores del log
3. Verifica que:
   - 2FA esté activado en Gmail
   - La contraseña sea la de "Contraseña de Aplicación"
   - El cliente tenga email en la BD

**¡Listo! Deberías poder enviar correos ahora.**
