// Script para limpiar cache y forzar recarga
console.log('🧹 Limpiando cache del navegador...');

// Forzar recarga de la página sin cache
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
        for(let registration of registrations) {
            registration.unregister();
            console.log('✅ Service Worker desregistrado');
        }
    });
}

// Limpiar localStorage y sessionStorage
localStorage.clear();
sessionStorage.clear();
console.log('✅ Storage limpiado');

// Forzar recarga de la página
if (window.location.search.indexOf('reload=') === -1) {
    window.location.href = window.location.href + '?reload=' + new Date().getTime();
}

console.log('🔄 Recargando página...');
