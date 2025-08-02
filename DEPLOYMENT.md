# 🚀 ASVIRA FTI - PRODUCTION DEPLOYMENT GUIDE

## 📋 Overview

Dokumentasi lengkap untuk deployment aplikasi Asvira FTI Chatbot ke VPS IDCloudHost dengan optimasi performa dan keamanan maksimal.

## 🎯 Target Performance

- **Response Time**: < 2 detik
- **Uptime**: > 99.9%
- **Concurrent Users**: 100+ users
- **Security**: OWASP Top 10 compliant
- **SEO**: Google PageSpeed 90+

## 📦 Scripts Included

### 1. `deploy-production.sh`
Script deployment lengkap untuk setup awal:
- ✅ System optimization
- ✅ Security hardening
- ✅ Performance tuning
- ✅ SSL certificate setup
- ✅ Monitoring setup

### 2. `update-production.sh`
Script update otomatis untuk maintenance:
- ✅ Backup otomatis
- ✅ Git pull & update
- ✅ Cache optimization
- ✅ Rollback protection
- ✅ Health check

### 3. `monitor-production.sh`
Script monitoring real-time:
- ✅ System resources
- ✅ Service status
- ✅ Application health
- ✅ Performance metrics
- ✅ Security status

## 🛠️ Prerequisites

### VPS Requirements
- **OS**: Ubuntu 20.04 LTS atau 22.04 LTS
- **RAM**: Minimal 2GB (Recommended: 4GB)
- **Storage**: Minimal 20GB (Recommended: 50GB)
- **CPU**: 2 cores (Recommended: 4 cores)

### Domain Requirements
- Domain yang sudah diarahkan ke VPS
- DNS A record pointing ke IP VPS
- Email untuk SSL certificate

## 🚀 Quick Deployment

### Step 1: Upload Scripts ke VPS
```bash
# Upload scripts ke VPS
scp deploy-production.sh update-production.sh monitor-production.sh user@your-vps-ip:/home/user/
```

### Step 2: Edit Configuration
```bash
# Edit domain di deploy-production.sh
nano deploy-production.sh
# Ganti "your-domain.com" dengan domain Anda
```

### Step 3: Run Deployment
```bash
# Jalankan deployment
chmod +x deploy-production.sh
./deploy-production.sh
```

## 📊 Monitoring & Maintenance

### Daily Monitoring
```bash
# Check system health
./monitor-production.sh

# Check logs
tail -f /var/www/asvira/storage/logs/laravel.log
```

### Weekly Updates
```bash
# Update application
./update-production.sh

# Check for system updates
sudo apt update && sudo apt upgrade -y
```

### Monthly Maintenance
```bash
# Clean old logs
sudo find /var/log -name "*.log" -mtime +30 -delete

# Optimize database
sudo mysql -e "OPTIMIZE TABLE asvira_prod.knowledge_bases;"

# Check SSL certificate
sudo certbot certificates
```

## 🔧 Manual Configuration

### 1. Environment Variables
```bash
# Edit .env file
nano /var/www/asvira/.env

# Production settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asvira_prod
DB_USERNAME=asvira_user
DB_PASSWORD=strong_password_here

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# AI API
OPENROUTER_API_KEY=your_api_key_here
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
```

### 2. Nginx Configuration
```nginx
# /etc/nginx/sites-available/asvira
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
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
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
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
```

### 3. MySQL Optimization
```sql
-- /etc/mysql/mysql.conf.d/optimization.cnf
[mysqld]
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_size = 64M
query_cache_type = 1
max_connections = 100
thread_cache_size = 8
```

### 4. Redis Configuration
```conf
# /etc/redis/redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

## 🔒 Security Hardening

### 1. Firewall Setup
```bash
# Enable UFW
sudo ufw enable

# Allow necessary ports
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw deny 3306/tcp   # Block MySQL from external
```

### 2. File Permissions
```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/asvira

