#!/bin/bash

# Asvira FTI Deployment Script
# Script untuk deployment aplikasi Asvira ke production

set -e

echo "🚀 Starting Asvira FTI Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "This doesn't look like a Laravel project. Please run this script from the project root."
    exit 1
fi

print_status "Checking prerequisites..."

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
if [[ $(echo "$PHP_VERSION 8.2" | tr " " "\n" | sort -V | head -n 1) != "8.2" ]]; then
    print_error "PHP 8.2 or higher is required. Current version: $PHP_VERSION"
    exit 1
fi

print_status "PHP version: $PHP_VERSION ✓"

# Check Composer
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed"
    exit 1
fi

print_status "Composer found ✓"

# Check Node.js
if ! command -v node &> /dev/null; then
    print_error "Node.js is not installed"
    exit 1
fi

print_status "Node.js found ✓"

# Check NPM
if ! command -v npm &> /dev/null; then
    print_error "NPM is not installed"
    exit 1
fi

print_status "NPM found ✓"

print_status "Installing/updating dependencies..."

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install NPM dependencies
npm ci --production

print_status "Building assets..."

# Build assets for production
npm run build

print_status "Setting up Laravel..."

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache configuration for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_status "Running database migrations..."

# Run migrations
php artisan migrate --force

print_status "Setting up storage..."

# Create storage links
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

print_status "Optimizing application..."

# Optimize for production
php artisan optimize

print_status "Deployment completed successfully! 🎉"

print_warning "Don't forget to:"
echo "  - Set up your web server (Apache/Nginx)"
echo "  - Configure your environment variables"
echo "  - Set up SSL certificates"
echo "  - Configure your database"
echo "  - Set up queue workers if needed"

print_status "Asvira FTI is ready to serve! 🤖" 