#!/usr/bin/env bash
# Exit on error
set -o errexit

echo "Starting build process..."

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm install
npm run build

# If DB_CONNECTION is sqlite, ensure the file exists on the persistent disk
if [ "$DB_CONNECTION" = "sqlite" ]; then
    if [ ! -f /var/www/html/storage/database.sqlite ]; then
        touch /var/www/html/storage/database.sqlite
        echo "Created sqlite database at /var/www/html/storage/database.sqlite"
    fi
    # Point the environment variable to the persistent storage
    export DB_DATABASE=/var/www/html/storage/database.sqlite
fi

# Run database migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link

# Clear caches and optimize for production
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build process completed!"
