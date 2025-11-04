// Configuración de Five Server para ejecutar PHP
module.exports = {
  // Ruta al ejecutable de PHP
  php: "/opt/homebrew/bin/php",
  
  // Puerto del servidor (opcional)
  port: 5500,
  
  // Configuración de Live Reload
  injectBody: true,
  
  // Configuración de CORS
  cors: true,
  
  // Habilitar PHP
  phpIni: null, // Usar configuración por defecto de PHP
  
  // Navegador predeterminado
  browser: "default"
};

