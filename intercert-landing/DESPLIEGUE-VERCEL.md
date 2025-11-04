# 🚀 Guía de Despliegue en Vercel con Formulario Funcionando

## ✨ ¿Por qué Vercel?

- ✅ **100% GRATIS** - Sin costo alguno
- ✅ **Soporta PHP** - Tu formulario funcionará perfectamente
- ✅ **Deploy automático** - Se actualiza solo cuando haces push a GitHub
- ✅ **SSL gratis** - HTTPS automático
- ✅ **CDN global** - Sitio super rápido en todo el mundo
- ✅ **Variables de entorno** - Para tus API keys

---

## 📋 Paso a Paso Completo

### **PARTE 1: Subir a GitHub** (5 minutos)

#### 1. Crear repositorio en GitHub

1. Ve a [github.com](https://github.com) e inicia sesión
2. Clic en **"+"** → **"New repository"**
3. Configura:
   - **Nombre**: `intercert-landing`
   - **Visibilidad**: `Public`
   - ⚠️ **NO marques** ninguna opción adicional
4. Clic en **"Create repository"**

#### 2. Conectar tu código local con GitHub

**⚠️ IMPORTANTE**: Reemplaza `TU-USUARIO-GITHUB` con tu nombre de usuario real.

```bash
cd "/Users/jbala/Desktop/PROYECTOINTERCERT/intercert/Intercert - Certificaciones ISO para Construcción en Cajamarca_files"

# Conectar con GitHub (reemplaza TU-USUARIO-GITHUB)
git remote add origin https://github.com/TU-USUARIO-GITHUB/intercert-landing.git

# Subir el código
git push -u origin main
```

**Ejemplo:** Si tu usuario es `juanperez`:
```bash
git remote add origin https://github.com/juanperez/intercert-landing.git
```

---

### **PARTE 2: Desplegar en Vercel** (5 minutos)

#### 1. Crear cuenta en Vercel

1. Ve a [vercel.com](https://vercel.com)
2. Haz clic en **"Sign Up"**
3. Selecciona **"Continue with GitHub"**
4. Autoriza a Vercel para acceder a tu cuenta de GitHub

#### 2. Importar tu repositorio

1. En el dashboard de Vercel, clic en **"Add New"** → **"Project"**
2. Busca y selecciona `intercert-landing`
3. Clic en **"Import"**

#### 3. Configurar el proyecto

**NO cambies nada** en la configuración inicial. Vercel detectará automáticamente:
- ✅ Framework: `Other`
- ✅ Root Directory: `./`
- ✅ Build Command: (ninguno necesario)

Simplemente haz clic en **"Deploy"**

⏳ Espera 1-2 minutos mientras Vercel despliega tu sitio...

---

### **PARTE 3: Configurar Variables de Entorno** (10 minutos)

⚠️ **IMPORTANTE**: El formulario NO funcionará hasta que configures las API keys.

#### 1. Obtener tus API Keys actuales

Primero necesitas copiar los valores de tus archivos de configuración:

**📧 Resend API** (para emails):
- Ve a [resend.com](https://resend.com/api-keys)
- Copia tu `API Key`

**🔗 HubSpot API** (para CRM):
- Ve a [HubSpot → Settings → Integrations → Private Apps](https://app.hubspot.com/private-apps)
- Copia tu `Access Token`
- Copia tu `Portal ID`

#### 2. Configurar en Vercel

1. En tu proyecto de Vercel, ve a **"Settings"**
2. En el menú izquierdo, clic en **"Environment Variables"**
3. Agrega cada variable:

| Name | Value | Environment |
|------|-------|-------------|
| `RESEND_API_KEY` | `re_tu_api_key_real` | Production |
| `RESEND_FROM_EMAIL` | `onboarding@resend.dev` | Production |
| `RESEND_TO_EMAILS` | `intercertlatam@gmail.com,otro@email.com` | Production |
| `HUBSPOT_ACCESS_TOKEN` | `pat-na1-tu-token-real` | Production |
| `HUBSPOT_PORTAL_ID` | `tu_portal_id` | Production |

Para cada variable:
1. Escribe el **Name** (nombre exacto de la tabla)
2. Pega el **Value** (tu valor real, sin comillas)
3. Selecciona **"Production"**
4. Clic en **"Save"**

#### 3. Actualizar archivos PHP para usar variables de entorno

Los archivos PHP necesitan leer las variables de entorno. Vercel las proporciona automáticamente vía `$_ENV` o `getenv()`.

---

### **PARTE 4: Redesplegar** (1 minuto)

Después de configurar las variables de entorno:

1. Ve a **"Deployments"** en tu proyecto
2. Clic en los **tres puntos (⋮)** del último deployment
3. Selecciona **"Redeploy"**
4. Confirma haciendo clic en **"Redeploy"**

⏳ Espera 1-2 minutos...

🎉 **¡LISTO! Tu sitio está en línea con el formulario funcionando.**

---

## 🌐 Tu Sitio en Vivo

Después del despliegue, tu sitio estará disponible en:

```
https://intercert-landing.vercel.app
```

O un dominio similar que Vercel asigne automáticamente.

### Agregar un dominio personalizado (Opcional)

Si tienes un dominio propio (ej: `www.intercert.com`):

1. En tu proyecto Vercel, ve a **"Settings"** → **"Domains"**
2. Clic en **"Add"**
3. Ingresa tu dominio: `intercert.com`
4. Sigue las instrucciones para configurar los DNS

---

## 🔄 Actualizar el Sitio en el Futuro

Cada vez que hagas cambios en tu código:

```bash
# 1. Guardar cambios
git add .
git commit -m "Descripción de los cambios"

# 2. Subir a GitHub
git push

# 3. ¡Vercel se actualiza automáticamente! 🎉
```

No necesitas hacer nada más. Vercel detecta el push y despliega automáticamente.

---

## ✅ Verificar que Todo Funciona

Después del despliegue, verifica:

1. ✅ **Diseño**: El sitio se ve correctamente
2. ✅ **Imágenes**: Todas las imágenes cargan
3. ✅ **Carrusel**: Las empresas se muestran en carrusel
4. ✅ **Animaciones**: Los números se animan al hacer scroll
5. ✅ **Formulario**: 
   - Rellena el formulario completo
   - Haz clic en "Enviar"
   - Deberías ver el modal de éxito
   - Verifica tu email para la confirmación
   - Revisa HubSpot para ver el contacto y negocio creados

---

## 🆘 Solución de Problemas

### ❌ Formulario no envía / Error 500

**Causa**: Variables de entorno no configuradas o incorrectas.

**Solución**:
1. Ve a **Settings** → **Environment Variables**
2. Verifica que todas las variables estén correctas
3. **Redeploy** el proyecto

### ❌ Emails no llegan

**Causa**: `RESEND_API_KEY` o `RESEND_FROM_EMAIL` incorrectos.

**Solución**:
1. Verifica tu API key en [resend.com/api-keys](https://resend.com/api-keys)
2. Asegúrate de usar un dominio verificado o `onboarding@resend.dev`
3. Actualiza la variable en Vercel
4. Redeploy

### ❌ No se crea el contacto en HubSpot

**Causa**: `HUBSPOT_ACCESS_TOKEN` incorrecto o sin permisos.

**Solución**:
1. Ve a HubSpot → Settings → Integrations → Private Apps
2. Verifica que el token tenga permisos para:
   - `crm.objects.contacts.write`
   - `crm.objects.deals.write`
3. Copia el nuevo token
4. Actualiza en Vercel
5. Redeploy

### ❌ Cambios no se reflejan

**Solución**:
1. Limpia cache del navegador (Ctrl + Shift + R)
2. Espera 2-3 minutos
3. Verifica que el push a GitHub fue exitoso
4. Verifica en Vercel → Deployments que el deployment fue exitoso

---

## 📊 Monitoreo y Logs

Para ver los logs de tu aplicación:

1. Ve a tu proyecto en Vercel
2. Clic en **"Deployments"**
3. Clic en el último deployment
4. Clic en **"Functions"** para ver los logs de los archivos PHP
5. Ahí podrás ver errores y mensajes de debug

---

## 🎯 Comandos Rápidos de Referencia

```bash
# Ver estado del repositorio
git status

# Agregar cambios
git add .

# Hacer commit
git commit -m "Mensaje descriptivo"

# Subir a GitHub (Vercel se actualiza solo)
git push

# Ver remotes configurados
git remote -v
```

---

## 💡 Tips Profesionales

1. **Usa Branch Protection**: En GitHub, protege la rama `main` para evitar pushes accidentales
2. **Habilita Notifications**: En Vercel, activa notificaciones para deployments
3. **Monitorea Analytics**: Vercel ofrece analytics gratuitos en la pestaña "Analytics"
4. **Preview Deployments**: Cada branch que crees tendrá su propio URL de preview

---

## 📞 ¿Necesitas Ayuda?

Si algo no funciona:
1. Revisa los logs en Vercel → Functions
2. Verifica las variables de entorno
3. Prueba el formulario localmente primero con Docker
4. Contacta soporte de Vercel (muy rápidos y útiles)

---

## 🎉 ¡Felicidades!

Tu sitio profesional con formulario funcionando está en línea. Ahora puedes:

- ✅ Recibir contactos de clientes
- ✅ Enviar emails de confirmación
- ✅ Crear contactos y negocios en HubSpot automáticamente
- ✅ Todo gratis con Vercel

**URL de tu sitio**: `https://intercert-landing.vercel.app` (o tu dominio personalizado)

---

¡Éxito con tu proyecto! 🚀

