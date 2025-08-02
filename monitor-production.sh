#!/bin/bash

# ========================================
# ASVIRA FTI - PRODUCTION MONITORING SCRIPT
# ========================================

echo "📊 Asvira FTI Production Monitoring"
echo "==================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="asvira"
PROJECT_PATH="/var/www/$PROJECT_NAME"

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

# ========================================
# 1. SYSTEM RESOURCES
# ========================================
echo ""
echo "🖥️  SYSTEM RESOURCES"
echo "==================="

# CPU Usage
CPU_USAGE=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)
if (( $(echo "$CPU_USAGE < 80" | bc -l) )); then
    print_success "CPU Usage: ${CPU_USAGE}%"
else
    print_warning "CPU Usage: ${CPU_USAGE}% (High)"
fi

# Memory Usage
MEMORY_USAGE=$(free | grep Mem | awk '{printf("%.1f", $3/$2 * 100.0)}')
if (( $(echo "$MEMORY_USAGE < 80" | bc -l) )); then
    print_success "Memory Usage: ${MEMORY_USAGE}%"
else
    print_warning "Memory Usage: ${MEMORY_USAGE}% (High)"
fi

# Disk Usage
DISK_USAGE=$(df -h / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -lt 80 ]; then
    print_success "Disk Usage: ${DISK_USAGE}%"
else
    print_warning "Disk Usage: ${DISK_USAGE}% (High)"
fi

# ========================================
# 2. SERVICE STATUS
# ========================================
echo ""
echo "🔧 SERVICE STATUS"
echo "================"

# Check Nginx
if systemctl is-active --quiet nginx; then
    print_success "Nginx: Running"
else
    print_error "Nginx: Stopped"
fi

# Check PHP-FPM
if systemctl is-active --quiet php8.1-fpm; then
    print_success "PHP-FPM: Running"
else
    print_error "PHP-FPM: Stopped"
fi

# Check MySQL
if systemctl is-active --quiet mysql; then
    print_success "MySQL: Running"
else
    print_error "MySQL: Stopped"
fi

# Check Redis
if systemctl is-active --quiet redis-server; then
    print_success "Redis: Running"
else
    print_error "Redis: Stopped"
fi

# ========================================
# 3. APPLICATION HEALTH
# ========================================
echo ""
echo "🌐 APPLICATION HEALTH"
echo "===================="

