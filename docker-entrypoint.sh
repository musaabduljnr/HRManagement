#!/bin/bash

# Clear configurations
rm -f bootstrap/cache/config.php
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate --force

# Disable conflicting MPMs and ensure prefork is active
a2dismod mpm_event || true
a2dismod mpm_worker || true
a2enmod mpm_prefork || true

# Start Apache in the foreground
exec apache2-foreground
