# 🐳 Configuración Docker - INTERCERT Landing Page

## 📋 Requisitos Previos

- Docker Desktop instalado
- Docker Compose instalado (incluido en Docker Desktop)

## 🚀 Inicio Rápido

### 1. Construir y Levantar el Contenedor

```bash
cd "/Users/jbala/Desktop/PROYECTOINTERCERT/intercert/Intercert - Certificaciones ISO para Construcción en Cajamarca_files"
docker-compose up -d --build
```

### 2. Verificar que el Contenedor Está Corriendo

```bash
docker-compose ps
```

Deberías ver:

```
NAME                  IMAGE                    STATUS         PORTS
intercert-landing     intercert-landing-web    Up X seconds   0.0.0.0:8080->80/tcp
```

### 3. Acceder a la Aplicación

Abre tu navegador en:

**http://localhost:8080**

## 🎯 Flujo del Formulario en Docker

1. ✅ Usuario llena el formulario
2. ✅ JavaScript envía POST a `http://localhost:8080/process-form-simple.php`
3. ✅ PHP procesa en el contenedor Apache
4. ✅ Se crea contacto en HubSpot
5. ✅ Se crea deal en HubSpot
6. ✅ Se envía email de confirmación al cliente
7. ✅ Se envía email de notificación a empleados
8. ✅ Respuesta JSON al navegador

## 📝 Comandos Útiles

### Ver Logs del Contenedor
```bash
docker-compose logs -f web
```

### Ver Logs de PHP
```bash
docker-compose exec web tail -f /var/log/apache2/error.log
```

### Entrar al Contenedor (Terminal)
```bash
docker-compose exec web bash
```

### Detener el Contenedor
```bash
docker-compose down
```

### Reiniciar el Contenedor
```bash
docker-compose restart
```

### Reconstruir Después de Cambios
```bash
docker-compose up -d --build
```

## 🔧 Actualizar Endpoint en JavaScript

Necesitas actualizar el archivo `assets/js/main.js` línea ~595:

**ANTES:**
```javascript
fetch('./process-form-simple.php', {
```

**DESPUÉS:**
```javascript
fetch('http://localhost:8080/process-form-simple.php', {
```

O si quieres que funcione en ambos entornos:

```javascript
const apiUrl = window.location.hostname === 'localhost' 
    ? 'http://localhost:8080/process-form-simple.php'
    : './process-form-simple.php';

fetch(apiUrl, {
```

## 📊 Estructura del Contenedor

```
/var/www/html/                  # Raíz del sitio
├── index.html                  # Página principal
├── process-form-simple.php     # Procesador del formulario
├── email-notifications-resend.php
├── config-hubspot.php
├── config-resend.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
└── .htaccess                   # Configuración Apache
```

## 🔍 Verificar que Funciona

### Test Manual

1. Abre http://localhost:8080
2. Llena el formulario de contacto
3. Envía el formulario
4. Abre la consola del navegador (F12)
5. Deberías ver:

```javascript
Response status: 200
Server response: {
  "success": true,
  "message": "Formulario procesado exitosamente - Contacto creado en HubSpot",
  "hubspot": {
    "contact_id": "164134465962",
    "deal_id": "45976067228"
  },
  "emails": {
    "confirmation_sent": true,
    "notification_sent": true
  }
}
```

### Test desde Terminal

```bash
curl -X POST http://localhost:8080/process-form-simple.php \
  -H "Content-Type: application/json" \
  -d '{
    "nombre_completo": "Test Docker",
    "email": "test@ejemplo.com",
    "pais_prefijo": "+51",
    "telefono": "987654321",
    "cargo": "Gerente",
    "nombre_empresa": "Empresa Test SAC",
    "ruc_empresa": "12345678901",
    "numero_empleados": "50-100 empleados",
    "sector_empresa": "Construcción",
    "tipo_servicio": "Certificación Inicial",
    "certificaciones": ["ISO 9001 - Gestión de la Calidad"],
    "comentarios": "Test desde Docker"
  }'
```

## 🐛 Solución de Problemas

### Error: Puerto 8080 ya está en uso

Cambiar el puerto en `docker-compose.yml`:

```yaml
ports:
  - "8081:80"  # Usar 8081 en lugar de 8080
```

### No se pueden enviar emails

Verifica que las credenciales en `config-resend.php` sean correctas.

### HubSpot no crea contactos

Verifica el token en `config-hubspot.php` y los logs:

```bash
docker-compose logs web | grep HubSpot
```

### Error 500 en el formulario

Ver logs detallados:

```bash
docker-compose exec web tail -100 /var/log/apache2/error.log
```

## 🚀 Despliegue a Producción

Cuando estés listo para producción:

1. **Crear imagen optimizada:**
   ```bash
   docker build -t intercert-landing:production .
   ```

2. **Subir al servidor:**
   - Usar Docker Hub, AWS ECR, o Google Container Registry
   - Configurar variables de entorno para producción
   - Habilitar HTTPS en `.htaccess`

3. **Variables de entorno en producción:**
   - Crear archivo `.env` con credenciales
   - No incluir credenciales en el código

## 📦 Archivos Docker

- `Dockerfile` - Configuración de la imagen
- `docker-compose.yml` - Orquestación de servicios
- `.dockerignore` - Archivos a excluir
- `.htaccess` - Configuración Apache

## ✅ Checklist de Configuración

- [ ] Docker Desktop instalado y corriendo
- [ ] Puerto 8080 disponible
- [ ] Credenciales de HubSpot configuradas
- [ ] Credenciales de Resend configuradas
- [ ] Contenedor construido: `docker-compose build`
- [ ] Contenedor levantado: `docker-compose up -d`
- [ ] Sitio accesible en http://localhost:8080
- [ ] Formulario probado y funcionando
- [ ] Emails de confirmación recibidos
- [ ] Contactos creados en HubSpot

---

**Creado:** 15 de Octubre, 2025  
**Stack:** PHP 8.1 + Apache + Docker  
**Puerto:** 8080 (local)

