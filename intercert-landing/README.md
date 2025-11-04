# Intercert - Certificaciones ISO para Construcción en Cajamarca

## Descripción
Sitio web profesional para Intercert, empresa especializada en certificaciones ISO para el sector construcción en Cajamarca, Perú. El sitio presenta los servicios de certificación ISO 9001, ISO 14001, ISO 45001 e ISO 37001.

## Características

### ✅ Correcciones Implementadas
- **Estructura de archivos organizada**: Archivos movidos a carpetas `assets/` apropiadas
- **HTML limpio y semántico**: Código HTML bien estructurado y legible
- **CSS optimizado**: Estilos organizados en archivos separados
- **JavaScript funcional**: Interactividad completa con validación de formularios
- **Diseño responsivo**: Compatible con dispositivos móviles y desktop
- **Accesibilidad mejorada**: Navegación por teclado y lectores de pantalla
- **SEO optimizado**: Meta tags y estructura semántica
- **Rendimiento optimizado**: Carga rápida y eficiente
- **Integración con HubSpot CRM**: Gestión automática de leads y negocios
- **Notificaciones por email**: Sistema automatizado con Resend API
- **Backup en Google Sheets**: Respaldo automático de formularios

### 🎨 Características del Diseño
- Diseño moderno y profesional
- Colores corporativos (azul, amarillo, blanco)
- Tipografía legible (Inter + Pacifico)
- Animaciones suaves y transiciones
- Iconos de Remix Icon
- Gradientes y efectos visuales atractivos

### 📱 Funcionalidades
- **Navegación suave**: Scroll suave entre secciones
- **Menú móvil**: Navegación optimizada para dispositivos móviles
- **Formulario de contacto**: Modal con validación en tiempo real
- **Integración WhatsApp**: Enlaces directos para contacto
- **Botón flotante**: Chat y scroll to top
- **Lazy loading**: Carga optimizada de imágenes
- **Notificaciones**: Sistema de mensajes para el usuario
- **PWA**: Funciona como aplicación web progresiva

### 🔒 Seguridad
- Rate limiting para prevenir spam
- Validación y sanitización de datos
- Headers de seguridad (CSP, XSS Protection, etc.)
- CORS configurado para dominios autorizados
- Encriptación de datos sensibles

## Estructura del Proyecto

```
/
├── index.html                       # Página principal
├── assets/
│   ├── css/
│   │   ├── tailwind.css            # Framework CSS
│   │   ├── style.css               # Estilos personalizados
│   │   └── fonts.css               # Fuentes de Google
│   ├── js/
│   │   └── main.js                 # JavaScript principal
│   └── images/
│       ├── iso-*.jpg               # Imágenes de certificaciones
│       ├── logo-*.webp/png         # Logos de Intercert
│       ├── background.jpg          # Imagen de fondo
│       ├── testimonial-*.jpg       # Fotos de testimonios
│       └── video caj.mp4           # Video promocional
├── process-form-hubspot.php        # Procesador de formularios (HubSpot + Sheets + Email)
├── process-form-secure.php         # Procesador seguro alternativo
├── email-notifications-resend.php  # Sistema de notificaciones por email
├── config-hubspot.php              # Configuración de HubSpot API
├── config-google-sheets.php        # Configuración de Google Sheets API
├── config-resend.php               # Configuración de Resend Email API
├── sw.js                           # Service Worker (PWA)
├── clear-cache.js                  # Script de limpieza de caché
├── deploy.sh                       # Script de despliegue
├── Dockerfile                      # Configuración Docker
├── docker-compose.yml              # Docker Compose
├── Makefile                        # Comandos de automatización
└── README.md                       # Este archivo
```

## Tecnologías Utilizadas

### Frontend
- **HTML5**: Estructura semántica
- **CSS3**: Estilos y animaciones
- **Tailwind CSS**: Framework de utilidades
- **JavaScript ES6+**: Interactividad
- **Remix Icon**: Iconografía
- **Google Fonts**: Tipografía

### Backend
- **PHP 7.4+**: Procesamiento de formularios
- **cURL**: Comunicación con APIs externas

