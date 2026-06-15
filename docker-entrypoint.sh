#!/bin/bash

# Clear configurations
rm -f bootstrap/cache/config.php
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate --force

# Reset permissions to ensure www-data has full access to log and cache directories
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Disable conflicting MPMs and ensure prefork is active
a2dismod mpm_event || true
a2dismod mpm_worker || true
a2enmod mpm_prefork || true

# Start Apache in the foreground
exec apache2-foreground
