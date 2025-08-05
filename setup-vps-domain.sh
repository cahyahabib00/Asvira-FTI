#!/bin/bash

echo "🚀 Setting up Asvira FTI on VPS with domain asvira.online..."
echo "============================================================"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Update system
print_status "Updating system..."
sudo apt update && sudo apt upgrade -y

# Install required packages
print_status "Installing required packages..."
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y nginx mysql-server php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-redis composer git redis-server htop iotop nethogs jpegoptim optipng ufw certbot python3-certbot-nginx

# Create /var/www directory if it doesn't exist
sudo mkdir -p /var/www

# Extract project if uploaded
if [ -f "asvira-project.tar.gz" ]; then
    print_status "Extracting project..."
    sudo tar -xzf asvira-project.tar.gz -C /var/www/
    sudo mv /var/www/Asvira-FTI /var/www/asvira
    rm asvira-project.tar.gz
else
    # Clone from GitHub
    print_status "Cloning from GitHub..."
    sudo git clone https://github.com/cahyahabib00/Asvira-FTI.git /var/www/asvira
fi

# Set permissions
sudo chown -R www-data:www-data /var/www/asvira
sudo chmod -R 755 /var/www/asvira
sudo chmod -R 775 /var/www/asvira/storage
sudo chmod -R 775 /var/www/asvira/bootstrap/cache

# Install Composer dependencies
cd /var/www/asvira
sudo composer install --no-dev --optimize-autoloader

# Create .env file
sudo cp .env.example .env
sudo php artisan key:generate

# Update .env for production
sudo sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sudo sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sudo sed -i "s/APP_URL=http:\/\/localhost/APP_URL=https:\/\/asvira.online/" .env

# Database configuration
sudo sed -i "s/DB_DATABASE=.*/DB_DATABASE=asvira_prod/" .env
sudo sed -i "s/DB_USERNAME=.*/DB_USERNAME=asvira_user/" .env
sudo sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=Asvira2024!@#/" .env

# Cache configuration
sudo sed -i 's/CACHE_DRIVER=file/CACHE_DRIVER=redis/' .env
sudo sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=redis/' .env
sudo sed -i 's/QUEUE_CONNECTION=sync/QUEUE_CONNECTION=redis/' .env

# Setup database
sudo mysql -e "CREATE DATABASE IF NOT EXISTS asvira_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'asvira_user'@'localhost' IDENTIFIED BY 'Asvira2024!@#';"
sudo mysql -e "GRANT ALL PRIVILEGES ON asvira_prod.* TO 'asvira_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Run migrations and seed
sudo php artisan migrate --force
sudo php artisan db:seed --force

# Laravel optimization
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo php artisan optimize
sudo php artisan storage:link

# Configure Nginx
sudo tee /etc/nginx/sites-available/asvira > /dev/null << 'EOF'
server {
    listen 80;
    server_name asvira.online www.asvira.online;
    root /var/www/asvira/public;

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
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
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
sudo ln -sf /etc/nginx/sites-available/asvira /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Configure firewall
sudo ufw --force enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny 3306/tcp

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart redis-server
sudo systemctl restart mysql

# Install SSL certificate
print_status "Installing SSL certificate..."
sudo certbot --nginx -d asvira.online -d www.asvira.online --non-interactive --agree-tos --email admin@asvira.online

print_success "Setup completed successfully!"
echo ""
echo "🎉 Asvira FTI is now live!"
echo "=========================="
echo "URL: https://asvira.online"
echo "Chatbot: https://asvira.online/chatbot"
echo "Admin: https://asvira.online/login"
echo ""
echo "🔐 Admin Credentials:"
echo "Email: habib56@gmail.com"
echo "Password: uap12345"
echo ""
echo "📊 Monitoring:"
echo "./monitor-production.sh"
echo ""
echo "🔄 Updates:"
echo "./update-production.sh"
echo "" 