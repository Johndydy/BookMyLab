#!/bin/sh

# Replace the port in Nginx config if $PORT is set (provided by Railway)
if [ -n "$PORT" ]; then
  sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/sites-available/default
fi

# Run migrations (optional, but good for first deploy)
# php artisan migrate --force

# Start PHP-FPM in the background
php-fpm -D

# Start Nginx in the foreground
nginx -g "daemon off;"
