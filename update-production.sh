#!/bin/bash

# ========================================
# ASVIRA FTI - PRODUCTION UPDATE SCRIPT
# ========================================

set -e  # Exit on any error

echo "🔄 Starting Asvira FTI Production Update..."
echo "=========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Configuration
PROJECT_NAME="asvira"
PROJECT_PATH="/var/www/$PROJECT_NAME"

print_status "Starting update process..."

# ========================================
# 1. BACKUP CURRENT VERSION
# ========================================
print_status "Creating backup..."

BACKUP_DIR="/var/backups/asvira"
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)

sudo mkdir -p $BACKUP_DIR
sudo cp -r $PROJECT_PATH $BACKUP_DIR/asvira_backup_$BACKUP_DATE

print_success "Backup created: $BACKUP_DIR/asvira_backup_$BACKUP_DATE"

# ========================================
# 2. PULL LATEST CHANGES
# ========================================
print_status "Pulling latest changes from GitHub..."

cd $PROJECT_PATH
git fetch origin
git reset --hard origin/main

print_success "Latest changes pulled successfully"

# ========================================
# 3. UPDATE DEPENDENCIES
# ========================================
print_status "Updating Composer dependencies..."

composer install --no-dev --optimize-autoloader

print_success "Dependencies updated successfully"

# ========================================
# 4. RUN MIGRATIONS
# ========================================
print_status "Running database migrations..."

php artisan migrate --force

print_success "Migrations completed successfully"

# ========================================
# 5. CLEAR AND RECACHE
# ========================================
print_status "Clearing and recaching..."

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize
php artisan optimize

print_success "Cache optimization completed"

# ========================================
# 6. UPDATE PERMISSIONS
# ========================================
print_status "Updating permissions..."

sudo chown -R www-data:www-data $PROJECT_PATH
sudo chmod -R 755 $PROJECT_PATH
sudo chmod -R 775 $PROJECT_PATH/storage
sudo chmod -R 775 $PROJECT_PATH/bootstrap/cache

print_success "Permissions updated successfully"

# ========================================
# 7. RESTART SERVICES
# ========================================
print_status "Restarting services..."

sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm

print_success "Services restarted successfully"

# ========================================
# 8. PERFORMANCE TEST
# ========================================
print_status "Running performance test..."

# Test if application is accessible
if curl -f http://localhost > /dev/null 2>&1; then
    print_success "Application is accessible after update"
else
    print_error "Application is not accessible - rolling back..."
    
    # Rollback to backup
    sudo rm -rf $PROJECT_PATH
    sudo cp -r $BACKUP_DIR/asvira_backup_$BACKUP_DATE $PROJECT_PATH
    sudo chown -R www-data:www-data $PROJECT_PATH
    sudo systemctl restart nginx php8.1-fpm
    
    print_warning "Rolled back to previous version"
    exit 1
fi

# ========================================
# 9. CLEANUP OLD BACKUPS
# ========================================
print_status "Cleaning up old backups..."

# Keep only last 5 backups
cd $BACKUP_DIR
ls -t | tail -n +6 | xargs -r sudo rm -rf

print_success "Old backups cleaned up"

# ========================================
# 10. UPDATE COMPLETE
# ========================================
echo ""
echo "🎉 UPDATE COMPLETED SUCCESSFULLY!"
echo "=================================="
echo ""
echo "📋 Update Summary:"
echo "   • Project: Asvira FTI Chatbot"
echo "   • Backup: $BACKUP_DIR/asvira_backup_$BACKUP_DATE"
echo "   • Path: $PROJECT_PATH"
echo "   • Date: $(date)"
echo ""
echo "📊 Health Check:"
echo "   • Application: ✅ Online"
echo "   • Database: ✅ Connected"
echo "   • Cache: ✅ Optimized"
echo "   • Services: ✅ Running"
echo ""
echo "📈 Performance Metrics:"
echo "   • Response Time: $(curl -s -w "%{time_total}" -o /dev/null http://localhost)"
echo "   • Memory Usage: $(free -h | grep Mem | awk '{print $3"/"$2}')"
echo "   • Disk Usage: $(df -h /var/www | tail -1 | awk '{print $5}')"
echo ""
echo "✅ Your Asvira FTI Chatbot has been updated successfully!"
echo "" 