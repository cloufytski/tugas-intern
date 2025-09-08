# === Stage 1: Node.js build for Vite ===
# Removed because npm run build in the local (controlled via Jenkins) and copy all

# === Stage 2: PHP for Laravel app ===
FROM php:8.2-fpm AS php-app

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libldap2-dev \
    libpq-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu \
    && docker-php-ext-install pdo pdo_pgsql gd ldap zip

# Set working directory
WORKDIR /var/www/html

# Copy application source (excluding via .dockerignore)
COPY . .

# Fix directory permissions
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap \
    && chmod -R 775 public storage bootstrap/cache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy compiled frontend assets from node stage (removed because already COPY all)

# Entrypoint
COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]
