#!/bin/bash

# Script para iniciar INTERCERT en Docker
echo "🐳 Iniciando INTERCERT Landing Page en Docker..."

# Verificar si Docker está corriendo
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker no está corriendo."
    echo "Por favor, inicia Docker Desktop y ejecuta este script nuevamente."
    echo ""
    echo "Pasos:"
    echo "1. Abre Docker Desktop"
    echo "2. Espera a que inicie completamente"
    echo "3. Ejecuta: ./start-docker.sh"
    exit 1
fi

echo "✅ Docker está corriendo"

# Detener contenedores previos si existen
echo "🧹 Limpiando contenedores previos..."
docker-compose down 2>/dev/null

# Construir y levantar el contenedor
echo "🔨 Construyendo imagen Docker..."
docker-compose build

if [ $? -eq 0 ]; then
    echo "✅ Imagen construida exitosamente"
    
    echo "🚀 Levantando contenedor..."
    docker-compose up -d
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "✅ ¡INTERCERT está corriendo!"
        echo ""
        echo "📍 Accede a: http://localhost:8080"
        echo ""
        echo "📝 Comandos útiles:"
        echo "   - Ver logs:     docker-compose logs -f"
        echo "   - Detener:      docker-compose down"
        echo "   - Reiniciar:    docker-compose restart"
        echo ""
        
        # Esperar 3 segundos y abrir el navegador
        sleep 3
        
        if command -v open > /dev/null; then
            echo "🌐 Abriendo navegador..."
            open http://localhost:8080
        fi
    else
        echo "❌ Error al levantar el contenedor"
        exit 1
    fi
else
    echo "❌ Error al construir la imagen"
    exit 1
fi

