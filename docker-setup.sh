#!/bin/bash

echo "Starting Docker setup for Inventory System..."

# Create .env from .env.example if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

# Build and start containers
echo "Building and starting Docker containers..."
docker-compose up -d

# Wait for services to be ready
echo "Waiting for services to be ready..."
sleep 10

# Run migrations
echo "Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Seed database (optional)
echo "Seeding database..."
docker-compose exec -T app php artisan db:seed

# Generate app key if not set
echo "Generating app key..."
docker-compose exec -T app php artisan key:generate

# Set permissions
echo "Setting file permissions..."
docker-compose exec -T app chown -R www-data:www-data storage bootstrap

echo "✅ Setup complete!"
echo ""
echo "Services running at:"
echo "  - App: http://localhost:8000"
echo "  - phpMyAdmin: http://localhost:8081"
echo ""
echo "Database credentials:"
echo "  - Host: mysql"
echo "  - Database: inventory_system"
echo "  - User: inventory_user"
echo "  - Password: (from .env)"
echo ""
echo "Useful commands:"
echo "  - docker-compose ps          (check status)"
echo "  - docker-compose logs -f     (view logs)"
echo "  - docker-compose exec app php artisan tinker  (run tinker)"
echo "  - docker-compose down        (stop services)"
