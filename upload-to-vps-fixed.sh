#!/bin/bash

# ========================================
# ASVIRA FTI - UPLOAD TO VPS SCRIPT (FIXED)
# ========================================

echo "🚀 Asvira FTI - Upload to VPS"
echo "=============================="

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

# Get VPS information
echo ""
print_status "Masukkan informasi VPS Anda:"
echo ""

read -p "IP Address VPS: " VPS_IP
read -p "Username VPS: " VPS_USER
read -p "Domain (atau IP jika belum punya domain): " VPS_DOMAIN

# Set domain to IP if empty
if [ -z "$VPS_DOMAIN" ]; then
    VPS_DOMAIN="$VPS_IP"
fi

print_status "Uploading files to VPS..."
echo "IP: $VPS_IP"
echo "User: $VPS_USER"
echo "Domain: $VPS_DOMAIN"
echo ""

# Upload deployment scripts to home directory
print_status "Uploading deployment scripts..."

if scp deploy-production.sh update-production.sh monitor-production.sh DEPLOYMENT.md "$VPS_USER@$VPS_IP:/home/$VPS_USER/"; then
    print_success "Deployment scripts uploaded successfully!"
else
    print_error "Failed to upload deployment scripts"
    exit 1
fi

# Upload entire project (optional)
read -p "Upload entire project? (y/n): " UPLOAD_PROJECT

if [[ $UPLOAD_PROJECT =~ ^[Yy]$ ]]; then
    print_status "Uploading entire project..."
    
    # Create temporary tar file
    tar -czf asvira-project.tar.gz --exclude=node_modules --exclude=vendor --exclude=.git .
    
    if scp asvira-project.tar.gz "$VPS_USER@$VPS_IP:/home/$VPS_USER/"; then
        print_success "Project uploaded successfully!"
        
        # Clean up
        rm asvira-project.tar.gz
    else
        print_error "Failed to upload project"
        rm asvira-project.tar.gz
        exit 1
    fi
fi

# Create remote setup script
print_status "Creating remote setup script..."

cat > remote-setup.sh << EOF
#!/bin/bash

echo "🚀 Setting up Asvira FTI on VPS..."
echo "==================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "\${BLUE}[INFO]\${NC} \$1"
}

print_success() {
    echo -e "\${GREEN}[SUCCESS]\${NC} \$1"
}

print_error() {
    echo -e "\${RED}[ERROR]\${NC} \$1"
}

# Update system
print_status "Updating system..."
sudo apt update && sudo apt upgrade -y

# Install required packages
print_status "Installing required packages..."
sudo apt install -y nginx mysql-server php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-redis composer git redis-server htop iotop nethogs jpegoptim optipng ufw certbot python3-certbot-nginx

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
sudo sed -i "s/APP_URL=http:\/\/localhost/APP_URL=https:\/\/$VPS_DOMAIN/" .env

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
sudo tee /etc/nginx/sites-available/asvira > /dev/null << 'NGINX_EOF'
server {
    listen 80;
    server_name $VPS_DOMAIN www.$VPS_DOMAIN;
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
NGINX_EOF

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
sudo systemctl restart php8.1-fpm
sudo systemctl restart redis-server
sudo systemctl restart mysql

print_success "Setup completed successfully!"
echo ""
echo "🎉 Asvira FTI is now live!"
echo "=========================="
echo "URL: http://$VPS_DOMAIN"
echo "Chatbot: http://$VPS_DOMAIN/chatbot"
echo "Admin: http://$VPS_DOMAIN/login"
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
EOF

# Upload remote setup script
if scp remote-setup.sh "$VPS_USER@$VPS_IP:/home/$VPS_USER/"; then
    print_success "Remote setup script uploaded!"
else
    print_error "Failed to upload remote setup script"
    exit 1
fi

# Clean up
rm remote-setup.sh

print_success "All files uploaded successfully!"
echo ""
echo "📋 Next Steps:"
echo "=============="
echo "1. SSH to your VPS:"
echo "   ssh $VPS_USER@$VPS_IP"
echo ""
echo "2. Run setup script:"
echo "   chmod +x remote-setup.sh"
echo "   ./remote-setup.sh"
echo ""
echo "3. Edit domain in deploy-production.sh:"
echo "   nano deploy-production.sh"
echo "   # Change DOMAIN=\"your-domain.com\" to your domain"
echo ""
echo "4. Run deployment:"
echo "   chmod +x deploy-production.sh"
echo "   ./deploy-production.sh"
echo ""
echo "✅ Your Asvira FTI will be live at: http://$VPS_DOMAIN"
echo "" 