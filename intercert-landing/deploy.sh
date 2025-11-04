#!/bin/bash

# 🚀 DEPLOYMENT SCRIPT - Landing Cajamarca
# Automated deployment with Docker

set -e  # Exit on error

echo "🚀 InterCert Landing Cajamarca - Deployment Script"
echo "=================================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# ============================================================================
# 1. PRE-DEPLOYMENT CHECKS
# ============================================================================
echo "📋 Step 1: Pre-deployment checks..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker is not installed${NC}"
    echo "Install Docker from: https://docs.docker.com/get-docker/"
    exit 1
fi
echo -e "${GREEN}✅ Docker installed${NC}"

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose is not installed${NC}"
    echo "Install Docker Compose from: https://docs.docker.com/compose/install/"
    exit 1
fi
echo -e "${GREEN}✅ Docker Compose installed${NC}"

# Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}⚠️  .env file not found${NC}"
    echo "Creating .env from template..."
    
    if [ -f env-docker-example.txt ]; then
        cp env-docker-example.txt .env
        echo -e "${YELLOW}📝 Please edit .env file with your credentials${NC}"
        echo "Run: nano .env"
        exit 1
    else
        echo -e "${RED}❌ Template file not found${NC}"
        exit 1
    fi
fi
echo -e "${GREEN}✅ .env file exists${NC}"

echo ""

# ============================================================================
# 2. BUILD DOCKER IMAGE
# ============================================================================
echo "🔨 Step 2: Building Docker image..."
docker-compose build --no-cache
echo -e "${GREEN}✅ Docker image built successfully${NC}"
echo ""

# ============================================================================
# 3. STOP EXISTING CONTAINERS
# ============================================================================
echo "🛑 Step 3: Stopping existing containers..."
docker-compose down
echo -e "${GREEN}✅ Old containers stopped${NC}"
echo ""

# ============================================================================
# 4. START CONTAINERS
# ============================================================================
echo "🚀 Step 4: Starting containers..."
docker-compose up -d
echo -e "${GREEN}✅ Containers started${NC}"
echo ""

# ============================================================================
# 5. HEALTH CHECK
# ============================================================================
echo "🏥 Step 5: Health check..."
sleep 5

if docker-compose ps | grep -q "Up"; then
    echo -e "${GREEN}✅ Containers are running${NC}"
else
    echo -e "${RED}❌ Containers failed to start${NC}"
    echo "Check logs with: docker-compose logs"
    exit 1
fi

# Test HTTP endpoint
if curl -f http://localhost/ > /dev/null 2>&1; then
    echo -e "${GREEN}✅ HTTP endpoint is responding${NC}"
else
    echo -e "${YELLOW}⚠️  HTTP endpoint not responding yet${NC}"
    echo "This is normal, wait a few seconds and check again"
fi

echo ""

# ============================================================================
# 6. DISPLAY STATUS
# ============================================================================
echo "📊 Step 6: Deployment status"
echo "=================================================="
docker-compose ps
echo ""

# ============================================================================
# 7. SHOW LOGS
# ============================================================================
echo "📋 Recent logs:"
echo "=================================================="
docker-compose logs --tail=20
echo ""

# ============================================================================
# 8. NEXT STEPS
# ============================================================================
echo -e "${GREEN}✅ DEPLOYMENT COMPLETED SUCCESSFULLY${NC}"
echo ""
echo "📝 Next steps:"
echo "1. Test the landing page: http://localhost/"
echo "2. Check logs: docker-compose logs -f"
echo "3. Configure SSL/HTTPS for production"
echo "4. Set up monitoring and alerts"
echo ""
echo "🔧 Useful commands:"
echo "  - View logs:     docker-compose logs -f"
echo "  - Restart:       docker-compose restart"
echo "  - Stop:          docker-compose down"
echo "  - Shell access:  docker-compose exec web bash"
echo ""
echo "🎉 Your landing page is now running!"

