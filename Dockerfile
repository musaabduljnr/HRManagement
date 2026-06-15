FROM php:7.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libmcrypt-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        zip \
        bcmath \
        mcrypt \
        mbstring \
        tokenizer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Update Apache configuration to serve /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer 2.2 (last version supporting PHP 7.1)
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy codebase
COPY . /var/www/html/

# Run composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Make entrypoint script executable
RUN chmod +x /var/www/html/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

# Configure Entrypoint
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
