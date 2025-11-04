# 🚀 Guía Completa: Subir a GitHub y Activar GitHub Pages

## ✅ Paso 1: Crear Repositorio en GitHub

1. Ve a [GitHub](https://github.com) e inicia sesión
2. Haz clic en el botón **"+"** en la esquina superior derecha
3. Selecciona **"New repository"**
4. Completa los datos:
   - **Repository name**: `intercert-landing` (o el nombre que prefieras)
   - **Description**: `Landing page profesional para INTERCERT - Certificaciones ISO`
   - **Visibility**: Elige `Public` (para GitHub Pages gratuito)
   - ⚠️ **NO marques** "Add a README file" ni ninguna otra opción
5. Haz clic en **"Create repository"**

---

## ✅ Paso 2: Conectar y Subir tu Código

### Opción A: Usando la Terminal (Recomendado)

Ejecuta estos comandos en tu terminal (ya estás en el directorio correcto):

```bash
# Agregar el repositorio remoto (REEMPLAZA "tu-usuario" con tu nombre de usuario de GitHub)
git remote add origin https://github.com/tu-usuario/intercert-landing.git

# Cambiar el nombre de la rama a "main" (si es necesario)
git branch -M main

# Subir el código a GitHub
git push -u origin main
```

### Opción B: Desde la Interfaz de GitHub Desktop

Si prefieres una interfaz gráfica:
1. Descarga [GitHub Desktop](https://desktop.github.com/)
2. Abre la aplicación y selecciona **"Add Existing Repository"**
3. Selecciona la carpeta de tu proyecto
4. Haz clic en **"Publish repository"**

---

## ✅ Paso 3: Activar GitHub Pages

1. Ve a tu repositorio en GitHub
2. Haz clic en **"Settings"** (Configuración)
3. En el menú lateral izquierdo, haz clic en **"Pages"**
4. En la sección **"Source"** (Fuente):
   - **Branch**: Selecciona `main`
   - **Folder**: Selecciona `/root`
5. Haz clic en **"Save"**
6. ⏳ Espera 1-2 minutos mientras GitHub despliega tu sitio
7. 🎉 Verás un mensaje verde con tu URL: `https://tu-usuario.github.io/intercert-landing/`

---

## ⚙️ Paso 4: Configurar los Archivos PHP (IMPORTANTE)

⚠️ **NOTA**: GitHub Pages solo soporta archivos estáticos (HTML, CSS, JS). Los archivos PHP **NO funcionarán** en GitHub Pages.

### Opciones para que funcione el formulario:

### **Opción 1: Usar un Backend Externo (Recomendado)**

Necesitas alojar los archivos PHP en un servidor separado que soporte PHP:

1. **Hosting PHP**: Contrata un hosting con soporte PHP (ej: Hostinger, Namecheap, DigitalOcean)
2. **Sube solo los PHP**: Sube estos archivos a tu hosting:
   - `process-form-simple.php`
   - `email-notifications-resend.php`
   - `config-resend.php`
   - `config-hubspot.php`
3. **Actualiza la URL**: En `assets/js/main.js`, cambia la URL del API:
   ```javascript
   // Cambiar de:
   const apiUrl = '/process-form-simple.php';
   
   // A:
   const apiUrl = 'https://tu-dominio.com/api/process-form-simple.php';
   ```

### **Opción 2: Usar un Servicio Serverless (Alternativa Moderna)**

Puedes migrar el procesamiento del formulario a servicios como:
- **Vercel** (soporta PHP)
- **Netlify Functions**
- **AWS Lambda**
- **Cloudflare Workers**

### **Opción 3: Solo GitHub Pages (Sin Formulario Funcional)**

Si solo quieres mostrar el diseño sin funcionalidad del formulario:
- GitHub Pages mostrará perfectamente el diseño
- El formulario no enviará datos (solo será visual)
- Útil para portfolio o demo

---

## 🔧 Paso 5: Actualizar URLs y Enlaces

Una vez que tu sitio esté en línea, actualiza estos elementos en `index.html`:

1. **Meta Tags** (líneas 6-20):
```html
<meta property="og:url" content="https://tu-usuario.github.io/intercert-landing/">
<link rel="canonical" href="https://tu-usuario.github.io/intercert-landing/">
```

2. **Links Absolutos**: Asegúrate de que todos los recursos usen rutas relativas (ya lo están).

---

## 📊 Paso 6: Verificar que Todo Funciona

Después de activar GitHub Pages:

1. ✅ Visita tu URL: `https://tu-usuario.github.io/intercert-landing/`
2. ✅ Verifica que el diseño se vea correctamente
3. ✅ Verifica que las imágenes carguen
4. ✅ Verifica que el carrusel funcione
5. ✅ Verifica las animaciones de conteo
6. ⚠️ El formulario NO funcionará hasta que configures un backend PHP

---

## 🔄 Paso 7: Actualizar el Sitio en el Futuro

Cada vez que hagas cambios:

```bash
# 1. Agregar archivos modificados
git add .

# 2. Hacer commit con un mensaje descriptivo
git commit -m "Descripción de los cambios"

# 3. Subir a GitHub
git push

# GitHub Pages se actualizará automáticamente en 1-2 minutos
```

---

## 🆘 Solución de Problemas Comunes

### ❌ "Error 404 - File not found"
- Verifica que hayas seleccionado la rama correcta en Settings > Pages
- Espera 2-3 minutos para que se procesen los cambios

### ❌ "CSS no carga / Sitio sin estilos"
- Verifica que las rutas sean relativas (sin `/` al inicio)
- Ejemplo correcto: `assets/css/style.css`
- Ejemplo incorrecto: `/assets/css/style.css`

### ❌ "Formulario no funciona"
- Es normal, GitHub Pages no soporta PHP
- Necesitas un backend separado (ver Paso 4)

### ❌ "Cambios no se reflejan"
- Limpia el cache del navegador (Ctrl + Shift + R)
- Espera 2-3 minutos para que GitHub Pages se actualice
- Verifica que hayas hecho `git push` correctamente

---

## 📞 URLs y Recursos Útiles

- **Tu repositorio**: `https://github.com/tu-usuario/intercert-landing`
- **Tu sitio en vivo**: `https://tu-usuario.github.io/intercert-landing/`
- **Documentación GitHub Pages**: https://pages.github.com/
- **Alternativas de hosting PHP**: 
  - [Hostinger](https://www.hostinger.com/)
  - [InfinityFree](https://www.infinityfree.net/) (Gratuito con PHP)
  - [000webhost](https://www.000webhost.com/) (Gratuito con PHP)

---

## ✨ Recomendación Final

Para un sitio profesional completamente funcional:

1. **GitHub Pages**: Para el frontend (HTML, CSS, JS) ✅ GRATIS
2. **Hosting PHP**: Para el backend (formularios, emails) 💰 ~$3-5/mes
3. **Dominio personalizado**: `www.intercert.com` 💰 ~$10-15/año

Esto te dará un sitio profesional, rápido y completamente funcional.

---

**¿Necesitas ayuda?** Avísame si tienes algún problema con alguno de estos pasos. 🚀

