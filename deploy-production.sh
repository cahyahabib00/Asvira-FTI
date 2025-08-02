#!/bin/bash

# ========================================
# ASVIRA FTI - PRODUCTION DEPLOYMENT SCRIPT
# ========================================

set -e  # Exit on any error

echo "🚀 Starting Asvira FTI Production Deployment..."
echo "=============================================="

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

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root"
   exit 1
fi

# Configuration
PROJECT_NAME="asvira"
PROJECT_PATH="/var/www/$PROJECT_NAME"
DOMAIN="your-domain.com"  # Change this to your domain
DB_NAME="asvira_prod"
DB_USER="asvira_user"
DB_PASS="Asvira2024!@#"  # Change this to strong password

print_status "Starting deployment process..."

# ========================================
# 1. SYSTEM UPDATE & DEPENDENCIES
# ========================================
print_status "Updating system and installing dependencies..."

sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-redis composer git redis-server htop iotop nethogs jpegoptim optipng ufw certbot python3-certbot-nginx

print_success "System packages installed successfully"

# ========================================
# 2. FIREWALL CONFIGURATION
# ========================================
print_status "Configuring firewall..."

sudo ufw --force enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny 3306/tcp

print_success "Firewall configured successfully"

# ========================================
# 3. MYSQL CONFIGURATION
# ========================================
print_status "Configuring MySQL..."

# Create MySQL configuration
sudo tee /etc/mysql/mysql.conf.d/optimization.cnf > /dev/null <<EOF
[mysqld]
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_size = 64M
query_cache_type = 1
max_connections = 100
thread_cache_size = 8
EOF

# Restart MySQL
sudo systemctl restart mysql

print_success "MySQL optimized successfully"

# ========================================
# 4. REDIS CONFIGURATION
# ========================================
print_status "Configuring Redis..."

# Optimize Redis configuration
sudo sed -i 's/# maxmemory <bytes>/maxmemory 256mb/' /etc/redis/redis.conf
sudo sed -i 's/# maxmemory-policy noeviction/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf

sudo systemctl restart redis-server

print_success "Redis configured successfully"

# ========================================
# 5. PROJECT SETUP
# ========================================
print_status "Setting up project..."

# Create project directory
sudo mkdir -p $PROJECT_PATH
sudo chown -R $USER:$USER $PROJECT_PATH

# Clone from GitHub
cd $PROJECT_PATH
if [ -d ".git" ]; then
    print_status "Updating existing repository..."
    git pull origin main
else
    print_status "Cloning repository from GitHub..."
    git clone https://github.com/cahyahabib00/Asvira-FTI.git .
fi

# Install Composer dependencies
print_status "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

print_success "Project setup completed"

# ========================================
# 6. ENVIRONMENT CONFIGURATION
# ========================================
print_status "Configuring environment..."

# Create .env file
cp .env.example .env

# Generate application key
php artisan key:generate

# Update .env for production
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i "s/APP_URL=http:\/\/localhost/APP_URL=https:\/\/$DOMAIN/" .env

# Database configuration
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

# Cache configuration
sed -i 's/CACHE_DRIVER=file/CACHE_DRIVER=redis/' .env
sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=redis/' .env
sed -i 's/QUEUE_CONNECTION=sync/QUEUE_CONNECTION=redis/' .env

print_success "Environment configured successfully"

# ========================================
# 7. DATABASE SETUP
# ========================================
print_status "Setting up database..."

# Create database and user
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Run migrations and seed
php artisan migrate --force
php artisan db:seed --force

print_success "Database setup completed"

# ========================================
# 8. LARAVEL OPTIMIZATION
# ========================================
print_status "Optimizing Laravel..."

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

# Create storage link
php artisan storage:link

print_success "Laravel optimization completed"

# ========================================
# 9. NGINX CONFIGURATION
# ========================================
print_status "Configuring Nginx..."

# Create Nginx configuration
sudo tee /etc/nginx/sites-available/$PROJECT_NAME > /dev/null <<EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $PROJECT_PATH/public;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Browser caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        
        # PHP-FPM optimizations
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable site
sudo ln -sf /etc/nginx/sites-available/$PROJECT_NAME /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

print_success "Nginx configured successfully"

# ========================================
# 10. PERMISSIONS & OWNERSHIP
# ========================================
print_status "Setting permissions..."

sudo chown -R www-data:www-data $PROJECT_PATH
sudo chmod -R 755 $PROJECT_PATH
sudo chmod -R 775 $PROJECT_PATH/storage
sudo chmod -R 775 $PROJECT_PATH/bootstrap/cache

print_success "Permissions set successfully"

# ========================================
# 11. SSL CERTIFICATE (Optional)
# ========================================
print_status "Setting up SSL certificate..."

# Check if domain is configured
if [ "$DOMAIN" != "your-domain.com" ]; then
    sudo certbot --nginx -d $DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN
    print_success "SSL certificate installed"
else
    print_warning "SSL certificate skipped - please configure domain first"
fi

# ========================================
# 12. LOG ROTATION
# ========================================
print_status "Setting up log rotation..."

sudo tee /etc/logrotate.d/$PROJECT_NAME > /dev/null <<EOF
$PROJECT_PATH/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
EOF

print_success "Log rotation configured"

# ========================================
# 13. RESTART SERVICES
# ========================================
print_status "Restarting services..."

sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
sudo systemctl restart redis-server
sudo systemctl restart mysql

print_success "Services restarted successfully"

# ========================================
# 14. PERFORMANCE TEST
# ========================================
print_status "Running performance test..."

# Test if application is accessible
if curl -f http://localhost > /dev/null 2>&1; then
    print_success "Application is accessible"
else
    print_error "Application is not accessible - please check configuration"
fi

# ========================================
# 15. DEPLOYMENT COMPLETE
# ========================================
echo ""
echo "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!"
echo "======================================"
echo ""
echo "📋 Deployment Summary:"
echo "   • Project: Asvira FTI Chatbot"
echo "   • Domain: $DOMAIN"
echo "   • Database: $DB_NAME"
echo "   • Path: $PROJECT_PATH"
echo ""
echo "🔗 Access URLs:"
echo "   • Main Site: http://$DOMAIN"
echo "   • Chatbot: http://$DOMAIN/chatbot"
echo "   • Admin Panel: http://$DOMAIN/login"
echo ""
echo "🔐 Admin Credentials:"
echo "   • Email: habib56@gmail.com"
echo "   • Password: uap12345"
echo ""
echo "📊 Monitoring Commands:"
echo "   • Check logs: tail -f $PROJECT_PATH/storage/logs/laravel.log"
echo "   • Check status: sudo systemctl status nginx php8.1-fpm redis-server mysql"
echo "   • Monitor resources: htop"
echo ""
echo "🔄 Update Commands:"
echo "   • cd $PROJECT_PATH && git pull origin main"
echo "   • composer install --no-dev --optimize-autoloader"
echo "   • php artisan migrate --force"
echo "   • php artisan config:cache && php artisan route:cache && php artisan view:cache"
echo "   • sudo systemctl restart nginx php8.1-fpm"
echo ""
echo "✅ Your Asvira FTI Chatbot is now live and optimized!"
echo "" 