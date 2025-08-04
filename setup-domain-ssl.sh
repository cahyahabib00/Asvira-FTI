#!/bin/bash

echo "🌐 Setting up SSL Certificate for asvira.online..."

# Wait for DNS propagation
echo "⏳ Waiting for DNS propagation..."
sleep 30

# Check if domain points to our VPS
echo "🔍 Checking DNS propagation..."
nslookup asvira.online

# Install SSL certificate
echo "🔒 Installing SSL certificate..."
sudo certbot --nginx -d asvira.online -d www.asvira.online --non-interactive --agree-tos --email habib56@gmail.com

# Test the domain
echo "🧪 Testing domain..."
curl -s -I https://asvira.online | head -5

echo "✅ Domain setup complete!"
echo "🌐 Your website should now be accessible at:"
echo "   - https://asvira.online"
echo "   - https://asvira.online/chatbot"
echo "   - https://asvira.online/login" 