# Set proper permissions
sudo chmod -R 755 /var/www/asvira
sudo chmod -R 775 /var/www/asvira/storage
sudo chmod -R 775 /var/www/asvira/bootstrap/cache
```

### 3. SSL Certificate
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## 📈 Performance Optimization

### 1. Laravel Optimization
```bash
# Production optimizations
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 2. Database Optimization
```sql
-- Create indexes
CREATE INDEX idx_knowledge_base_category ON knowledge_bases(category);
CREATE INDEX idx_knowledge_base_created ON knowledge_bases(created_at);

-- Optimize tables
OPTIMIZE TABLE knowledge_bases;
ANALYZE TABLE knowledge_bases;
```

### 3. Image Optimization
```bash
# Install optimization tools
sudo apt install jpegoptim optipng

# Optimize images
find /var/www/asvira/public/images -name "*.jpg" -exec jpegoptim --strip-all {} \;
find /var/www/asvira/public/images -name "*.png" -exec optipng {} \;
```

## 🚨 Troubleshooting

### Common Issues

#### 1. Application Not Accessible
```bash
# Check Nginx status
sudo systemctl status nginx

# Check Nginx configuration
sudo nginx -t

# Check PHP-FPM status
sudo systemctl status php8.1-fpm

# Check logs
sudo tail -f /var/log/nginx/error.log
```

#### 2. Database Connection Issues
```bash
# Check MySQL status
sudo systemctl status mysql

# Test database connection
mysql -u asvira_user -p asvira_prod

# Check MySQL logs
sudo tail -f /var/log/mysql/error.log
```

#### 3. High Memory Usage
```bash
# Check memory usage
free -h

# Check PHP-FPM settings
sudo nano /etc/php/8.1/fpm/pool.d/www.conf

# Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

#### 4. Slow Response Time
```bash
# Check Redis status
sudo systemctl status redis-server

# Check cache hit rate
redis-cli info stats

# Clear Laravel cache
php artisan cache:clear
```

## 📊 Monitoring Commands

### System Resources
```bash
# CPU & Memory
htop

# Disk usage
df -h

# Network
nethogs
```

### Application Logs
```bash
# Laravel logs
tail -f /var/www/asvira/storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.1-fpm.log
```

### Database Monitoring
```bash
# MySQL status
sudo mysql -e "SHOW STATUS;"

# Slow queries
sudo tail -f /var/log/mysql/slow.log

# Connection count
sudo mysql -e "SHOW PROCESSLIST;"
```

## 🔄 Backup Strategy

### 1. Database Backup
```bash
# Create backup script
sudo nano /usr/local/bin/backup-asvira.sh

#!/bin/bash
BACKUP_DIR="/var/backups/asvira"
DATE=$(date +%Y%m%d_%H%M%S)

# Database backup
mysqldump -u asvira_user -p'password' asvira_prod > $BACKUP_DIR/db_backup_$DATE.sql

# Application backup
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz /var/www/asvira

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

### 2. Automated Backup
```bash
# Add to crontab
sudo crontab -e

# Daily backup at 2 AM
0 2 * * * /usr/local/bin/backup-asvira.sh
```

## 📞 Support

### Emergency Contacts
- **Developer**: [Your Contact]
- **Server Provider**: IDCloudHost Support
- **Domain Provider**: [Your Domain Provider]

### Useful Commands
```bash
# Restart all services
sudo systemctl restart nginx php8.1-fpm mysql redis-server

# Check all services
sudo systemctl status nginx php8.1-fpm mysql redis-server

# View real-time logs
sudo journalctl -f

# Monitor system resources
htop
```

## ✅ Deployment Checklist

### Pre-Deployment
- [ ] VPS specifications meet requirements
- [ ] Domain DNS configured correctly
- [ ] SSH access configured
- [ ] Backup strategy planned

### Deployment
- [ ] Scripts uploaded to VPS
- [ ] Domain configured in scripts
- [ ] Deployment script executed successfully
- [ ] All services running properly
- [ ] SSL certificate installed
- [ ] Application accessible

### Post-Deployment
- [ ] Performance testing completed
- [ ] Security scan passed
- [ ] Monitoring setup configured
- [ ] Backup system tested
- [ ] Documentation updated

---

**Last Updated**: $(date)
**Version**: 1.0.0
**Author**: Asvira FTI Development Team 