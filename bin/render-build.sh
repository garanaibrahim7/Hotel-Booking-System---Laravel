#!/usr/bin/env bash
# exit on error
set -o errexit

echo "Starting Render Build Process..."

# 1. Install PHP dependencies
echo "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# 2. Build React Frontend
echo "Installing NPM dependencies and building React frontend..."
cd Hotel-Booking-System---React
npm ci
npm run build
cd ..

# 3. Setup Laravel Storage
echo "Linking storage..."
php artisan storage:link

# 4. Run Migrations (Force in production)
echo "Running database migrations..."
php artisan migrate --force

# 5. Optimize Laravel
echo "Caching configurations and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Build Completed Successfully!"
