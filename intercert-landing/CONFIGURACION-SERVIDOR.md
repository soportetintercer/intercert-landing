# 🚀 Configuración del Servidor de Desarrollo

## ✅ Solución al Error 500

El error se debía a que **Five Server** no tenía configurado el ejecutable de PHP.

### 📝 Configuración Implementada

He creado el archivo `fiveserver.config.js` con la configuración correcta:

```javascript
module.exports = {
  php: "/opt/homebrew/bin/php",  // Ruta a PHP en tu sistema
  port: 5500,
  cors: true
};
```

### 🔄 Pasos para Aplicar la Configuración

1. **Detén Five Server** (si está corriendo)
   - Cierra el servidor actual
   - Detén el proceso en VS Code

2. **Reinicia Five Server**
   - En VS Code, abre el Command Palette (Cmd+Shift+P)
   - Busca "Five Server: Start Server"
   - O haz clic en "Go Live" en la barra de estado

3. **Verifica que PHP funciona**
   - Abre el navegador en `http://localhost:5500`
   - Prueba el formulario de contacto
   - Deberías ver las respuestas JSON correctamente

### 🎯 Flujo Actual del Formulario

Cuando un usuario envía el formulario:

1. ✅ **JavaScript** captura los datos (`main.js`)
2. ✅ **POST** a `process-form-simple.php`
3. ✅ **HubSpot** crea contacto y deal
4. ✅ **Email de confirmación** al cliente
5. ✅ **Email de notificación** a empleados
6. ✅ **Respuesta JSON** al navegador

### 📧 Emails Configurados

**Cliente recibe:**
- Email de confirmación con colores corporativos
- Información de contacto (Cajamarca y Sede Principal)
- WhatsApp links

**Empleados reciben:**
- Email de notificación con todos los datos del lead
- Información completa del formulario
- Enviado a: setter@, gerentecomercial@, karem.intercert@

### 🔍 Verificar si Funciona

Después de reiniciar Five Server, abre la consola del navegador y deberías ver:

```
Response status: 200
Server response: {
  "success": true,
  "message": "Formulario procesado exitosamente - Contacto creado en HubSpot",
  "hubspot": {
    "contact_id": "...",
    "deal_id": "..."
  },
  "emails": {
    "confirmation_sent": true,
    "notification_sent": true
  }
}
```

### 🆘 Si Aún No Funciona

Si después de reiniciar Five Server sigue fallando:

1. **Verifica la ruta de PHP:**
   ```bash
   which php
   ```
   
2. **Actualiza `fiveserver.config.js`** con la ruta correcta

3. **Alternativa: Usar PHP Built-in Server:**
   ```bash
   cd /path/to/project
   php -S localhost:8000
   ```
   
   Y actualiza el fetch en `main.js`:
   ```javascript
   fetch('http://localhost:8000/process-form-simple.php', {
   ```

### 📊 Archivos Importantes

- `process-form-simple.php` - Procesador principal del formulario
- `email-notifications-resend.php` - Funciones de email
- `config-hubspot.php` - Configuración de HubSpot
- `config-resend.php` - Configuración de Resend (emails)
- `fiveserver.config.js` - Configuración del servidor (NUEVO)

### ⚡ Producción

Para producción, asegúrate de que tu servidor web (Apache/Nginx) tenga:
- PHP 7.4 o superior
- Extension cURL habilitada
- Extension JSON habilitada
- Permisos de escritura para logs

---

**Creado:** 15 de Octubre, 2025  
**Sistema:** macOS con Homebrew PHP