# Check if application is accessible
if curl -f http://localhost > /dev/null 2>&1; then
    print_success "Application: Accessible"
    
    # Response time
    RESPONSE_TIME=$(curl -s -w "%{time_total}" -o /dev/null http://localhost)
    if (( $(echo "$RESPONSE_TIME < 2" | bc -l) )); then
        print_success "Response Time: ${RESPONSE_TIME}s"
    else
        print_warning "Response Time: ${RESPONSE_TIME}s (Slow)"
    fi
else
    print_error "Application: Not accessible"
fi

# ========================================
# 4. DATABASE STATUS
# ========================================
echo ""
echo "🗄️  DATABASE STATUS"
echo "=================="

# Check database connection
if mysql -u asvira_user -p'Asvira2024!@#' -e "SELECT 1;" > /dev/null 2>&1; then
    print_success "Database: Connected"
    
    # Check table count
    TABLE_COUNT=$(mysql -u asvira_user -p'Asvira2024!@#' asvira_prod -e "SELECT COUNT(*) FROM knowledge_bases;" 2>/dev/null | tail -1)
    print_status "Knowledge Base Records: $TABLE_COUNT"
else
    print_error "Database: Connection failed"
fi

# ========================================
# 5. LOG MONITORING
# ========================================
echo ""
echo "📝 LOG MONITORING"
echo "================"

# Check Laravel logs
if [ -f "$PROJECT_PATH/storage/logs/laravel.log" ]; then
    LOG_SIZE=$(du -h "$PROJECT_PATH/storage/logs/laravel.log" | cut -f1)
    print_status "Laravel Log Size: $LOG_SIZE"
    
    # Check for errors in last 100 lines
    ERROR_COUNT=$(tail -100 "$PROJECT_PATH/storage/logs/laravel.log" | grep -c "ERROR\|CRITICAL" || true)
    if [ "$ERROR_COUNT" -eq 0 ]; then
        print_success "Recent Errors: None"
    else
        print_warning "Recent Errors: $ERROR_COUNT found"
    fi
else
    print_error "Laravel Log: Not found"
fi

# Check Nginx error logs
if [ -f "/var/log/nginx/error.log" ]; then
    NGINX_ERRORS=$(tail -100 /var/log/nginx/error.log | grep -c "error" || true)
    if [ "$NGINX_ERRORS" -eq 0 ]; then
        print_success "Nginx Errors: None"
    else
        print_warning "Nginx Errors: $NGINX_ERRORS found"
    fi
fi

# ========================================
# 6. CACHE STATUS
# ========================================
echo ""
echo "💾 CACHE STATUS"
echo "=============="

# Check Redis memory usage
REDIS_MEMORY=$(redis-cli info memory | grep "used_memory_human:" | cut -d: -f2 | tr -d '\r')
print_status "Redis Memory: $REDIS_MEMORY"

# Check cache hit rate
CACHE_HITS=$(redis-cli info stats | grep "keyspace_hits:" | cut -d: -f2 | tr -d '\r')
CACHE_MISSES=$(redis-cli info stats | grep "keyspace_misses:" | cut -d: -f2 | tr -d '\r')

if [ "$CACHE_HITS" -gt 0 ] && [ "$CACHE_MISSES" -gt 0 ]; then
    HIT_RATE=$(echo "scale=2; $CACHE_HITS * 100 / ($CACHE_HITS + $CACHE_MISSES)" | bc)
    print_status "Cache Hit Rate: ${HIT_RATE}%"
fi

# ========================================
# 7. SECURITY STATUS
# ========================================
echo ""
echo "🔒 SECURITY STATUS"
echo "================="

# Check firewall status
if ufw status | grep -q "Status: active"; then
    print_success "Firewall: Active"
else
    print_error "Firewall: Inactive"
fi

# Check SSL certificate
if [ -f "/etc/letsencrypt/live/your-domain.com/fullchain.pem" ]; then
    SSL_EXPIRY=$(openssl x509 -enddate -noout -in /etc/letsencrypt/live/your-domain.com/fullchain.pem | cut -d= -f2)
    print_status "SSL Certificate: Valid until $SSL_EXPIRY"
else
    print_warning "SSL Certificate: Not configured"
fi

# ========================================
# 8. PERFORMANCE METRICS
# ========================================
echo ""
echo "📈 PERFORMANCE METRICS"
echo "====================="

# Load average
LOAD_AVERAGE=$(uptime | awk -F'load average:' '{print $2}' | awk '{print $1}' | sed 's/,//')
print_status "Load Average: $LOAD_AVERAGE"

# Uptime
UPTIME=$(uptime -p)
print_status "System Uptime: $UPTIME"

# Active connections
ACTIVE_CONNECTIONS=$(ss -tuln | wc -l)
print_status "Active Connections: $ACTIVE_CONNECTIONS"

# ========================================
# 9. RECOMMENDATIONS
# ========================================
echo ""
echo "💡 RECOMMENDATIONS"
echo "================="

# CPU recommendations
if (( $(echo "$CPU_USAGE > 80" | bc -l) )); then
    print_warning "Consider optimizing application or upgrading CPU"
fi

# Memory recommendations
if (( $(echo "$MEMORY_USAGE > 80" | bc -l) )); then
    print_warning "Consider increasing RAM or optimizing memory usage"
fi

# Disk recommendations
if [ "$DISK_USAGE" -gt 80 ]; then
    print_warning "Consider cleaning up disk space or expanding storage"
fi

# Log recommendations
if [ "$ERROR_COUNT" -gt 10 ]; then
    print_warning "High error count detected - review application logs"
fi

# ========================================
# 10. SUMMARY
# ========================================
echo ""
echo "📋 MONITORING SUMMARY"
echo "===================="
echo "Generated: $(date)"
echo "System: $(uname -a | awk '{print $2}')"
echo "Kernel: $(uname -r)"
echo ""

# Overall health score
HEALTH_SCORE=100

if (( $(echo "$CPU_USAGE > 80" | bc -l) )); then HEALTH_SCORE=$((HEALTH_SCORE - 10)); fi
if (( $(echo "$MEMORY_USAGE > 80" | bc -l) )); then HEALTH_SCORE=$((HEALTH_SCORE - 10)); fi
if [ "$DISK_USAGE" -gt 80 ]; then HEALTH_SCORE=$((HEALTH_SCORE - 10)); fi
if ! systemctl is-active --quiet nginx; then HEALTH_SCORE=$((HEALTH_SCORE - 20)); fi
if ! systemctl is-active --quiet mysql; then HEALTH_SCORE=$((HEALTH_SCORE - 20)); fi

if [ "$HEALTH_SCORE" -ge 90 ]; then
    print_success "Overall Health: Excellent ($HEALTH_SCORE%)"
elif [ "$HEALTH_SCORE" -ge 70 ]; then
    print_warning "Overall Health: Good ($HEALTH_SCORE%)"
else
    print_error "Overall Health: Poor ($HEALTH_SCORE%)"
fi

echo ""
echo "✅ Monitoring completed successfully!"
echo "" 