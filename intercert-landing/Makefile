# 🐳 Makefile - Landing Cajamarca InterCert
# Simplified Docker commands

.PHONY: help build up down restart logs shell test clean backup

# Default target
.DEFAULT_GOAL := help

# Colors
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[1;33m
NC := \033[0m # No Color

## help: Show this help message
help:
	@echo "$(BLUE)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"
	@echo "$(GREEN)  InterCert Landing Cajamarca - Docker Commands$(NC)"
	@echo "$(BLUE)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"
	@echo ""
	@echo "$(YELLOW)Available commands:$(NC)"
	@echo ""
	@awk '/^##/ {desc=substr($$0, 4); getline; printf "  $(GREEN)%-15s$(NC) %s\n", $$1, desc}' $(MAKEFILE_LIST)
	@echo ""
	@echo "$(BLUE)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"

## init: Initialize project (first time setup)
init:
	@echo "$(GREEN)🔧 Initializing project...$(NC)"
	@if [ ! -f .env ]; then \
		cp env-docker-example.txt .env; \
		echo "$(YELLOW)⚠️  Please edit .env with your credentials$(NC)"; \
		echo "Run: nano .env"; \
	else \
		echo "$(GREEN)✅ .env already exists$(NC)"; \
	fi

## build: Build Docker images
build:
	@echo "$(GREEN)🔨 Building Docker images...$(NC)"
	docker-compose build --no-cache

## up: Start containers in background
up:
	@echo "$(GREEN)🚀 Starting containers...$(NC)"
	docker-compose up -d
	@echo "$(GREEN)✅ Containers started$(NC)"
	@echo "Access at: http://localhost"

## down: Stop and remove containers
down:
	@echo "$(YELLOW)🛑 Stopping containers...$(NC)"
	docker-compose down
	@echo "$(GREEN)✅ Containers stopped$(NC)"

## restart: Restart containers
restart:
	@echo "$(YELLOW)🔄 Restarting containers...$(NC)"
	docker-compose restart
	@echo "$(GREEN)✅ Containers restarted$(NC)"

## logs: Show container logs (follow mode)
logs:
	@echo "$(BLUE)📋 Showing logs...$(NC)"
	docker-compose logs -f

## logs-web: Show only web container logs
logs-web:
	@echo "$(BLUE)📋 Showing web logs...$(NC)"
	docker-compose logs -f web

## shell: Access container shell
shell:
	@echo "$(BLUE)🐚 Accessing container shell...$(NC)"
	docker-compose exec web bash

## status: Show container status
status:
	@echo "$(BLUE)📊 Container status:$(NC)"
	@echo ""
	docker-compose ps
	@echo ""
	@echo "$(BLUE)📈 Resource usage:$(NC)"
	@echo ""
	docker stats intercert-landing-cajamarca --no-stream

## test: Test the landing page
test:
	@echo "$(BLUE)🧪 Testing landing page...$(NC)"
	@curl -f http://localhost/ > /dev/null 2>&1 && \
		echo "$(GREEN)✅ Landing page is responding$(NC)" || \
		echo "$(YELLOW)⚠️  Landing page not responding$(NC)"

## deploy: Full deployment (build + up)
deploy: build up
	@echo "$(GREEN)✅ Deployment completed$(NC)"
	@sleep 3
	@$(MAKE) test

## update: Update and redeploy
update:
	@echo "$(GREEN)🔄 Pulling latest changes...$(NC)"
	git pull
	@echo "$(GREEN)🔨 Rebuilding...$(NC)"
	docker-compose up -d --build
	@echo "$(GREEN)✅ Update completed$(NC)"

## backup: Backup configuration and logs
backup:
	@echo "$(GREEN)💾 Creating backup...$(NC)"
	@mkdir -p backups
	@tar czf backups/config-backup-$$(date +%Y%m%d-%H%M%S).tar.gz .env docker-compose.yml
	@docker run --rm -v intercert_logs:/data -v $$(pwd)/backups:/backup \
		alpine tar czf /backup/logs-backup-$$(date +%Y%m%d-%H%M%S).tar.gz /data
	@echo "$(GREEN)✅ Backup created in backups/$(NC)"
	@ls -lh backups/ | tail -2

## clean: Clean unused Docker resources
clean:
	@echo "$(YELLOW)🧹 Cleaning Docker resources...$(NC)"
	docker system prune -f
	@echo "$(GREEN)✅ Cleanup completed$(NC)"

## clean-all: Clean everything (including volumes)
clean-all:
	@echo "$(YELLOW)⚠️  This will remove all containers, images, and volumes!$(NC)"
	@read -p "Are you sure? [y/N] " -n 1 -r; \
	echo; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		docker-compose down -v; \
		docker system prune -a -f --volumes; \
		echo "$(GREEN)✅ Everything cleaned$(NC)"; \
	else \
		echo "$(YELLOW)Cancelled$(NC)"; \
	fi

## install-deps: Install deployment dependencies
install-deps:
	@echo "$(GREEN)📦 Checking dependencies...$(NC)"
	@command -v docker >/dev/null 2>&1 || { echo "$(YELLOW)⚠️  Docker not installed$(NC)"; exit 1; }
	@command -v docker-compose >/dev/null 2>&1 || { echo "$(YELLOW)⚠️  Docker Compose not installed$(NC)"; exit 1; }
	@echo "$(GREEN)✅ All dependencies installed$(NC)"

## ssl-cert: Generate self-signed SSL certificate (for testing)
ssl-cert:
	@echo "$(GREEN)🔒 Generating self-signed SSL certificate...$(NC)"
	@mkdir -p ssl
	openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
		-keyout ssl/server.key \
		-out ssl/server.crt \
		-subj "/C=PE/ST=Cajamarca/L=Cajamarca/O=InterCert/CN=localhost"
	@echo "$(GREEN)✅ Certificate generated in ssl/$(NC)"

## health: Check container health
health:
	@echo "$(BLUE)🏥 Health check:$(NC)"
	@docker inspect --format='{{.State.Health.Status}}' intercert-landing-cajamarca || \
		echo "$(YELLOW)No health check configured$(NC)"

## prod: Production deployment with SSL
prod: init build
	@echo "$(GREEN)🚀 Production deployment...$(NC)"
	@echo "$(YELLOW)⚠️  Ensure .env has production values!$(NC)"
	@read -p "Continue? [y/N] " -n 1 -r; \
	echo; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d; \
		$(MAKE) test; \
		echo "$(GREEN)✅ Production deployment completed$(NC)"; \
	else \
		echo "$(YELLOW)Cancelled$(NC)"; \
	fi

