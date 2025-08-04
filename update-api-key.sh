#!/bin/bash

echo "🔐 Updating API Key Securely..."

# Update API key on server
ssh habibjanson@103.82.93.100 << 'EOF'
cd /var/www/asvira

echo "📝 Updating .env file with new API key..."
# Backup current .env
cp .env .env.backup

# Update API key in .env file
sed -i 's/OPENROUTER_API_KEY=.*/OPENROUTER_API_KEY=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/' .env
sed -i 's|OPENROUTER_API_URL=.*|OPENROUTER_API_URL=https://openrouter.ai/api/v1/chat/completions|' .env

echo "🔄 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache

echo "🧪 Testing API key..."
php artisan tinker --execute="echo 'API Key configured: ' . (env('OPENROUTER_API_KEY') ? 'YES' : 'NO'); echo PHP_EOL;"

echo "🚀 Restarting services..."
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

echo "✅ API key updated successfully!"
EOF

echo "🎉 API key has been updated securely on the server!"
echo "🔗 Test your chatbot at: https://asvira.online/chatbot" 