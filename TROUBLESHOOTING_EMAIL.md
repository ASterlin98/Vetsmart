# 📧 Troubleshooting - Correos No Llegan

## Pasos de Diagnóstico Rápido

### 1. Ejecutar el Diagnóstico Automático

```bash
cd C:\xampp\htdocs\Vetsmart
php tests/diagnostico_email.php
```

Este script verificará:
- ✓ Archivo .env configurado
- ✓ Credenciales SMTP
- ✓ Conexión a servidor SMTP
- ✓ PHPMailer instalado
- ✓ Base de datos
- ✓ Últimos logs

### 2. Verificar Logs

Los logs se guardan en `error_log` de PHP. Busca líneas con:
- `📧 [EMAIL]` - Logs del sistema de correos
- `📝 [CITA]` - Logs del controlador de citas
- `❌` - Errores

**Ubicación típica del log:**
- Windows (XAMPP): `C:\xampp\apache\logs\error.log` o `C:\xampp\php\logs\php_errors.log`

### 3. Problemas Comunes y Soluciones

#### ❌ "MAIL_FROM está configurado como 'tu.email@gmail.com'"

**Problema:** El archivo .env tiene el valor placeholder

**Solución:**
```env
MAIL_FROM=andres.rojast98@gmail.com  # Tu email real de Gmail
```

#### ❌ "No se puede conectar a smtp.gmail.com:465"

**Causas posibles:**
1. Firewall bloqueando la conexión
2. Puerto incorrecto
3. Credenciales incorrectas

**Solución:**
```bash
# Intenta conectar manualmente
telnet smtp.gmail.com 465

# O con PowerShell
Test-NetConnection -ComputerName smtp.gmail.com -Port 465
```

#### ❌ "El cliente no tiene email registrado"

**Problema:** El usuario en la BD no tiene campo `email` completo

**Solución:**
```sql
-- Verificar si el cliente tiene email
SELECT id, nombre, apellido, email FROM usuarios WHERE id = 49;

-- Actualizar email si está vacío
UPDATE usuarios SET email = 'cliente@example.com' WHERE id = 49;
```

#### ❌ "Error: SMTP connect() failed"

**Causas posibles:**
1. Contraseña incorrecta
2. Cuenta de Gmail no está configurada para aplicaciones
3. 2FA no habilitado

**Solución para Gmail:**
1. Habilitar 2FA: https://myaccount.google.com/security
2. Generar contraseña de aplicación: https://myaccount.google.com/apppasswords
3. Usar esa contraseña en .env (16 caracteres con espacios)

#### ❌ "Correo se envía pero va a SPAM"

**Problema:** El correo llega a la carpeta de spam

**Causas:**
- MAIL_FROM no coincide con MAIL_USERNAME
- HTML del correo tiene demasiadas imágenes externas
- Falta SPF/DKIM configuration

**Solución:**
1. Asegurar que `MAIL_FROM` = `MAIL_USERNAME`
2. Instruir al cliente a marcar como "No es spam"
3. Verificar DNS records en el proveedor de dominio

### 4. Información de Base de Datos

```sql
-- Ver clientes con y sin email
SELECT id, nombre, apellido, email, role_id 
FROM usuarios 
WHERE role_id = 6 
ORDER BY id DESC;

-- Ver citas recientes
SELECT c.id, c.fecha, c.cliente_id, c.estado,
       cli.nombre, cli.email
FROM citas c
LEFT JOIN usuarios cli ON c.cliente_id = cli.id
ORDER BY c.id DESC LIMIT 10;

-- Ver clientes sin email
SELECT id, nombre, apellido, email 
FROM usuarios 
WHERE (email IS NULL OR email = '') 
AND role_id = 6;
```

### 5. Test Manual de Correo

```bash
php tests/test_email_system.php
```

Este script:
1. Verifica la configuración SMTP
2. Intenta enviar un correo de prueba
3. Muestra el resultado

### 6. Ver Configuración Actual

```bash
# Ver el contenido del .env
type .env
```

Debe mostrar algo como:
```env
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx  (contraseña de app)
MAIL_PORT=465
MAIL_SECURE=smtps
MAIL_FROM=tu_email@gmail.com
MAIL_FROM_NAME=VetSmart
```

---

## Flujo de Verificación Paso a Paso

```
┌─ ¿Se crea la cita en BD?
│  ├─ NO → Error en formulario, verifica errores PHP
│  └─ SÍ ↓
├─ ¿Tiene email el cliente?
│  ├─ NO → Actualiza usuario con email
│  └─ SÍ ↓
├─ ¿Se intenta enviar correo?
│  ├─ NO → Verifica logs, ErrorHelper.php
│  └─ SÍ ↓
├─ ¿Credenciales SMTP válidas?
│  ├─ NO → Revisa .env y credenciales Gmail
│  └─ SÍ ↓
├─ ¿Se conecta a SMTP?
│  ├─ NO → Verifica firewall/puerto
│  └─ SÍ ↓
├─ ¿Se envía exitosamente?
│  ├─ NO → Revisa logs de SMTP
│  └─ SÍ ↓
└─ ¿Llega a inbox o spam?
   ├─ INBOX → ✅ TODO FUNCIONA
   └─ SPAM → Verifica MAIL_FROM, instrui al usuario
```

---

## Comandos Útiles

```bash
# Limpiar logs
echo "" > C:\xampp\apache\logs\error.log

# Ver últimas líneas del log
Get-Content C:\xampp\apache\logs\error.log -Tail 20

# Filtrar solo errores de email
Get-Content C:\xampp\apache\logs\error.log | Select-String "EMAIL|CITA"

# Probar autenticación SMTP (si tienes telnet)
telnet smtp.gmail.com 465

# Verificar resolución de DNS
nslookup smtp.gmail.com
```

---

## Estado de Logging Actual

Con los cambios realizados, ahora verás logs como:

```
📧 [EMAIL] Iniciando envío de correo a: cliente@example.com
📧 [EMAIL] Config SMTP - Host: smtp.gmail.com, Port: 465, Secure: smtps
📝 [CITA] Buscando cita para cliente_id: 49, fecha: 2025-11-22 15:30:00
📝 [CITA] Cita encontrada - Email cliente: cliente@example.com
📧 [EMAIL] Intentando enviar correo...
✅ [EMAIL] Correo enviado exitosamente a: cliente@example.com
```

O en caso de error:

```
⚠️  [CITA] El cliente no tiene email registrado
❌ [EMAIL] Error al enviar correo: SMTP connect() failed
❌ [EMAIL] Trace: ... stack trace ...
```

---

## Próximas Acciones Recomendadas

1. ✅ Ejecuta el diagnóstico: `php tests/diagnostico_email.php`
2. ✅ Revisa los logs para mensajes de `[EMAIL]` y `[CITA]`
3. ✅ Si hay errores, comparte los logs en el chat
4. ✅ Agenda una cita de prueba y verifica que llegue el email

¿Necesitas ayuda con alguno de estos pasos?
