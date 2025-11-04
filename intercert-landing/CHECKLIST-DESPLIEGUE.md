# ✅ Checklist Rápido de Despliegue

## 🎯 OBJETIVO: Subir a Vercel con Formulario Funcionando

---

## PASO 1: GitHub (5 min) ⏱️

### ☐ 1.1 Crear repositorio
- [ ] Ir a https://github.com/new
- [ ] Nombre: `intercert-landing`
- [ ] Visibilidad: `Public`
- [ ] **NO marcar** ninguna opción adicional
- [ ] Clic en "Create repository"

### ☐ 1.2 Conectar tu código
Ejecuta estos comandos (reemplaza `TU-USUARIO-GITHUB`):

```bash
cd "/Users/jbala/Desktop/PROYECTOINTERCERT/intercert/Intercert - Certificaciones ISO para Construcción en Cajamarca_files"

git remote add origin https://github.com/TU-USUARIO-GITHUB/intercert-landing.git

git push -u origin main
```

**✅ VERIFICAR**: Ve a GitHub y verifica que veas tus archivos

---

## PASO 2: Vercel (5 min) ⏱️

### ☐ 2.1 Crear cuenta
- [ ] Ir a https://vercel.com/signup
- [ ] Clic en "Continue with GitHub"
- [ ] Autorizar a Vercel

### ☐ 2.2 Importar proyecto
- [ ] Clic en "Add New" → "Project"
- [ ] Buscar `intercert-landing`
- [ ] Clic en "Import"
- [ ] **NO cambiar nada**
- [ ] Clic en "Deploy"
- [ ] ⏳ Esperar 1-2 minutos

**✅ VERIFICAR**: Tu sitio estará en `https://intercert-landing.vercel.app`

---

## PASO 3: Variables de Entorno (10 min) ⏱️

### ☐ 3.1 Obtener API Keys

**Resend** (para emails):
- [ ] Ir a https://resend.com/api-keys
- [ ] Copiar tu API Key

**HubSpot** (para CRM):
- [ ] Ir a HubSpot → Settings → Integrations → Private Apps
- [ ] Copiar Access Token
- [ ] Copiar Portal ID

### ☐ 3.2 Configurar en Vercel

En tu proyecto Vercel:
- [ ] Ir a "Settings" → "Environment Variables"
- [ ] Agregar cada variable:

| Variable | Dónde obtenerla |
|----------|-----------------|
| `RESEND_API_KEY` | https://resend.com/api-keys |
| `RESEND_FROM_EMAIL` | `onboarding@resend.dev` |
| `RESEND_TO_EMAILS` | `intercertlatam@gmail.com` |
| `HUBSPOT_ACCESS_TOKEN` | HubSpot → Private Apps |
| `HUBSPOT_PORTAL_ID` | HubSpot → Settings |

Para cada una:
- [ ] Poner el nombre exacto
- [ ] Pegar el valor (sin comillas)
- [ ] Seleccionar "Production"
- [ ] Clic en "Save"

### ☐ 3.3 Redesplegar
- [ ] Ir a "Deployments"
- [ ] Clic en ⋮ del último deployment
- [ ] Clic en "Redeploy"
- [ ] Confirmar
- [ ] ⏳ Esperar 1-2 minutos

---

## PASO 4: Verificar que Todo Funciona ✅

### ☐ 4.1 Probar el sitio
- [ ] Abrir `https://intercert-landing.vercel.app`
- [ ] El diseño se ve bien
- [ ] Las imágenes cargan
- [ ] El carrusel funciona
- [ ] Las animaciones de números funcionan

### ☐ 4.2 Probar el formulario
- [ ] Abrir el formulario
- [ ] Rellenar todos los campos
- [ ] Enviar
- [ ] Ver modal de éxito ✅
- [ ] Revisar email (debería llegar confirmación)
- [ ] Revisar HubSpot (debería aparecer el contacto)

---

## 🎉 ¡COMPLETADO!

Si todos los checks están ✅, tu sitio está funcionando al 100%.

**URL de tu sitio**: `https://intercert-landing.vercel.app`

---

## 🔄 Para Actualizaciones Futuras

Cada vez que hagas cambios:

```bash
git add .
git commit -m "Descripción del cambio"
git push
```

Vercel se actualiza automáticamente. ¡Así de fácil! 🚀

---

## 🆘 Si Algo No Funciona

1. **Formulario no envía**: Verifica las variables de entorno en Vercel
2. **Emails no llegan**: Verifica `RESEND_API_KEY`
3. **No se crea en HubSpot**: Verifica `HUBSPOT_ACCESS_TOKEN`
4. **Cambios no se ven**: Limpia cache (Ctrl + Shift + R)

**Ver logs**: Vercel → Deployments → Functions

---

📖 **Guía detallada completa**: Ver `DESPLIEGUE-VERCEL.md`

