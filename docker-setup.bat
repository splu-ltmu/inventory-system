@echo off
REM Docker setup script for Windows

echo Starting Docker setup for Inventory System...

REM Create .env from .env.example if it doesn't exist
if not exist .env (
    echo Creating .env file from .env.example...
    copy .env.example .env
)

REM Build and start containers
echo Building and starting Docker containers...
docker-compose up -d

REM Wait for services
timeout /t 10 /nobreak

REM Run migrations
echo Running database migrations...
docker-compose exec -T app php artisan migrate --force

REM Seed database
echo Seeding database...
docker-compose exec -T app php artisan db:seed

REM Generate app key
echo Generating app key...
docker-compose exec -T app php artisan key:generate

REM Set permissions
echo Setting file permissions...
docker-compose exec -T app chown -R www-data:www-data storage bootstrap

echo.
echo ✅ Setup complete!
echo.
echo Services running at:
echo   - App: http://localhost:8000
echo   - phpMyAdmin: http://localhost:8081
echo.
echo Database credentials:
echo   - Host: mysql
echo   - Database: inventory_system
echo   - User: inventory_user
echo   - Password: (from .env)
echo.
echo Useful commands:
echo   - docker-compose ps          (check status)
echo   - docker-compose logs -f     (view logs)
echo   - docker-compose exec app php artisan tinker  (run tinker)
echo   - docker-compose down        (stop services)