### Integraciones
- **HubSpot CRM**: Gestión de contactos y negocios
- **Google Sheets API**: Backup de datos
- **Resend API**: Envío de notificaciones por email

### DevOps
- **Docker**: Contenedorización
- **Docker Compose**: Orquestación de servicios
- **Makefile**: Automatización de tareas

## Instalación y Uso

### Opción 1: Servidor Local Simple
1. **Clonar o descargar** el proyecto
2. **Abrir** `index.html` en un navegador web
3. **Opcional**: Servir desde un servidor local para mejor rendimiento

```bash
# Con Python
python -m http.server 8000

# Con Node.js (http-server)
npx http-server

# Con PHP
php -S localhost:8000
```

### Opción 2: Docker (Recomendado para Producción)
```bash
# Construir e iniciar
docker-compose up -d

# Usar Makefile
make up
make logs
make down
```

## Configuración

### Variables de Entorno
Configurar las credenciales en los archivos de configuración:

1. **`config-hubspot.php`**: Token de acceso de HubSpot
2. **`config-google-sheets.php`**: Credenciales de Service Account
3. **`config-resend.php`**: API Key de Resend

### Contacto
- **Teléfono**: +51 986 123 418
- **Email**: info@intercertlatam.com
- **WhatsApp**: Configurado para contacto directo

### Personalización
- Colores en `assets/css/style.css`
- Contenido en `index.html`
- Funcionalidades en `assets/js/main.js`
- Procesamiento de formularios en `process-form-hubspot.php`

## Optimizaciones Implementadas

### SEO
- Meta tags optimizados
- Estructura semántica HTML5
- URLs amigables (anclas)
- Contenido relevante y keywords

### Rendimiento
- CSS y JS minificados
- Imágenes optimizadas
- Lazy loading
- Carga asíncrona de recursos

### Accesibilidad
- Contraste adecuado
- Navegación por teclado
- Etiquetas ARIA
- Texto alternativo en imágenes

### Responsive Design
- Mobile-first approach
- Breakpoints optimizados
- Menú móvil funcional
- Imágenes adaptables

## Navegadores Compatibles

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Dispositivos móviles iOS/Android

## Flujo de Trabajo del Formulario

1. **Usuario completa el formulario** en la página web
2. **Validación en el frontend** (JavaScript)
3. **Envío al backend** (`process-form-hubspot.php`)
4. **Procesamiento paralelo**:
   - Crear/actualizar contacto en HubSpot CRM
   - Crear negocio (deal) asociado al contacto
   - Guardar en Google Sheets como backup
   - Enviar notificación por email a ejecutivos
5. **Respuesta al usuario** con confirmación

## Deployment

### Producción
```bash
# Usando el script de deploy
./deploy.sh

# O con Docker Compose
docker-compose -f docker-compose.yml up -d
```

### Comandos Útiles
```bash
# Ver logs
make logs

# Reiniciar servicios
make restart

# Limpiar caché
node clear-cache.js
```

## Mantenimiento

### Actualizar Contenido
1. Editar `index.html` para cambiar textos
2. Reemplazar imágenes en `assets/images/`
3. Modificar estilos en `assets/css/style.css`

### Agregar Funcionalidades
1. Editar `assets/js/main.js`
2. Agregar nuevos estilos si es necesario
3. Probar en diferentes dispositivos

### Actualizar Integraciones
1. **HubSpot**: Actualizar token en `config-hubspot.php`
2. **Google Sheets**: Renovar credenciales de Service Account
3. **Resend**: Actualizar API Key en `config-resend.php`

## Soporte

Para soporte técnico o modificaciones:
- Revisar la documentación del código
- Verificar la consola del navegador para errores
- Probar en diferentes navegadores
- Revisar logs del servidor para errores de backend

## Licencia

© 2024 Intercert Cajamarca. Todos los derechos reservados.

---

**Nota**: Este sitio web ha sido completamente reestructurado y optimizado para ofrecer la mejor experiencia de usuario y rendimiento.
