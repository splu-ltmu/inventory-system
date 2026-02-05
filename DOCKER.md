# Docker Setup Guide for Inventory System

## Prerequisites

- Docker Desktop installed and running
- Docker Compose (included with Docker Desktop)
- Git (for cloning the project)

## Quick Start

### 1. Clone & Setup Environment

```bash
# Clone repository
git clone <your-repo-url>
cd inventory-system

# Copy environment file
cp .env.example .env
```

### 2. Configure .env

Edit `.env` and set Docker-specific values:

```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=inventory_system
DB_USERNAME=inventory_user
DB_PASSWORD=root

REDIS_HOST=redis
REDIS_PORT=6379

APP_PORT=8000
DB_PORT=3306
PHPMYADMIN_PORT=8081
```

### 3. Start Services

**On Linux/Mac:**
```bash
bash docker-setup.sh
```

**On Windows:**
```cmd
docker-setup.bat
```

**Or manually:**
```bash
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

## Access Points

- **Application:** http://localhost:8000
- **phpMyAdmin:** http://localhost:8081
  - Username: `inventory_user` or `root`
  - Password: (from `.env` DB_PASSWORD)

## Common Commands

```bash
# View running containers
docker-compose ps

# View logs
docker-compose logs -f

# Run Artisan commands
docker-compose exec app php artisan <command>

# Access application shell
docker-compose exec app bash

# Run Tinker (REPL)
docker-compose exec app php artisan tinker

# Stop services
docker-compose down

# Stop and remove volumes (CAREFUL - deletes data!)
docker-compose down -v

# Rebuild containers
docker-compose up -d --build
```

## Database Backup & Restore

**Backup:**
```bash
docker-compose exec mysql mysqldump -u root -proot inventory_system > backup.sql
```

**Restore:**
```bash
docker-compose exec -T mysql mysql -u root -proot inventory_system < backup.sql
```

## Troubleshooting

**Port already in use:**
```bash
# Change ports in .env:
APP_PORT=8001
DB_PORT=3307
PHPMYADMIN_PORT=8082
```

**Containers not starting:**
```bash
docker-compose logs -f
```

**Permission issues:**
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap
```

**Database connection errors:**
```bash
# Wait a few seconds for MySQL to initialize, then:
docker-compose exec app php artisan migrate
```

## Production Considerations

- Use strong passwords in `.env`
- Set `APP_DEBUG=false` in production
- Use environment-specific `.env` files
- Enable HTTPS with proper certificates
- Configure proper logging and monitoring
- Restrict database access with firewalls
- Use managed database services when possible

## File Structure

```
inventory-system/
├── Dockerfile              # PHP-FPM image definition
├── docker-compose.yml      # Service orchestration
├── docker-setup.sh        # Linux/Mac setup script
├── docker-setup.bat       # Windows setup script
├── .dockerignore          # Files to exclude from build
└── docker/
    ├── nginx/
    │   └── conf.d/
    │       └── default.conf
    └── mysql/
        └── my.cnf
```
